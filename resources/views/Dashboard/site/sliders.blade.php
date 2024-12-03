@extends('layouts.admin')

@section('title', trans('admin.site'))


@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ trans('admin.site') }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a> /
                            {{ trans('admin.site') }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!--start filter-->
    <!--end filter-->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    @if (auth('user')->user()->has_permission('create-activityTypes'))
                        <a href="{{ route('dashboard.site-setting.sliders.create') }}" type="button"
                            class="btn btn-info">{{ trans('admin.Add') }}</a>
                    @else
                        <a href="#" type="button" class="btn btn-info disabled">{{ trans('admin.Add') }}</a>
                    @endif
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered table-striped data-table responsive">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('admin.image') }}</th>
                                <th>{{ trans('admin.title') }}</th>
                                <th>{{ trans('admin.sub_title') }}</th>
                                <th>{{ trans('admin.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $slider)
                                <tr>
                                    <td> {{ $slider->id }}</td>
                                    <td> <img src="{{ $slider && method_exists($slider, 'getMedia') && $slider->getMedia('sliders')->first() 
                                        ? $slider->getMedia('sliders')->first()->getUrl() 
                                        : asset('assets/pages/img/products/model2.jpg') }}" alt="" style="width: 150px; height: auto;"> </td>
                                    <td> {{ $slider->title }}</td>
                                    <td> {{ $slider->title }}</td>
                                    <td>
                                        <div class="btn-group"><button type="button"
                                                class="btn btn-success">الإجراءات</button><button type="button"
                                                class="btn btn-success dropdown-toggle" data-toggle="dropdown"
                                                aria-expanded="false"></button>
                                            <div class="dropdown-menu" role="menu" style=""><a
                                                    class="dropdown-item"
                                                    href="{{ route('dashboard.site-setting.sliders.edit', $slider->id)  }}">تعديل</a><a
                                                    class="dropdown-item delete-popup" href="#" data-toggle="modal"
                                                    data-target="#modal-default"
                                                    data-url="{{route('dashboard.site-setting.sliders.delete', $slider->id) }}">حذف</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
