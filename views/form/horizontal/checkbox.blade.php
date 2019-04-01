<div class="form-group {{ $error_class ?? '' }}">
    <div class="col-sm-offset-3 col-sm-6">
        <div class="checkbox">
            <label for="{{ $id ?? '' }}">
                {!! $field !!} {!! $label ?? '' !!}
                <span class="help-block">{{ $error_text ?? '' }}</span>
            </label>
        </div>
    </div>
</div>