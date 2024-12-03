@extends('layouts.frontend')

@section('title', trans('frontend.home'))

@section('header')

@include('Frontend.category.header')
@endsection 
@section('content')
    @include('Frontend.category.products')
@endsection
 