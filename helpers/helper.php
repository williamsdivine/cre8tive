<?php
function userid(mysqli $conn, string $table, string $field, int $length = 3, string $prefix = 'usr'): string
{
    $id = '';

    // Get max number based on length (e.g., 999 for length 3)
    $max = (int)str_repeat('9', $length); // "999" → 999

    for ($i = 1; $i <= $max; $i++) {
        // Pad number with zeros on the left to match the length
        $num = str_pad($i, $length, '0', STR_PAD_LEFT); // e.g., 1 → 001
        $id = $prefix . $num; // e.g., usr001

        // Check if the ID already exists in the table
        $safeId = $conn->real_escape_string($id);
        $sql = "SELECT COUNT(*) AS count FROM `$table` WHERE `$field` = '$safeId'";
        $result = $conn->query($sql);

        if ($result) {
            $row = $result->fetch_assoc();
            if ((int)$row['count'] > 0) {
                continue; // ID exists, try next
            } else {
                break; // ID is unique, stop
            }
        } else {
            return ''; // Query failed
        }
    }

    return $id;
}
