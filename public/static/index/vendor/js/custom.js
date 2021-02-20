(function ($) {
  "use strict"; // Start of use strict

// Preloader Start
  $(window).load(function () {
    $('.loader').fadeOut();
    $('#preloader')
        .delay(350)
        .fadeOut('slow');
    $('body')
        .delay(350)
  });
// Preloader End

/// MAIN MENU SCRIPT START

  // Smooth scrolling using jQuery easing
  $('a.js-scroll-trigger[href*="#"]:not([href="#"])').click(function() {
    if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
      if (target.length) {
        $('html, body').animate({
          scrollTop: (target.offset().top - 48)
        }, 1000, "easeInOutExpo");
        return false;
      }
    }
  });

  // Closes responsive menu when a scroll trigger link is clicked
  $('.js-scroll-trigger').click(function() {
    $('.navbar-collapse').collapse('hide');
  });

  // Activate scrollspy to add active class to navbar items on scroll
  $('body').scrollspy({
    target: '#mainNav',
    offset: 54
  });

  // Collapse Navbar
  var navbarCollapse = function() {
    if ($("#mainNav").offset().top > 100) {
      $("#mainNav").addClass("navbar-shrink");
    } else {
      $("#mainNav").removeClass("navbar-shrink");
    }
  };
  // Collapse now if page is not at top
  navbarCollapse();
  // Collapse the navbar when page is scrolled
  $(window).scroll(navbarCollapse);

  // mobile Menu area
  $('nav.mobile_menu').meanmenu({
    meanScreenWidth: '991'
  });
  $('nav.mean-nav li > a:first-child').on('click', function () {
    $('a.meanmenu-reveal').click();
  });

  // mobile Menu area

  // MAIN MENU SCRIPT END

  // Hero Effect JS Start
  var $wrap = $('#wrapper'),
      lFollowX = 0,
      lFollowY = 0,
      x = 0,
      y = 0,
      friction = 1 / 10;

  function animate() {
    x += (lFollowX - x) * friction;
    y += (lFollowY - y) * friction;

    $wrap.css({
      'transform': 'translate(-50%, -50%) perspective(600px) rotateY(' + -x + 'deg) rotateX(' + y + 'deg)'
    });
    window.requestAnimationFrame(animate);
  }

  $(window).on('mousemove click', function(e) {
    var lMouseX = Math.max(-100, Math.min(100, $(window).width() / 2 - e.clientX));
    var lMouseY = Math.max(-100, Math.min(100, $(window).height() / 2 - e.clientY));
    lFollowX = (12 * lMouseX) / 100; // 100 : 12 = lMouxeX : lFollow
    lFollowY = (10 * lMouseY) / 100;
  });

  animate();
  // Hero Effect JS End

  // Jquery counterUp
  $('.counter').counterUp({
    time: 3000
  });

  //----- Initialize WOW JS ------
  new WOW().init();

  // --------Paroller JS---------
  if($('.paroller').length){
    $('.paroller').paroller({
      factor: 0.02,            // multiplier for scrolling speed and offset, +- values for direction control
      factorLg: 0.02,          // multiplier for scrolling speed and offset if window width is less than 1200px, +- values for direction control
      type: 'foreground',     // background, foreground
      direction: 'horizontal' // vertical, horizontal
    });
  }

// --------Paroller JS---------

  // ----------------- AOS Animation
  if ($("[data-aos]").length) {
    AOS.init({
      duration: 1000,
      mirror: true,
      disable: 'mobile'
    });
  }

// Veno Box

  $('.venobox').venobox();

  // Testimonial Slider
  var testimonial_slide = $('.testimonial-slider');
  testimonial_slide.owlCarousel({
    loop:true,
    margin:0,
    autoplay:false,
    dots:true,
    nav:false,
    animateOut: 'slideOutUp',
    animateIn: 'flipInX',
    autoplayHoverPause: true,
    smartSpeed: 1e3,
    autoplayTimeout: 8e3,
    autoplaySpeed: 1e3,
    responsive:{
      0:{
        items:1
      },
      600:{
        items:1
      },
      992:{
        items:1
      }
    }
  });
  $('.testimonial_slide_nav .testi_next').on('click', function() {
    testimonial_slide.trigger('next.owl.carousel');
  });

  $('.testimonial_slide_nav .testi_prev').on('click', function() {
    testimonial_slide.trigger('prev.owl.carousel');
  });
  // Testimonial Slider

  // Clients Logo Slider
  var client_logo_slide = $('.client-logo-slider');
  client_logo_slide.owlCarousel({
    loop:true,
    margin:15,
    autoplay:true,
    dots:false,
    nav:false,
    smartSpeed: 1e3,
    autoplayTimeout: 8e3,
    autoplaySpeed: 1e3,
    autoplayHoverPause: !0,
    responsive:{
      0:{
        items:1
      },
      600:{
        items:4
      },
      992:{
        items:5
      }
    }
  });
  // Clients Logo Slider

  // Scroll To Top
    $(window).scroll(function () {
        if ($(this).scrollTop() > 200) {
            $('#scroll').fadeIn();
        } else {
            $('#scroll').fadeOut();
        }
    });
    $('#scroll').click(function () {
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    });
  // Scroll To Top

  // Domain Name Search input text Script

  $(".domain-search-input-wrap").mouseover(function(){
    $(".domain-search-custom-placeholder").css("visibility", "hidden");
  });
  $(".domain-search-input-wrap").mouseout(function(e){
    if(e.target.value === "") {
      $(".domain-search-custom-placeholder").css("visibility", "visible");
    }
  });

  //  Disable copy





})(jQuery); // End of use strict