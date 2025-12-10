<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DataTable with Multi-Select Footer Filters</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- Select2 CSS (for multi-select dropdowns) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2>Users Table with Footer Filters</h2>
    <table id="multi-filter-select" class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
        </tr>
        </tfoot>
        <tbody>
        <tr><td>John Doe</td><td>john@example.com</td><td>Active</td></tr>
        <tr><td>Mary Jane</td><td>mary@example.com</td><td>Pending</td></tr>
        <tr><td>Mike Smith</td><td>mike@example.com</td><td>Inactive</td></tr>
        <tr><td>Alice Brown</td><td>alice@example.com</td><td>Active</td></tr>
        <tr><td>Bob White</td><td>bob@example.com</td><td>Pending</td></tr>
        </tbody>
    </table>
</div>

<!-- Scripts -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {

    // Initialize DataTable
    var table = $('#multi-filter-select').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        lengthChange: true
    });

    // Add a select filter for each column in footer
    $('#multi-filter-select tfoot th').each(function() {
        var title = $(this).text();
        $(this).html('<select class="form-select form-select-sm" multiple="multiple" style="width:100%"><option value="">All '+title+'</option></select>');
    });

    // Populate select options for each column
    table.columns().every(function() {
        var column = this;
        var select = $(column.footer()).find('select');
        column.data().unique().sort().each(function(d, j) {
            if(d) select.append('<option value="'+d+'">'+d+'</option>');
        });
        // Initialize Select2
        select.select2({
            placeholder: "Filter "+column.header().innerText,
            allowClear: true
        });

        // Filter on change
        select.on('change', function() {
            var vals = $(this).val();
            if(vals && vals.length > 0){
                column.search(vals.join('|'), true, false).draw();
            } else {
                column.search('', true, false).draw();
            }
        });
    });

});
</script>
</body>
</html>
