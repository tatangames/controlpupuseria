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


        // roles y permisos
        Permission::create(['name' => 'grupo.superadmin.roles-y-permisos', 'description' => 'Contenedor para el grupo llamado: Roles y Permisos'])->syncRoles($role1);


        // Vista de Ingreso (2 roles)
        Permission::create(['name' => 'rol.superadmin.inicio', 'description' => 'Cuando inicia el sistema, se redirigirá la vista al grupo Inicio'])->syncRoles($role1, $role2);




    }
}
