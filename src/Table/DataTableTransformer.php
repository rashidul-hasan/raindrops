<?php
/**
 * Created by PhpStorm.
 * User: rashed
 * Date: 21-Apr-17
 * Time: 2:57 PM
 */

namespace Rashidul\RainDrops\Table;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;
use Rashidul\RainDrops\Model\ModelHelper;

class DataTableTransformer extends TransformerAbstract
{
    /**
     * @param Model $model
     * @return array
     */
    protected $model;

    public function transform(Model $model)
    {

        $this->model = $model;

        $data = [];

        //$data['select'] = $model->id;

        $fields = ModelHelper::getIndexFields( $model );

        foreach ($fields as $field => $value)
        {
            // 1. first decide how to show the data
            // function, determines data type by examining 'show' element
            $dataType = $this->getDataType($value); // img/doc/string/noshow/relation/exact/enum

            $fieldName = '';
            $relatedColumnName = '';

            // setup the key name of the data array
            if ($dataType == 'relation'){
                $relationOptions = $value['show'];
                $fieldName = $relationOptions[0];
                $relatedColumnName = $relationOptions[1];
            } else {
                $fieldName = $field;
            }

            // TODO.
            // data type should be predicted from field type and
            // use as default if there's no `show` attribute specified
            // explicitly

            switch ($dataType){

                case 'string':
                    $data[$fieldName] = $this->generateStringRow($field, $value);
                    break;

                case 'details_link':
                    $data[$fieldName] = $this->generateDetailsLinkRow($field, $value);
                    break;

                case 'url':
                    $data[$fieldName] = $this->generateUrlRow($field, $value);
                    break;

                case 'tel':
                    $data[$fieldName] = $this->generatePhoneNumberRow($field, $value);
                    break;

                case 'mailto':
                    $data[$fieldName] = $this->generateMailtoRow($field, $value);
                    break;

                case 'exact':
                    $data[$fieldName] = $this->generateExactStringRow($field, $value);
                    break;

                case 'enum':
                    $data[$fieldName] = $this->generateEnumRow($field, $value);
                    break;

                case 'img':
                    $data[$fieldName] = $this->generateImageRow($field, $value);
                    break;

                case 'doc':
                    $data[$fieldName] = $this->generateDocRow($field, $value);
                    break;

                case 'time':
                    $data[$fieldName] = $this->generateTimeRow($field, $value);
                    break;

                case 'datetime':
                    $data[$fieldName] = $this->generateDateTimeRow($field, $value);
                    break;

                case 'html':
                    $data[$fieldName] = $this->generateHtmlRow($field, $value);
                    break;

                case 'relation':
                    $data[$fieldName] = [$relatedColumnName => $this->generateRelationRow($field, $value)];
                    break; // array, first element is name of the relation, 2nd is the column name to

                case 'relation-details':
                    $data[$fieldName] = $this->generateRelationDetailsRow($field, $value);
                    break;

                // display from the related model
            }

        }

        // now add the actions column
        $data['action'] = ModelHelper::getActionLinks($model);

        return $data;
    }


    private function generateDetailsLinkRow($field, $value, $index = false)
    {
        $row = '<tr><td>%s:</td><td> %s</td></tr>';
        $row_data = '';
        if ($this->model->{$field}){
            $field_value = $this->model->{$field};
            $row_data = "<a href='{$this->model->getShowUrl()}'>{$field_value}</a>";
        }

        if ($index){
            return sprintf('<td>%s</td>', $row_data);
        }

        return $row_data;
    }

    private function generateMailtoRow($field, $value, $index = false)
    {
        $row = '<tr><td>%s:</td><td> %s</td></tr>';
        $row_data = '';
        if ($this->model->{$field}){
            $field_value = $this->model->{$field};
            $row_data = "<a href='mailto:{$field_value}'>{$field_value}</a>";
        }

        if ($index){
            return sprintf('<td>%s</td>', $row_data);
        }

        return $row_data;
    }

    private function generateUrlRow($field, $value, $index = false)
    {
        $row = '<tr><td>%s:</td><td> %s</td></tr>';
        $row_data = '';
        if ($this->model->{$field}){
            $field_value = $this->model->{$field};
            $row_data = "<a href='{$field_value}' target='_blank'>{$field_value}</a>";
        }

        if ($index){
            return sprintf('<td>%s</td>', $row_data);
        }

        return $row_data;
    }

    private function generatePhoneNumberRow($field, $value, $index = false)
    {
        $row = '<tr><td>%s:</td><td> %s</td></tr>';
        $row_data = '';
        if ($this->model->{$field}){
            $field_value = $this->model->{$field};
            $row_data = "<a href='tel://{$field_value}'>{$field_value}</a>";
        }

        if ($index){
            return sprintf('<td>%s</td>', $row_data);
        }

        return $row_data;
    }

    private function generateHtmlRow($field, $value, $index = false)
    {
        $row = '<tr><td>%s:</td><td> %s</td></tr>';
        $row_data = '';
        if ($this->model->{$field}){
            $field_value = $this->model->{$field};
            $row_data = "<iframe srcdoc='{$field_value}'></iframe>";
            //$row_data = "<iframe srcdoc='{$field_value}' width='500' height='500'></iframe>";
            //$row_data = $enumOptionsArray[$option];
        }

        if ($index){
            return sprintf('<td>%s</td>', $row_data);
        }

        return $row_data;
    }

    /**
     * We are storing time as datetime data type in mysql
     * here we show a mysql datetime formatted as 12h time
     * @param $field
     * @param $value
     * @param bool $index
     * @return string
     */
    private function generateTimeRow($field, $value, $index = false)
    {
        $row = '<tr><td>%s:</td><td> %s</td></tr>';
        $row_data = '';
        if ($this->model->{$field}){
            $row_data = Carbon::createFromFormat('Y-m-d H:i:s', $this->model->{$field})->format('g:i A');
            //$row_data = $enumOptionsArray[$option];
        }

        if ($index){
            return sprintf('<td>%s</td>', $row_data);
        }

        return $row_data;
    }

    /**
     * We are storing time as datetime data type in mysql
     * here we show a mysql datetime formatted as human readable
     * date time
     * @param $field
     * @param $value
     * @param bool $index
     * @return string
     */
    private function generateDateTimeRow($field, $value, $index = false)
    {
        $row = '<tr><td>%s:</td><td> %s</td></tr>';
        $row_data = '';
        if ($this->model->{$field}){
            $row_data = Carbon::createFromFormat('Y-m-d H:i:s', $this->model->{$field})->format('l jS \of F Y \a\t h:i:a');
            //$row_data = $enumOptionsArray[$option];
        }

        if ($index){
            return sprintf('<td>%s</td>', $row_data);
        }

        return $row_data;
    }

    /**
     * For enum fields, show the title string for the option
     * @param $field
     * @param $value
     * @return string
     */
    private function generateEnumRow($field, $value, $index = false)
    {
        $row = '<tr><td>%s:</td><td> %s</td></tr>';
        $row_data = '';
        $enumOptionsArray = $value['options'];
        if ($this->model->{$field}){
            $option = $this->model->getOriginal($field);
//            $option = $this->model->{$field};
            $row_data = $enumOptionsArray[$option];
        }

        if ($index){
            return sprintf('<td>%s</td>', $row_data);
        }

        return $row_data;
    }

    /**
     * Generate row for exact value in the db
     * @param $field
     * @param $value
     * @return string
     */
    private function generateExactStringRow($field, $value, $index = false)
    {
        $row = '<tr><td>%s:</td><td> %s</td></tr>';
        $row_data = '';
        if ($this->model->{$field}){
            $row_data = $this->model->{$field};
        }

        if ($index){
            return sprintf('<td>%s</td>', $row_data);
        }

        return $row_data;
    }

    /**
     * Generate row for a column which holds another model's id
     * Show details of the model on expandable table row
     * @param $field
     * @param $value
     * @param bool $index
     * @return string
     * @internal param $item
     */
    private function generateRelationDetailsRow($field, $value, $index = false)
    {

        // TODO.
        // 1. check to see if relationship is defined in the model
        $row = '<tr><td>%s: <button class="btn btn-default btn-sm row-toggle">
                <i class="fa fa-plus-circle"></i></button></td><td> %s</td></tr>';

        $row_details = '<tr class="hidden"><td colspan="2"><table>%s</table></td>';
        $row_data = '';
        $showArray = $value['show'];

        if ($this->model->{$field}){
            $relatedModel = $this->model->{$showArray[0]};
            // TODO.
            // 1. check if returned related model is actually a subclass of eloquent
            $row_data = $relatedModel->{$showArray[1]};

            $rowDetailsTable = $relatedModel->detailsTable();
            $row_details = sprintf($row_details, $rowDetailsTable);
            $row_data .= $row_details;
        }

        if ($index){
            return sprintf('<td>%s</td>', $row_data);
        }

        return $row_data;

    }

    /**
     * Generate row for a column which holds another model's id
     * @param $field
     * @param $value
     * @param bool $index
     * @return string
     * @internal param $item
     */
    private function generateRelationRow($field, $value, $index = false)
    {

        // TODO
        // 1. check to see if relationship is defined in the model
        $row = '<tr><td>%s:</td><td> %s</td></tr>';
        $row_data = '';
        $showArray = $value['show'];

        if ($this->model->{$field}){
            $relatedModel = $this->model->{$showArray[0]};
            // TODO.
            // 1. check if returned related model is actually a subclass of eloquent
            // 2. handle relationship more than 2 levels
            if ($relatedModel){

                array_shift($showArray); // remove the first element of the array
                foreach ($showArray as $item) {
                    $row_data .= $relatedModel->{$item} . ' ';
                }

            }

        }

        if ($index){
            return sprintf('<td>%s</td>', $row_data);
        }

        return $row_data;

//        $row = '<tr><th>%s:</th><td> %s</td></tr>';
//        $row_data = '';
//        $relationName = $value['show'][0];
//
//        // check the second element, if its an array then show only the fields from the related model
//        // which are present in the array, other
//        $showOption = is_array($value['show'][1]) ? 'array' : $value['show'][1];
//
//        if ($this->model->{$field} && $this->model->has($relationName)){
//
//            $relatedModel = $this->model->{$relationName};
//            $row_data = $relatedModel->{$showOption};
//
//            // generate single row or another details table based on the
//            // $showOption value
//
////            switch($showOption){
////
////                // case true, show all
////                case true:
////                    $row_data = $relatedModel->detailsTable();
////                    break;
////
////                // case when its an array, selectively show
////
////                // default is when its an column name
////                default:
////                    $row_data = $relatedModel->{$showOption};
////
////            }
//
//            // TODO
//            // 1. check if returned related model is actually a subclass of eloquent
//            // 2. handle relationship more than 2 levels
//
//        }
//
//        if ($index){
//            return sprintf('<td>%s</td>', $row_data);
//        }
//
//        return sprintf($row, $value['label'], $row_data);
    }

    /**
     * Generate row with document download link, .doc/.pdf etc.
     *
     * @param $field
     * @param $value
     * @return string
     * @internal param $item
     */
    private function generateDocRow($field, $value, $index = false)
    {
        // TODO
        // document preview on a modal system
        $url = url($this->model->paths["$field"] . '/' . $this->model->{$field});

        $row = '<tr>
                    <td>%s:</td>
                    <td><a href="%s" download>Download</a>
                    </td>
                </tr>';

        return sprintf($row, $value['label'], $url);
    }

    /**
     * for image fields, wrap it inside an image tag
     * @param $field
     * @param $value
     * @return string
     */
    private function generateImageRow($field, $value, $index = false)
    {
        $row = '<tr><td>%s:</td><td>
                     <a href="%s" data-lightbox="lightbox-1" data-title="%s" class="widget-user-image">
                        <div class="img-thumb-wrap clearfix">
                        %s
                        </div>
                     </a></td></tr>';

        $url = '';

        $path = $this->model->paths["$field"];
        if ($this->model->{$field}){
            $filename = $this->model->{$field};
            $url = url($path . '/' . $filename);
            $img = sprintf('<img class="img-thumb img-responsive ui small rounded image" src="%s" alt="%s">', $url, $value['label']);
        } else {
            $img = '';
        }

        return $img;

    }

    /**
     * Generate a string row, uppercasing the value
     * @param $field
     * @param $value
     * @return string
     */
    private function generateStringRow($field, $value, $index = false)
    {
        $row = '<tr><td>%s:</td><td> %s</td></tr>';
        $row_data = '';
        if ($this->model->{$field}){
            $row_data = ucwords( $this->model->{$field} );
        }

        if ($index){
            return sprintf('<td>%s</td>', $row_data);
        }

        return $row_data;
    }


    /**
     * Get the type of data this row contains
     * @param $value
     * @return int| string
     */
    private function getDataType($value)
    {
        $type = 'exact';

        if ( isset($value['show'] ) )
        {
            if (is_array($value['show'])){
                $type = count($value['show']) === 3 && $value['show'][2] === true ? 'relation-details' : 'relation';
            } else {
                $type = $value['show'];
            }

        } else {
            $type = $this->defaultDataTypeForField($value);
        }

        return $type;

    }

    /**
     * Predict default table row type if there's no `show`
     * attribute specified explicitly
     * @param $value
     * @return string
     */
    private function defaultDataTypeForField($value)
    {
        if (isset($value['type'])){

            switch($value['type']){
                case 'select' :
                    return 'enum';

                default:
                    return 'exact';
            }
        }

        return 'exact';
    }



}