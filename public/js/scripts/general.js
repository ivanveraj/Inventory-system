
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
    navActive = $('body').data('active');
    $('.nav-link').removeClass('active');
    $('.' + navActive).addClass('active');
});


function addToastr(type, title, message) {
    toastr['' + type](message, title);
}

function blockPage() {
    $(".loader_wrapper").show()
}

function unblockPage() {
    $(".loader_wrapper").fadeOut("slow");
}


function deleteErrorInputs(element) {
    $(element + ' [data-name]').removeClass('parsley-error');
    $(element + ' [data-error]').html('');
}

function addErrorInputs(element, response) {
    deleteErrorInputs(element)
    if (response.responseJSON.hasOwnProperty('errors')) {
        $.each(response.responseJSON.errors, function (key, value) {
            $(element + ' [data-name="' + key + '"]').addClass('parsley-error');
            $(element + ' [data-error="' + key + '"]').html(`<li class="parsley-required">${value[0]}</li>`);
        });
    }
}


function SweetConfirmation(msj, confirmation, cancelation) {
    return Swal.fire({
        title: msj,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmation,
        cancelButtonText: cancelation
    }).then((result) => {
        if (result.isConfirmed) {
            return true;
        }
        return false;
    })
}


function SweetDetail(btnOk) {
    return Swal.fire({
        title: '<i class="mdi mdi-information-variant bg-sky-500 rounded-full"></i>' + `${ActionTrue}`,
        html: Detail,
        confirmButtonColor: '#1b2c3f',
        confirmButtonText: btnOk
    }).then((result) => {
        if (result.isConfirmed) {
            return true;
        }
    })
}

function initSelectProduct(id, route) {
    $(id).select2({
        placeholder: "--Codigo producto",
        width: '100%',
        ajax: {
            url: route,
            type: 'GET',
            dataType: 'json',
            delay: 100,
            data: function (params) {
                return {
                    searchTerm: params.term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        }
    });
}