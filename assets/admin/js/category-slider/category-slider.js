require('bootstrap');
require('select2');
require('sweetalert2');
let modal = $('#modal_add_slide');
let modalTitle = $('.modal-title');
let modalBody = $('.modal-body');

$(document).ready(function () {
    $('.card-body#category_slider_category').select2();

    $('#add-slide').on('click', function (e) {
        e.preventDefault();
        displayModal('category_slider_add', $(this).data('id'));
    });

    $('.edit-slide').on('click', function (e) {
        e.preventDefault();
        displayEditModal('category_slider_update', $(this).data('id'));
    });

    $('.delete-slide').on('click', function (e) {
        e.preventDefault();
        displayDeleteModal('category_slider_delete', $(this).data('id'));
    });

    $('.card-body').on('click', '#edit-slide', function (e) {
        e.preventDefault();
        displayEditModal('category_slider_update', $(this).data('id'));
    });

    $('.card-body').on('click', '#delete-slide', function (e) {
        e.preventDefault();
        displayDeleteModal('category_slider_delete', $(this).data('id'));
    });

    function displayModal(route, id) {
        modalBody.empty()
            .load(Routing.generate(route, {'id': id}));
        modalTitle.text('Προσθήκη κατηγορίας');
        modal.modal('show');
        $('.modal-header').css('background-color', '#5ba2e6');
        $('#admin_category_isPublished').select2();
    }

    function displayEditModal(route, id) {
        modalBody.empty()
            .load(Routing.generate(route, {'id': id}));
        modalTitle.text('Επεξεργασία slide');
        modal.modal('show');
        $('.modal-header').css('background-color', '#5ba2e6');
        $('#admin_category_isPublished').select2();
    }

    function displayDeleteModal(route, id) {
        modalBody.empty()
            .load(Routing.generate(route, {'id': id}));
        modalTitle.text('Διαγραφή slide');
        modal.modal('show');
        $('.modal-header').css('background-color', '#e65b5b');
    }

});

