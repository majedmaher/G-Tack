let host = document.location;

let TableUrl = new URL('/admin/denouncement', host.origin);

let pathSegments = host.pathname.split('/');
let currentLang = pathSegments[1];
if(currentLang != 'ar' || currentLang != 'en'){
    currentLang = 'en';
}
console.log(currentLang);

var table = $('#get_denouncement').DataTable({
    processing: true,
    ajax: TableUrl,
    columns: [
        { data: "DT_RowIndex", name: "DT_RowIndex" },
        { data: "reason", name: "reason" },
        { data: "product.title_" + currentLang , name: "product.title_" + currentLang},
        { data: "user.name" , name: "user.name"},
        { data: "action", name: "action" },
    ]
});

let DeleteUrl = new URL('admin/denouncement', host.origin);
$(document).on('click', '#showModalDeleteDenouncement', function (e) {
    e.preventDefault();
    $('#nameDetele').val($(this).data('name'));
    var id = $(this).data('id');
    $('#modalDenouncementDelete').modal('show');
    gg(id);
});
function gg(id) {
    $(document).off("click", "#deleteDenouncement").on("click", "#deleteDenouncement", function (e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'DELETE',
            url: DeleteUrl + '/' + id,
            data: '',
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.status == false) {
                    // errors
                    $('#list_error_message3').html("");
                    $('#list_error_message3').addClass("alert alert-danger");
                    $('#list_error_message3').text(response.message);
                } else {
                    $('#error_message').html("");
                    $('#error_message').addClass("alert alert-success");
                    $('#error_message').text(response.message);
                    $('#modalDenouncementDelete').modal('hide');
                    table.ajax.reload(null, false);
                }
            }
        });
    });
}
