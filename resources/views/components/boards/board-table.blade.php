@props(['board'])
<table id="table-container"
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
</script>
