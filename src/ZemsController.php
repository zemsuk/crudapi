<?php
namespace Zems\Crudapi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use DB;

class ZemsController extends Controller
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
        $frm = ['details'=>'zems_read', 'create'=>'zems_create', 'edit'=>'zems_edit'];
        if (array_key_exists($this->method, $rm)) {
            if(array_key_exists($this->request->route('ext'), $frm)){
                $jump = $frm[$this->request->route('ext')];
                return $this->$jump($data);
            } else {
                $jump = $rm[$this->method];
                return $this->$jump($data);
            }            
        }
    }    
    public function zems_get($data = false)
    {
        $model = $data['model'];    
        $models = 'App\\Models\\'.$model;      
        if(isset($data['fields'])){
            $fields = $data['fields'];           
        } else {
            $fields = "*";
        }
        $models = $models::select($fields);
        if(isset($data['join'])){
            foreach($data['join'] as $join_table => $field){                
                $models = $models->join($join_table, $field[0], '=', $field[1]);
            }
        }        
        if(isset($data['where'])){
            $models = $models->where($data['where']);
        }
        if($this->request->route()->getPrefix() == 'api'){
            if(isset($data['where_api'])){
                $models = $models->where($data['where_api']);
            }
        }        
        if(isset($data['order_by'])){
            $models = $models->orderBy($data['order_by'][0], $data['order_by'][1]);           
        }        
        if(isset($data['group_by'])){
            $models = $models->groupBy($data['group_by']);           
        }        
        if(isset($data['limit'])){
            $models = $models->limit($data['limit']);
        }        
        if(isset($data['pagination'])){
            $json_data = $models->paginate($data['pagination']);            
        } else {
            $json_data = $models->get();
        }
         
        if(!$json_data->isEmpty()){
            $zems['content'] = $json_data;
            if(isset($data['other'])){
                foreach($data['other'] as $key => $other){
                    $zems['other'][$key] = $other;
                }
            }
            if($this->request->route()->getPrefix() == '/zems'){                
                return View::make($data['view'], compact('zems'));
            }else{
                return $zems;
            } 
        } else {
            return ['error'=>'Ops!! Something went wrong!! Please Try againg'];
        }      
    }
    
    public function zems_read($data = false)
    {
        $id = $this->request->id;
        $model = $data['model'];    
        $models = 'App\\Models\\'.$model;       
        
        if(isset($data['fields'])){
            $fields = $data['fields'];           
        } else {
            $fields = "*";
        }
        $models = $models::select($fields);
        
        if(isset($data['join'])){
            foreach($data['join'] as $join_table => $field){                
                $models = $models->join($join_table, $field[0], '=', $field[1]);
            }
        }                
        if(isset($data['where'])){
            $models = $models->where($data['where']);
        }        
        $plural = Str::snake($model);
        $plural = Str::plural($plural, 2);        
        $json_data = $models->where($plural.'.id',$id)->first();        
        if($json_data){
            $zems['content'] = $json_data;
            if(isset($data['other'])){
                foreach($data['other'] as $key => $other){
                    $zems['other'][$key] = $other;
                }
            }
            if($this->request->route()->getPrefix() == '/zems'){               
                return View::make($data['view']."_details", compact('zems'));
            }else{
                return $zems;
            } 
        } else {
            return ['error'=>'Ops!! Something went wrong!! Please Try againg'];
        }
    }
    public function zems_create($data = false)
    {
        $join = [];
        if(isset($data['join'])){
            foreach($data['join'] as $join_table => $field){
                $tbl_name =  $join_table;
                if (strpos($join_table, ' as ') !== false) {
                    $jtn = explode(" as ", $join_table);
                    $tbl_name =  $jtn[1];
                }
                $join[$tbl_name] = DB::table($join_table)->get();                              
            }
        }        
        if($this->request->route()->getPrefix() == '/zems'){             
            return View::make($data['view']."_create", compact('join'));
        } else {
            return ['msg'=>'Please Create your form with the help of you provided field'];
        }
    }
    public function zems_store($data = false)
    {
        if(isset($data['fields'])){
            $fields = $data['fields'];        
        } else {
            $fields = $this->request->post();
        }
        $model = $data['model'];
        $models = 'App\\Models\\'.$model;
        $store = new $models;
        $json_data = $this->input_data($store);
        if($store->save()){
            $zems['content']['id'] = $store->id;
            $zems['content'] = $json_data;
            if(isset($data['other'])){
                foreach($data['other'] as $key => $other){
                    $zems['other'][$key] = $other;
                }
            }
            if($this->request->route()->getPrefix() == '/zems'){   
                $current_route = $this->request->route()->getName(); 
                return redirect("/zems/".$current_route)->with(['msg' => 'Data Insterted Successfully', 'zems'=>$zems]);                
            }else{
                return $zems;
            }
        } else {
            return ['error'=>'Ops!! Something went wrong!! Please Try againg'];
        }
        
    }
    public function zems_edit($data = false)
    {
        $model = $data['model'];
        $models = 'App\\Models\\'.$model;
        $id = $this->request->id;
        $zems['content'] = $models::find($id);
        if(isset($data['other'])){
            foreach($data['other'] as $key => $other){
                $zems['other'][$key] = $other;
            }
        }
        $join = [];
        if(isset($data['join'])){
            foreach($data['join'] as $join_table => $field){
                $tbl_name =  $join_table;
                if (strpos($join_table, ' as ') !== false) {
                    $jtn = explode(" as ", $join_table);
                    $tbl_name =  $jtn[1];
                }
                $join[$tbl_name] = DB::table($join_table)->get();                              
            }
        }
        if($this->request->route()->getPrefix() == '/zems'){                
            return View::make($data['view']."_edit", compact('zems', 'join'));
        }else{
            return ['msg'=>'Please Create your Edit form with the help of you provided field'];
        }
        
    }
    public function zems_update($data = false)
    {
        if(isset($data['fields'])){
            $fields = $data['fields'];        
        } else {
            $fields = $this->request->post();
        }
        $model = $data['model'];
        $models = 'App\\Models\\'.$model;
        $id = $this->request->id;
        $store = $models::find($id);        
        $json_data = $this->input_data($store);
        if($store->save()){
            $zems['content'] = $json_data;
            if(isset($data['other'])){
                foreach($data['other'] as $key => $other){
                    $zems['other'][$key] = $other;
                }
            }
            if($this->request->route()->getPrefix() == '/zems'){   
                $current_route = $this->request->route()->getName();
                return redirect("/zems/".$current_route)->with(['msg' => 'Data Updated Successfully', 'zems'=>$zems]);                
            }else{
                return $zems;
            }
        } else {
            return ['error'=>'Ops!! Something went wrong!! Please Try againg'];
        }
    }
    public function zems_delete($data = false)
    {
        $model = $data['model'];
        $models = 'App\\Models\\'.$model;
        $id = $this->request->id;
        $store = $models::find($id);        
        if($store->delete()){
            $zems['content'] = ['id' => $id];
            if(isset($data['other'])){
                foreach($data['other'] as $key => $other){
                    $zems['other'][$key] = $other;
                }
            }
            if($this->request->route()->getPrefix() == '/zems'){   
                $current_route = $this->request->route()->getName(); 
                return redirect("/zems/".$current_route)->with(['msg' => 'Data Deleted Successfully', 'zems'=>$zems]);                
            }else{
                return $zems;
            }
        } else {
            return ['error'=>'Ops!! Something went wrong!! Please Try againg'];
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
                    // $fRequired[] = '<b>'.$fk.'</b> is Required';
                    $fRequired[$fk] = 'is Required';
                }
            } 
        }
        // return $fRequired;
        if($fRequired) {
            // echo "<hr>";
            // foreach($fRequired as $fr){
            //     echo "<div style='color:red'>$fr</div>";
            // }
            // echo "<hr>";
            echo json_encode($fRequired);
            exit();
        } 
    }
    public function input_data($store = false)
    {
        $req = $this->request->all();
        $data = [];
        foreach($req as $k => $r){
            if($k != "_token" && $k != "_method" && $k != "_file"){                
                $store->$k = $r;
                $data[$k] = $r;
            }
        }      
        return $data;
    }
    
}
