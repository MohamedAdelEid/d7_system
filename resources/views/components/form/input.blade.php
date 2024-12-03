@php
    if(!isset($type))
        $type = "text";

    if(!isset($value))
        $value = null;

    if(!isset($attribute))
        $attribute = '';

    if(!isset($no_laple))
        $no_laple = false;
   
@endphp

{{-- <div class="form-group">
    @if ($no_laple == false)
        <label for="username">{{$label}}</label>
    @endif
    <input class="{{$class}}" value="{{$value}}" type="{{$type}}" name="{{$name}}" {{$attribute}}>
    @error($name)
        <span style="color: red; margin: 20px;">
            {{ $message }}
        </span>
    @enderror
</div> --}}
<div class="form-group">
    @if ($no_laple == false)
        <label for="username">{!! $label !!}</label>
    @endif
    <input class="{{$class}}"  value="{{$value}}" type="{{$type}}" name="{{$name}}" {{$attribute}}>
    @error($name)
        <span style="color: red; margin: 20px;">
            {{ $message }}
        </span>
    @enderror
</div>

{{-- <div class="col-lg-6">
    @include('components.form.input', [
        'type' => 'text',
        'name' => "name",
        'label' => 'label',
        'value' => 20,
        'attribute' => 'required',
        'class' => 'form-control',
    ])
</div> --}}