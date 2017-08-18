<script>

    (function () {

        // for edit form, do the dropdown generation stuff once the page is loaded
        var allSelectElements = $('select');
        $.each(allSelectElements, function (index, item) {
            handleDependentDropdown($(this));
        });

        // on change event
        allSelectElements.on('change', function(e){

            // https://stackoverflow.com/questions/39152877/consider-marking-event-handler-as-passive-to-make-the-page-more-responsive
            e.preventDefault();

            handleDependentDropdown($(this));

        })
        // for dropdowns with only one option, change event may not fire,
        // so we need to do that manually
        .on('click', function (e) {
            e.preventDefault();
            if($(this).children().length == 2) {
                $(this).change();
            }
        });

        // populate the dependent dropdown with appropriate options
        // for a given element
        function handleDependentDropdown(parentElement) {

            var childElementSelector = '[data-parent=' + parentElement.attr('name') + ']';
            var childElement = $(childElementSelector);

            // first check if any other fields depends on this dropdown,
            // if not, just return
            if ( !childElement.length )
            {
                return;
            }

            // disable the input for now
            childElement.prop('disabled', true);

            var childElementName = childElement.attr('name');

            // generate <option> based on this childElements selected value
            var selectedId = parentElement.find(":selected").val();

            // get data objects
            var dataObject = raindrops[childElementName].data;
            var indexColumnName = raindrops[childElementName].indexColumn;
            var childSelectedId = raindrops[childElementName].selectedId;
            var keyName = raindrops[childElementName].keyName;

            // remove all options from dependent select
            childElement.find("option").remove();

            // add the default placeholder option
            var placeholderOption = new Option('--Select One--', '', true, true);
            placeholderOption.disabled = true;
            childElement.append(placeholderOption);

            $.each(dataObject, function(key, obj){

                if (selectedId == obj[parentElement.attr('name')])
                {
                    var isSelected = childSelectedId == obj[keyName];
                    childElement.append(new Option(obj[indexColumnName], obj[keyName], true, isSelected));
                }
            });

            // im done lets enable the elemnt again
            childElement.prop('disabled', false);

            // now, we check if there's any other element which is dependent
            // on this child element we just created, if it is, then we need to
            // populate that too. just call this function recursively
            if (childElementExists(childElement)){

                handleDependentDropdown(childElement);

            }

        }

        // check if there's any child dependent element exists
        // for a given element
        function childElementExists(parentElement) {

            var childElementSelector = '[data-parent=' + parentElement.attr('name') + ']';
            var childElement = $(childElementSelector);

            return !!childElement.length;
        }

    })();


</script>