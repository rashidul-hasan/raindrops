@extends(config('raindrops.crud.layout'))

@section('crud')
    <div class="row">
        <div class="col-md-12">
            {!! $table->render() !!}
        </div>
    </div>
@stop

