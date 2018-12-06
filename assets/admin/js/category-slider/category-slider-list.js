$(document).ready(function () {
    $('#filter-categories').keyup(function () {
        let filter = $(this).val();
        console.log(filter);
        $('li').each(function () {
            let length = $(this).find("span").text().length > 0;

            if (length && $(this).find("span").text().search(new RegExp(filter, "i")) < 0) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    });
});