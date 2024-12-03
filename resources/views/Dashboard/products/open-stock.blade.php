@extends('layouts.admin')

@section('title', trans('admin.products'))

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0">{{ trans('admin.products') }}</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{route('dashboard.home')}}">{{ trans('admin.Home') }}</a> / <a href="{{route('dashboard.products.index')}}">{{ trans('admin.products') }}</a> / {{ trans('admin.open stock') }}</li>
            </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container-fluid">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
                <div class="card-header">
                <h3 class="card-title">{{ trans('admin.open stock') }}</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form method="post" action="" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                @foreach ($product->Branches as $key=> $branch)
                                    <h3>{{$branch->name}}</h3>
                                    <table  class="table table-bordered table-striped">
                                        <tr>
                                            <th>{{ trans('admin.product') }}</th>
                                            <th>{{ trans('admin.quantity') }}</th>
                                        </tr>
                                        <tr>
                                            <td>
                                                {{$product->name}}
                                                <input name="open_stock[{{$branch->id}}][{{$key}}][product_id]" value="{{$product->id}}"  type="hidden" class="form-control">
                                                <input name="open_stock[{{$branch->id}}][{{$key}}][branch_id]" value="{{$branch->id}}"  type="hidden" class="form-control">
                                            </td>
                                            <td>
                                                <input name="open_stock[{{$branch->id}}][{{$key}}][quantity]" type="number" class="form-control" required>
                                            </td>
                                            <input hidden name="open_stock[{{$branch->id}}][{{$key}}][unit_price]" value="{{ $product->getPurchasePrice() }}" step=".01"  type="number" class="form-control" required>
                                        </tr>
                                    </table>
                                @endforeach
                            </div>
                            {{-- <div class="col-lg-4">
                                <x-form.input type="text" class="form-control" attribute="required"
                                    name="name" value="{{ isset($data) ? $data->name : old('name') }}"
                                    label="{{ trans('admin.name') }}"/>
                            </div> --}}
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">{{ trans('admin.Add') }}</button>
                    </div>
                </form>
            </div>
            <!-- /.card -->
            </div>
        </div><!-- /.container-fluid -->
    </section>
@endsection

