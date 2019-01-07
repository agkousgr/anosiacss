let modal = $('#modal_add_offer');
let modalTitle = $('.modal-title');
let modalBody = $('.modal-body');
require('../moment.min.js');
require('bootstrap');
require('bootstrap-daterangepicker');
// require('select2');

$(document).ready(function () {
    $('#add-offer').on('click', function (e) {
        e.preventDefault();
        $('#add-offer-container').toggleClass('hidden');
        // displayModal('latest_offer_add');
    });

    $('.edit-offer').on('click', function (e) {
        e.preventDefault();
        displayEditModal('latest_offer_update', $(this).data('id'));
    });

    $('.delete-offer').on('click', function (e) {
        e.preventDefault();
        displayDeleteModal('latest_offer_delete', $(this).data('id'));
    });

    function displayModal(route) {
        modalBody.empty()
            .load(Routing.generate(route), function () {
                $('input[name="latest-offer"]').daterangepicker({
                    singleDatePicker: true,
                    timePicker: true,
                    locale: {
                        format: 'YYYY-MM-DD hh:mm'
                    },
                    autoApply: true,
                });
            });
        modalTitle.text('Προσθήκη νέας προσφοράς');
        modal.modal('show');
        $('.container-fluid').find('input[name="latest-offer"]').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            },
            autoApply: true,
        });
        // $('#slider_isPublished').select2();
    }

    function displayEditModal(route, id) {
        modalBody.empty()
            .load(Routing.generate(route, {'id': id}));
        modalTitle.text('Επεξεργασία προσφοράς');
        modal.modal('show');
        // $('#slider_isPublished').select2();
    }

    function displayDeleteModal(route, id) {
        modalBody.empty()
            .load(Routing.generate(route, {'id': id}));
        modalTitle.text('Διαγραφή προσφοράς');
        modal.modal('show');
        $('.modal-header').css('background-color', '#e65b5b');
    }

});

