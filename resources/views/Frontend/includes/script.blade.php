@livewireScripts


    
<script src="{{ asset('assets') }}/plugins/jquery.min.js" type="text/javascript"></script>
<script src="{{ asset('assets') }}/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<script src="{{ asset('assets') }}/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="{{ asset('assets') }}/corporate/scripts/back-to-top.js" type="text/javascript"></script>
<script src="{{ asset('assets') }}/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->

<!-- BEGIN PAGE LEVEL JAVASCRIPTS (REQUIRED ONLY FOR CURRENT PAGE) -->
<script src="{{ asset('assets') }}/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script><!-- pop up -->
<script src="{{ asset('assets') }}/plugins/owl.carousel/owl.carousel.min.js" type="text/javascript"></script>
<!-- slider for products -->
<script src='{{ asset('assets') }}/plugins/zoom/jquery.zoom.min.js' type="text/javascript"></script><!-- product zoom -->
<script src="{{ asset('assets') }}/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script>
<!-- Quantity -->

<script src="{{ asset('assets') }}/corporate/scripts/layout.js" type="text/javascript"></script>
<script src="{{ asset('assets') }}/pages/scripts/bs-carousel.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        Layout.init();
        Layout.initOWL();
        Layout.initImageZoom();
        Layout.initTouchspin();
        Layout.initTwitter();
    });
</script>
<script>
    $(document).ready(function () {
    $('#owl-carousel').owlCarousel({
        loop: true, // Enable infinite loop
        margin: 10, // Space between items
        dots: true, // Show pagination dots
        autoplay: true, // Enable autoplay
        autoplayTimeout: 3000, // Time between slides (in ms)
        autoplayHoverPause: true, // Pause autoplay on hover
        responsive: {
            0: {
                items: 1 // 1 item on small screens (0px to 576px)
            },
            576: {
                items: 2 // 2 items on tablets (576px to 768px)
            },
            768: {
                items: 3 // 3 items on medium devices (768px to 992px)
            },
            992: {
                items: 4 // 4 items on large devices (992px to 1200px)
            },
            1200: {
                items: 4 // 5 items on extra large screens (1200px and above)
            }
        }
    });
});

</script>




