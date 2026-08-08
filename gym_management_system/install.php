<?php
require_once 'config/database.php';

echo "<h2>Installing Database...</h2>";

$sqlFile = __DIR__ . '/sql/schema.sql';

if (!file_exists($sqlFile)) {
    die("schema.sql not found");
}

$sql = file_get_contents($sqlFile);

// Split by ; but keep it simple - PDO will exec multiple?
try {
    $pdo->exec("USE gym_management");

    // Execute file content as multiple statements
    // For safety, split and exec one by one ignoring comments
    $statements = array_filter(
        array_map(
            'trim',
            explode(
                ';',
                $sql
            )
        )
    );

    $count = 0;

    foreach ($statements as $stmt) {
        if (empty($stmt) || strpos($stmt, '--') === 0) {
            continue;
        }

        if (strlen($stmt) < 10) {
            continue;
        }

        try {
            $pdo->exec($stmt);
            $count++;
        } catch (Exception $e) {
            // Ignore errors like duplicate inserts
            // echo "Error: " . $e->getMessage() . "<br>";
        }
    }

    echo "<p style='color:green;'>✅ Database imported! $count queries executed.</p>";
    echo "<p>Tables created. You can now <a href='index.php'>Login</a> with admin / password</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}
?>
