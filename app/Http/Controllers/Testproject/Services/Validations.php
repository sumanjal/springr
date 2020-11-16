<?php

namespace App\Http\Controllers\Testproject\Services;

use App\Http\Controllers\Controller;
use App\Http\Testdetails;
use Illuminate\Http\Request;
use DB;
use Validator;
use Illuminate\Validation\Rule;

class Validations extends Controller
{
    const NO_ERROR = 'no error';
    const MAX255 = '|max:255';

    /**
     * [validations description]
     *
     * @param  Request $request [description]
     * @param  string  $id      [description]
     *
     * @return [type]           [description]
     */

    public function userValidations(Request $request, $id = null)
    {
        if ($id) {
            //edit
            $code = [
              'required',
              Rule::unique('usermanagement')->ignore($id, 'id')
            ];
        } else {
            //create
            $code =  'required|unique:' . ('usermanagement') . (self::MAX255);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required:' . ('usermanagement') . (self::MAX255),
            'code' => $code,
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $result = $this->constructAPIresponse(201, '', $errors);
        } else {
            $result = $this->constructAPIresponse(200, self::NO_ERROR, '');
        }
        return $result;
    }

    /**
     * [constructAPIresponse description]
     *
     * @param  [type] $status [description]
     * @param  [type] $data   [description]
     * @param  [type] $error  [description]
     *
     * @return [type]         [description]
     */

    public function constructAPIresponse($status, $data, $error)
    {
        $result['status'] = $status;
        $result['data'] = $data;
        $result['error'] = $error;

        return $result;
    }
}
