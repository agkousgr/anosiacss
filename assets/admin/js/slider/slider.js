let modal = $('#modal_add_slide');
let modalTitle = $('.modal-title');
let modalBody = $('.modal-body');
require('bootstrap');
require('select2');

$(document).ready(function () {
    $('#add-slide').on('click', function (e) {
        e.preventDefault();
        displayModal('slider_add');
    });

    $('#add-slide').on('click', function (e) {
        e.preventDefault();
        displayEditModal('slider_update', $(this).data('id'));
    });

    function displayModal(route) {
        console.log('zong');
        modalBody.empty()
            .load(Routing.generate(route));
        modalTitle.text('Προσθήκη νέου slide');
        modal.modal('show');
        $('#slider_isPublished').select2();
    }

    function displayEditModal(route, id) {
        console.log('zong');
        modalBody.empty()
            .load(Routing.generate(route, {'id': id}));
        modalTitle.text('Προσθήκη νέου slide');
        modal.modal('show');
        $('#slider_isPublished').select2();
    }

});

