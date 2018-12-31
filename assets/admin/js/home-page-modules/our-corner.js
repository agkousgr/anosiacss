let modal = $('#modal_our_corner');
let modalTitle = $('.modal-title');
let modalBody = $('.modal-body');
require('bootstrap');
require('select2');

$(document).ready(function () {

    $('.edit-category').on('click', function (e) {
        e.preventDefault();
        displayEditModal('our_corner_update', $(this).data('id'));
    });

    function displayEditModal(route, id) {
        modalBody.empty()
            .load(Routing.generate(route, {'id': id}));
        modalTitle.text('Επεξεργασία');
        modal.modal('show');
        $('#our_corner_category').select2();
    }

});

