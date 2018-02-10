<div class="form-group {{ $error_class or '' }}">
    <label for="{{ $id or '' }}" class="col-sm-3 control-label">{!! $label or '' !!}</label>
    <div class="col-sm-6">
        {!! $field !!}
        <span class="help-block">{{ $error_text or '' }}</span>
    </div>
</div>