(function ($) {
    'use strict';
    $(window).on('load',function(){
        //==========preloder===========
        var preLoder = $(".preloader");
        preLoder.fadeOut(1000);
    });

    //==========fixed header & scroll to top button===========
    $(window).on('scroll', function(){
        if($(window).scrollTop() > 300) {
            $('.bottom-header').addClass('animated fadeInDown fixed-header');
            $('.scroll-to-top').fadeIn();
            $('.scroll-to-top button').addClass('active');
        } else {
            $('.bottom-header').removeClass('animated fadeInDown fixed-header');
            $('.scroll-to-top').fadeOut();
            $('.scroll-to-top button').removeClass('active');
        }
    });
    $(document).ready(function(){

        // ===========search option view on lg(large) monitor===========
        $(".nav-form").on('click', function(event) {
            $(".nav-form").addClass("active");
            event.stopPropagation();
        });
        $(document).on('click', function(event){
            if (!$(event.target).hasClass('active')) {
                $(".nav-form").removeClass("active");
            }
        });
        
        // ========== language selection menu ==========
        // ========== language selection menu ==========
        $('#demo').flagStrap({
            countries: {
                "SP": "ESP",
                "BD": "BAN",
                "US": "USA"
            },
            buttonSize: "",
            buttonType: "",
            buttonText: "Country of",
            labelMargin: "10px",
            scrollable: false,
            scrollableHeight: "350px"
        });

        // ========== odometer initialize==========
        $('.odometer').appear(function(e) {
            var odo = $(".odometer");
            odo.each(function() {
                var countNumber = $(this).attr("data-count");
                $(this).html(countNumber);
            });
        });

        // ========== slick slider ===========
        $('.slick-slides-js').each(function(){
            
          var $this             = $(this),
                slidestoshow    = ( typeof $(this).data('slidestoshow') != 'undefined' && $(this).data('slidestoshow').length !=0 ) ?  $(this).data('slidestoshow') : 1,
                speed            = ( typeof $(this).data('speed') != 'undefined' && $(this).data('speed').length !=0 ) ?  $(this).data('speed') : 500,
                slidesToScroll  = ( typeof $(this).data('slidesToScroll') != 'undefined' && $(this).data('slidesToScroll').length !=0 ) ?  $(this).data('slidesToScroll') : 1,
                centerMode      = ( typeof $(this).data('centerMode') != 'undefined' && $(this).data('centerMode').length !=0 ) ?  $(this).data('centerMode') : false,
                centerPadding   = ( typeof $(this).data('centerPadding') != 'undefined' && $(this).data('centerPadding').length !=0 ) ?  $(this).data('centerPadding') : '0',
                autoplay        = ( typeof $(this).data('autoplay') != 'undefined' && $(this).data('autoplay').length !=0 ) ?  $(this).data('autoplay') : true,
                arrows          = ( typeof $(this).data('arrows') != 'undefined' && $(this).data('arrows').length !=0 ) ?  $(this).data('arrows') : true,
                dots            = ( typeof $(this).data('dots') != 'undefined' && $(this).data('dots').length !=0 ) ?  $(this).data('dots') : false,
                prevArrow       = ( typeof $(this).data('prevArrow') != 'undefined' && $(this).data('prevArrow').length !=0 ) ?  $(this).data('prevArrow') : '<button class="prev">←</button>',
                nextArrow       = ( typeof $(this).data('nextArrow') != 'undefined' && $(this).data('nextArrow').length !=0 ) ?  $(this).data('nextArrow') : '<button class="next">→</button>',
                mobile_view     = ( typeof $(this).data('mobile_view') != 'undefined' && $(this).data('mobile_view').length !=0 ) ? $(this).data('mobile_view') : 1,
                sm_tablet_view  = ( typeof $(this).data('small_tablet_view') != 'undefined' && $(this).data('small_tablet_view').length !=0 ) ? $(this).data('small_tablet_view') : 1,
                tablet_view     = ( typeof $(this).data('tablet_view') != 'undefined' && $(this).data('tablet_view').length !=0 ) ? $(this).data('tablet_view') : 1,
                laptop_view     = ( typeof $(this).data('laptop_view') != 'undefined' && $(this).data('laptop_view').length !=0 ) ? $(this).data('laptop_view') : 1,
                main_view       = ( typeof $(this).data('1200') != 'undefined' && $(this).data('1200').length !=0 ) ? $(this).data('1200') : 1;
               
                // alert(mobile_view);
            $($this).slick({
                slidesToShow: slidestoshow,
                speed: speed,
                slidesToScroll: slidesToScroll,
                centerMode: centerMode,
                centerPadding : centerPadding,
                autoplay : autoplay,
                prevArrow: prevArrow,
                nextArrow: nextArrow,
                arrows: arrows,
                dots: dots,
                responsive: [
                    {
                      breakpoint: 1700,
                      settings: {
                        slidesToShow: main_view,
                      }
                    },
                    {
                      breakpoint: 1700,
                      settings: {
                        slidesToShow: laptop_view,
                      }
                    },
                    {
                      breakpoint: 800,
                      settings: {
                        slidesToShow: tablet_view,
                      }
                    },
                    {
                      breakpoint: 600,
                      settings: {
                        slidesToShow: sm_tablet_view,
                      }
                    },
                    {
                      breakpoint: 320,
                      settings: {
                        slidesToShow: mobile_view,
                      }
                    }
                ]
            });

        });

        //==========modal video popup===========
        $(".vdo-btn").modalVideo();
        
        // ==========progresscircle intialize===========
        $('.single-skill').circlechart();

        //==========price range===========//
        if($("#slider-range").length){
            $("#slider-range").slider({
            range: true,
            min: 10,
            max: 1000,
            values: [ 0.00, 500.00 ],
            slide: function( event, ui ) {
                $( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
            }
            });
            $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
            " - $" + $( "#slider-range" ).slider( "values", 1 ) );
        }
         
        //==========product details slider===========//
        var productCarousel = $('.product-slider');
        productCarousel.owlCarousel({
            loop: true,
            dots: false,
            nav: false,
            margin: 0,
            mouseDrag: false,
            touchDrag: false,
            autoplay: false,
            startPosition: 0,
            autoplayTimeout: 4000,
            autoplayHoverPause: true,
            thumbs: true,
            thumbsPrerendered: true,
            animateOut: 'fadeOut',
            animateIn: 'fadeIn',
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 1
                },
                960: {
                    items: 1
                },
                1200: {
                    items: 1
                },
                1920: {
                    items: 1
                }
            }
        }); 

        //==========product quantity===========//
        $('.quantity').each(function () {
            var spinner = jQuery(this),
                input = spinner.find('input[type="number"]'),
                btnUp = spinner.find('.quantity-up'),
                btnDown = spinner.find('.quantity-down'),
                min = input.attr('min'),
                max = input.attr('max');

            btnUp.on('click', function () {
                var oldValue = parseFloat(input.val());
                if (oldValue >= max) {
                    var newVal = oldValue;
                } else {
                    var newVal = oldValue + 1;
                }
                spinner.find("input").val(newVal);
                spinner.find("input").trigger("change");
            });

            btnDown.on('click', function () {
                var oldValue = parseFloat(input.val());
                if (oldValue <= min) {
                    var newVal = oldValue;
                } else {
                    var newVal = oldValue - 1;
                }
                spinner.find("input").val(newVal);
                spinner.find("input").trigger("change");
            });

        });

        //==========product rating===========//
        $('.rating').rating();

    });
});