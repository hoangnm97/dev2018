<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 7/6/2018
 * Time: 9:02 AM
 */

namespace App\Http\Controllers\Backend\Access;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use YaroslavMolchan\Rbac\Models\Permission;
use YaroslavMolchan\Rbac\Models\PermissionGroup;
use YaroslavMolchan\Rbac\Models\Role;


class AccessController extends Controller
{


    public function role_manager(){
        $roles = Role::all();
        return view('backend.access.access.role_index', [
            'roles' => $roles
        ]);
    }

    public function role_create(Request $request){

        if($request->isMethod('post')){


            $validator = \Validator::make($request->all(), [
                'slug' => 'required',
                'name' => 'required',
                'permission_group' => 'required',
            ], [
                'slug.required'     => 'Slug bắt buộc phải nhập',
                'name.required'     => 'Tên bắt buộc phải nhập',
                'permission_group.required'     => 'Bạn chưa chọn nhóm quyền',
            ]);

            if($validator->passes()){

                $role = new Role();
                $role->slug = $request->get('slug');
                $role->name = $request->get('name');
                $role->save();


                $permission_group_selected = $request->get('permission_group');

                // attach to permission group
                $role->attachGroup($permission_group_selected);

                return redirect()->route('access.role.manager')->withFlashSuccess('Create role thành công');

            } else {
                return redirect()->route('access.role.create')->withErrors($validator)->withInput();
            }
        }

        $permission_groups =PermissionGroup::all();

        return view('backend.access.access.role_form', [
            'action' => 'create',
            'permission_groups' => $permission_groups
        ]);
    }


    public function role_update($id, Request $request){
        $role = Role::find($id);
        if(!$role){
            echo "<pre>"; print_r('Not found'); echo "</pre>"; die;
        }

        $curent_permission_groups = $role->permissionGroups()->get()->mapWithKeys(function($item){
            return [$item['id'] => $item['name']];
        })->toArray();



        if($request->isMethod('post')){


            $validator = \Validator::make($request->all(), [
                'name' => 'required',
                'permission_group' => 'required',
            ], [
                'name.required'     => 'Tên bắt buộc phải nhập',
                'permission_group.required'     => 'Bạn chưa chọn nhóm quyền',
            ]);

            if($validator->passes()){
                $permission_group_selected = $request->get('permission_group');
                // detach from permission group
                foreach ($role->permissionGroups as $curent_permission_group){
                    $role->detachGroup($curent_permission_group);
                }

                // attach to permission group
                $role->attachGroup($permission_group_selected);


                $role->name = $request->get('name');
                $role->save();

                return redirect()->route('access.role.manager')->withFlashSuccess('Update role thành công');

            } else {
                return redirect()->route('access.role.update', ['id' => $role->id])->withErrors($validator)->withInput();
            }
        }

        $permission_groups =PermissionGroup::all();

        return view('backend.access.access.role_form', [
            'action' => 'update',
            'role' => $role,
            'curent_permission_groups' => $curent_permission_groups,
            'permission_groups' => $permission_groups
        ]);
    }



    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: permission index
     */
    public function permission_manager(){

        $permissions = Permission::all();

        return view('backend.access.access.permission_index', [
            'permissions' => $permissions
        ]);
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * $desc: create permission
     */
    public function permission_create( Request $request){

        if($request->isMethod('post')){
            $validator = \Validator::make($request->all(), [

                'slug' => 'required|unique:permissions,slug',
                'name' => 'required',
            ], [
                'slug.required'     => 'Slug bắt buộc phải nhập',
                'slug.unique'     => 'Slug đã tồn tại. vui lòng chọn tên khác',
                'name.required'     => 'Tên bắt buộc phải nhập',
            ]);

            if($validator->passes()){


                $newPermission = new Permission();
                $newPermission->slug = $request->get('slug');
                $newPermission->name = $request->get('name');
                $newPermission->save();

                return redirect()->route('access.permission.manager')->withFlashSuccess('Thêm quyền thành công');

            } else {

                return redirect()->route('access.permission.create')->withErrors($validator)->withInput();
            }

        }

        return view('backend.access.access.permission_form', [
            'action' => 'create'
        ]);
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: Update quyền
     */
    public function permission_update($id, Request $request){

        $permission = Permission::find($id);
        if(!$permission){
            echo "<pre>"; print_r('Not found'); echo "</pre>"; die;
        }


        if($request->isMethod('post')){
            $validator = \Validator::make($request->all(), [
                'name' => 'required',
            ], [
                'name.required'     => 'Tên bắt buộc phải nhập',
            ]);

            if($validator->passes()){

                $permission->name = $request->get('name');
                $permission->save();

                return redirect()->route('access.permission.manager')->withFlashSuccess('Update quyền thành công');

            } else {
                return redirect()->route('access.permission.update', ['id' => $permission->id])->withErrors($validator)->withInput();
            }

        }

        return view('backend.access.access.permission_form', [
            'action' => 'update',
            'permission' => $permission,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: permission group index
     */
    public function permission_group_manager(){

        $permissionGroups = PermissionGroup::all();

        foreach ($permissionGroups as $permissionGroup){
//            echo "<pre>"; print_r($permissionGroup->permissions); echo "</pre>"; die();
        }

        return view('backend.access.access.permission_group_index', [
            'permissionGroups' => $permissionGroups
        ]);
    }

    public function permission_group_create( Request $request){

        if($request->isMethod('post')){
            $validator = \Validator::make($request->all(), [
                'name' => 'required',
                'permission' => 'required',
            ], [
                'name.required'     => 'Tên bắt buộc phải nhập',
                'permission.required'     => 'Phải chọn ít nhất 1 quyền',
            ]);

            if($validator->passes()){

                $newPermissionGroup = new PermissionGroup();
                $newPermissionGroup->name = $request->get('name');
                $newPermissionGroup->save();

                $permissions = $request->get('permission');

                foreach ($permissions as $permission){
                    $newPermissionGroup->attachPermission($permission);
                }

                return redirect()->route('access.permission_group.manager')->withFlashSuccess('Thêm nhóm quyền thành công');

            } else {
                return redirect()->route('access.permission_group.create')->withErrors($validator)->withInput();
            }

        }

        $permissions = Permission::all();

        return view('backend.access.access.permission_group_form', [
            'action' => 'create',
            'permissions' => $permissions
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: Cập nhật nhóm quyền
     */

    public function permission_group_update($id, Request $request){
        $permissionGroup = PermissionGroup::find($id);

        if(!$permissionGroup){
            echo "<pre>"; print_r('Not found!'); echo "</pre>"; die;
        }

        $curent_permissions = $permissionGroup->permissions()->get()->mapWithKeys(function($item){
            return [$item['id'] => $item['name']];
        })->toArray();

        if($request->isMethod('post')){


            $validator = \Validator::make($request->all(), [
                'name' => 'required',
                'permission' => 'required',
            ], [
                'name.required'     => 'Tên bắt buộc phải nhập',
                'permission.required'     => 'Phải chọn ít nhất 1 quyền',
            ]);

            if($validator->passes()){

                $name = $request->get('name');
                $permissions = $request->get('permission');

                // xóa toàn bộ quyền cũ
                foreach ($curent_permissions as $key => $value){
                    $permissionGroup->detachPermission($key);
                }

                // thêm mới những quyền được select
                foreach ($permissions as $permission){
                    $permissionGroup->attachPermission($permission);
                }

                $permissionGroup->name = $name;
                $permissionGroup->save();

                return redirect()->route('access.permission_group.manager')->withFlashSuccess('Update nhóm quyền thành công');
            } else {
                return redirect()->route('access.permission_group.update', ['id' => $permissionGroup->id ])->withErrors($validator)->withInput();
            }



        }

        $permissions = Permission::all();

        return view('backend.access.access.permission_group_form', [
            'action' => 'update',
            'permissionGroup' => $permissionGroup,
            'curent_permissions' => $curent_permissions,
            'permissions' => $permissions
        ]);



    }
}
