  <!-- Fonts START -->
  <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|PT+Sans+Narrow|Source+Sans+Pro:200,300,400,600,700,900&amp;subset=all" rel="stylesheet" type="text/css">
  <link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900&amp;subset=all" rel="stylesheet" type="text/css"><!--- fonts for slider on the index page -->  
  <!-- Fonts END -->

  <!-- Global styles START -->          
  <link href="{{ asset('assets') }}/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="{{ asset('assets') }}/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Global styles END --> 
   
  <!-- Page level plugin styles START -->
  <link href="{{ asset('assets') }}/pages/css/animate.css" rel="stylesheet">
  <link href="{{ asset('assets') }}/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet">
  <link href="{{ asset('assets') }}/plugins/owl.carousel/assets/owl.carousel.css" rel="stylesheet">
  <!-- Page level plugin styles END -->

  <!-- Theme styles START -->
  <link href="{{ asset('assets') }}/pages/css/components.css" rel="stylesheet">
  <link href="{{ asset('assets') }}/pages/css/slider.css" rel="stylesheet">
  <link href="{{ asset('assets') }}/pages/css/style-shop.css" rel="stylesheet" type="text/css">
  <link href="{{ asset('assets') }}/corporate/css/style.css" rel="stylesheet">
  <link href="{{ asset('assets') }}/corporate/css/style-responsive.css" rel="stylesheet">
  <link href="{{ asset('assets') }}/corporate/css/themes/red.css" rel="stylesheet" id="style-color">
  <link href="{{ asset('assets') }}/corporate/css/custom.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

  @if (app()->getLocale() == 'ar')
  <link href="https://fonts.googleapis.com/css?family=Cairo:400,700" rel="stylesheet">
  <style>
    * :not(.fa):not(.fas):not(.far):not(.fal):not(.fab){
          font-family: 'Cairo', sans-serif !important;
      }
  </style>
@endif
@livewireStyles
<style>
/* تنسيق القائمة الأساسية */
.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.list-group-item {
    position: relative;
    padding: 10px;
    border-bottom: 1px solid #ccc;
}

.list-group-item > a {
    text-decoration: none;
    color: #333;
    display: inline-block;
}

/* تنسيق القائمة الفرعية */
.dropdown-menu {
    list-style: none;
    padding: 0;
    margin: 0;
    display: none; /* مخفية افتراضيًا */
    position: absolute;
    left: 100%; /* تظهر على يمين العنصر الأب */
    top: 0;
    background: #f9f9f9;
    border: 1px solid #ddd;
    z-index: 100;
    min-width: 200px;
}

.dropdown-menu li {
    padding: 8px 10px;
    border-bottom: 1px solid #ddd;
}

.dropdown-menu li:last-child {
    border-bottom: none;
}

.dropdown-menu li a {
    text-decoration: none;
    color: #555;
}

/* إظهار القائمة الفرعية عند الإشارة */
.list-group-item:hover > .dropdown-menu {
    display: block;
}

/* التأكد من أن القسم الرئيسي لا يتأثر */
.list-group-item:hover > a {
    color: #000; /* تغيير اللون عند الإشارة إذا أردت */
}

@media (max-width: 768px) {
    .header .logo img {
        max-height: 60px; /* تقليل الحجم للشاشات الأصغر */
    }
}

@media (max-width: 480px) {
    .header .logo img {
        max-height: 50px; /* تقليل الحجم أكثر للشاشات الصغيرة جدًا */
    }
}


</style>