let modal = $('#modal_add_slide');
let modalTitle = $('.modal-title');
let modalBody = $('.modal-body');
require('bootstrap');
// require('select2');

$(document).ready(function () {
    $('#add-slide').on('click', function (e) {
        e.preventDefault();
        displayModal('promo_categories_add');
    });

    $('.edit-slide').on('click', function (e) {
        e.preventDefault();
        displayEditModal('promo_categories_update', $(this).data('id'));
    });

    $('.delete-slide').on('click', function (e) {
        e.preventDefault();
        displayDeleteModal('promo_categories_delete', $(this).data('id'));
    });

    function displayModal(route) {
        modalBody.empty()
            .load(Routing.generate(route));
        modalTitle.text('Προσθήκη νέου banner');
        modal.modal('show');
        // $('#promo_categories_isPublished').select2();
    }

    function displayEditModal(route, id) {
        modalBody.empty()
            .load(Routing.generate(route, {'id': id}));
        modalTitle.text('Επεξεργασία banner');
        modal.modal('show');
        // $('#promo_categories_isPublished').select2();
    }

    function displayDeleteModal(route, id) {
        modalBody.empty()
            .load(Routing.generate(route, {'id': id}));
        modalTitle.text('Διαγραφή banner');
        modal.modal('show');
        $('.modal-header').css('background-color', '#e65b5b');
    }

});

