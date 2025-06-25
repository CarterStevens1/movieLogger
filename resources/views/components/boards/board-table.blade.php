@props(['board'])
@php
    // Method 1: Simple conversion with default colors
    $tagsArray = collect(explode(',', $board->tags))
        ->filter() // Remove empty values
        ->map(function ($tag, $index) {
            $colors = ['#ef4444', '#f59e0b', '#10b981', '#6366f1', '#8b5cf6', '#ec4899', '#06b6d4'];
            return [
                'name' => trim($tag),
                'color' => $colors[$index % count($colors)],
            ];
        })
        ->values()
        ->toArray();
@endphp

<!-- Hidden file input for CSV import -->
<input type="file" id="csvFileInput" accept=".csv" style="display: none;" onchange="importCSV(event)">

<!-- Tag menu-->
<div id="contextMenu" class="hidden fixed bg-white border border-gray-300 rounded shadow-lg z-50 py-2 min-w-40">
    <div class="px-3 py-1 text-xs font-semibold text-gray-500 border-b border-gray-200 mb-1 pb-3">Apply Tag
    </div>
    <div id="contextMenuTags" class="text-gray-500 uppercase font-medium"></div>
    <div class="border-t border-gray-200 mt-1 pt-1">
        <div class="px-3 py-1 hover:bg-gray-100 cursor-pointer text-sm text-red-600" onclick="removeTag()">Remove Tag
        </div>
    </div>
    <div class="space-y-3 px-3 border-t border-gray-200 mt-1 pt-1">
        <p class="px-3 py-1 text-xs font-semibold text-gray-500 border-b border-gray-200 mb-1 pb-3">Options</p>
        <button onclick="document.getElementById('csvFileInput').click()"
            class="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700 transition-colors text-sm">
            Import CSV
        </button>
        <button onclick="exportCSV()"
            class="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700 transition-colors text-sm">
            Export CSV
        </button>
    </div>
</div>

<!-- Column context menu for sorting -->
<div id="columnContextMenu"
    class="hidden fixed bg-white border border-gray-300 rounded shadow-lg z-50 py-2 min-w-40
[&_div]:px-3 [&_div]:py-1 [&_div]:text-gray-500">
    <div class="text-xs font-semibold border-b border-gray-200 mb-1 pb-3">Sort Column
    </div>
    <div class="hover:bg-gray-100 cursor-pointer text-sm" onclick="sortColumn('asc')">Sort A-Z
    </div>
    <div class="hover:bg-gray-100 cursor-pointer text-sm" onclick="sortColumn('desc')">Sort Z-A
    </div>
</div>

<div class="rounded overflow-auto max-h-screen shadow-md">
    <table class="border-collapse w-full min-w-max" id="excelTable">
        <thead>
            <tr id="headerRow" class="[&_th]:h-6 [&_th]:relative [&_th]:border-white/20 [&_th]:p-0 [&_th]:text-center">
                <th class="w-10 min-w-10 border-b-2 text-xs font-bold">
                </th>
                @foreach ($board->columns as $column)
                    <th class="min-w-20 cursor-pointer border-s-1 border-b-3 text-xs font-bold hover:bg-white/10"
                        oncontextmenu="showColumnMenu(event, {{ $column->column_index }})"
                        data-col="{{ $column->column_index }}" data-column-id="{{ $column->id }}">
                        {{ $column->label }}
                        <span class="sort-indicator ml-1 text-xs" id="sort-{{ $column->column_index }}">
                            @if ($column->sort_config)
                                {{ $column->sort_config['direction'] === 'asc' ? '↑' : '↓' }}
                            @endif
                        </span>
                    </th>
                @endforeach
                <th class="min-w-20 cursor-pointer border align-middle text-base transition-colors select-none"
                    onclick="addColumn()">+</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            @foreach ($board->rows as $row)
                <tr class="*:w-auto [&_td]:h-6 [&_td]:relative [&_td]:border [&_td]:border-white/20 [&_td]:p-0">
                    <td class="w-10 min-w-10 border-r-3 text-center text-xs font-bold"
                        data-row-id="{{ $row->id }}">
                        {{ $row->label }}
                    </td>
                    @foreach ($board->columns as $column)
                        <td class="min-w-30" data-cell-id="{{ $row->row_index }}-{{ $column->column_index }}">
                            <input type="text"
                                class="cell-input size-full resize-none border-none bg-transparent px-1.5 py-1 font-sans text-xs outline-none"
                                data-row="{{ $row->row_index }}" data-col="{{ $column->column_index }}"
                                data-row-id="{{ $row->id }}" data-column-id="{{ $column->id }}"
                                onchange="saveCell(this)" oncontextmenu="showTagMenu(event, this)"
                                ontouchstart="handleTouchStart(event, this)" ontouchend="handleTouchEnd(event, this)">
                        </td>
                    @endforeach
                    <td class="min-w-30 cursor-pointer text-center align-middle text-base transition-colors select-none"
                        onclick="addColumn()">+</td>
                </tr>
            @endforeach
            <tr
                class="add-row-tr [&_td]:cursor-pointer [&_td]:transition-colors [&_td]:text-center [&_td]:align-middle [&_td]:text-base [&_td]:text-gray-600 [&_td]:select-none [&_td]:border [&_td]:border-white/20 [&_td]:p-0 [&_td]:relative [&_td]:min-w-10 [&_td]:w-10 [&_td]:h-6">
                <td onclick="addRow()">+</td>
                @foreach ($board->columns as $column)
                    <td onclick="addRow()">+</td>
                @endforeach
                <td onclick="addRow()">+</td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    // Add to your existing script section
    let currentRows = {{ $board->rows->count() }};
    let currentCols = {{ $board->columns->count() }};
    let tableData = {};
    let cellTags = {};
    let tags = @json($tagsArray ?? []);
    let boardId = {{ $board->id }};
    let boardColumns = @json($board->columns->pluck('column_index')->toArray());
    let boardRows = @json($board->rows->pluck('row_index')->toArray());
    let existingCells = @json(
        $board->cells->keyBy(function ($cell) {
            return $cell->boardRow->row_index . '-' . $cell->boardColumn->column_index;
        }));

    let activeCell = null;
    let activeColumn = null;
    let touchStartTime = 0;
    let touchTimer = null;
    let touchMoved = false;
    let saveTimeouts = {};
    let pendingCellSaves = {};
    let autoSaveInterval;
    let hasUnsavedChanges = false;

    Object.entries(existingCells).forEach(([key, cellData]) => {
        const [row, col] = key.split('-');
        const input = document.querySelector(`input[data-row="${row}"][data-col="${col}"]`);

        if (input && cellData) {
            // Set value
            if (cellData.value) {
                input.value = cellData.value;
                tableData[key] = cellData.value;
            }

            // Set cell ID for future updates
            input.setAttribute('data-cell-id', cellData.id);

            // Apply tag if exists
            if (cellData.tag_config) {
                const cell = input.parentElement;
                cell.style.backgroundColor = cellData.tag_config.color;

                if (isDarkColor(cellData.tag_config.color)) {
                    input.style.color = 'white';
                } else {
                    input.style.color = 'black';
                }

                cellTags[key] = cellData.tag_config;
            }
        }
    });

    function startAutoSave() {
        autoSaveInterval = setInterval(async () => {
            if (hasUnsavedChanges) {
                await saveAllPendingCells();
                hasUnsavedChanges = false;
            }
        }, 10000); // Auto-save every 10 seconds
    }


    // Mobile touch handlers
    function handleTouchStart(event, input) {
        touchStartTime = Date.now();
        touchMoved = false;

        // Long press detection - show context menu after 500ms
        touchTimer = setTimeout(() => {
            if (!touchMoved) {
                // Prevent default touch behavior
                event.preventDefault();

                // Create a synthetic event with touch coordinates
                const touch = event.touches[0];
                const syntheticEvent = {
                    clientX: touch.clientX,
                    clientY: touch.clientY,
                    preventDefault: () => {}
                };

                // Show context menu
                showTagMenu(syntheticEvent, input);

                // Add haptic feedback if available
                if (navigator.vibrate) {
                    navigator.vibrate(50);
                }
            }
        }, 500);

        // Listen for touch move to cancel long press
        input.addEventListener('touchmove', handleTouchMove, {
            once: true
        });
    }

    function handleTouchEnd(event, input) {
        const touchDuration = Date.now() - touchStartTime;

        // Clear the long press timer
        if (touchTimer) {
            clearTimeout(touchTimer);
            touchTimer = null;
        }

        // If it was a short tap and context menu is open, close it
        if (touchDuration < 500 && !touchMoved) {
            const contextMenu = document.getElementById('contextMenu');
            if (!contextMenu.classList.contains('hidden')) {
                hideTagMenu();
            }
        }
    }

    function handleTouchMove(event) {
        touchMoved = true;
        if (touchTimer) {
            clearTimeout(touchTimer);
            touchTimer = null;
        }
    }

    // Show context menu
    function showTagMenu(event, input) {
        event.preventDefault();
        activeCell = input;

        const contextMenu = document.getElementById('contextMenu');
        const contextMenuTags = document.getElementById('contextMenuTags');

        // Clear existing menu items
        contextMenuTags.innerHTML = '';

        // Add tag options
        tags.forEach(tag => {
            const menuItem = document.createElement('div');
            menuItem.className = 'px-3 py-1 hover:bg-gray-100 cursor-pointer text-sm flex items-center gap-2';
            menuItem.innerHTML = `
                    <div class="w-4 h-4 rounded" style="background-color: ${tag.color}"></div>
                    <span>${tag.name}</span>
                `;
            menuItem.onclick = () => applyTag(tag);
            contextMenuTags.appendChild(menuItem);
        });

        // Position menu at click location (viewport relative)
        let x = event.clientX;
        let y = event.clientY;

        // Ensure menu doesn't go off-screen
        const menuRect = contextMenu.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        // Show menu temporarily to get dimensions
        contextMenu.classList.remove('hidden');
        const menuWidth = contextMenu.offsetWidth;
        const menuHeight = contextMenu.offsetHeight;

        // Adjust position if menu would go off-screen
        if (x + menuWidth > viewportWidth) {
            x = viewportWidth - menuWidth - 10;
        }
        if (y + menuHeight > viewportHeight) {
            y = viewportHeight - menuHeight - 10;
        }

        // Ensure minimum margins
        x = Math.max(10, x);
        y = Math.max(10, y);

        // Position menu
        contextMenu.style.left = x + 'px';
        contextMenu.style.top = y + 'px';
        contextMenu.style.position = 'fixed'; // This ensures it stays relative to viewport
    }

    // Show column context menu for sorting
    function showColumnMenu(event, colIndex) {
        event.preventDefault();
        activeColumn = colIndex;

        const columnContextMenu = document.getElementById('columnContextMenu');

        // Position menu at click location
        let x = event.clientX;
        let y = event.clientY;

        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        columnContextMenu.classList.remove('hidden');
        const menuWidth = columnContextMenu.offsetWidth;
        const menuHeight = columnContextMenu.offsetHeight;

        if (x + menuWidth > viewportWidth) {
            x = viewportWidth - menuWidth - 10;
        }
        if (y + menuHeight > viewportHeight) {
            y = viewportHeight - menuHeight - 10;
        }

        x = Math.max(10, x);
        y = Math.max(10, y);

        columnContextMenu.style.left = x + 'px';
        columnContextMenu.style.top = y + 'px';
        columnContextMenu.style.position = 'fixed';
    }

    // Hide context menus
    function hideTagMenu() {
        document.getElementById('contextMenu').classList.add('hidden');
        activeCell = null;
    }

    function hideColumnMenu() {
        document.getElementById('columnContextMenu').classList.add('hidden');
        activeColumn = null;
    }


    // Update sortColumn function to save to database
    async function sortColumn(direction) {
        if (activeColumn === null) return;

        // Get all rows except the header and add-row
        const tableBody = document.getElementById('tableBody');
        const rows = Array.from(tableBody.querySelectorAll('tr:not(.add-row-tr)'));

        // Get column index in the boardColumns array
        const colIndex = boardColumns.indexOf(activeColumn);

        if (colIndex === -1) return;

        // Create array of row data with original positions
        const rowData = rows.map((row, index) => {
            const cellInput = row.children[colIndex + 1]?.querySelector('input');
            const value = (cellInput?.value || '').toString().toLowerCase();
            const rowId = row.getAttribute('data-row-id'); // Assuming you have row IDs

            return {
                row: row,
                value: value,
                originalIndex: index,
                rowId: rowId
            };
        });

        // Sort the data array
        rowData.sort((a, b) => {
            if (direction === 'asc') {
                return a.value.localeCompare(b.value);
            } else {
                return b.value.localeCompare(a.value);
            }
        });

        // Update the actual data positions in the database
        const updates = [];
        rowData.forEach((item, newIndex) => {
            if (item.rowId) {
                updates.push({
                    rowId: item.rowId,
                    newPosition: newIndex + 1 // Positions usually start from 1
                });
            }
        });

        // Send position updates to database
        try {
            await fetch('/board-cells/reorder', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content')
                },
                body: JSON.stringify({
                    updates: updates
                })
            });
        } catch (error) {
            console.error('Error updating row positions:', error);
            return; // Don't update UI if database update fails
        }

        // Update the UI based on new positions
        // Clear existing rows from tbody (except add-row)
        const addRowTr = tableBody.querySelector('.add-row-tr');
        tableBody.innerHTML = '';

        // Re-append rows in sorted order but update their row numbers
        rowData.forEach((item, index) => {
            const row = item.row;
            // Update the row number cell (first cell)
            const rowNumberCell = row.children[0];
            if (rowNumberCell) {
                rowNumberCell.textContent = index + 1; // Update row number
            }

            // Update any data attributes that track position
            row.setAttribute('data-position', index + 1);

            tableBody.appendChild(row);
        });

        // Re-append add-row at the end
        if (addRowTr) {
            tableBody.appendChild(addRowTr);
        }

        // Clear all sort indicators first
        clearSortIndicators();

        // Update sort indicator for active column
        const indicator = document.getElementById(`sort-${activeColumn}`);
        if (indicator) {
            indicator.textContent = direction === 'asc' ? '↑' : '↓';
        }

        // Save sort config to database
        const columnElement = document.querySelector(`th[data-col="${activeColumn}"]`);
        const columnId = columnElement?.getAttribute('data-column-id');

        if (columnId) {
            try {
                await fetch(`/board-columns/${columnId}/sort`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        sort_config: {
                            direction: direction,
                            timestamp: Date.now()
                        }
                    })
                });
            } catch (error) {
                console.error('Error saving sort config:', error);
            }
        }

        hideColumnMenu();
    }


    // Clear all sort indicators
    function clearSortIndicators() {
        for (let col = 0; col < currentCols; col++) {
            const indicator = document.getElementById(`sort-${col}`);
            if (indicator) {
                indicator.textContent = '';
            }
        }
    }

    // CSV Import functionality
    function importCSV(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const csvData = e.target.result;
            parseCSV(csvData);
        };
        reader.readAsText(file);
    }

    function parseCSV(csvData) {
        const lines = csvData.split('\n');
        const data = lines.map(line => {
            const result = [];
            let current = '';
            let inQuotes = false;

            for (let i = 0; i < line.length; i++) {
                const char = line[i];
                if (char === '"') {
                    inQuotes = !inQuotes;
                } else if (char === ',' && !inQuotes) {
                    result.push(current.trim());
                    current = '';
                } else {
                    current += char;
                }
            }
            result.push(current.trim());
            return result;
        }).filter(row => row.some(cell => cell !== ''));

        // Add columns if needed
        const maxCols = Math.max(...data.map(row => row.length));
        const columnsToAdd = maxCols - currentCols;
        if (columnsToAdd > 0) {
            for (let i = 0; i < columnsToAdd; i++) {
                addColumn(); // This will handle database creation
            }
        }

        // Add rows if needed
        const rowsToAdd = data.length - currentRows;
        if (rowsToAdd > 0) {
            for (let i = 0; i < rowsToAdd; i++) {
                addRow(); // This will handle database creation
            }
        }

        // Wait a moment for DOM updates, then populate data
        setTimeout(async () => {
            const cellsToSave = [];

            data.forEach((row, rowIndex) => {
                row.forEach((cellValue, colIndex) => {
                    const actualRowIndex = boardRows[rowIndex];
                    const actualColIndex = boardColumns[colIndex];
                    if (actualRowIndex !== undefined && actualColIndex !== undefined &&
                        cellValue.trim() !== '') {
                        const input = document.querySelector(
                            `input[data-row="${actualRowIndex}"][data-col="${actualColIndex}"]`
                        );
                        if (input) {
                            input.value = cellValue;
                            const rowId = input.getAttribute('data-row-id');
                            const columnId = input.getAttribute('data-column-id');

                            if (rowId && columnId) {
                                cellsToSave.push({
                                    board_row_id: parseInt(rowId),
                                    board_column_id: parseInt(columnId),
                                    value: cellValue
                                });
                            }
                        }
                    }
                });
            });

            // Bulk save all cells at once
            if (cellsToSave.length > 0) {
                try {
                    await fetch('/board-cells/bulk', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content')
                        },
                        body: JSON.stringify({
                            board_id: boardId,
                            cells: cellsToSave
                        })
                    });
                } catch (error) {
                    console.error('Error bulk saving cells:', error);
                }
            }
        }, 100);
    }

    // CSV Export functionality
    function exportCSV() {
        const csvData = [];

        // Determine actual data bounds using database indices
        let maxRowIndex = 0;
        let maxColIndex = 0;

        boardRows.forEach(rowIndex => {
            boardColumns.forEach(colIndex => {
                const input = document.querySelector(
                    `input[data-row="${rowIndex}"][data-col="${colIndex}"]`);
                if (input && input.value.trim() !== '') {
                    maxRowIndex = Math.max(maxRowIndex, boardRows.indexOf(rowIndex));
                    maxColIndex = Math.max(maxColIndex, boardColumns.indexOf(colIndex));
                }
            });
        });

        // Build CSV data
        for (let rowIdx = 0; rowIdx <= maxRowIndex; rowIdx++) {
            const rowData = [];
            const actualRowIndex = boardRows[rowIdx];

            for (let colIdx = 0; colIdx <= maxColIndex; colIdx++) {
                const actualColIndex = boardColumns[colIdx];
                const input = document.querySelector(
                    `input[data-row="${actualRowIndex}"][data-col="${actualColIndex}"]`);
                let cellValue = input ? input.value || '' : '';

                // Escape CSV special characters
                if (cellValue.includes(',') || cellValue.includes('"') || cellValue.includes('\n')) {
                    cellValue = '"' + cellValue.replace(/"/g, '""') + '"';
                }

                rowData.push(cellValue);
            }
            csvData.push(rowData.join(','));
        }

        // Create and download file
        const csvContent = csvData.join('\n');
        const blob = new Blob([csvContent], {
            type: 'text/csv;charset=utf-8;'
        });
        const link = document.createElement('a');

        if (link.download !== undefined) {
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'spreadsheet_data.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }

    // Apply tag to cell
    function applyTag(tag) {
        if (activeCell) {
            const row = activeCell.getAttribute('data-row');
            const col = activeCell.getAttribute('data-col');
            const cellId = `${row}-${col}`;

            cellTags[cellId] = tag;

            // Apply background color to cell
            const cell = activeCell.parentElement;
            cell.style.backgroundColor = tag.color;

            // Make text white if background is dark
            if (isDarkColor(tag.color)) {
                activeCell.style.color = 'white';
            } else {
                activeCell.style.color = 'black';
            }

            // Save to database
            saveCellTag(activeCell, tag);
        }
        hideTagMenu();
    }

    // Remove tag from cell
    function removeTag() {
        if (activeCell) {
            const row = activeCell.getAttribute('data-row');
            const col = activeCell.getAttribute('data-col');
            const cellId = `${row}-${col}`;

            delete cellTags[cellId];

            // Remove background color
            const cell = activeCell.parentElement;
            cell.style.backgroundColor = '';
            activeCell.style.color = '';

            // Save to database (null tag_config)
            saveCellTag(activeCell, null);
        }
        hideTagMenu();
    }

    // Check if color is dark
    function isDarkColor(color) {
        const hex = color.replace('#', '');
        const r = parseInt(hex.substr(0, 2), 16);
        const g = parseInt(hex.substr(2, 2), 16);
        const b = parseInt(hex.substr(4, 2), 16);
        const brightness = ((r * 299) + (g * 587) + (b * 114)) / 1000;
        return brightness < 128;
    }

    // Hide context menus when clicking elsewhere
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#contextMenu')) {
            hideTagMenu();
        }
        if (!event.target.closest('#columnContextMenu')) {
            hideColumnMenu();
        }
    });

    // Generate column label (A, B, C, ..., Z, AA, AB, etc.)
    function getColumnLabel(index) {
        let result = '';
        while (index >= 0) {
            result = String.fromCharCode(65 + (index % 26)) + result;
            index = Math.floor(index / 26) - 1;
        }
        return result;
    }

    // Save cell data
    async function saveCell(input) {
        const row = input.getAttribute('data-row');
        const col = input.getAttribute('data-col');
        const key = `${row}-${col}`;

        // Store the current value locally immediately
        tableData[key] = input.value;

        // Clear existing timeout for this cell
        if (saveTimeouts[key]) {
            clearTimeout(saveTimeouts[key]);
        }

        // Set a new timeout to save after 500ms of no changes
        saveTimeouts[key] = setTimeout(async () => {
            const rowId = input.getAttribute('data-row-id');
            const columnId = input.getAttribute('data-column-id');
            const value = input.value;

            try {
                const response = await fetch('/board-cells', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            ?.getAttribute('content')
                    },
                    body: JSON.stringify({
                        board_id: boardId,
                        board_row_id: parseInt(rowId),
                        board_column_id: parseInt(columnId),
                        value: value
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    input.setAttribute('data-cell-id', data.cell.id);
                }
            } catch (error) {
                console.error('Error saving cell:', error);
            }

            // Clean up
            delete saveTimeouts[key];
        }, 500); // Wait 500ms after last change
    }

    // Add new function for saving cell tags
    async function saveCellTag(input, tagConfig) {
        const cellId = input.getAttribute('data-cell-id');
        const rowId = input.getAttribute('data-row-id');
        const columnId = input.getAttribute('data-column-id');

        try {
            let response;

            if (cellId) {
                // Update existing cell
                response = await fetch(`/board-cells/${cellId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        tag_config: tagConfig
                    })
                });
            } else {
                // Create new cell with tag
                response = await fetch('/board-cells', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        board_id: boardId,
                        board_row_id: parseInt(rowId),
                        board_column_id: parseInt(columnId),
                        value: input.value || '',
                        tag_config: tagConfig
                    })
                });
            }

            if (response.ok) {
                const data = await response.json();
                input.setAttribute('data-cell-id', data.cell.id);
            }
        } catch (error) {
            console.error('Error saving cell tag:', error);
        }
    }
    async function saveAllPendingCells() {
        const cellsToSave = [];

        document.querySelectorAll('.cell-input').forEach(input => {
            if (input.value.trim() !== '' && !input.getAttribute('data-cell-id')) {
                const rowId = input.getAttribute('data-row-id');
                const columnId = input.getAttribute('data-column-id');

                if (rowId && columnId) {
                    cellsToSave.push({
                        board_row_id: parseInt(rowId),
                        board_column_id: parseInt(columnId),
                        value: input.value
                    });
                }
            }
        });

        if (cellsToSave.length > 0) {
            try {
                const response = await fetch('/board-cells/bulk', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        board_id: boardId,
                        cells: cellsToSave
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    // Update cell IDs
                    data.cells.forEach((cell, index) => {
                        if (cellsToSave[index]) {
                            const input = document.querySelector(
                                `input[data-row-id="${cell.board_row_id}"][data-column-id="${cell.board_column_id}"]`
                            );
                            if (input) {
                                input.setAttribute('data-cell-id', cell.id);
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Error auto-saving cells:', error);
            }
        }
    }

    // Start auto-save when page loads
    document.addEventListener('DOMContentLoaded', function() {
        startAutoSave();
    });

    // Mark changes as unsaved
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('cell-input')) {
            hasUnsavedChanges = true;
        }
    });
    // Add new row
    async function addRow() {
        try {
            const response = await fetch('/board-rows', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content')
                },
                body: JSON.stringify({
                    board_id: boardId
                })
            });

            if (response.ok) {
                const data = await response.json();

                // Dynamically add the new row instead of reloading
                addRowToDom(data.row);
                currentRows++;
            }
        } catch (error) {
            console.error('Error adding row:', error);
        }
    }

    function addRowToDom(rowData) {
        const tableBody = document.getElementById('tableBody');
        const addRowTr = document.querySelector('.add-row-tr');

        // Create new row
        const newRow = document.createElement('tr');
        newRow.className = '*:w-auto [&_td]:h-6 [&_td]:relative [&_td]:border [&_td]:border-white/20 [&_td]:p-0';

        // Row header
        const rowHeader = document.createElement('td');
        rowHeader.className = 'w-10 min-w-10 border-r-3 text-center text-xs font-bold';
        rowHeader.setAttribute('data-row-id', rowData.id);
        rowHeader.textContent = rowData.label;
        newRow.appendChild(rowHeader);

        // Data cells for each column
        boardColumns.forEach(colIndex => {
            const cell = document.createElement('td');
            cell.className = 'min-w-30';
            cell.setAttribute('data-cell-id', `${rowData.row_index}-${colIndex}`);

            const input = document.createElement('input');
            input.type = 'text';
            input.className =
                'cell-input size-full resize-none border-none bg-transparent px-1.5 py-1 font-sans text-xs outline-none';
            input.setAttribute('data-row', rowData.row_index);
            input.setAttribute('data-col', colIndex);
            input.setAttribute('data-row-id', rowData.id);
            input.onchange = function() {
                saveCell(this);
            };
            input.oncontextmenu = function(e) {
                showTagMenu(e, this);
            };
            input.ontouchstart = function(e) {
                handleTouchStart(e, this);
            };
            input.ontouchend = function(e) {
                handleTouchEnd(e, this);
            };

            cell.appendChild(input);
            newRow.appendChild(cell);
        });

        // Add column button
        const addColCell = document.createElement('td');
        addColCell.className =
            'min-w-30 cursor-pointer text-center align-middle text-base transition-colors select-none';
        addColCell.textContent = '+';
        addColCell.onclick = addColumn;
        newRow.appendChild(addColCell);

        // Insert before the add row
        tableBody.insertBefore(newRow, addRowTr);

        // Update boardRows array
        boardRows.push(rowData.row_index);
    }

    // Replace the existing addColumn function
    async function addColumn() {
        try {
            const response = await fetch('/board-columns', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content')
                },
                body: JSON.stringify({
                    board_id: boardId
                })
            });

            if (response.ok) {
                const data = await response.json();

                // Dynamically add the new column
                addColumnToDom(data.column);
                currentCols++;
            }
        } catch (error) {
            console.error('Error adding column:', error);
        }
    }

    // Function to dynamically add column to DOM
    function addColumnToDom(columnData) {
        const table = document.getElementById('excelTable');

        // Add header
        const headerRow = document.getElementById('headerRow');
        const newHeader = document.createElement('th');
        newHeader.className = 'min-w-20 cursor-pointer border-s-1 border-b-3 text-xs font-bold hover:bg-white/10';
        newHeader.innerHTML =
            `${columnData.label}<span class="sort-indicator ml-1 text-xs" id="sort-${columnData.column_index}"></span>`;
        newHeader.setAttribute('data-col', columnData.column_index);
        newHeader.setAttribute('data-column-id', columnData.id);
        newHeader.oncontextmenu = function(e) {
            showColumnMenu(e, columnData.column_index);
        };
        headerRow.insertBefore(newHeader, headerRow.lastElementChild);

        // Add cells to existing data rows (not add-row)
        const rows = table.querySelectorAll('tbody tr:not(.add-row-tr)');
        rows.forEach((row) => {
            const rowHeader = row.querySelector('td[data-row-id]');
            const rowId = rowHeader?.getAttribute('data-row-id');
            const rowIndex = parseInt(row.querySelector('input')?.getAttribute('data-row') || '1');

            const newCell = document.createElement('td');
            newCell.className = 'min-w-30';
            newCell.setAttribute('data-cell-id', `${rowIndex}-${columnData.column_index}`);

            const input = document.createElement('input');
            input.type = 'text';
            input.className =
                'cell-input size-full resize-none border-none bg-transparent px-1.5 py-1 font-sans text-xs outline-none';
            input.setAttribute('data-row', rowIndex);
            input.setAttribute('data-col', columnData.column_index);
            input.setAttribute('data-column-id', columnData.id);
            if (rowId) input.setAttribute('data-row-id', rowId);
            input.onchange = function() {
                saveCell(this);
            };
            input.oncontextmenu = function(e) {
                showTagMenu(e, this);
            };
            input.ontouchstart = function(e) {
                handleTouchStart(e, this);
            };
            input.ontouchend = function(e) {
                handleTouchEnd(e, this);
            };

            newCell.appendChild(input);
            row.insertBefore(newCell, row.lastElementChild);
        });

        // Add cell to add-row
        const addRowTr = document.querySelector('.add-row-tr');
        const addRowCell = document.createElement('td');
        addRowCell.className =
            'cursor-pointer transition-colors text-center align-middle text-base select-none border border-white/20 p-0 relative min-w-30 h-6';
        addRowCell.textContent = '+';
        addRowCell.onclick = addRow;
        addRowTr.insertBefore(addRowCell, addRowTr.lastElementChild);

        // Update boardColumns array
        boardColumns.push(columnData.column_index);
    }


    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        const activeElement = document.activeElement;
        if (activeElement && activeElement.classList.contains('cell-input')) {
            const row = parseInt(activeElement.getAttribute('data-row'));
            const col = parseInt(activeElement.getAttribute('data-col'));

            let newRow = row;
            let newCol = col;

            switch (e.key) {
                case 'ArrowUp':
                    const prevRowIndex = boardRows[boardRows.indexOf(row) - 1];
                    newRow = prevRowIndex !== undefined ? prevRowIndex : row;
                    e.preventDefault();
                    break;
                case 'ArrowDown':
                    const nextRowIndex = boardRows[boardRows.indexOf(row) + 1];
                    newRow = nextRowIndex !== undefined ? nextRowIndex : row;
                    e.preventDefault();
                    break;
                case 'ArrowLeft':
                    const prevColIndex = boardColumns[boardColumns.indexOf(col) - 1];
                    newCol = prevColIndex !== undefined ? prevColIndex : col;
                    e.preventDefault();
                    break;
                case 'ArrowRight':
                    const nextColIndex = boardColumns[boardColumns.indexOf(col) + 1];
                    newCol = nextColIndex !== undefined ? nextColIndex : col;
                    e.preventDefault();
                    break;
                case 'Enter':
                    const enterNextRowIndex = boardRows[boardRows.indexOf(row) + 1];
                    newRow = enterNextRowIndex !== undefined ? enterNextRowIndex : row;
                    e.preventDefault();
                    break;
                case 'Tab':
                    if (e.shiftKey) {
                        const tabPrevColIndex = boardColumns[boardColumns.indexOf(col) - 1];
                        if (tabPrevColIndex !== undefined) {
                            newCol = tabPrevColIndex;
                        } else {
                            newCol = boardColumns[boardColumns.length - 1];
                            const tabPrevRowIndex = boardRows[boardRows.indexOf(row) - 1];
                            newRow = tabPrevRowIndex !== undefined ? tabPrevRowIndex : row;
                        }
                    } else {
                        const tabNextColIndex = boardColumns[boardColumns.indexOf(col) + 1];
                        if (tabNextColIndex !== undefined) {
                            newCol = tabNextColIndex;
                        } else {
                            newCol = boardColumns[0];
                            const tabNextRowIndex = boardRows[boardRows.indexOf(row) + 1];
                            newRow = tabNextRowIndex !== undefined ? tabNextRowIndex : row;
                        }
                    }
                    e.preventDefault();
                    break;
            }

            if (newRow !== row || newCol !== col) {
                const nextInput = document.querySelector(
                    `input[data-row="${newRow}"][data-col="${newCol}"]`
                );
                if (nextInput) {
                    nextInput.focus();
                    nextInput.select();
                }
            }
        }
    });
</script>
