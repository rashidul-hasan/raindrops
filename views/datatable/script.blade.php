<script type="text/javascript">
    jQuery(document).ready(function(){

        {!! $id !!} = jQuery('#{!! $id !!}').DataTable(
            {!! json_encode($options) !!}
        );

    });
</script>