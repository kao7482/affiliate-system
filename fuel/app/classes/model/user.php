<?php

class Model_User extends \Model_Base
{
    protected static $_table_name = 'users';
    protected static $_parent_key = null;
    protected static $_primary_key = ['id'];

    protected static $_properties = [
        'id'                        => [
            'data_type' => 'bigint',
            'label'     => 'id',
            'form'      => ['type' => false],
            'table'     => true,
            'sort'      => true,
            'search'    => true,
            'align'     => 'left',
            'default'   => null,
        ],
        'is_alias'                  => [
            'data_type' => 'bigint',
            'label'     => 'id',
            'form'      => ['type' => false],
            'table'     => true,
            'sort'      => true,
            'search'    => true,
            'align'     => 'left',
            'default'   => null,
        ],
        'username'                  => [
            'data_type'  => 'varchar',
            'label'      => 'username',
            'validation' => [
                'required',
                'min_length' => [4],
                'max_length' => [32],
            ],
            'form'       => [
                'type'        => 'text',
                'placeholder' => 'username',
                'class'       => 'form-control',
            ],
            'table'      => true,
            'sort'       => true,
            'search'     => true,
            'align'      => 'left',
            'default'    => null,
        ],
        'password'                  => [
            'data_type'  => 'varchar',
            'label'      => 'password',
            'validation' => [
                'required',
                'min_length' => [6],
                'max_length' => [64],
            ],
            'form'       => [
                'type'        => 'password',
                'placeholder' => 'password',
                'class'       => 'form-control',
            ],
            'table'      => false,
            'sort'       => false,
            'search'     => false,
            'align'      => 'left',
            'default'    => null,
        ],
        'group_id'        => [
            'data_type' => 'bigint',
            'label'     => 'id',
            'form'      => ['type' => false],
            'table'     => true,
            'sort'      => true,
            'search'    => true,
            'align'     => 'left',
            'default'   => null,
        ],
        'email'                     => [
            'data_type' => 'varchar',
            'label'     => 'email',
            'form'      => ['type' => false],
            'table'     => true,
            'sort'      => true,
            'search'    => true,
            'align'     => 'left',
            'default'   => null,
        ],
        'first_name'             => [
            'data_type' => 'varchar',
            'label'     => 'first_name',
            'validation' => [
                'required',
                'min_length' => [4],
                'max_length' => [32],
            ],
            'form'      => [
                'type'        => 'text',
                'placeholder' => 'base_domain',
                'class'       => 'form-control',
            ],
            'table'     => true,
            'sort'      => true,
            'search'    => true,
            'align'     => 'left',
            'default'   => null,
        ],
        'last_name'             => [
            'data_type' => 'varchar',
            'label'     => 'last_name',
            'validation' => [
                'required',
                'min_length' => [4],
                'max_length' => [32],
            ],
            'form'      => [
                'type'        => 'text',
                'placeholder' => 'base_domain',
                'class'       => 'form-control',
            ],
            'table'     => true,
            'sort'      => true,
            'search'    => true,
            'align'     => 'left',
            'default'   => null,
        ],
        'country_id'                => [
            'data_type'    => 'bigint',
            'has_relation' => [
                'type'          => '_belongs_to',
                'name'          => 'country',
                'display_value' => 'name',
                'condition'     => [
                    'where' => [
                        [
                            'status',
                            'active'
                        ],
                    ],
                ],
            ],
            'label'        => 'country_id',
            'options'      => [],
            'form'         => [
                'type'        => 'select',
                'options'     => [],
                'placeholder' => 'country_id',
                'class'       => 'form-control',
            ],
            'table'        => true,
            'sort'         => true,
            'search'       => true,
            'align'        => 'left',
            'default'      => null,
        ],
        'gender'                 => [
            'data_type' => 'enum',
            'label'     => 'sys_type',
            'options'    => ['male', 'female'],
            'form'       => [
                'type'        => 'radio',
                'options'     => ['male' => 'male', 'female' => 'female'],
                'placeholder' => 'status',
                'class'       => 'form-radio',
            ],
            'table'     => true,
            'sort'      => true,
            'search'    => true,
            'align'     => 'left',
            'default'   => null,
        ],
        'tel'                       => [
            'data_type' => 'varchar',
            'label'     => 'tel',
            'form'      => ['type' => false],
            'table'     => true,
            'sort'      => true,
            'search'    => true,
            'align'     => 'left',
            'default'   => null,
        ],
        'last_login'                => [
            'data_type' => 'varchar',
            'label'     => 'last_login',
            'form'      => ['type' => false],
            'table'     => true,
            'sort'      => true,
            'search'    => true,
            'align'     => 'left',
            'default'   => null,
        ],
        'previous_login'                => [
            'data_type' => 'varchar',
            'label'     => 'previous_login',
            'form'      => ['type' => false],
            'table'     => true,
            'sort'      => true,
            'search'    => true,
            'align'     => 'left',
            'default'   => null,
        ],
        'login_hash'                => [
            'data_type' => 'varchar',
            'label'     => 'login_hash',
            'form'      => ['type' => false],
            'table'     => true,
            'sort'      => true,
            'search'    => true,
            'align'     => 'left',
            'default'   => null,
        ],
        'user_id'                => [
            'data_type' => 'int',
            'label'     => 'user_id',
            'form'      => ['type' => false],
            'table'     => true,
            'sort'      => true,
            'search'    => true,
            'align'     => 'left',
            'default'   => null,
        ],
        'status'                    => [
            'data_type'  => 'enum',
            'label'      => 'status',
            'validation' => [
                'required',
            ],
            'options'    => ['active', 'inactive'],
            'form'       => [
                'type'        => 'radio',
                'options'     => ['active' => 'active', 'inactive' => 'inactive'],
                'placeholder' => 'status',
                'class'       => 'form-radio',
            ],
            'table'      => true,
            'sort'       => true,
            'search'     => false,
            'align'      => 'center',
            'default'    => 'active',
        ],
        'created_id'                => [
            'data_type' => 'int',
            'label'     => 'created_id',
            'form'      => ['type' => false],
            'table'     => false,
            'sort'      => false,
            'search'    => false,
            'align'     => 'center',
            'default'   => null,
        ],
        'created_at'                => [
            'data_type' => 'int',
            'label'     => 'created_at',
            'form'      => ['type' => false],
            'sort'      => true,
            'search'    => false,
            'default'   => null,
        ],
        'updated_id'                => [
            'data_type' => 'int',
            'label'     => 'updated_id',
            'form'      => ['type' => false],
            'table'     => false,
            'sort'      => false,
            'search'    => false,
            'align'     => 'center',
            'default'   => null,
        ],
        'updated_at'                => [
            'data_type' => 'int',
            'label'     => 'updated_at',
            'form'      => ['type' => false],
            'table'     => false,
            'sort'      => false,
            'search'    => false,
            'align'     => 'center',
            'default'   => null,
        ],
        'deleted_id'                => [
            'data_type' => 'int',
            'label'     => 'deleted_id',
            'form'      => ['type' => false],
            'table'     => false,
            'sort'      => false,
            'search'    => false,
            'align'     => 'center',
            'default'   => null,
        ],
        'deleted_at'                => [
            'data_type' => 'int',
            'label'     => 'deleted_at',
            'form'      => ['type' => false],
            'table'     => false,
            'sort'      => false,
            'search'    => false,
            'align'     => 'center',
            'default'   => null,
        ],
    ];

}