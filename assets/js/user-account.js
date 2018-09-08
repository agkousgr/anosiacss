$(document).ready(function () {

    $('#identity-link').on('click', function (e) {
        e.preventDefault();
        $('.register-form').toggleClass('hidden');
        $('.address-form').addClass('hidden');
        $('.orders-form').addClass('hidden');
    })
    $('#address-link').on('click', function (e) {
        e.preventDefault();
        $('.address-form').toggleClass('hidden');
        $('.register-form').addClass('hidden');
        $('.orders-form').addClass('hidden');
    })
    $('#history-link').on('click', function (e) {
        e.preventDefault();
        $('.orders-form').toggleClass('hidden');
        $('.register-form').addClass('hidden');
        $('.address-form').addClass('hidden');
    })

    $('#edit-main-address').on('click', function (e) {
        e.preventDefault();
        $('form[name="user_address"]').toggleClass('hidden');
        $('#view-main-address').toggleClass('hidden');
        $('.extra-addresses').toggleClass('hidden');
        $('.addresses-footer').toggleClass('hidden');
    })

    if ($('#view-main-address').hasClass('hidden') === false) {
        $('form[name="user_address"]').addClass('hidden');
    }

    $('.delete-address').on('click', function () {
        let data = {
            'id': $(this).data('id'),
        }

        swal({
            title: 'Προσοχή!',
            text: "Είστε σίγουρος για την διαγραφή της διεύθυνσης;",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Άκυρο',
            confirmButtonText: 'Διαγραφή'
        }).then((result) => {
            if (result.value) {
                $.post(Routing.generate('delete_address'), data, function () {
                    location.reload();
                });
            }
        })

    });

});