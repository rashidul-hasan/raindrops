@extends(config('raindrops.crud.layout'))
<style>
    .tc{
        text-align: center;
        clear: both;
    }
    button[type=submit] i{
        margin-left: 5px;
    }
</style>
@section('raindrops')

    <div class="row" style="margin: 15px 0;">
        <div class="pull-right">
            @if(isset($buttons))
                @foreach($buttons as $button)
                    <a href="{{ $button['url'] }}" class="{{ $button['class'] }}">{{ $button['text'] }}</a>
                @endforeach
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            {!! $form->render() !!}
        </div>
    </div>

@stop

@if(isset($include_view))
    @includeIf($include_view)
@endif
