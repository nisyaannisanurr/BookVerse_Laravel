<?php
// Check which tables lack updated_at
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=bookverse', 'root', '');
$tables = $pdo->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'bookverse' AND TABLE_NAME NOT IN ('cache','sessions','migrations','cache_locks','roles')")->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    $cols = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='bookverse' AND TABLE_NAME='$table'")->fetchAll(PDO::FETCH_COLUMN);
    $hasUpdated = in_array('updated_at', $cols);
    $hasCreated = in_array('created_at', $cols);
    echo "$table: created=$" . ($hasCreated?'YES':'NO') . " updated=" . ($hasUpdated?'YES':'NO') . "\n";
}
