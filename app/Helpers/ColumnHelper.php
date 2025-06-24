<?php

use App\Models\BoardColumns;

/**
 * Generate Excel-style column names from position numbers
 * A, B, C, ..., Z, AA, AB, AC, ..., AZ, BA, BB, etc.
 * 
 * @param int $position Position number (1-based)
 * @return string Excel-style column name
 * @throws InvalidArgumentException If position is less than 1
 */
function generateColumnName(int $position): string
{
    if ($position < 1) {
        throw new InvalidArgumentException('Position must be greater than 0');
    }

    $columnName = '';

    while ($position > 0) {
        $position--; // Convert to 0-based for modulo calculation
        $columnName = chr(65 + ($position % 26)) . $columnName;
        $position = intval($position / 26);
    }

    return $columnName;
}

/**
 * Get the next available position for a board's columns
 * 
 * @param int $boardId
 * @return int Next position number
 */
function getNextColumnPosition(int $boardId): int
{
    // This would typically query the database
    // For now, returning a placeholder
    return BoardColumns::where('board_id', $boardId)
        ->max('position') + 1 ?? 1;
}

/**
 * Generate default column names for a new board (A through T)
 * 
 * @return array Array of column names
 */
function generateDefaultColumnNames(): array
{
    $names = [];
    for ($i = 1; $i <= 20; $i++) {
        $names[] = generateColumnName($i);
    }
    return $names;
}

/**
 * Create default columns for a board
 * 
 * @param int $boardId
 * @return array Created column data
 */
function createDefaultBoardColumns(int $boardId): array
{
    $columns = [];
    $names = generateDefaultColumnNames();

    foreach ($names as $index => $name) {
        $columns[] = [
            'board_id' => $boardId,
            'title' => $name,
            'position' => $index + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // Bulk insert for better performance
    BoardColumns::insert($columns);

    return $columns;
}

// Example usage and test cases (for reference):
/*
echo generateColumnName(1);    // A
echo generateColumnName(26);   // Z
echo generateColumnName(27);   // AA
echo generateColumnName(52);   // AZ
echo generateColumnName(53);   // BA
echo generateColumnName(702);  // ZZ
echo generateColumnName(703);  // AAA
*/