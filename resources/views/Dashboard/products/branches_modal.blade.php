@if($product->Branches->isNotEmpty())
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>{{ trans('admin.BranchName') }}</th>
                <th>{{ trans('admin.QtyAvailable') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($product->Branches as $branch)
                <tr>
                    <td>{{ $branch->name }}</td>
                    <td>{{ $product->getStockByBranch($branch->id) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>{{ trans('admin.no_branches_found') }}</p>
@endif