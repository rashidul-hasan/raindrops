@extends(config('raindrops.crud.layout'))

@section('raindrops-header')
    @include('raindrops::styles.styles')
@stop

@section('raindrops-action')
    {!! $buttons !!}
@stop

@section('raindrops')

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

@isset($include)
    @if(is_array($include))
        @foreach($include as $view)
            @includeIf($view)
        @endforeach
    @else
        @includeIf($include)
    @endif
@endisset

