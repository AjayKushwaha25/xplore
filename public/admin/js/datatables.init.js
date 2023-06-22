$(document).ready(function () {
    $("#datatable").DataTable({
        "order": [[ 0, "desc" ]],
        fixedHeader:{
            headerOffset: $('#page-topbar').outerHeight(),
            header: true
        }
    }),
    $(".dataTables_length select").addClass("form-select form-select-sm");
});
