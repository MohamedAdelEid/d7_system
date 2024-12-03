@if (request()->routeIs('category'))
    <div class="title-wrapper" style="background: #1b2024 url({{ isset($category) && $category->getMedia('category_cover')->first() ? $category->getMedia('category_cover')->first()->getUrl() : '' }}) repeat 100% 100%; "> 
@else
    <div class="title-wrapper" style="background: #1b2024 url({{ isset($category) && $category->getMedia('brand_cover')->first() ? $category->getMedia('brand_cover')->first()->getUrl() : '' }}) repeat 100% 100%; ">
@endif
    <div class="container">
        <div class="container-inner">
            <h1><span>{{ $category->name }}</h1>
        </div>
    </div>
</div>
