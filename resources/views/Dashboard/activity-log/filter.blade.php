@php
    $proccessTypes = ['suppliers' => 'موردين',
    'customers' => 'عملاء',
    'products' => 'منتجات',
    'sales' => 'مبيعات',
    'purchase' => 'مشتريات',
    'stock_transfer' => 'تحويل مخزون',
    'expenses' => 'مصروفات',
    'spoiled_stock' => 'مخزون تالف',
    'accounts' => 'حسابات',
    'create' => 'إضافة',
    'update' => 'تعديل',
    'delete' => 'حذف'];

@endphp
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
                    $usersCollection = collect($users)->pluck('name', 'id');
                ?>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>{{ trans('admin.User') }}</label>
                            <select class="form-control select2 user_id" name="user_id" id="user_id" style="width: 100%;">
                                <option value="" selected >{{ trans('admin.Select') }}</option>
                                @foreach ($usersCollection as $id => $name)
                                    <option value="{{ $id }}" @if (Request()->user_id == $id) selected @endif>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('admin.Proccess Type') }}</label>
                            <select class="form-control select2 proccess_type" name="proccess_type" id="proccess_type" style="width: 100%;">
                                <option value="" selected >{{ trans('admin.Select') }}</option>
                                @foreach ($proccessTypes as $proccess_type=>$name)
                                    <option value="{{ $proccess_type }}" @if (Request()->proccess_type == $proccess_type ) selected @endif>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                       
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>{{ trans('admin.date_from') }}</label>
                            <input type="date" class="form-control" name="date_from" id="date_from" value="{{ Request()->date_from }}">
                        </div>
                        <div class="form-group">
                            <label>{{ trans('admin.date_to') }}</label>
                            <input type="date" class="form-control" name="date_to" id="date_to" value="{{ Request()->date_to }}">
                        </div>
                       
                    </div>
                </div>
                
            </div>
        </div>
        <!-- /.card-body -->
      </div>
    </div>
  </section>