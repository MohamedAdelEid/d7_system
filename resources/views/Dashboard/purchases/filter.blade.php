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
                    $branchescollection = collect($branches)->pluck('name', 'id');
                    $supplierCollection = collect($suppliers)->pluck('name', 'id');
                    $usersCollection = collect($users)->pluck('name', 'id');
                ?>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>{{ trans('admin.branch') }}</label>
                            <select class="form-control select2 branch_id" name="branch_id" id="branch_id" style="width: 100%;">
                                <option value="" selected >{{ trans('admin.Select') }}</option>
                                @foreach ($branchescollection as $id => $name)
                                    <option value="{{ $id }}" @if (Request()->branch_id == $id) selected @endif>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>{{ trans('admin.supplier') }}</label>
                            <select class="form-control select2 supplier_id" name="supplier_id" id="supplier_id" style="width: 100%;">
                                <option value="" selected >{{ trans('admin.Select') }}</option>
                                @foreach ($supplierCollection as $id => $name)
                                    <option value="{{ $id }}" @if (Request()->contact_id == $id) selected @endif>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>{{ trans('admin.Created by') }}</label>
                            <select class="form-control select2 user_id" name="created_by" id="created_by" style="width: 100%;">
                                <option value="" selected >{{ trans('admin.Select') }}</option>
                                @foreach ($usersCollection as $id => $name)
                                    <option value="{{ $id }}" @if (Request()->created_by == $id) selected @endif>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>{{ trans('admin.payment_status') }}</label>
                            <select class="form-control select2 payment_status" name="payment_status" id="payment_status" style="width: 100%;">
                                <option value="" selected >{{ trans('admin.Select') }}</option>
                                <option value="due" @selected(Request()->payment_status == 'due')>{{ trans('admin.Due') }}</option>
                                <option value="final" @selected(Request()->payment_status == 'final')>{{ trans('admin.Final') }}</option>
                                <option value="partial" @selected(Request()->payment_status == 'partial')>{{ trans('admin.Partial') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>{{ trans('admin.date_from') }}</label>
                            <input type="date" class="form-control" name="date_from" id="date_from" value="{{ Request()->date_from }}">
                        </div>
                    </div>
                    <div class="col-lg-4">
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