<div class="form-group {{ $error_class or '' }}">
    <div class="col-sm-offset-3 col-sm-6">
        <div class="checkbox">
            <label for="{{ $id or '' }}">
                {!! $field !!} {!! $label or '' !!}
                <span class="help-block">{{ $error_text or '' }}</span>
            </label>
        </div>
    </div>
</div>