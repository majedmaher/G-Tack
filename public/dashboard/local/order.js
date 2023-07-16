let host = document.location;

let TableUrl = new URL('/admin/order', host.origin);

let pathSegments = host.pathname.split('/');
let currentLang = pathSegments[1];
if (currentLang !== 'ar' && currentLang !== 'en') {
    currentLang = 'en';
}

var table = $('#get_order').DataTable({
    processing: true,
    ajax: TableUrl,
    columns: [
        { data: "DT_RowIndex", name: "DT_RowIndex" },
        { data: "total", name: "total" },
        { data: "buyer.name", name: "buyer.name" },
        { data: "product.title_"+currentLang , name: "product.title_"+currentLang },
        { data: "seller.name", name: "seller.name" },
        { data: "status", name: "status" },
        { data: "payment_status", name: "payment_status" },
        { data: "action", name: "action" },
    ]
});

let DeleteUrl = new URL('admin/order', host.origin);
$(document).on('click', '#showModalDeleteOrder', function (e) {
    e.preventDefault();
    $('#nameDetele').val($(this).data('name'));
    var id = $(this).data('id');
    console.log(id);
    $('#modalOrderDelete').modal('show');
    gg(id);
});
function gg(id) {
    $(document).off("click", "#deleteOrder").on("click", "#deleteOrder", function (e) {
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
                    $('#modalOrderDelete').modal('hide');
                    table.ajax.reload(null, false);
                }
            }
        });
    });
}

let statusUrl = new URL('admin/status/order', host.origin);
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

let showUrl = new URL('admin/order', host.origin);
$(document).on('click', '#showModalOrder', function (e) {
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
                L.marker([response.data.product.lat, response.data.product.lng]).addTo(map);
                $('#image').html('<img src='+ response.data.product.file +' style="width: 30px; height: 30px;">');
                $('#name').text(response.data.product.title_ar);
                $('#product_owner').text(response.data.seller.name);
                $('#price').text(response.data.product.price);
                $('#discount').text(response.data.product.discount);
                $('#views').text(response.data.product.views);
                $('#status').text(response.data.product.status);
                $('#show').text(response.data.product.show);
                $('#category').text(response.data.product.category.title_ar);
                $('#sub_category').text(response.data.product.sub_category.title_ar);

                $('#name_buyer').text(response.data.buyer.name);
                $('#phone_buyer').text(response.data.buyer.phone);
                $('#Status_buyer').text(response.data.buyer.status);

                $('#name_seller').text(response.data.seller.name);
                $('#phone_seller').text(response.data.seller.phone);
                $('#Status_seller').text(response.data.seller.status);
            }
        }
    });
    $('#showModalOrder1').modal('show');
});
