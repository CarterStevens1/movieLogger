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
                @for ($col = 0; $col < 20; $col++)
                    <th class="min-w-20 cursor-pointer border-s-1 border-b-3 text-xs font-bold hover:bg-white/10"
                        oncontextmenu="showColumnMenu(event, {{ $col }})" data-col="{{ $col }}">
                        {{ chr(65 + ($col % 26)) }}{{ $col >= 26 ? intval($col / 26) : '' }}
                        <span class="sort-indicator ml-1 text-xs" id="sort-{{ $col }}"></span>
                    </th>
                @endfor
                <th class="min-w-20 cursor-pointer border align-middle text-base transition-colors select-none"
                    onclick="addColumn()">+</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            @for ($row = 1; $row <= 50; $row++)
                <tr class="[&_td]:h-6 [&_td]:relative [&_td]:border [&_td]:border-white/20 [&_td]:p-0">
                    <td class="w-10 min-w-10 border-r-3 text-center text-xs font-bold">
                        {{ $row }}</td>
                    @for ($col = 0; $col < 20; $col++)
                        <td class="min-w-20" data-cell-id="{{ $row }}-{{ $col }}">
                            <input type="text"
                                class="cell-input size-full resize-none border-none bg-transparent px-1.5 py-1 font-sans text-xs outline-none"
                                data-row="{{ $row }}" data-col="{{ $col }}" onchange="saveCell(this)"
                                oncontextmenu="showTagMenu(event, this)" ontouchstart="handleTouchStart(event, this)"
                                ontouchend="handleTouchEnd(event, this)">
                        </td>
                    @endfor
                    <td class="min-w-20 cursor-pointer text-center align-middle text-base transition-colors select-none"
                        onclick="addColumn()">+</td>
                </tr>
            @endfor
            <tr
                class="add-row-tr [&_td]:cursor-pointer [&_td]:transition-colors [&_td]:text-center [&_td]:align-middle [&_td]:text-base [&_td]:text-gray-600 [&_td]:select-none [&_td]:border [&_td]:border-white/20 [&_td]:p-0 [&_td]:relative [&_td]:min-w-10 [&_td]:w-10 [&_td]:h-6">
                <td onclick="addRow()">+</td>
                @for ($col = 0; $col < 20; $col++)
                    <td onclick="addRow()">+</td>
                @endfor
                <td onclick="addRow()">+</td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    let currentRows = 50;
    let currentCols = 20;
    let tableData = {};
    let cellTags = {};
    let tags = @json($tagsArray ?? []);

    let activeCell = null;
    let activeColumn = null;
    let touchStartTime = 0;
    let touchTimer = null;
    let touchMoved = false;

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

    // Sort column functionality
    function sortColumn(direction) {
        if (activeColumn === null) return;

        // Get all data from the column
        const columnData = [];
        for (let row = 1; row <= currentRows; row++) {
            const input = document.querySelector(`input[data-row="${row}"][data-col="${activeColumn}"]`);
            if (input) {
                const cellId = `${row}-${activeColumn}`;
                columnData.push({
                    row: row,
                    value: input.value || '',
                    tags: cellTags[cellId] || null,
                    originalIndex: row
                });
            }
        }

        // Separate non-empty and empty cells
        const nonEmptyCells = columnData.filter(item => item.value.trim() !== '');
        const emptyCells = columnData.filter(item => item.value.trim() === '');

        // Sort only the non-empty cells
        nonEmptyCells.sort((a, b) => {
            const aVal = a.value.toLowerCase();
            const bVal = b.value.toLowerCase();

            if (direction === 'asc') {
                return aVal.localeCompare(bVal);
            } else {
                return bVal.localeCompare(aVal);
            }
        });

        // Combine sorted non-empty cells with empty cells at the end
        const sortedData = [...nonEmptyCells, ...emptyCells];

        // Apply sorted data back to the column
        sortedData.forEach((item, index) => {
            const targetRow = index + 1;
            const input = document.querySelector(`input[data-row="${targetRow}"][data-col="${activeColumn}"]`);
            if (input) {
                input.value = item.value;

                // Update tableData
                const cellId = `${targetRow}-${activeColumn}`;
                tableData[cellId] = item.value;

                // Apply tags if they exist
                const cell = input.parentElement;
                if (item.tags) {
                    cellTags[cellId] = item.tags;
                    cell.style.backgroundColor = item.tags.color;
                    if (isDarkColor(item.tags.color)) {
                        input.style.color = 'white';
                    } else {
                        input.style.color = 'black';
                    }
                } else {
                    delete cellTags[cellId];
                    cell.style.backgroundColor = '';
                    input.style.color = '';
                }
            }
        });

        // Update sort indicator
        clearSortIndicators();
        const sortIndicator = document.getElementById(`sort-${activeColumn}`);
        if (sortIndicator) {
            sortIndicator.textContent = direction === 'asc' ? '↑' : '↓';
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

        // Ensure we have enough columns
        const maxCols = Math.max(...data.map(row => row.length));
        while (currentCols < maxCols) {
            addColumn();
        }

        // Ensure we have enough rows
        while (currentRows < data.length) {
            addRow();
        }

        // Populate data
        data.forEach((row, rowIndex) => {
            row.forEach((cellValue, colIndex) => {
                if (rowIndex < currentRows && colIndex < currentCols) {
                    const input = document.querySelector(
                        `input[data-row="${rowIndex + 1}"][data-col="${colIndex}"]`);
                    if (input) {
                        input.value = cellValue;
                        saveCell(input);
                    }
                }
            });
        });

        // Reset file input
        event.target.value = '';
    }

    // CSV Export functionality
    function exportCSV() {
        const csvData = [];

        // Determine actual data bounds
        let maxRow = 0;
        let maxCol = 0;

        for (let row = 1; row <= currentRows; row++) {
            for (let col = 0; col < currentCols; col++) {
                const input = document.querySelector(`input[data-row="${row}"][data-col="${col}"]`);
                if (input && input.value.trim() !== '') {
                    maxRow = Math.max(maxRow, row);
                    maxCol = Math.max(maxCol, col);
                }
            }
        }

        // Build CSV data
        for (let row = 1; row <= maxRow; row++) {
            const rowData = [];
            for (let col = 0; col <= maxCol; col++) {
                const input = document.querySelector(`input[data-row="${row}"][data-col="${col}"]`);
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
    function saveCell(input) {
        const row = input.getAttribute('data-row');
        const col = input.getAttribute('data-col');
        const key = `${row}-${col}`;
        tableData[key] = input.value;
    }

    // Add new row
    function addRow() {
        currentRows++;
        const tableBody = document.getElementById('tableBody');
        const addRowTr = document.querySelector('.add-row-tr');

        // Create new row
        const newRow = document.createElement('tr');
        newRow.className = '[&_td]:h-6 [&_td]:relative [&_td]:border [&_td]:border-white/20 [&_td]:p-0';
        // Row header
        const rowHeader = document.createElement('td');
        rowHeader.className =
            'font-bold text-center text-xs min-w-10 w-10';
        rowHeader.textContent = currentRows;
        newRow.appendChild(rowHeader);

        // Data cells
        for (let col = 0; col < currentCols; col++) {
            const cell = document.createElement('td');
            cell.className = 'min-w-20';
            cell.setAttribute('data-cell-id', `${currentRows}-${col}`);
            const input = document.createElement('input');
            input.type = 'text';
            input.className =
                'cell-input border-none px-1.5 py-1 w-full h-full bg-transparent text-xs font-sans outline-none resize-none';
            input.setAttribute('data-row', currentRows);
            input.setAttribute('data-col', col);
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
        }

        // Add column button
        const addColCell = document.createElement('td');
        addColCell.className =
            'cursor-pointer transition-colors text-center align-middle text-base select-none min-w-20';
        addColCell.textContent = '+';
        addColCell.onclick = addColumn;
        newRow.appendChild(addColCell);

        // Insert before the add row
        tableBody.insertBefore(newRow, addRowTr);
    }

    // Add new column
    function addColumn() {
        currentCols++;
        const table = document.getElementById('excelTable');

        // Add header
        const headerRow = document.getElementById('headerRow');
        const newHeader = document.createElement('th');
        newHeader.className =
            'font-bold text-xs border min-w-20 cursor-pointer hover:bg-white/10 border-s-1 border-b-3';
        newHeader.innerHTML =
            `${getColumnLabel(currentCols - 1)}<span class="sort-indicator text-xs ml-1" id="sort-${currentCols - 1}"></span>`;
        newHeader.setAttribute('data-col', currentCols - 1);
        newHeader.oncontextmenu = function(e) {
            showColumnMenu(e, currentCols - 1);
        };
        headerRow.insertBefore(newHeader, headerRow.lastElementChild);

        // Add cells to existing rows
        const rows = table.querySelectorAll('tbody tr:not(.add-row-tr)');
        rows.forEach((row, index) => {
            const newCell = document.createElement('td');
            newCell.className = 'min-w-20';
            newCell.setAttribute('data-cell-id', `${index + 1}-${currentCols - 1}`);
            const input = document.createElement('input');
            input.type = 'text';
            input.className =
                'cell-input border-none px-1.5 py-1 w-full h-full bg-transparent text-xs font-sans outline-none resize-none';
            input.setAttribute('data-row', index + 1);
            input.setAttribute('data-col', currentCols - 1);
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
            'cursor-pointer transition-colors text-center align-middle text-base select-none border border-white/20 p-0 relative min-w-20 h-6';
        addRowCell.textContent = '+';
        addRowCell.onclick = addRow;
        addRowTr.insertBefore(addRowCell, addRowTr.lastElementChild);
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
                    newRow = Math.max(1, row - 1);
                    e.preventDefault();
                    break;
                case 'ArrowDown':
                    newRow = Math.min(currentRows, row + 1);
                    e.preventDefault();
                    break;
                case 'ArrowLeft':
                    newCol = Math.max(0, col - 1);
                    e.preventDefault();
                    break;
                case 'ArrowRight':
                    newCol = Math.min(currentCols - 1, col + 1);
                    e.preventDefault();
                    break;
                case 'Enter':
                    newRow = Math.min(currentRows, row + 1);
                    e.preventDefault();
                    break;
                case 'Tab':
                    if (e.shiftKey) {
                        newCol = col > 0 ? col - 1 : currentCols - 1;
                        newRow = col > 0 ? row : Math.max(1, row - 1);
                    } else {
                        newCol = col < currentCols - 1 ? col + 1 : 0;
                        newRow = col < currentCols - 1 ? row : Math.min(currentRows, row + 1);
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
