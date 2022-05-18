<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
   // roles y permisos por defecto

    public function run()
    {
        // administrador con todos los permisos
        $role1 = Role::create(['name' => 'Super-Admin']);

        // revisa las ordenes
        $role2 = Role::create(['name' => 'Revisador']);


        Permission::create(['name' => 'seccion.estadisticas', 'description' => 'Vista para estadisticas de la App'])->syncRoles($role1, $role2);

        // roles y permisos
        Permission::create(['name' => 'seccion.permisos', 'description' => 'Vista para permisos'])->syncRoles($role1);

        // vista configuracion
        Permission::create(['name' => 'seccion.configuracion', 'description' => 'Vista configuracion'])->syncRoles($role1);

        // vista personal
        Permission::create(['name' => 'seccion.personal', 'description' => 'Vista personal'])->syncRoles($role1);

        // vista servicios
        Permission::create(['name' => 'seccion.servicios', 'description' => 'vista servicios'])->syncRoles($role1);

        // vista administradores
        Permission::create(['name' => 'seccion.administradores', 'description' => 'vista administradores'])->syncRoles($role1);


    }
}
