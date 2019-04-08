$(document).ready(function () {
    $(".show-password, .hide-password").on('click', function() {
        if ($(this).hasClass('show-password')) {
            $("#user_registration_password_first").attr("type", "text");
            $(this).parent().find(".show-password").hide();
            $(this).parent().find(".hide-password").show();
        } else {
            $("#user_registration_password_first").attr("type", "password");
            $(this).parent().find(".hide-password").hide();
            $(this).parent().find(".show-password").show();
        }
    });
});