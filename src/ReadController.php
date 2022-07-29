<?php

namespace Zems\Restapi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;

class ReadController extends Controller
{
    public $method;
    public $request;
    function __construct(Request $request)
    {
        // $this->method = $method;
        $this->method = $request->method();
        $this->request = $request;
        
    }
    
    public function restapi($data = false)
    {
        // echo "<h3>$this->method</h3>";
        $rm = ['GET' => "zems_get", 'POST'=> "zems_store", 'PUT'=>"zems_update", 'DELETE'=>"zems_delete"];
        $frm = ['gui'=>'zems_gui', 'details'=>'zems_read', 'create'=>'zems_create', 'edit'=>'zems_edit'];
        if (array_key_exists($this->method, $rm)) {
            if(array_key_exists($this->request->route('ext'), $frm)){
                $jump = $frm[$this->request->route('ext')];
                $this->$jump($data);
            } else {
                $jump = $rm[$this->method];
                $this->$jump($data);
            }            
        }
    }
    public function zems_gui($data = false)
    {        
        print view('restapi::index');
    }
    public function zems_get($data = false)
    {
        // echo "<h3>$this->method</h3>";        
        $models = $data['model'];    
        $models = 'App\\Models\\'.$models;
        if(isset($data['fields'])){
            $fields = $data['fields'];
            $jData = $models::select($fields)->get();
        } else {
            $jData = $models::all();
        }
        if(!$jData->isEmpty()){
            print json_encode($jData);
        } else {
            print json_encode("Ops!! Something went wrong!!");
        }        
    }
    public function zems_read($data = false)
    {
        // echo "<h4>Rest Read</h4>";
        $id = $this->request->id;
        $models = $data['model'];    
        $models = 'App\\Models\\'.$models;
        if(isset($data['fields'])){
            $fields = $data['fields'];
            $jData = $models::select($fields)->where('id',$id)->get();
        } else {
            $jData = $models::where('id',$id)->get();
        }
        
        if(!$jData->isEmpty()){
            print json_encode($jData);
        } else {
            print json_encode("Ops!! Something went wrong!!");
        }
    }
    public function zems_create($data = false)
    {
        echo "<h3>$this->method</h3>";
        $model = $data['model'];
        echo "<h4>Rest Create</h4>";        
        // var_dump($data);
        print(view('restapi::create'));
    }
    public function zems_store($data = false)
    {
        $fields = $data['fields'];
        $this->zems_valid($fields);
        $req = $this->request->all();
        
        $model = $data['model'];
        $models = 'App\\Models\\'.$model;
        $store = new $models;
        echo "Hi<br>";
        foreach($req as $k => $r){
            if($k != "_token" && $k != "_method"){
                echo $k.": ";
                echo $r."<br/>";
                $store->$k = $r;
            }
        }
        if($store->save()){
            print json_encode($req);
        } else {
            print json_encode("Ops!! Something went wrong!!");
        }
        
    }
    public function zems_edit($data = false)
    {
        echo "<h4>Rest Edit Form</h4>";
        $model = $data['model'];
        $models = 'App\\Models\\'.$model;
        $id = $this->request->id;
        echo "= $id =";
        $edit = $models::find($id);
        // var_dump($edit);
        print(view('restapi::edit', compact('edit')));
    }
    public function zems_update($data = false)
    {
        echo "<h4>Rest Update!!</h4>";
        $fields = $data['fields'];
        $model = $data['model'];
        $models = 'App\\Models\\'.$model;
        $store = new $models;
        $id = $this->request->id;
        echo "= $id =";
        $store = $models::find($id);
        // $req = $this->request->all();
        // $this->zems_valid($fields); 
        $idata = $this->input_data($store);
        if($store->save()){
            print json_encode($idata);
        } else {
            print json_encode("Ops!! Something went wrong!!");
        }
    }
    public function zems_delete($data = false)
    {
        // echo "<h4>Rest Delete</h4>";
        $model = $data['model'];
        $models = 'App\\Models\\'.$model;
        $id = $this->request->id;
        $store = $models::find($id);        
        if($store->save()){
            print json_encode($id);
        } else {
            print json_encode("Ops!! Something went wrong!!");
        }
        
    }
    public function zems_valid($fields)
    {
        $fRequired = [];
        foreach($fields as $fk){
            if (!array_key_exists($fk, $this->request->post())) { 
                if(strtolower($this->method) == 'post' && $fk == 'id'){
                    // $fRequired[] = $fk.' is Ok'; 
                } else {
                    $fRequired[] = '<b>'.$fk.'</b> is Required';
                }
            } 
        }
        if($fRequired) {
            echo "<hr>";
            foreach($fRequired as $fr){
                echo "<div style='color:red'>$fr</div>";
            }
            echo "<hr>";
            exit();
        } 
    }
    public function input_data($store = false)
    {
        $req = $this->request->all();
        foreach($req as $k => $r){
            if($k != "_token" && $k != "_method"){
                // echo $k.": ";
                // echo $r."<br/>";
                $store->$k = $r;
            }
        }
    }
    
}
