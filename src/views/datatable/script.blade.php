<script type="text/javascript">
    jQuery(document).ready(function(){
        // dynamic table
        oTable = jQuery('#{!! $id !!}').DataTable(
                {!! json_encode($options) !!}
        );
    });
</script>