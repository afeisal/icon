<?php

namespace App\Http\Controllers\api;

use App\User;
use App\Type;
use App\Source;
use App\Technical;
use App\Code;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class UserController extends Controller
{
    //============== for login==========================
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return ['status' => 'failed', 'content' => $validator->errors()];
        }
        $user = User::where('email',$request->email)->first();
        $pw=$request->password;
        $hashed =$user->password;


        if (!Hash::check($request->password,$user->password)) {
            return ['status' => 'failed', 'content' => 'كلمة المرور غير صحيحة'];
        }
        if($user->api_token==null){
            $user->api_token=Str::random(50);
            $user->save();
        }


        $user = User::with('codes')->where('email',$request->email)->first();
        return ['status'=>'success','content'=>$user,'api_token'=>$user->api_token];

    }

    //======== for view one code========================================
    public function view($id){
        if(!Auth::user()->permission->view_code){
            return ['status'=>'failed','content'=>'ليس لديك صلاحية لهذا '];
        }

        $code=Code::with(['type','technical','source','agent'])->find($id);
        if(is_null($code)){
            return ['status'=>'failed','content'=>'لا يوجد محتوي لهذا'];
        }
        return ['status'=>'success','content'=>$code];
    }
    //======== for list all codes============
    public function index(){
        if(!Auth::user()->permission->list_code){
            return ['status'=>'failed','content'=>'ليس لديك صلاحية لهذا '];
        }
        $codes=Code::with(['type','technical','source','agent'])->paginate(9);
        return ['status'=>'success','content'=>$codes];

    }
    // ================for generate code==============
    public function generate(Request $request){

        if(!Auth::user()->permission->create_code){
           return ['status'=>'failed','content'=>'ليس لديك صلاحية لهذا '];
        }
        $validator = Validator::make($request->all(), [
            'proposal_type' => 'required|numeric',
            'technical_approval' => 'required|numeric',
            'client_source' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return ['status' => 'failed', 'content' => $validator->errors()];
        }
        $sale_agent=Auth::user()->alt;
        $proposal_type=Type::find($request->proposal_type)->alt;
        $tec=Technical::find($request->technical_approval)->alt;
        $source=Source::find($request->client_source)->alt;
        $prefix=$proposal_type.$tec;
        $next=$source.$sale_agent;
        $code=$this->generate_bill_code($prefix,$next);
        $requests=$request->except('api_token');
        $requests['code']=$code;
        $requests['technical_id']=$request->technical_approval;
        $requests['source_id']=$request->client_source;
        $requests['type_id']=$request->proposal_type;
        $requests['sale_agent']=Auth::id();
        Code::create($requests);
        return ['status'=>'success','content'=>'تم التسجيل بنجاح'];

    }
    // ==================== for create proposal number=======================
    public function generate_bill_code($prefix,$next)
    {
        $last = DB::table('codes')
            ->orderBy('id', 'desc')->first();

        if (is_null($last)) {
            return $prefix . '-00000'.'-'.$next;
        }
        $code = (int)substr($last->code, 5, 10) + 1;

        if (strlen($code) > 5) {
            $code = null;
        } else {
            $code = str_pad($code, 5, '0', STR_PAD_LEFT);
        }

        return $prefix . '-' . $code.'-'.$next;
    }



}
