let modal = $('#modal_add_slide');
let modalTitle = $('.modal-title');
let modalBody = $('.modal-body');
require('bootstrap');
// require('select2');

$(document).ready(function () {
    $('#add-slide').on('click', function (e) {
        e.preventDefault();
        displayModal('slider_add');
    });

    $('.edit-slide').on('click', function (e) {
        e.preventDefault();
        displayEditModal('slider_update', $(this).data('id'));
    });

    $('.delete-slide').on('click', function (e) {
        e.preventDefault();
        alert('zong');
        displayDeleteModal('slider_delete', $(this).data('id'));
    });

    function displayModal(route) {
        modalBody.empty()
            .load(Routing.generate(route));
        modalTitle.text('Προσθήκη νέου slide');
        modal.modal('show');
        // $('#slider_isPublished').select2();
    }

    function displayEditModal(route, id) {
        modalBody.empty()
            .load(Routing.generate(route, {'id': id}));
        modalTitle.text('Επεξεργασία slide');
        modal.modal('show');
        // $('#slider_isPublished').select2();
    }

    function displayDeleteModal(route, id) {
        modalBody.empty()
            .load(Routing.generate(route, {'id': id}));
        modalTitle.text('Διαγραφή κατηγορίας');
        modal.modal('show');
        $('.modal-header').css('background-color', '#e65b5b');
    }

});

