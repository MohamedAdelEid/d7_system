<div class="card-body">
    <div class="row">
        <div class="col-lg-6">
            @include('components.form.input', [
                'class' => 'form-control',
                'attribute' => 'required',
                'name' => "username",
                'value' => isset($data) ? $data->username : old('username') ,
                'label' => trans('admin.Username'),
            ])
        </div>

        <div class="col-lg-6">
            @include('components.form.input', [
                'class' => 'form-control',
                'name' => "name",
                'label' => trans('admin.Name'),
                'value' => isset($data) ? $data->name : old('name') ,
                'attribute' => 'required',
            ])
        </div>

        <div class="col-lg-6">
            @include('components.form.input', [
                'type' => 'password',
                'class' => 'form-control',
                'name' => "password",
                'label' => trans('admin.Password'),
                'value' => old('password'),
            ])
        </div>
        <div class="col-lg-6">
            @include('components.form.select', [
                'collection' => $branches,
                'index' => 'id',
                'select' => isset($data) ? $data->branch_id : old('branch_id'), // استخدم branch_id مباشرةً
                'name' => 'branch_id',
                'label' => trans('admin.main_branch'),
                'class' => 'form-control select2',
                'firstDisabled' => true,
                'attribute' => 'required',
            ])
            
        </div>

        <div class="col-lg-6">
            <div class="">
                @php
                $branch_ids = [];
                if(isset($data)){
                    $branch_ids = $data->Branches()->pluck('branch_id')->toArray();
                }
            @endphp
            <x-form.multiple-select class="form-control select2" id=""
                :collection="$branches" :selectArr="$branch_ids" index="id"
                name="branch_ids[]" label="{{ trans('admin.branches') }}" display="name"  attribute="required"/>
            
            </div>
        </div>

        <div class="col-lg-6">
            @include('components.form.select', [
                'collection' => $roles,
                'index' => 'id',
                'select' => isset($data) ? $data->getRoleId() : old('role_id'),
                'name' => 'role_id',
                'label' => trans('admin.Roles'),
                'class' => 'form-control select2',
                'firstDisabled' => true,
                'attribute' => 'required',
            ])
        </div>
    </div>
</div>