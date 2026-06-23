<?php

declare(strict_types=1);
include "../db_connect.php";
const OIM_DB_FILE = __DIR__ . 'db_connect.php';

function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $folder = dirname(OIM_DB_FILE);

    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }

    $pdo = new PDO('sqlite:' . OIM_DB_FILE);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    ensureSchema($pdo);

    return $pdo;
}

function ensureSchema(PDO $pdo): void
{
    $pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    userid TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    password TEXT NOT NULL,
    role TEXT NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);
SQL
    );

    $pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS requisitions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    section_name TEXT NOT NULL,
    branch_officer TEXT NOT NULL,
    item TEXT NOT NULL,
    model TEXT,
    quantity INTEGER NOT NULL DEFAULT 1,
    status TEXT NOT NULL DEFAULT 'pending',
    remarks TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
);
SQL
    );

    $seedUsers = [
        ['userid' => 'so_user', 'name' => 'Section Officer', 'role' => 'Section Officer'],
        ['userid' => 'bo_user', 'name' => 'Branch Officer', 'role' => 'Branch Officer'],
        ['userid' => 'itsc_user', 'name' => 'ITSC Operator', 'role' => 'ITSC'],
    ];

    $stmt = $pdo->prepare('INSERT OR IGNORE INTO users (userid, name, password, role) VALUES (:userid, :name, :password, :role)');

    foreach ($seedUsers as $user) {
        $stmt->execute([
            ':userid' => $user['userid'],
            ':name' => $user['name'],
            ':password' => password_hash('12345', PASSWORD_DEFAULT),
            ':role' => $user['role'],
        ]);
    }
}

function getUserByUserid(string $userid): ?array
{
    $stmt = getPDO()->prepare('SELECT * FROM users WHERE userid = :userid LIMIT 1');
    $stmt->execute([':userid' => $userid]);

    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function updateUserPassword(string $userid, string $hash): bool
{
    $stmt = getPDO()->prepare('UPDATE users SET password = :password WHERE userid = :userid');

    return $stmt->execute([
        ':password' => $hash,
        ':userid' => $userid,
    ]);
}

function insertRequisition(array $payload): int
{
    $stmt = getPDO()->prepare(<<<'SQL'
INSERT INTO requisitions (section_name, branch_officer, item, model, quantity, status, remarks)
VALUES (:section_name, :branch_officer, :item, :model, :quantity, :status, :remarks)
SQL
    );

    $stmt->execute([
        ':section_name' => trim($payload['section_name'] ?? ''),
        ':branch_officer' => $payload['branch_officer'],
        ':item' => $payload['item'],
        ':model' => $payload['model'] ?? null,
        ':quantity' => (int) ($payload['quantity'] ?? 1),
        ':status' => $payload['status'] ?? 'pending',
        ':remarks' => $payload['remarks'] ?? null,
    ]);

    return (int) getPDO()->lastInsertId();
}

function updateRequisitionStatus(int $id, string $status, ?string $remarks = null): bool
{
    $stmt = getPDO()->prepare('UPDATE requisitions SET status = :status, remarks = :remarks, updated_at = CURRENT_TIMESTAMP WHERE id = :id');

    return $stmt->execute([
        ':status' => $status,
        ':remarks' => $remarks,
        ':id' => $id,
    ]);
}

function fetchRequisitionsForBranchOfficer(string $branchOfficer, array $statuses = []): array
{
    $sql = 'SELECT * FROM requisitions WHERE branch_officer = ?';
    $params = [$branchOfficer];

    if (!empty($statuses)) {
        $placeholder = implode(',', array_fill(0, count($statuses), '?'));
        $sql .= ' AND status IN (' . $placeholder . ')';
        $params = array_merge($params, $statuses);
    }

    $sql .= ' ORDER BY updated_at DESC';

    $stmt = getPDO()->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchRequisitionsByStatuses(array $statuses): array
{
    if (empty($statuses)) {
        return [];
    }

    $placeholder = implode(',', array_fill(0, count($statuses), '?'));
    $stmt = getPDO()->prepare('SELECT * FROM requisitions WHERE status IN (' . $placeholder . ') ORDER BY updated_at DESC');
    $stmt->execute($statuses);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchLastIssueDate(string $item): ?string
{
    if ($item === '') {
        return null;
    }

    $stmt = getPDO()->prepare('SELECT updated_at FROM requisitions WHERE item = ? ORDER BY updated_at DESC LIMIT 1');
    $stmt->execute([$item]);

    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$record) {
        return null;
    }

    $timestamp = strtotime($record['updated_at']);

    return $timestamp ? date('d-M-Y', $timestamp) : null;
}

function fetchBranchOfficers(): array
{
    $stmt = getPDO()->query('SELECT userid, name FROM users WHERE role = "Branch Officer" ORDER BY name ASC');

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function countPendingRequisitionsForBranch(string $branchOfficer): int
{
    $stmt = getPDO()->prepare('SELECT COUNT(*) FROM requisitions WHERE branch_officer = ? AND status = ?');
    $stmt->execute([$branchOfficer, 'pending']);

    return (int) $stmt->fetchColumn();
}
