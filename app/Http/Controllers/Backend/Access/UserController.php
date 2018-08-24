<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/6/2018
 * Time: 9:02 AM
 */

namespace App\Http\Controllers\Backend\Access;


use App\Http\Controllers\Controller;
use Google\AdsApi\Dfp\v201802\User;
use Illuminate\Http\Request;
use YaroslavMolchan\Rbac\Models\Permission;
use YaroslavMolchan\Rbac\Models\PermissionGroup;
use YaroslavMolchan\Rbac\Models\Role;


class UserController extends Controller
{


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: permission index
     */
    public function index(Request $request){

        $id = $request->get('id');
        $name = $request->get('name');
        $email = $request->get('email');

        $user_query = new \App\User();

        if(!empty($id)){
            $user_query = $user_query->where('id', $id);
        }

        if(!empty($name)){
            $user_query = $user_query->where('name','LIKE',"%{$name}%");
        }
        if(!empty($email)){
            $user_query = $user_query->where('email','LIKE',"%{$email}%");
        }


        $users = $user_query->paginate(15);

        return view('backend.access.user.index', [
            'users' => $users
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: create new User
     */
    public function create(Request $request){

        $roles = Role::all();

        if($request->isMethod('post')){

            $validator = \Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required',
                'password' => 'required',
            ], [
                'name.required'     => 'Tên bắt buộc phải nhập',
                'email.required'     => 'Email bắt buộc phải nhập',
                'password.required'     => 'Password bắt buộc phải nhập',
            ]);

            if($validator->passes()){

//                return \App\User::create([
//                    'name' => $data['name'],
//                    'email' => $data['email'],
//                    'password' => bcrypt($data['password']),
//                ]);

                $newUser = new \App\User();
                $newUser->name= $request->get('name');
                $newUser->email= $request->get('email');
                $newUser->password= bcrypt($request->get('password'));
                $newUser->team= $request->get('team');
                $newUser->save();

                $update_roles = $request->get('roles');

                if(!is_null($update_roles) && is_array($update_roles)){
                    foreach ($update_roles as $new_role){
                        $newUser->attachRole($new_role);
                    }
                }

                return redirect()->route('access.user.manager')->withFlashSuccess('Create user thành công');

            } else {
                return redirect()->route('access.user.create')->withErrors($validator)->withInput();
            }
        }

        return view('backend.access.user.user_form', [
            'action' => 'create',
            'roles' => $roles
        ]);
    }


    public function update($id, Request $request){

        $roles = Role::all();

        $user = \App\User::find($id);

        if(!$user){
            echo "<pre>"; print_r('Not found!'); echo "</pre>"; die;
        }

        $curent_roles = $user->roles()->get()->mapWithKeys(function($item){
            return [$item['id'] => $item['name']];
        })->toArray();

        if($request->isMethod('post')){

            $validator = \Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required',
            ], [
                'name.required'     => 'Tên bắt buộc phải nhập',
                'email.required'     => 'Email bắt buộc phải nhập',
            ]);

            if($validator->passes()){

                foreach ($curent_roles as $role_id => $role_name){
                    $user->detachRole($role_id);
                }

                $update_roles = $request->get('roles');

                if(!is_null($update_roles) && is_array($update_roles)){

                    foreach ($update_roles as $new_role){
                        $user->attachRole($new_role);
                    }
                }


                $user->name = $request->get('name');
                $user->email = $request->get('email');
                $user->password = bcrypt($request->get('password'));

                $user->save();

                return redirect()->route('access.user.manager')->withFlashSuccess('Update user thành công');

            } else {
                return redirect()->route('access.user.update', ['id' => $user->id ])->withErrors($validator)->withInput();
            }
        }


        return view('backend.access.user.user_form', [
            'action' => 'update',
            'user' => $user,
            'curent_roles' => $curent_roles,
            'roles' => $roles
        ]);
    }

}
