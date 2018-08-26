let modal = $('#modal_add_slide');
let modalTitle = $('.modal-title');
let modalBody = $('.modal-body');
require('datatables.net');
require('datatables.net-bs4');
require('bootstrap');
require('select2');

$(document).ready(function () {

    $('#articles').DataTable();

    $('.card-body').on('click', '#delete-article', function (e) {
        e.preventDefault();
        displayDeleteModal('article_delete', $(this).data('id'));
    });

    function displayDeleteModal(route, id) {
        modalBody.empty()
            .load(Routing.generate(route, {'id': id}));
        modalTitle.text('Διαγραφή άρθρου');
        modal.modal('show');
        $('.modal-header').css('background-color', '#e65b5b');
    }

});

