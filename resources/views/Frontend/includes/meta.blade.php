<meta charset="utf-8">
<title>@yield('title', 'App Name')</title>

<meta content="width=device-width, initial-scale=1.0" name="viewport">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<meta content="Metronic Shop UI description" name="description">
<meta content="Metronic Shop UI keywords" name="keywords">
<meta content="keenthemes" name="author">

<meta property="og:site_name" content="-CUSTOMER VALUE-">
<meta property="og:title" content="-CUSTOMER VALUE-">
<meta property="og:description" content="-CUSTOMER VALUE-">
<meta property="og:type" content="website">
<meta property="og:image" content="-CUSTOMER VALUE-"><!-- link to image for socio -->
<meta property="og:url" content="-CUSTOMER VALUE-">

<link rel="shortcut icon" href="favicon.html">

<style>
  .content-wrapper {
    width: 100%;
    padding: 0 15px;
    margin: 0 auto;
}

.brand-list {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start; /* يجعل العناصر بجانب بعضها */
    gap: 10px; /* للتحكم في المسافة بين العناصر */
    margin-bottom: 35px;
    background-color: white;
    padding: 10px;
}

.brand-box {
    flex: 0 1 calc(15% - 10px); /* العرض يعتمد على النسبة، مع طرح المسافة بين العناصر */
    text-align: center;
    box-sizing: border-box;
    padding: 10px;
}

.brand-box img {
    max-width: 150px;
    height: auto;
    display: block;
    margin: 0 auto;
}

@media (max-width: 1200px) {
    .brand-box {
        flex: 0 1 calc(25% - 10px);
    }
}

@media (max-width: 992px) {
    .brand-box {
        flex: 0 1 calc(33.33% - 10px);
    }
}

@media (max-width: 768px) {
    .brand-box {
        flex: 0 1 calc(50% - 10px);
    }
}

@media (max-width: 576px) {
    .brand-box {
        flex: 0 1 calc(100% - 10px);
    }
}

    .carousel-image {
    width: 100%; 
    height: 400px; 
    object-fit: cover; 
    object-position: center; 
}

@media (max-width: 768px) {
    .carousel-image {
        height: 300px; 
    }
}

@media (max-width: 576px) {
    .carousel-image {
        height: 200px; 
    }
}


</style>
