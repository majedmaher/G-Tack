let host = document.location;

let TableUrl = new URL('/admin/product', host.origin);

let pathSegments = host.pathname.split('/');
let currentLang = pathSegments[1];
if (currentLang !== 'ar' && currentLang !== 'en') {
    currentLang = 'en';
}

var table = $('#get_product').DataTable({
    processing: true,
    ajax: TableUrl,
    columns: [
        { data: "DT_RowIndex", name: "DT_RowIndex" },
        { data: "file", name: "file" },
        { data: "title_"+currentLang , name: "title_"+currentLang },
        { data: "category.title_"+currentLang , name: "category.title_"+currentLang },
        { data: "user.name", name: "user.name" },
        { data: "price", name: "price" },
        { data: "views", name: "views" },
        { data: "status", name: "status" },
        { data: "action", name: "action" },
    ]
});


let DeleteUrl = new URL('admin/product', host.origin);
$(document).on('click', '#showModalDeleteProduct', function (e) {
    e.preventDefault();
    $('#nameDetele').val($(this).data('name'));
    var id = $(this).data('id');
    console.log(id);
    $('#modalProductDelete').modal('show');
    gg(id);
});
function gg(id) {
    $(document).off("click", "#deleteProduct").on("click", "#deleteProduct", function (e) {
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
                    $('#modalProductDelete').modal('hide');
                    table.ajax.reload(null, false);
                }
            }
        });
    });
}

let statusUrl = new URL('admin/status/product', host.origin);
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

let showUrl = new URL('admin/product', host.origin);
$(document).on('click', '#showModalProduct', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    $.ajax({
        type: 'GET',
        url: showUrl + '/' + id,
        data: "",
        success: function (response) {
            if (response.status == 404) {
                $('#error_message').html("");
                $('#error_message').addClass("alert alert-danger");
                $('#error_message').text(response.message);
            } else {
                L.marker([response.data.lat, response.data.lng]).addTo(map);
                $('#discount').text(response.data.discount);
                if(currentLang == 'ar'){
                    $('#category').text(response.data.category.title_ar);
                    $('#sub_category').text(response.data.sub_category.title_ar);
                    $('#description').text(response.data.description_ar);
                }else{
                    $('#category').text(response.data.category.title_en);
                    $('#sub_category').text(response.data.sub_category.title_en);
                    $('#description').text(response.data.description_en);
                }
                if(response.data.is_sale == 1){
                $('#is_sale').text("تم البيع");
                }else{
                $('#is_sale').text("لم يتم البيع");
                }
                $('#show').text(response.data.show);
                $('#type').text(response.data.type);

            }
        }
    });
    $('#showModalProduct1').modal('show');
});
