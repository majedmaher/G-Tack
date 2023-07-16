let host = document.location;

let TableUrl = new URL('/admin/subCategory', host.origin);

let pathSegments = host.pathname.split('/');
let currentLang = pathSegments[1];
if (currentLang !== 'ar' && currentLang !== 'en') {
    currentLang = 'en';
}

console.log(currentLang);
var table = $('#get_sub_category').DataTable({
    processing: true,
    ajax: TableUrl,
    columns: [
        { data: "DT_RowIndex", name: "DT_RowIndex" },
        { data: "image", name: "image" },
        { data: "category.title_"+currentLang , name: "category.title_"+currentLang },
        { data: "title_"+currentLang , name: "title_"+currentLang },
        { data: "status", name: "status" },
        { data: "action", name: "action" },
    ]
});
//  view modal subCategory
$(document).on('click', '#ShowModalSubCategory', function (e) {
    e.preventDefault();
    $('#modalSubCategoryAdd').modal('show');
});

let AddUrl = new URL('admin/subCategory', host.origin);
// subCategory admin
$(document).on('click', '#addSubCategory', function (e) {
    e.preventDefault();
    let formdata = new FormData($('#formSubCategoryAdd')[0]);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'POST',
        url: AddUrl,
        data: formdata,
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.status == false) {
                // errors
                $('#list_error_message').html("");
                $('#list_error_message').addClass("alert alert-danger");
                $('#list_error_message').text(response.message);
            } else {
                $('#error_message').html("");
                $('#error_message').addClass("alert alert-success");
                $('#error_message').text(response.message);
                $('#modalSubCategoryAdd').modal('hide');
                $('#formSubCategoryAdd')[0].reset();
                table.ajax.reload(null, false);
            }
        }
    });
});

let EditUrl = new URL('admin/subCategory', host.origin);
// view modification data
$(document).on('click', '#showModalEditSubCategory', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    $('#modalSubCategoryUpdate').modal('show');
    $.ajax({
        type: 'GET',
        url: EditUrl + '/' + id + '/edit',
        data: "",
        success: function (response) {
            if (response.status == 404) {
                $('#error_message').html("");
                $('#error_message').addClass("alert alert-danger");
                $('#error_message').text(response.message);
            } else {
                $('#id').val(id);
                $('#title_en').val(response.data.title_en);
                $('#title_ar').val(response.data.title_ar);
                $("#status option[value='" + response.data.status + "']").prop("selected", true);
                $("#parent_id option[value='" + response.data.category.id + "']").prop("selected", true);
            }
        }
    });
});

let UpdateUrl = new URL('admin/subCategory', host.origin);
$(document).on('click', '#updateSubCategory', function (e) {
    e.preventDefault();
    let formdata = new FormData($('#formSubCategoryUpdate')[0]);
    var id = $('#id').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'POST',
        url: UpdateUrl + '/' + id,
        data: formdata,
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.status == false) {
                // errors
                $('#list_error_message2').html("");
                $('#list_error_message2').addClass("alert alert-danger");
                $('#list_error_message2').text(response.message);
            } else {
                $('#error_message').html("");
                $('#error_message').addClass("alert alert-success");
                $('#error_message').text(response.message);
                $('#modalSubCategoryUpdate').modal('hide');
                $('#formSubCategoryUpdate')[0].reset();
                table.ajax.reload(null, false);
            }
        }
    });
});

let DeleteUrl = new URL('admin/subCategory', host.origin);
$(document).on('click', '#showModalDeleteSubCategory', function (e) {
    e.preventDefault();
    $('#nameDetele').val($(this).data('name'));
    var id = $(this).data('id');
    console.log(id);
    $('#modalSubCategoryDelete').modal('show');
    gg(id);
});
function gg(id) {
    $(document).off("click", "#deleteSubCategory").on("click", "#deleteSubCategory", function (e) {
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
                    $('#modalSubCategoryDelete').modal('hide');
                    table.ajax.reload(null, false);
                }
            }
        });
    });
}

let statusUrl = new URL('admin/status/subCategory', host.origin);
$(document).on('click', '#status', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'PUT',
        url: statusUrl + '/' + id,
        data: "",
        success: function (response) {
            if (response.status == 400) {
                // errors
                $('#list_error_message3').html("");
                $('#list_error_message3').addClass("alert alert-danger");
                $('#list_error_message3').text(response.message);
            } else {
                $('#error_message').html("");
                $('#error_message').addClass("alert alert-success");
                $('#error_message').text(response.message);
                table.ajax.reload(null, false);
            }
        }
    });
});
