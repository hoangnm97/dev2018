<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 3/17/2018
 * Time: 9:29 AM
 */
return [
    'roles' => [
        'administrator' => 'Administrator',
        'sale-manager' => 'SaleManager',
        'seller' => 'Seller',
        'user' => 'User',
    ],

    'permissions' => [
        'lead.create'           => 'Lead create',
        'lead.edit'             => 'Lead edit',
        'lead.view'             => 'Lead view',
        'lead.delete'           => 'Lead delete',
        'admin.view'            => 'Admin view'
    ],

    'permissionGroups' => [
        [
            'name' => 'AdministratorPermissionGroup',
            'module' => 'AdministratorPermissionGroup',
            'permissions' => [
                'admin.view',
                'lead.create',
                'lead.edit',
                'lead.view',
                'lead.delete',
            ]
        ],

        [
            'name' => 'SaleManagerPermissionGroup',
            'module' => 'SaleManagerPermissionGroup',
            'permissions' => [
                'admin.view'
            ]
        ],

        [
            'name' => 'SellerPermissionGroup',
            'module' => 'SellerPermissionGroup',
            'permissions' => [
                'admin.view',
            ]
        ],
    ],

    'rolePermissionGroups' => [
        'Administrator'     => 'AdministratorPermissionGroup',
        'SaleManager'       => 'SaleManagerPermissionGroup',
        'Seller'            => 'SellerPermissionGroup'
    ]

];