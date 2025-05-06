<?php
// calculates the remaining time of a membership
function getRemainingTime(DateTimeImmutable $end): string {
    $now = new DateTimeImmutable();
    if ($now > $end) return 'Expired';

    $diff = $now->diff($end);
    $months = $diff->y * 12 + $diff->m;
    $days = $diff->d;

    return ($months ? "$months month(s) " : "") . ($days ? "$days day(s)" : "");
}

// search handler
function searchTable(PDO $pdo, string $table, array $columns, string $condition = '', string $order = '', string $searchTerm = ''): array {
    if (!empty($searchTerm)) {
        $like = '%' . $searchTerm . '%';
        $searchConditions = array_map(fn($col) => "$col LIKE ?", $columns);
        $where = implode(" OR ", $searchConditions);

        $sql = "SELECT * FROM $table WHERE ($where)";
        if ($condition) $sql .= " AND ($condition)";
        if ($order) $sql .= " ORDER BY $order";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_fill(0, count($columns), $like));
        return $stmt->fetchAll();
    } else {
        $sql = "SELECT * FROM $table";
        if ($condition) $sql .= " WHERE $condition";
        if ($order) $sql .= " ORDER BY $order";

        return $pdo->query($sql)->fetchAll();
    }
}
?>
