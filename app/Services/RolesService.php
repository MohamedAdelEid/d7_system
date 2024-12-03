<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RolesService
{
    public function insert($request){
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name', 
        ]);

    

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $validatedData['name'],
                'display_name' => $validatedData['name'],
                'description' => $validatedData['description']
            ]);

            $role->attachPermissions($validatedData['permissions']);

            DB::commit();
            return $role;
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'حدث خطأ أثناء إنشاء الدور'], 500);
        }
    }

    public function update($role, $request){
        $role->name            = $request->name;
        $role->display_name    = $request->name;
        $role->description     = $request->description;
        $role->save();

        $role->syncPermissions($request->permissions); //update role permassion
    }
}