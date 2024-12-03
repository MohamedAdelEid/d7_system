@extends('layouts.admin')

@section('title', trans('admin.Change in Price Report'))


@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> {{ trans('admin.Change in Price Report') }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a> / <a
                                href="{{ route('dashboard.reports.change.in.price.report') }}">{{ trans('admin.Change in Price Report') }}</a>
                        </li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content">
        <div class="container-fluid">
            <div class="card collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">{{ trans('admin.filter') }}</h3>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <!-- /.card-tools -->
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="col-lg-12">
                        <?php
                        $productsCollection = collect($products)->pluck('name', 'id');
                        $usersCollection = collect($users)->pluck('name', 'id');
                        ?>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ trans('admin.product') }}</label>
                                    <select class="form-control select2 product_id" name="product_id" id="product_id"
                                        style="width: 100%;">
                                        <option value="" selected>{{ trans('admin.Select') }}</option>
                                        @foreach ($productsCollection as $id => $name)
                                            <option value="{{ $id }}"
                                                @if (Request()->product_id == $id) selected @endif>{{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ trans('admin.Created by') }}</label>
                                    <select class="form-control select2 user_id" name="created_by" id="created_by"
                                        style="width: 100%;">
                                        <option value="" selected>{{ trans('admin.Select') }}</option>
                                        @foreach ($usersCollection as $id => $name)
                                            <option value="{{ $id }}"
                                                @if (Request()->created_by == $id) selected @endif>{{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ trans('admin.date_from') }}</label>
                                    <input type="date" class="form-control" name="date_from" id="date_from"
                                        value="{{ Request()->date_from }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ trans('admin.date_to') }}</label>
                                    <input type="date" class="form-control" name="date_to" id="date_to"
                                        value="{{ Request()->date_to }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- /.col -->
                <div class="col-md-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{ trans('admin.Change in Price Report') }}
                            </h3>
                        </div>


                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="change-in-price-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ trans('admin.product') }}</th>
                                        <th>{{ trans('admin.old_unit_price') }}</th>
                                        <th>{{ trans('admin.new_unit_price') }}</th>
                                        <th>{{ trans('admin.Created by') }}</th>
                                        <th>{{ trans('admin.date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->

                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection


@section('script')
    <script>
        $(document).ready(function() {
            var table = $('#change-in-price-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('dashboard.reports.change.in.price.report') }}",
                    "data": function(d) {
                        d.product_id = $('#product_id').val();
                        d.created_by = $('#created_by').val();
                        d.date_from = $('#date_from').val();
                        d.date_to = $('#date_to').val();
                    }
                },
                columns: [{
                        data: 'product_name',
                        name: 'product_name'
                    },
                    {
                        data: 'old_unit_price',
                        name: 'old_unit_price'
                    },
                    {
                        data: 'new_unit_price',
                        name: 'new_unit_price'
                    },
                    {
                        data: 'changed_by',
                        name: 'changed_by'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                ],
                dom: 'lBfrtip',
                buttons: [{
                        extend: 'copy',
                        exportOptions: {
                            search: 'none',
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            search: 'none',
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'csv',
                        exportOptions: {
                            search: 'none',
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            search: 'none',
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            search: 'none',
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'colvis',
                        exportOptions: {
                            search: 'none',
                            columns: ':visible'
                        }
                    },
                ],
            });
            $(document).on('change', '#product_id, #date_from, #date_to, #created_by', function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
