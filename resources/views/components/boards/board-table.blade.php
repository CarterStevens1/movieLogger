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
{{-- <table id="table-container"
    class="w-full table-auto [&_th]:min-w-[150px] overflow-auto [&_th]:p-2 [&_td]:p-2 [&_th]:border [&_td]:border [&_td]:h-0 ">


</table>
<script type="module">
    // Create an editable table dynamically with 21 columns and 50 rows
    const createEditableTable = (rows, cols) => {
        const table = document.getElementById('table-container');
        const thead = document.createElement('thead');
        const tbody = document.createElement('tbody');
        // Create table header
        const headerRow = document.createElement('tr');
        for (let col = 0; col < cols; col++) {
            const th = document.createElement('th');
            th.contentEditable = "true";
            headerRow.appendChild(th);
        }
        thead.appendChild(headerRow);

        // Create table body
        for (let row = 0; row < rows; row++) {
            const tr = document.createElement('tr');
            for (let col = 0; col < cols; col++) {
                const td = document.createElement('td');
                td.contentEditable = "true";
                tr.appendChild(td);
            }
            tbody.appendChild(tr);
        }

        table.appendChild(thead);
        table.appendChild(tbody);
        return table;
    };
    // Append the table to the container
    const container = document.getElementById('table-container');
    if (container) {
        const table = createEditableTable(50, 21);
        enableColumnResizing(table);
    }
</script> --}}


<!-- Context Tag Menu -->
<!-- Context Menu -->
<div id="contextMenu" class="hidden fixed bg-white border border-gray-300 rounded shadow-lg z-50 py-2 min-w-40">
    <div class="px-3 py-1 text-xs font-semibold text-gray-500 border-b border-gray-200 mb-1 pb-3">Apply Tag</div>
    <div id="contextMenuTags"></div>
    <div class="border-t border-gray-200 mt-1 pt-1">
        <div class="px-3 py-1 hover:bg-gray-100 cursor-pointer text-sm text-red-600" onclick="removeTag()">Remove Tag
        </div>
    </div>
</div>

<div class="rounded overflow-auto max-h-screen shadow-md">
    <table class="border-collapse w-full min-w-max" id="excelTable">
        <thead>
            <tr id="headerRow">
                <th
                    class="font-bold text-center text-xs text-white border-b-2 border-white/20 p-0 relative min-w-10 w-10 h-6">
                </th>
                @for ($col = 0; $col < 20; $col++)
                    <th
                        class="font-bold text-center text-xs text-white border-b-3 border-s-1 border-white/20 p-0 relative min-w-20 h-6">
                        {{ chr(65 + ($col % 26)) }}{{ $col >= 26 ? intval($col / 26) : '' }}</th>
                @endfor
                <th class="bg-blue-50 hover:bg-blue-100 cursor-pointer transition-colors text-center align-middle text-base text-gray-600 select-none border border-gray-200 p-0 relative min-w-20 h-6"
                    onclick="addColumn()">+</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            @for ($row = 1; $row <= 50; $row++)
                <tr>
                    <td
                        class=" font-bold text-center text-xs text-white border border-r-3 border-white/20 p-0 relative min-w-10 w-10 h-6">
                        {{ $row }}</td>
                    @for ($col = 0; $col < 20; $col++)
                        <td class="border border-white/20 p-0 relative min-w-20 h-6"
                            data-cell-id="{{ $row }}-{{ $col }}">
                            <input type="text"
                                class="cell-input border-none px-1.5 py-1 w-full h-full bg-transparent text-xs font-sans outline-none resize-none"
                                data-row="{{ $row }}" data-col="{{ $col }}" onchange="saveCell(this)"
                                oncontextmenu="showContextMenu(event, this)"
                                ontouchstart="handleTouchStart(event, this)" ontouchend="handleTouchEnd(event, this)">
                        </td>
                    @endfor
                    <td class="bg-blue-50 hover:bg-blue-100 cursor-pointer transition-colors text-center align-middle text-base text-gray-600 select-none border border-gray-200 p-0 relative min-w-20 h-6"
                        onclick="addColumn()">+</td>
                </tr>
            @endfor
            <tr class="add-row-tr">
                <td class="bg-blue-50 hover:bg-blue-100 cursor-pointer transition-colors text-center align-middle text-base text-gray-600 select-none border border-gray-200 p-0 relative min-w-10 w-10 h-6"
                    onclick="addRow()">+</td>
                @for ($col = 0; $col < 20; $col++)
                    <td class="bg-blue-50 hover:bg-blue-100 cursor-pointer transition-colors text-center align-middle text-base text-gray-600 select-none border border-gray-200 p-0 relative min-w-20 h-6"
                        onclick="addRow()">+</td>
                @endfor
                <td class="bg-gray-50 hover:bg-blue-100 cursor-pointer transition-colors text-center align-middle text-base text-gray-600 select-none border border-gray-200 p-0 relative min-w-20 h-6"
                    onclick="addRow()">+</td>
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
                showContextMenu(syntheticEvent, input);

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
                hideContextMenu();
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
    function showContextMenu(event, input) {
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
    // Hide context menu
    function hideContextMenu() {
        document.getElementById('contextMenu').classList.add('hidden');
        activeCell = null;
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
        hideContextMenu();
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
        hideContextMenu();
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

    // Hide context menu when clicking elsewhere
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#contextMenu')) {
            hideContextMenu();
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

        // Row header
        const rowHeader = document.createElement('td');
        rowHeader.className =
            'bg-gray-50 font-bold text-center text-xs text-gray-600 border border-gray-200 p-0 relative min-w-10 w-10 h-6';
        rowHeader.textContent = currentRows;
        newRow.appendChild(rowHeader);

        // Data cells
        for (let col = 0; col < currentCols; col++) {
            const cell = document.createElement('td');
            cell.className = 'border border-gray-200 p-0 relative min-w-20 h-6';
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
                showContextMenu(e, this);
            };
            input.ontouchstart = function(e) {
                handleTouchStart(e, this);
            };
            input.ontouchend = function(e) {
                handleTouchEnd(e, this);
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
            'bg-blue-50 hover:bg-blue-100 cursor-pointer transition-colors text-center align-middle text-base text-gray-600 select-none border border-gray-200 p-0 relative min-w-20 h-6';
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
            'bg-gray-50 font-bold text-center text-xs text-gray-600 border border-gray-200 p-0 relative min-w-20 h-6';
        newHeader.textContent = getColumnLabel(currentCols - 1);
        headerRow.insertBefore(newHeader, headerRow.lastElementChild);

        // Add cells to existing rows
        const rows = table.querySelectorAll('tbody tr:not(.add-row-tr)');
        rows.forEach((row, index) => {
            const newCell = document.createElement('td');
            newCell.className = 'border border-gray-200 p-0 relative min-w-20 h-6';
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
                showContextMenu(e, this);
            };
            newCell.appendChild(input);
            row.insertBefore(newCell, row.lastElementChild);
        });

        // Add cell to add-row
        const addRowTr = document.querySelector('.add-row-tr');
        const addRowCell = document.createElement('td');
        addRowCell.className =
            'bg-blue-50 hover:bg-blue-100 cursor-pointer transition-colors text-center align-middle text-base text-gray-600 select-none border border-gray-200 p-0 relative min-w-20 h-6';
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
