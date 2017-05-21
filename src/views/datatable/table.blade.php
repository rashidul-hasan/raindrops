<table id="{!! $id !!}" class="{!! $class !!}">

    <thead>
        {!! $headers !!}
    </thead>

    <tbody></tbody>

</table>

@if (!$noScript)
    @include(Config::get('raindrops.datatable.table.script_view'), array('id' => $id, 'options' => $options))
@endif
