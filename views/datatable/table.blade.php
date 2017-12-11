<table id="{!! $id !!}" class="{!! $class !!}">

    <thead>
        {!! $headers !!}
    </thead>

    <tbody></tbody>

</table>

@if ($printScript)
    @include(Config::get('raindrops.table.index.script_view'), array('id' => $id, 'options' => $options))
@endif
