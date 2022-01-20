<?php

namespace App;

use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission as ModelsPermission;

class Permission extends ModelsPermission
{
    public static function defaultPermissions()
    {
        return [
            'view role',
            'create role',
            'edit role',
            'delete role',

            'view user',
            'create user',
            'edit user',
            'delete user',

            'view order',
            'create order',
            'edit order',
            'delete order',

            'view company',
            'create company',
            'edit company',
            'delete company',
        ];
    }

    public function isDeleteLabel()
    {
        return Str::contains($this->name, 'delete') ? 'text-danger' : null;
    }
}
