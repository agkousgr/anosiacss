require('bootstrap');
require('select2');
const ClassicEditor = require('@ckeditor/ckeditor5-build-classic');

$(document).ready(function () {

    $('select').select2();

    ClassicEditor
        .create(document.querySelector('.ckeditor-summary'))
        .then(editor => {
            console.log(editor);
        })
        .catch(error => {
            console.error(error);
        });

    ClassicEditor
        .create(document.querySelector('.ckeditor-descr'))
        .then(editor => {
            console.log(editor);
        })
        .catch(error => {
            console.error(error);
        });

});

