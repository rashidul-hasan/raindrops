<table id="{!! $id !!}" class="{!! $class !!}">

    <thead>
        {!! $headers !!}
    </thead>

    <tbody></tbody>

</table>

@if (!$noScript)
    @include(Config::get('raindrops.table.index.script_view'), array('id' => $id, 'options' => $options))
@endif
