@extends(config('raindrops.crud.layout'))

@section('crud')

    <div class="row">
        <div class="col-md-12">
            {!! $form->render() !!}
        </div>
    </div>

@stop

@if(isset($view_path) && View::exists($view_path))
    @include($view_path)
@endif