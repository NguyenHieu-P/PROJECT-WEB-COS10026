<?php
// About Page — Bùi Đào Việt Anh.
function about_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function about_load_contributions(): array
{
    require __DIR__ . '/settings.php';
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $connection = new mysqli($host, $user, $pwd, $sql_db);
    try {
        $connection->set_charset('utf8mb4');
        $result = $connection->query('SELECT student_id, part1, part2 FROM about');
        $members = [];
        while ($row = $result->fetch_assoc()) {
            $members[$row['student_id']] = $row;
        }
        $result->free();
        return $members;
    } finally {
        $connection->close();
    }
}
