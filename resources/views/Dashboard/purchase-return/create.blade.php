<form method="post" action="{{route('dashboard.purchases.purchase-return.store', $transaction->id)}}">
    @csrf
    <div class="row">
        <div class="col-lg-12">
            <table id="example1" class="table table-bordered table-striped responsive">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ trans('admin.name') }}</th>
                    <th>{{ trans('admin.quantity') }}</th>
                    <th>{{ trans('admin.return quantity') }}</th>
                    <th>{{ trans('admin.unit_price') }}</th>
                    <th>{{ trans('admin.total') }}</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($transaction->TransactionPurchaseLines as $line)
                        <input type="hidden" name="transaction_id" value="{{$transaction->id}}">
                        <input type="hidden" name="products_return[{{$line->id}}][transactions_purchase_line_id]" value="{{$line->id}}">
                        <input type="hidden" name="products_return[{{$line->id}}][unit_id]" value="{{$line->unit_id}}">
                        <input type="hidden" name="products_return[{{$line->id}}][product_id]" value="{{$line->product_id}}">
                        <tr>
                            <td>{{$line->id}}</td>
                            <td>{{$line->Product?->name}}</td>
                            <td>{{$line->quantity}} {{$line->Unit?->actual_name}}</td>
                            <td>
                                <input class="form-control change_return_quantity change_return_quantity_{{$line->id}}"  type="number"
                                    name="products_return[{{$line->id}}][return_quantity]" 
                                    value="0" data-product_row_id="{{$line->id}}">
                            </td>
                            <td><input name="products_return[{{$line->id}}][unit_price]" class="form-control product_row_unit_price_{{$line->id}}" readonly value="{{$line->unit_price}}"></td>
                            <td>
                                <input class="form-control product_row_total return_line_total_{{$line->id}}" readonly value="0">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-lg-12">
            <h4>{{ trans('admin.total') }} : <span class="return_total">0</span></h4>
        </div>
    </div>

    <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ trans('admin.Add') }}</button>
    </div>
</form>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('theme/dashboard/plugins/jquery-ui/jquery-ui.min.js')}}"></script>

<script>
    
    $(document).on("change",".change_return_quantity", function(){	
        var product_row_id = $(this).attr('data-product_row_id');
        var quantity = $(this).val();
        var price = $('.product_row_unit_price_' + product_row_id).val();
        $('.return_line_total_' + product_row_id).val(quantity * price);
        HandelTotal();
    })
    function HandelTotal(){
        var sum = 0;     
        $('.product_row_total:visible').each(function() {
            sum += parseFloat($(this).val());
        });
        $('.return_total').html(sum);
    }
</script>