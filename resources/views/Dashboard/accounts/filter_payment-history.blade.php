@php
    $usersCollection = App\Models\User::pluck('name', 'id');
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
                <div class="row">
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
            </div>
        </div>
        <!-- /.card-body -->
      </div>
    </div>
  </section>