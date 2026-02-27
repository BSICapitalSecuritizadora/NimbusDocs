<?php
require __DIR__ . "/vendor/autoload.php";
$pdo = new PDO("mysql:host=db;dbname=nimbusdocs;charset=utf8mb4", "root", "root");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$term = "%172%";
$where = [];
$params = [];
$where[] = "(details LIKE :search_details OR ip_address LIKE :search_ip OR actor_id LIKE :search_actor OR target_id LIKE :search_target)";
$params[":search_details"] = $term;
$params[":search_ip"]      = $term;
$params[":search_actor"]   = $term;
$params[":search_target"]  = $term;

$whereSql = "WHERE " . implode(" AND ", $where);
try {
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM audit_logs $whereSql");
    foreach ($params as $key => $val) {
        $stmtCount->bindValue($key, $val);
    }
    $stmtCount->execute();
    echo "Audit test: Success\n";
} catch (Exception $e) {
    echo "Audit test: " . $e->getMessage() . "\n";
}

