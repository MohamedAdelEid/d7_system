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
        {{-- <div class="card-body">
          <div class="col-lg-6">
            <label>{{ trans('admin.branch') }}</label>
            <select name="branch_id" class="form-control select2" id="branch_id">
              @foreach ($branches as $branch)
                  <option value="{{$branch->id}}">{{$branch->name}}</option>
              @endforeach
            </select>
          </div>
        </div> --}}
        <!-- /.card-body -->
      </div>
    </div>
  </section>