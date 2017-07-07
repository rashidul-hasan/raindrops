<?php
/**
 * Created by PhpStorm.
 * User: rashidul
 * Date: 07-Jul-17
 * Time: 9:52 PM
 */

namespace Rashidul\RainDrops\Crud;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Rashidul\RainDrops\Facades\DataTable;
use Rashidul\RainDrops\Facades\DetailsTable;
use Rashidul\RainDrops\Form\Builder;
use Rashidul\RainDrops\Table\DataTableBuilder;
use Rashidul\RainDrops\Table\DetailsTableBuilder;

class ResponseBuilder
{

    /**
     * Send json or view response base on the request type
     * @param $request
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function send($request, $data)
    {
        // if request is ajax, send json
        if($request->ajax()){

            // first, render any form or table object
            if ( isset($data['form']) && $data['form'] instanceof Builder )
            {
                $data['form'] = $data['form']->render();
            }

            if ( isset($data['table']) )
            {
                if ($data['table'] instanceof DetailsTableBuilder || $data['table'] instanceof DataTableBuilder)
                {
                    $data['table'] = $data['table']->render();
                }
            }

            // if $data contains `item` and thats an instance of Eloquent, cast it to array
            if ( isset($data['item']) && $data['item'] instanceof Model )
            {
                $data['item'] = $data['item']->toArray();
            }

            return response()->json([
                'success' => $data['success'],
                'data' => $data
            ], 200);

        }

        // otherwise send view response

        // if $data has `success` & `message` flash the message to session
        // according to the value of `success` <true or false>
        if ( isset($data['success']) && isset($data['message']) )
        {
            $status = $data['success'] ? 'success' : 'error';
            Session::flash($status, $data['message']);
        }

        if (isset($data['view']))
        {
            return view($data['view'], $data);
        }

        if (isset($data['redirect']))
        {
            return redirect($data['redirect']);
        }

        // finally, if no response is defined, just redirect to previous url
        return redirect()->back();


    }
}