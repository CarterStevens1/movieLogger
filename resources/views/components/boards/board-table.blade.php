@props(['board'])
@php
    // Conver tags to array of objects with base colors assigned - Ideally colors should be dynamically generated
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


<!-- Tag menu for applying tags to cells-->
<div id="contextMenu" class="hidden fixed bg-white border border-gray-300 rounded shadow-lg z-50 py-2 min-w-40">
    <div class="px-3 py-1 text-xs font-semibold text-gray-500 border-b border-gray-200 mb-1 pb-3">Apply Tag
    </div>
    <div id="contextMenuTags" class="text-gray-500 uppercase font-medium"></div>
    <div class="border-t border-gray-200 mt-1 pt-1">
        <div class="px-3 py-1 hover:bg-gray-100 cursor-pointer text-sm text-red-600" onclick="removeTag()">Remove
            Tag
        </div>
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

<div class="rounded shadow-md w-full overflow-hidden">
    <div class="overflow-x-auto">
        <table class="border-collapse table-auto min-w-full" id="excelTable">
            <thead>
                <tr id="headerRow"
                    class="[&_th]:h-6 [&_th]:relative [&_th]:border-white/20 [&_th]:p-0 [&_th]:text-center">
                    <th class="w-8 sm:w-10 border-b text-xs font-bold">
                    </th>
                    @foreach ($board->columns as $column)
                        <th class="min-w-24 sm:min-w-32 cursor-pointer border-s border-b text-xs font-bold hover:bg-white/10"
                            oncontextmenu="showColumnMenu(event, {{ $column->column_index }})"
                            data-col="{{ $column->column_index }}" data-column-id="{{ $column->id }}">
                            <div class="px-1 sm:px-2 truncate">
                                {{ $column->label }}
                                <span class="sort-indicator ml-1 text-xs" id="sort-{{ $column->column_index }}">
                                    @if ($column->sort_config)
                                        {{ $column->sort_config['direction'] === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </span>
                            </div>
                        </th>
                    @endforeach
                    <th class="w-8 sm:w-10 cursor-pointer border align-middle text-base transition-colors select-none"
                        onclick="addColumn()">+</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @foreach ($board->rows as $row)
                    <tr
                        class="[&_td]:h-6 [&_td]:relative [&_td]:border-r [&_td]:border-y [&_td]:border-white/20 [&_td]:p-0">
                        <td class="w-8 sm:w-10 border-r text-center text-xs font-bold"
                            data-row-id="{{ $row->id }}">
                            <div class="truncate px-4">{{ $row->label }}</div>
                        </td>
                        @foreach ($board->columns as $column)
                            <td class="min-w-24 sm:min-w-32"
                                data-cell-id="{{ $row->row_index }}-{{ $column->column_index }}">
                                <input type="text"
                                    class="cell-input w-full h-full resize-none border-none bg-transparent px-1 sm:px-1.5 py-1 font-sans text-xs outline-none"
                                    data-row="{{ $row->row_index }}" data-col="{{ $column->column_index }}"
                                    data-row-id="{{ $row->id }}" data-column-id="{{ $column->id }}"
                                    onchange="saveCell(this)" oncontextmenu="showTagMenu(event, this)"
                                    ontouchstart="handleTouchStart(event, this)"
                                    ontouchend="handleTouchEnd(event, this)">
                            </td>
                        @endforeach
                        <td class="w-8 sm:w-10 cursor-pointer text-center align-middle text-base transition-colors select-none"
                            onclick="addColumn()">+</td>
                    </tr>
                @endforeach
                <tr
                    class="add-row-tr [&_td]:cursor-pointer [&_td]:transition-colors [&_td]:text-center [&_td]:align-middle [&_td]:text-base [&_td]:select-none [&_td]:border [&_td]:border-white/20 [&_td]:px-4 [&_td]:relative [&_td]:h-6">
                    <td class="w-8 sm:w-10 px-4" onclick="addRow()">+</td>
                    @foreach ($board->columns as $column)
                        <td class="min-w-24 sm:min-w-32 px-4" onclick="addRow()">+</td>
                    @endforeach
                    <td class="w-8 sm:w-10 px-4" onclick="addRow()">+</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Get current rows and columns from database
    let currentRows = {{ $board->rows->count() }};
    let currentCols = {{ $board->columns->count() }};
    let tableData = {}; // Stores the current values of each cell
    let cellTags = {}; // Stores the tags of each cell
    let tags = @json($tagsArray ?? []); //convert tags to json to be used in the script
    let boardId = {{ $board->id }}; // Get Board ID from database
    let boardColumns = @json($board->columns->pluck('column_index')->toArray()); //Get board columns indexs from database
    let boardRows = @json($board->rows->pluck('row_index')->toArray()); //Get board rows indexs from database
    let existingCells = @json(
        $board->cells->keyBy(function ($cell) {
            return $cell->boardRow->row_index . '-' . $cell->boardColumn->column_index;
        })); // Get existing cells from database

    let activeCell = null;
    let activeColumn = null;
    let touchStartTime = 0;
    let touchTimer = null;
    let touchMoved = false;
    let saveTimeouts = {};
    let pendingCellSaves = {};
    let autoSaveInterval; //Auto save interval to reduce overload
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

    // Function to start the auto save interval to reduce server load
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

    // Function to handle touch end events
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
    // Function to handle touch move events
    function handleTouchMove(event) {
        touchMoved = true;
        if (touchTimer) {
            clearTimeout(touchTimer);
            touchTimer = null;
        }
    }

    // Show context menu using the tags
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
    // Hide column context menu
    function hideColumnMenu() {
        document.getElementById('columnContextMenu').classList.add('hidden');
        activeColumn = null;
    }


    // Sort columns by a-z or z-a at the top of the column
    async function sortColumn(direction) {
        if (activeColumn === null) return;

        try {
            // Get all rows except the header and add-row
            const tableBody = document.getElementById('tableBody');
            const rows = Array.from(tableBody.querySelectorAll('tr:not(.add-row-tr)'));

            // Get column index in the boardColumns array
            const colIndex = boardColumns.indexOf(activeColumn);
            if (colIndex === -1) {
                console.error('Column not found in boardColumns array');
                return;
            }

            // Get column element and validate column ID
            const columnElement = document.querySelector(`th[data-col="${activeColumn}"]`);
            const columnId = columnElement?.getAttribute('data-column-id');

            if (!columnId) {
                console.error('Column ID not found! Make sure your column headers have data-column-id attributes');
                alert('Cannot save to database: Column ID not found');
                return;
            }

            // Extract all values from the specific column with their row information
            const columnData = rows.map((row, index) => {
                const cellInput = row.children[colIndex + 1]?.querySelector('input');
                const value = cellInput?.value || '';
                const rowId = row.querySelector('td[data-row-id]')?.getAttribute('data-row-id');
                const cellId = cellInput?.getAttribute('data-cell-id');

                return {
                    value: value,
                    originalIndex: index,
                    rowId: rowId,
                    cellId: cellId,
                    inputElement: cellInput,
                    rowElement: row
                };
            });

            // Sort the data
            const sortedData = [...columnData].sort((a, b) => {
                // Handle empty values - put them at the end for both directions
                if (!a.value && !b.value) return 0;
                if (!a.value) return 1;
                if (!b.value) return -1;

                const valueA = a.value.toString().toLowerCase();
                const valueB = b.value.toString().toLowerCase();

                if (direction === 'asc') {
                    return valueA.localeCompare(valueB);
                } else {
                    return valueB.localeCompare(valueA);
                }
            });

            // Prepare database updates
            const updates = [];

            // Update the DOM and prepare database updates
            rows.forEach((row, index) => {
                const cellInput = row.children[colIndex + 1]?.querySelector('input');
                const newValue = sortedData[index].value;
                const rowId = row.querySelector('td[data-row-id]')?.getAttribute('data-row-id');
                const cellId = cellInput?.getAttribute('data-cell-id');

                if (cellInput) {
                    // Update the input value in the DOM
                    cellInput.value = newValue;

                    // Update local data storage
                    const rowIndex = cellInput.getAttribute('data-row');
                    const colIndex = cellInput.getAttribute('data-col');
                    const key = `${rowIndex}-${colIndex}`;
                    tableData[key] = newValue;
                }

                // Prepare database update if we have the necessary IDs
                if (rowId && columnId) {
                    updates.push({
                        rowId: parseInt(rowId),
                        cellId: cellId, // May be null for new cells
                        columnId: parseInt(columnId),
                        newValue: newValue
                    });
                }
            });

            // Send updates to database
            if (updates.length > 0) {
                const response = await fetch('/update-cell-values', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        updates: updates,
                        columnId: parseInt(columnId),
                        boardId: boardId // Include board ID for additional validation
                    })
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Database update failed:', errorText);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                // Update cell IDs if they were created
                if (result.cellUpdates) {
                    result.cellUpdates.forEach(update => {
                        if (update.cellId && update.rowId) {
                            const input = document.querySelector(
                                `input[data-row-id="${update.rowId}"][data-column-id="${columnId}"]`
                            );
                            if (input && !input.getAttribute('data-cell-id')) {
                                input.setAttribute('data-cell-id', update.cellId);
                            }
                        }
                    });
                }
            }

            // Update sort indicators
            clearSortIndicators();
            const indicator = document.getElementById(`sort-${activeColumn}`);
            if (indicator) {
                indicator.textContent = direction === 'asc' ? '↑' : '↓';
            }

            // Save sort configuration to database
            await updateColumnSortConfig(columnId, direction);

        } catch (error) {
            console.error('Error during sort operation:', error);
            alert('Failed to save sort order to database: ' + error.message);
        } finally {
            hideColumnMenu();
        }
    }

    // Helper function to update column sort configuration
    async function updateColumnSortConfig(columnId, direction) {
        try {
            const response = await fetch(`/board-columns/${columnId}/sort`, {
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

            if (!response.ok) {
                console.error('Failed to save sort config:', await response.text());
            }
        } catch (error) {
            console.error('Error saving sort config:', error);
        }
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
    // CSV Import functionality
    function importCSV(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = async function(e) {
            const csvData = e.target.result;
            await parseCSV(csvData);
        };
        reader.readAsText(file);
    }

    // Parse CSV data with improved synchronization
    async function parseCSV(csvData) {
        const lines = csvData.split('\n');
        const data = lines.map(line => {
            // ... (your existing CSV parsing logic) ...
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

        // Step 1: Create all necessary columns
        const maxCols = Math.max(...data.map(row => row.length));
        const columnsToAdd = maxCols - boardColumns.length;

        // Create an array of promises for adding columns
        const columnPromises = [];
        if (columnsToAdd > 0) {
            for (let i = 0; i < columnsToAdd; i++) {
                columnPromises.push(addColumn());
            }
        }

        // Wait for all column creation requests to complete
        await Promise.all(columnPromises);

        // Step 2: Create all necessary rows
        const rowsToAdd = data.length - boardRows.length;

        // Create an array of promises for adding rows
        const rowPromises = [];
        if (rowsToAdd > 0) {
            for (let i = 0; i < rowsToAdd; i++) {
                rowPromises.push(addRow());
            }
        }

        // Wait for all row creation requests to complete
        await Promise.all(rowPromises);

        // Wait a brief moment for the DOM to settle after all promises resolve
        // This is a safety net, but Promise.all should be enough.
        await new Promise(resolve => setTimeout(resolve, 50));

        // Step 3: Re-read the DOM to get the definitive, correct IDs
        const newBoardRows = Array.from(document.querySelectorAll('td[data-row-id]')).map(td => td.getAttribute(
            'data-row-id'));
        const newBoardColumns = Array.from(document.querySelectorAll('th[data-column-id]')).map(th => th
            .getAttribute('data-column-id'));

        // Now, create a map for easy lookup
        const rowIndexToIdMap = {};
        document.querySelectorAll('td[data-row-id]').forEach(td => {
            const rowId = td.getAttribute('data-row-id');
            const rowIndex = td.previousElementSibling?.getAttribute('data-row') ?? 0;
            rowIndexToIdMap[rowIndex] = rowId;
        });

        const colIndexToIdMap = {};
        document.querySelectorAll('th[data-column-id]').forEach(th => {
            const colId = th.getAttribute('data-column-id');
            const colIndex = th.getAttribute('data-col');
            colIndexToIdMap[colIndex] = colId;
        });

        // Step 4: Populate data and prepare for bulk save
        const cellsToSave = [];
        data.forEach((row, rowIndex) => {
            row.forEach((cellValue, colIndex) => {
                // Get the correct IDs from our new, accurate maps
                const rowId = newBoardRows[rowIndex];
                const columnId = newBoardColumns[colIndex];

                if (rowId && columnId && cellValue.trim() !== '') {
                    cellsToSave.push({
                        board_row_id: parseInt(rowId),
                        board_column_id: parseInt(columnId),
                        value: cellValue
                    });

                    // Also update the input element value on the DOM
                    const input = document.querySelector(
                        `input[data-row-id="${rowId}"][data-column-id="${columnId}"]`);
                    if (input) {
                        input.value = cellValue;
                    }
                }
            });
        });

        // Step 5: Bulk save all cells at once
        if (cellsToSave.length > 0) {
            try {
                const response = await fetch('/board-cells/bulk', {
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

                if (response.ok) {
                    const result = await response.json();
                    // Update cell IDs if they were created
                    if (result.cells) {
                        result.cells.forEach(cell => {
                            const input = document.querySelector(
                                `input[data-row-id="${cell.board_row_id}"][data-column-id="${cell.board_column_id}"]`
                            );
                            if (input) {
                                input.setAttribute('data-cell-id', cell.id);
                            }
                        });
                    }
                }
            } catch (error) {
                console.error('Error bulk saving cells:', error);
            }
        }
    }

    // CSV Export functionality
    function exportCSV() {
        const csvData = [];

        // Get filename from user
        let filename = prompt('Enter filename for CSV export:', 'spreadsheet_data');

        // Handle cancel or empty input
        if (filename === null) {
            return; // User cancelled
        }

        // Clean filename and ensure .csv extension
        filename = filename.trim();
        if (filename === '') {
            filename = 'spreadsheet_data';
        }

        // Remove invalid characters and ensure .csv extension
        filename = filename.replace(/[<>:"/\\|?*]/g, '_');
        if (!filename.toLowerCase().endsWith('.csv')) {
            filename += '.csv';
        }

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
            link.setAttribute('download', filename);
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

    // Check if color is dark and convert text if so
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

    // Function fo saving pending cells in the auto save interval
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

                // Add the new row to the DOM and update state
                addRowToDom(data.row);
            }
        } catch (error) {
            console.error('Error adding row:', error);
        }
    }

    function addRowToDom(rowData) {
        const tableBody = document.getElementById('tableBody');
        const addRowTr = document.querySelector('.add-row-tr');

        const newRow = document.createElement('tr');
        newRow.className =
            '*:w-auto [&_td]:h-6 [&_td]:relative [&_td]:border-r [&_td]:border-y [&_td]:border-white/20 [&_td]:p-0';

        const rowHeader = document.createElement('td');
        rowHeader.className = 'w-10 min-w-10 border-r text-center text-xs font-bold';
        rowHeader.setAttribute('data-row-id', rowData.id); // Use the correct database ID
        rowHeader.textContent = rowData.label;
        newRow.appendChild(rowHeader);

        const boardColumns = Array.from(document.querySelectorAll('#headerRow th[data-column-id]')).map(th => ({
            id: th.getAttribute('data-column-id'),
            index: th.getAttribute('data-col')
        }));

        boardColumns.forEach(col => {
            const cell = document.createElement('td');
            cell.className = 'min-w-30';

            const input = document.createElement('input');
            input.type = 'text';
            input.className =
                'cell-input size-full resize-none border-none bg-transparent px-1.5 py-1 font-sans text-xs outline-none';
            input.setAttribute('data-row', rowData.row_index);
            input.setAttribute('data-col', col.index);
            input.setAttribute('data-row-id', rowData.id);
            input.setAttribute('data-column-id', col.id);
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

        const addColCell = document.createElement('td');
        addColCell.className =
            'min-w-30 cursor-pointer text-center align-middle text-base transition-colors select-none';
        addColCell.textContent = '+';
        addColCell.onclick = addColumn;
        newRow.appendChild(addColCell);

        tableBody.insertBefore(newRow, addRowTr);
    }


    // Add new column
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

                // Add the new column to the DOM and update state
                addColumnToDom(data.column);
            }
        } catch (error) {
            console.error('Error adding column:', error);
        }
    }

    // Function to dynamically add column to DOM
    function addColumnToDom(columnData) {
        const table = document.getElementById('excelTable');
        const headerRow = document.getElementById('headerRow');
        const newHeader = document.createElement('th');
        newHeader.className = 'min-w-20 cursor-pointer border-s border-b text-xs font-bold hover:bg-white/10';
        newHeader.innerHTML =
            `${columnData.label}<span class="sort-indicator ml-1 text-xs" id="sort-${columnData.column_index}"></span>`;
        newHeader.setAttribute('data-col', columnData.column_index);
        newHeader.setAttribute('data-column-id', columnData.id);
        newHeader.oncontextmenu = function(e) {
            showColumnMenu(e, columnData.column_index);
        };
        headerRow.insertBefore(newHeader, headerRow.lastElementChild);

        const rows = table.querySelectorAll('tbody tr:not(.add-row-tr)');
        rows.forEach((row) => {
            const rowHeader = row.querySelector('td[data-row-id]');
            const rowId = rowHeader?.getAttribute('data-row-id');
            const rowIndex = parseInt(row.querySelector('input')?.getAttribute('data-row') || '1');

            const newCell = document.createElement('td');
            newCell.className = 'min-w-30';

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

        const addRowTr = document.querySelector('.add-row-tr');
        const addRowCell = document.createElement('td');
        addRowCell.className =
            'cursor-pointer transition-colors text-center align-middle text-base select-none border border-white/20 p-4 relative min-w-30 h-6';
        addRowCell.textContent = '+';
        addRowCell.onclick = addRow;
        addRowTr.insertBefore(addRowCell, addRowTr.lastElementChild);
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        const activeElement = document.activeElement;
        if (!activeElement?.classList.contains('cell-input')) return;

        const row = parseInt(activeElement.getAttribute('data-row'));
        const col = parseInt(activeElement.getAttribute('data-col'));
        const rowIndex = boardRows.indexOf(row);
        const colIndex = boardColumns.indexOf(col);

        let newRow = row;
        let newCol = col;

        const getArrayValue = (array, index, fallback) =>
            array[index] !== undefined ? array[index] : fallback;

        // Helper function to wrap around array bounds
        const wrapIndex = (array, index) =>
            index < 0 ? array.length - 1 : index >= array.length ? 0 : index;

        const navigationHandlers = {
            'ArrowUp': () => {
                newRow = getArrayValue(boardRows, rowIndex - 1, row);
            },
            'ArrowDown': () => {
                newRow = getArrayValue(boardRows, rowIndex + 1, row);
            },
            'ArrowLeft': () => {
                newCol = getArrayValue(boardColumns, colIndex - 1, col);
            },
            'ArrowRight': () => {
                newCol = getArrayValue(boardColumns, colIndex + 1, col);
            },
            'Enter': () => {
                newRow = getArrayValue(boardRows, rowIndex + 1, row);
            },
            'Tab': () => {
                if (e.shiftKey) {
                    // Shift+Tab: Move backward
                    if (colIndex > 0) {
                        newCol = boardColumns[colIndex - 1];
                    } else {
                        newCol = boardColumns[boardColumns.length - 1];
                        newRow = getArrayValue(boardRows, rowIndex - 1, row);
                    }
                } else {
                    // Tab: Move forward
                    if (colIndex < boardColumns.length - 1) {
                        newCol = boardColumns[colIndex + 1];
                    } else {
                        newCol = boardColumns[0];
                        newRow = getArrayValue(boardRows, rowIndex + 1, row);
                    }
                }
            }
        };

        const handler = navigationHandlers[e.key];
        if (handler) {
            e.preventDefault();
            handler();

            // Focus new cell if position changed
            if (newRow !== row || newCol !== col) {
                const nextInput = document.querySelector(
                    `input[data-row="${newRow}"][data-col="${newCol}"]`
                );
                nextInput?.focus();
                nextInput?.select();
            }
        }
    });
</script>
