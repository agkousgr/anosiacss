let modal = $('#modal_add_slide');
let modalTitle = $('.modal-title');
let modalBody = $('.modal-body');
require('bootstrap');
require('select2');
require('jquery-plugin-components');

$(document).ready(function () {
    $('#articles').DataTable();

    // Setup - add a text input to each footer cell
    $('#articles thead th').each(function () {
        var title = $(this).text();
        $(this).html('<input type="text" placeholder="Search ' + title + '" />');
    });

    // DataTable
    var table = $('#articles').DataTable();

    // Apply the search
    table.columns().every(function () {
        var that = this;

        $('input', this.header()).on('keyup change', function () {
            if (that.search() !== this.value) {
                that
                    .search(this.value)
                    .draw();
            }
        });
    });

    $('#add-article').on('click', function (e) {
        e.preventDefault();
        displayModal('article_add');
    });

    $('.card-body').on('click', '#edit-article', function (e) {
        e.preventDefault();
        displayEditModal('article_update', $(this).data('id'));
    });

    $('.card-body').on('click', '#delete-article', function (e) {
        e.preventDefault();
        displayDeleteModal('article_delete', $(this).data('id'));
    });

    function displayModal(route) {
        modalBody.empty()
            .load(Routing.generate(route));
        modalTitle.text('Προσθήκη άρθρου');
        modal.modal('show');
        $('.modal-header').css('background-color', '#5ba2e6');
        $('#admin_category_isPublished').select2();
    }

    function displayEditModal(route, id) {
        modalBody.empty()
            .load(Routing.generate(route, {'id': id}));
        modalTitle.text('Επεξεργασία άρθρου');
        modal.modal('show');
        $('.modal-header').css('background-color', '#5ba2e6');
        $('#admin_category_isPublished').select2();
    }

    function displayDeleteModal(route, id) {
        modalBody.empty()
            .load(Routing.generate(route, {'id': id}));
        modalTitle.text('Διαγραφή άρθρου');
        modal.modal('show');
        $('.modal-header').css('background-color', '#e65b5b');
    }

});

