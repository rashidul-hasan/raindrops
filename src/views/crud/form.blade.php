@extends(config('raindrops.crud.layout'))

@section('crud')

    <div class="row">
        <div class="col-md-12">
            {!! $form->render() !!}
        </div>
    </div>

@stop

@if(isset($include_view))
    @includeIf($include_view)
@endif
