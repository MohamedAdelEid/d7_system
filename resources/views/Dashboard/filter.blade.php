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
                    ?>
                    <form action="" id="fromFilter">
                        <div class="row">

                            <div class="col-lg-4">

                                <div class="form-group">
                                    <label>{{ trans('admin.branch') }}</label>
                                    <select class="form-control select2 branch_id" name="branch_id" id="branch_id"
                                        style="width: 100%;">
                                        <option value="" selected>{{ trans('admin.Select') }}</option>
                                        @foreach ($branchescollection as $id => $name)
                                            <option value="{{ $id }}"
                                                @if (Request()->branch_id == $id) selected @endif>{{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>{{ trans('admin.date_from') }}</label>
                                    <input type="date" class="form-control" name="date_from" id="date_from"
                                        value="{{ Request()->date_from }}">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>{{ trans('admin.date_to') }}</label>
                                    <input type="date" class="form-control" name="date_to" id="date_to"
                                        value="{{ Request()->date_to }}">
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-primary">{{ __("admin.filter") }}</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
    </div>
</section>
