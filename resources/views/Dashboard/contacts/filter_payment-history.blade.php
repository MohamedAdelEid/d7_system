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
            <div class="col-lg-6">
     
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>{{ trans('admin.date_from') }}</label>
                            <input type="date" class="form-control" name="date_from" id="date_from" value="{{ Request()->date_from }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
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