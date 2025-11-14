'use strict'
$(function () {
    $('#loginForm').on('submit', async function (e) {
        e.preventDefault();
        const form = $(this);
        try {
            const response = await $.post('/user/login', form.serialize());
            toastr.success(response.message);

            setTimeout(function() {
                window.location.reload();
            }, 1000);

        }catch (e) {
            const error = e.responseJSON;
            toastr.error(error.message);
        }
    });

});
