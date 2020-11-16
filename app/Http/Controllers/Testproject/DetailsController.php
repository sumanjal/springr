<?php

namespace App\Http\Controllers;

use App\cr;
use Illuminate\Http\Request;
use sApp\Http\Controllers\Testproject\Services\Validations;
use Exception;
use DB;

class DetailsController extends Controller
{

    public function ____construct() {
        $this->validation = new Validations();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       // 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validate = $this->validation->userValidations($request);
            if ($validate[STATUS] === 200) {
                DB::beginTransaction();
                $data = $this->constructArry($request);
                DB::table('usermanagement')->insert($data);
                DB::commit();
                $response = $this->validation->constructAPIresponse(200, "Success", '');
            } else {
                $response =$this->validation->constructAPIresponse(201, "", $validate['error']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            $response = $this->validation->constructAPIresponse(500, "", [LINE_NUMBER => $e->getLine(),ERROR_MESSAGE => $e->getMessage(), FILE_NAME => $e->getFile()]);
        }
        return response()->json($response);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\cr  $cr
     * @return \Illuminate\Http\Response
     */
    public function show(cr $cr)
    {
        try{
            DB::beginTransaction();
            $data= json_decode(json_encode(DB::table('usermanagement')
                    ->select('*')
                    ->get()
                    ->toArray()),true);
            foreach($data as $key=>$value) {
                if ($value['stillworking'] == true){
                    // $total_experience = date('Y-m-d', $value['doj']);
                    $diff = abs(strtotime(date('Y-m-d')) - strtotime( date('Y-m-d', $value['doj'])));
                    $years = floor($diff / (365*60*60*24));
                    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                    $total_experience = $years .'years'. $months.'months';
                    $data['total_experience'] =  $total_experience;
                }else{
                    $diff = abs(strtotime(date('Y-m-d', $value['dol'])) - strtotime( date('Y-m-d', $value['doj'])));
                    $years = floor($diff / (365*60*60*24));
                    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                    $total_experience = $years .'years'. $months.'months';
                    $data['total_experience'] =  $total_experience;
                }
            }
            $response = $this->validation->constructAPIresponse(200, $data, '');

            DB::commit();
        }catch(Exception $e) {
            DB::rollback();
            $response = $this->validation->constructAPIresponse(500, "", [LINE_NUMBER => $e->getLine(),ERROR_MESSAGE => $e->getMessage(), FILE_NAME => $e->getFile()]);

        }
    }

    public function remove(Request $request) {
        try{
            DB::beginTransaction();
            DB::table('usermanagement')->where('id',$request['id'])->delete();
            DB::commit();
            $response = $this->validation->constructAPIresponse(200, "Success", '');

        }catch(Exception $e){
            DB::rollBack();
            $response = $this->validation->constructAPIresponse(500, "", [LINE_NUMBER => $e->getLine(),ERROR_MESSAGE => $e->getMessage(), FILE_NAME => $e->getFile()]);

        }
        return $response;
    }

    public function constructArry($data){
        $result = [];
        $result['email'] = $data['email'];
        $result['name']  = $data['name'];
        $result['doj']   =  strtotime($data['doj']);
        $result['dol']   =  startime($data['dol']);
        $result['stillworking'] = $data['stillworking'];
        $result['logo'] = $data['logo'];
        return $result;
    }
}
