const Tools = {
    format: {
        phone: function(phone, separator = ' ') {

            phone = phone.replace('+00', '+').replace(/ /g, '').replace(/-/g, '').replace('/\./g', '');
            if(!phone)
                return '';
            return `${phone.substring(0, 3)}${separator}${phone.substring(3, 6)}${separator}${phone.substring(6, 9)}${phone.substring(9, 12) ? separator+phone.substring(9, 12) : ''}`.trim() ;
        }
    }
}

document.addEventListener("DOMContentLoaded", function(event) {

    const showNavbar = (toggleId, navId, bodyId, headerId) =>{
        const toggle = document.getElementById(toggleId),
            nav = document.getElementById(navId),
            bodypd = document.querySelector(bodyId),
            headerpd = document.getElementById(headerId)

        // Validate that all variables exist
        if (toggle && nav && bodypd && headerpd) {
            toggle.addEventListener('click', () => {
                // show navbar
                nav.classList.toggle('show')
                // change icon
                toggle.classList.toggle('bx-x')
                // add padding to body
                bodypd.classList.toggle('body-pd')
                // add padding to header
                headerpd.classList.toggle('body-pd')
            })
        }
    }

    showNavbar('header-toggle','nav-bar','body','header')

    /*===== LINK ACTIVE =====*/
    const linkColor = document.querySelectorAll('.nav_link')

    function colorLink(){
        if(linkColor){
            linkColor.forEach(l=> l.classList.remove('active'))
            this.classList.add('active')
        }
    }
    linkColor.forEach(l=> l.addEventListener('click', colorLink))

    // Your code to run since DOM is loaded and ready
});

$(function() {
    $(this)
        .ajaxSuccess(function( event, response, settings) {
            if(typeof response.responseJSON.message != 'undefined' && response.responseJSON.message) {
                toastr.success(response.responseJSON.message);
            }
        })
        .ajaxError(function( event, response, settings) {
            if(typeof response.responseJSON.message != 'undefined' && response.responseJSON.message) {
                toastr.error(response.responseJSON.message);
            }
        });
})
