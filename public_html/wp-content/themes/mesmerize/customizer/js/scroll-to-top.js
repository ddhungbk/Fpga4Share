jQuery(function ($) {
    var $window = $(window);
    var $buttonTop = $('.scroll-to-top');
    var scrollTimer;
  
    $buttonTop.on('click', function () {
      $('html, body').animate({
        scrollTop: 0,
      }, 400);
    });
  
    $window.on('scroll', function () {
      clearTimeout(scrollTimer);
      scrollTimer = setTimeout(function() {
       if ($window.scrollTop() > 100) {
          $buttonTop.addClass('scroll-to-top-visible');
        } else {
          $buttonTop.removeClass('scroll-to-top-visible');
        }         
      }, 250);
    });  
  })