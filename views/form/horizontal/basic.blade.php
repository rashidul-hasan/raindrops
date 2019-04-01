<div class="form-group {{ $error_class ?? '' }}">
    <label for="{{ $id ?? '' }}" class="col-sm-3 control-label">{!! $label ?? '' !!}</label>
    <div class="col-sm-6">
        {!! $field !!}
        <span class="help-block">{{ $error_text ?? '' }}</span>
    </div>
</div>