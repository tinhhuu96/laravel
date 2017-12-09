<?php

namespace App\Http\Controllers\AdminController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Permission;
use App\Permission_user;
use App\User;

class PermissionController extends Controller
{
    public function index()
    {
    	$arpermis = Permission::all();
    	$arUser = DB::table('users')
            ->join('permission_user', 'users.id', '=', 'permission_user.user_id')
            ->select('users.*', 'permission_user.user_id', 'permission_user.id as idperuser', 'permission_user.permission_id' )
            ->get();
     	return view('admin.user.permission', [ 'arpermis'=>$arpermis, 'aruser'=>$arUser]);
    }

    public function store(Request $request)
    {
        if ($request->aname == "") {
            return response()->json(['alert'=>'Lỗi nhập rỗng !', 'a'=>'e']) ;
        }
        $ar = count(Permission::where('name','=',$request->aname)->get());
        if ($ar > 0) {
            return response()->json(['alert'=>'Nhập vào tồn tại !', 'a'=>'e']) ;
        }
        Permission::create(['name'=>$request->aname]);
        return response()->json(['alert'=>'Thêm thành công !', 'a'=>'s']) ;
    }

    public function setPermission(Request $request)
    {
    	$iduser = $request->auser;
    	$idpermis= $request->apermis;
    	$idperuser = $request->aidperuser;
    	if($idpermis == 1){
    		return 0;
    	}
    	if ($idperuser > 0 ) {
    		DB::table('permission_user')->where('id', $idperuser)->update(['permission_id' => $idpermis]);
    		return 1;
    	}
    }

    public function update(Request $request)
    {
    	$id = $request->aid;
    	$name = trim($request->aname);
		$objper = Permission::find($id);
    	$objper->name = $name;
    	$objper->update();
    	return $name;
    }



    public function destroy(Request $request)
    {
        $id = $request->aid;
    	// dd('chạy');
    	Permission::where('id',$id)->delete();

    	return "Xóa thành công";
    }
}
