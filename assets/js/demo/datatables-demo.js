// Call the dataTables jQuery plugin
$(document).ready(function() {
    // Initialize all tables with the 'dataTable' class
    $('.dataTable').DataTable({
        "language": {
            "lengthMenu": "Show _MENU_ entries",
            "zeroRecords": "No matching records found",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "infoEmpty": "Showing 0 to 0 of 0 entries",
            "infoFiltered": "(filtered from _MAX_ total entries)",
            "search": "Search:",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        },
        "pageLength": 10,
        "responsive": true
    });
}); 