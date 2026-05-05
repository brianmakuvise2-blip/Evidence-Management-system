<?php
$db = new PDO("sqlite:" . __DIR__ . "/database/database.sqlite");

echo "=== USERS TABLE COLUMNS ===\n";
$cols = $db->query("PRAGMA table_info(users)")->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $c) {
    echo "  [{$c['cid']}] {$c['name']} ({$c['type']}) default={$c['dflt_value']} notnull={$c['notnull']}\n";
}

echo "\n=== MIGRATIONS RUN ===\n";
$migs = $db->query("SELECT migration FROM migrations ORDER BY batch, id")->fetchAll(PDO::FETCH_COLUMN);
foreach ($migs as $m) echo "  $m\n";

echo "\n=== ROLES IN DB ===\n";
$roles = $db->query("SELECT id, name FROM roles ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
foreach ($roles as $r) echo "  ID:{$r['id']} {$r['name']}\n";

echo "\n=== INSTITUTIONS IN DB ===\n";
$insts = $db->query("SELECT id, name, is_active FROM institutions ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
foreach ($insts as $i) echo "  ID:{$i['id']} {$i['name']} active={$i['is_active']}\n";
