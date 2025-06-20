@props(['board'])
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



<div class="border border-gray-300 rounded overflow-auto max-h-screen shadow-md">
    <table class="border-collapse w-full min-w-max" id="excelTable">
        <thead class="sticky -top-1 z-1">
            <tr id="headerRow">
                <th
                    class="bg-blue-50 font-bold text-center text-xs text-gray-600 border border-gray-200 p-0 relative min-w-10 w-10 h-6">
                </th>
                @for ($col = 0; $col < 20; $col++)
                    <th
                        class="bg-blue-50 font-bold text-center text-xs text-gray-600 border border-gray-200 p-0 relative min-w-20 h-6">
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
                        class="bg-blue-50 font-bold text-center text-xs text-gray-600 border border-gray-200 p-0 relative min-w-10 w-10 h-6">
                        {{ $row }}</td>
                    @for ($col = 0; $col < 20; $col++)
                        <td class="border border-gray-200 p-0 relative min-w-20 h-6">
                            <input type="text"
                                class="cell-input border-none px-1.5 py-1 w-full h-full bg-transparent text-xs font-sans outline-none resize-none"
                                data-row="{{ $row }}" data-col="{{ $col }}" onchange="saveCell(this)">
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
            const input = document.createElement('input');
            input.type = 'text';
            input.className =
                'cell-input border-none px-1.5 py-1 w-full h-full bg-transparent text-xs font-sans outline-none resize-none';
            input.setAttribute('data-row', currentRows);
            input.setAttribute('data-col', col);
            input.onchange = function() {
                saveCell(this);
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
            const input = document.createElement('input');
            input.type = 'text';
            input.className =
                'cell-input border-none px-1.5 py-1 w-full h-full bg-transparent text-xs font-sans outline-none resize-none';
            input.setAttribute('data-row', index + 1);
            input.setAttribute('data-col', currentCols - 1);
            input.onchange = function() {
                saveCell(this);
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

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Excel-style table initialized with 20 columns and 50 rows');
    });
</script>
