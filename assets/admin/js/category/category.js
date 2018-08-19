let modal = $('#modal_add_slide');
let modalTitle = $('.modal-title');
let modalBody = $('.modal-body');
require('bootstrap');
require('select2');

$(document).ready(function () {
    $('#add-category').on('click', function (e) {
        e.preventDefault();
        displayModal('category_add');
    });

    $('.card-body').on('click', '#edit-category', function (e) {
        e.preventDefault();
        displayEditModal('category_update', $(this).data('id'));
    });

    function displayModal(route) {
        modalBody.empty()
            .load(Routing.generate(route));
        modalTitle.text('Προσθήκη κατηγορίας');
        modal.modal('show');
        $('#admin_category_isPublished').select2();
    }

    function displayEditModal(route, id) {
        modalBody.empty()
            .load(Routing.generate(route, {'id': id}));
        modalTitle.text('Επεξεργασία κατηγορίας');
        modal.modal('show');
        $('#admin_category_isPublished').select2();
    }

});

