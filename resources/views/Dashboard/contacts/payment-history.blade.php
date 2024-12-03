@extends('layouts.admin')

@section('title', trans('admin.payment_history'))

@section('content')
    <div class="container-fluid">

        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="m-0">{{ trans('admin.payment_history') }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">{{ trans('admin.Home') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ trans('admin.payment_history') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">{{ trans('admin.Contact Information') }}</h5>
                <div class="row">
                    <div class="col-md-4"><strong>{{ trans('admin.Name') }}:</strong> {{ $contact->name }}</div>
                    <div class="col-md-4"><strong>{{ trans('admin.phone') }}:</strong> {{ $contact->phone }}</div>
                    <div class="col-md-4">
                        <strong>{{ trans('admin.Address') }}:</strong>
                        {{ $contact->address ?? '' }} - {{ $contact->governorate?->governorate_name_ar ?? '' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card mb-4 collapsed-card">
            <div class="card-header d-flex  ">
                <h3 class="card-title">{{ trans('admin.filter') }}</h3>
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
            </div>
            <div class="card-body">
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

        <!-- Summary Cards Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3 data-statistic="opening_balance">{{ number_format($statistics['opening_balance'], 2) }}
                                </h3>
                                <p>{{ trans('admin.opening_balance') }}</p>
                            </div>
                            <div class="icon"><i class="ion ion-cash"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3 data-statistic="total_payments">{{ number_format($statistics['total_payments'], 2) }}
                                </h3>
                                <p>{{ trans('admin.total_paid') }}</p>
                            </div>
                            <div class="icon"><i class="ion ion-cash"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3
                                    data-statistic="{{ $contact->type == 'customer' ? 'total_sales' : 'total_purchases' }}">
                                    {{ number_format($statistics[$contact->type == 'customer' ? 'total_sales' : 'total_purchases'], 2) }}
                                </h3>
                                <p>{{ $contact->type == 'customer' ? trans('admin.total_sales') : trans('admin.total_purchases') }}
                                </p>
                            </div>
                            <div class="icon"><i class="ion ion-cash"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3 data-statistic="total_due">{{ number_format($final_change_amount, 2) }}</h3>
                                <p>{{ trans('admin.total_due') }}</p>
                            </div>
                            <div class="icon"><i class="ion ion-cash"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons and Dropdown -->
        <div class="row mb-4">
            <div class="col-auto">
                <a href="" class="dropdown-item fire-popup bg-blue " data-toggle="modal"
                    data-target="#modal-default-big"
                    data-url="{{ route('dashboard.contacts.pay-popup', ['id' => $contact->id]) }}">{{ trans('admin.Pay') }}</a>
            </div>
            <div class="col-auto">
                <select name="contact_id" id="contact_select" class="form-control select2" style="width: 200px;"
                    onchange="handleSelectChange(this)">
                    @foreach ($type == 'customer' ? $customer : $supplier as $contactItem)
                        <option
                            value="{{ route('dashboard.contacts.payment-history', ['id' => $contactItem->id, 'type' => $type]) }}"
                            {{ $contactItem->id == $contact->id ? 'selected' : '' }}>
                            {{ $contactItem->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Contact Information Section -->


        <!-- Transaction Table Section -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ trans('admin.transaction_history') }}</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped data-table">
                    <thead>
                        <tr>
                            <th>{{ trans('admin.number') }}</th>
                            <th>{{ trans('admin.date') }}</th>
                            <th>{{ trans('admin.ref_no') }}</th>
                            <th>{{ trans('admin.contacts') }}</th>
                            <th>{{ trans('admin.type') }}</th>
                            <th>{{ trans('admin.method') }}</th>
                            {{-- <th>{{ trans('admin.operation') }}</th> --}}
                            <th>{{ trans('admin.account') }}</th>
                            <th>{{ trans('admin.amount') }}</th>
                            <th>{{ trans('admin.change_on_amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- transaction data will be populated here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script type="text/javascript">
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ route('dashboard.contacts.payment-history', ['id' => $contact->id, 'type' => $contact->type]) }}",
                "data": function(d) {
                    d.date_from = $('#date_from').val();
                    d.date_to = $('#date_to').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    orderable: false
                },
                {
                    data: 'ref_no',
                    name: 'ref_no',
                    orderable: false
                },
                {
                    data: 'contact_name',
                    name: 'contact_name',
                    orderable: false
                },
                {
                    data: 'contact_type',
                    name: 'contact_type',
                    orderable: false
                },
                {
                    data: 'label',
                    name: 'label',
                    orderable: false
                },
                // {
                //     data: 'operation',
                //     name: 'operation',
                //     orderable: false
                // },
                {
                    data: 'account_name',
                    name: 'account_name',
                    orderable: false
                },
                {
                    data: 'amount',
                    name: 'amount',
                    orderable: false
                },
                {
                    data: 'change_amount',
                    name: 'change_amount',
                    orderable: false
                },
            ],
            dom: 'lBfrtip',
            buttons: [{
                    extend: 'copy',
                    exportOptions: {
                        modifier: {
                            page: 'all',
                            search: 'none'
                        }
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        modifier: {
                            page: 'all',
                            search: 'none'
                        }
                    }
                },
                {
                    extend: 'csv',
                    exportOptions: {
                        modifier: {
                            page: 'all',
                            search: 'none'
                        }
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        modifier: {
                            page: 'all',
                            search: 'none'
                        }
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        modifier: {
                            page: 'all',
                            search: 'none'
                        }
                    }
                },
                {
                    extend: 'colvis',
                    exportOptions: {
                        modifier: {
                            page: 'all',
                            search: 'none'
                        }
                    }
                },
            ],
        });

        $(document).on('change', '#date_from, #date_to', function() {
            table.ajax.reload();
        });
    </script>
    <script>
        function handleSelectChange(select) {
            const url = select.value;
            if (url) {
                window.location.href = url;
            }
        }
    </script>

@endsection
