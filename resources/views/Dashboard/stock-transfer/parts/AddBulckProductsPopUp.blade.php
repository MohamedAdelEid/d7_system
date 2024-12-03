<div class="modal fade" id="getByBrand" tabindex="-1" role="dialog" aria-labelledby="getByBrandLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="getByBrandLabel">إضافة منتجات</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
              <div class="row">
                  <div class="col-lg-6">
                      @include('components.form.select', [
                          'collection' => $brands,
                          'index' => 'id',
                          'select' => isset($data) ? $data->brand_id : old('brand_id'),
                          'name' => 'brand_id',
                          'label' => trans('admin.brand'),
                          'class' => 'form-control select2 brand_AddBulckProducts',
                          'id' => 'brand_id',
                          'display' => 'name',
                          'firstDisabled' => true,
                          'attribute' => '',
                      ])
                  </div>

                  <div class="col-lg-12">
                      <table class="table table-bordered table-striped">
                          <thead>
                              <tr>
                                  <th>{{ trans('admin.name') }}</th>
                                  <th style="min-width: 120px;">{{ trans('admin.unit') }}</th>
                                  <th>{{ trans('admin.quantity') }}</th>
                                  <th>{{ trans('admin.available quantity') }}</th>
                                  <th>{{ trans('admin.action') }}</th>
                              </tr>
                          </thead>
                          <tbody class="sell_table_AddBulckProducts">
                              <!-- Products will be loaded here -->
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
              <button type="button" class="btn btn-primary">إضافة</button>
          </div>
      </div>
  </div>
</div>