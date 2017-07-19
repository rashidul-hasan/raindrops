<script>
    // when the page loads, remove all options from
    // all the select inpputs which are dependent on another
    /*$(document).ready(function () {
        $('[data-depends^=]').find('option').remove();
    });*/

    $('select').on('change', function(){

        // first check if any other fields depends on this dropdown,
        // if not, just
        var $this = $(this);
        var selector = '[data-depends=' + $(this).attr('name') + ']';
        var element = $(selector);

        // disable the input for now
        element.prop('disabled', true);

        var elementName = element.attr('name');
        //console.log(elementName);
        if ( !element.length )
        {
            console.log('not found kutta');
            return;
        }
        // generate <option> based on this elements selected value
        var selectedId = $(this).find(":selected").val();

        console.log(selectedId);

        // get data objects
        var dataObject = raindrops[elementName].data;
        var indexColumnName = raindrops[elementName].indexColumn;
        //console.log(typeof dataObject);
        // get only those objects

        // remove all options from dependent select
        element.find("option").remove();

        $.each(dataObject, function(key, obj){

//                console.log(typeof obj.county_id);
            console.log(obj[$this.attr('name')]);
            if (selectedId == obj[$this.attr('name')])
            {
//                    console.log('paisi');
                element.append(new Option(obj[indexColumnName], obj.id));
            }
        });

        // im done lets enable the elemnt again
        element.prop('disabled', false);

    });
</script>