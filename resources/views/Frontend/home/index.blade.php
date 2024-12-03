@extends('layouts.frontend')

@section('title', trans('frontend.home'))

@section('content')
@include('Frontend.home.new-product')
@include('Frontend.home.products')
@endsection
@include('Frontend.home.slider')
@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryItems = document.querySelectorAll('.sidebar-menu .list-group-item');
        const products = document.querySelectorAll('#products-container .product-item');
    
        categoryItems.forEach(category => {
            category.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-id');
    
                products.forEach(product => {
                    if (categoryId === 'all') {
                        product.style.display = 'block';
                    } else {
                        if (product.classList.contains('category-' + categoryId)) {
                            product.style.display = 'block';
                        } else {
                            product.style.display = 'none';
                        }
                    }
                });
            });
        });
    });
    </script>
    
@endsection