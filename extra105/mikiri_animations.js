console.log('mikiri_animations.js loaded');
$(document).ready(function() {
    function animateBackground() {
        $('.background').each(function(index, element) {
            $(element).animate({
                left: '+=100px'
            }, 5000, function() {
                $(element).css('left', '-100px');
                animateBackground();
            });
        });
    }

    animateBackground();
});