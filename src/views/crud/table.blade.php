@extends(config('raindrops.crud.layout'))

@section('raindrops-header')
    @include('raindrops::styles.styles')
@stop

@section('raindrops')

    <div class="row" style="margin: 15px 0;">
        <div class="col-md-4">
            <h2 style="margin-top: 10px;">{{$title or ''}}</h2>
        </div>
        <div class="col-md-8">
            <div class="pull-right " style="margin-top: 10px;">
                @if(isset($buttons))
                    @foreach($buttons as $button)
                        <a href="{{ $button['url'] }}" class="{{ $button['class'] }}">{{ $button['text'] }}</a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            {!! $table->render() !!}
        </div>
    </div>

@stop

@section('raindrops-footer')
    @include('raindrops::scripts.php-to-js')
    @include('raindrops::scripts.dropdown')
    @include('raindrops::scripts.delete')
@stop

@if(isset($include_view))
    @includeIf($include_view)
@endif

