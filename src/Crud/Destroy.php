<?php
/**
 * Created by PhpStorm.
 * User: rashidul
 * Date: 14-Oct-17
 * Time: 8:19 PM
 */

namespace Rashidul\RainDrops\Crud;


use Illuminate\Database\QueryException;

trait Destroy
{

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     * @internal param Request $request
     */
    public function destroy($id)
    {
        // get item obj by id
        try
        {
            $this->model = $this->model->findOrFail($id);
        }
        catch (\Exception $e)
        {
            $data['success'] = false;
            $data['message'] = $e->getMessage();
            return $this->responseBuilder->send($this->request, $data);
        }

        // let the user do something before destroying the item
        if (method_exists($this, 'deleting'))
        {
            $this->deleting();
        }

        try{
            if ($this->model->delete()){
                $this->viewData['success'] = true;
                $this->viewData['message'] = $this->model->getEntityName() . ' Deleted!';
            } else {
                $this->viewData['success'] = false;
                $this->viewData['message'] = 'Something went wrong';
            }
        } catch (QueryException $e){
            $this->viewData['message'] = $e->getMessage();
            $this->viewData['success'] = false;
        }

        return $this->responseBuilder->send($this->request, $this->viewData);

    }

}