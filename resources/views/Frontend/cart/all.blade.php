@extends('layouts.frontend')

@section('title', trans('frontend.home'))


@section('content')
    <form action="{{ route('checkout') }}" method="POST">
        @csrf
        <div class="row margin-bottom-40">
            <div class="col-md-12 col-sm-12">
                <h1>{{ trans('frontend.shopping_list') }} </h1>
                @if (session('error'))
                <div class="alert alert-danger p-1">
                    {{ session('error') }}
                </div>
            @endif
                @livewire('cart-items')
            </div>
        </div>
    </form>
@endsection
