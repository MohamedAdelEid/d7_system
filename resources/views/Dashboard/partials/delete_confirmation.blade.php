<div class="modal-body">
  <p>{{ trans('admin.Are you sure about this procedure') }}</p>
</div>
<div class="modal-footer justify-content-between">
  <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('admin.Cancel') }}</button>
  <a class="btn btn-default" href="{{$url}}">{{ trans('admin.Delete') }}</a>
</div>