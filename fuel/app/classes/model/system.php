<?php

class Model_System extends \Model_Base
{
    protected static $_table_name = 'systems';
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
        'name'                  => [
            'data_type'  => 'varchar',
            'label'      => 'username',
            'validation' => [
                'required',
                'min_length' => [4],
                'max_length' => [64],
            ],
            'form'       => [
                'type'        => 'text',
                'placeholder' => 'name',
                'class'       => 'form-control',
            ],
            'table'      => true,
            'sort'       => true,
            'search'     => true,
            'align'      => 'left',
            'default'    => null,
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
        'base_domain'                => [
            'data_type' => 'varchar',
            'label'     => 'base_domain',
            'validation' => [
                'required',
                'min_length' => [6],
                'max_length' => [64],
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
        'sys_type'                 => [
            'data_type' => 'enum',
            'label'     => 'sys_type',
            'options'    => ['IN', 'EX'],
            'form'       => [
                'type'        => 'radio',
                'options'     => ['internal' => 'IN', 'external' => 'EX'],
                'placeholder' => 'status',
                'class'       => 'form-radio',
            ],
            'table'     => true,
            'sort'      => true,
            'search'    => true,
            'align'     => 'left',
            'default'   => null,
        ],
        'security_key'                 => [
            'data_type' => 'varchar',
            'label'     => 'security_key',
            'form'      => ['type' => false],
            'table'     => true,
            'sort'      => true,
            'search'    => true,
            'align'     => 'left',
            'default'   => null,
        ],
        'ref_code'                 => [
            'data_type' => 'varchar',
            'label'     => 'ref_code',
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
        'currency_id'               => [
            'data_type'    => 'bigint',
            'has_relation' => [
                'type'          => '_belongs_to',
                'name'          => 'currency',
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
            'label'        => 'currency_id',
            'options'      => [],
            'form'         => [
                'type'        => 'select',
                'options'     => [],
                'placeholder' => 'currency_id',
                'class'       => 'form-control',
            ],
            'table'        => true,
            'sort'         => true,
            'search'       => true,
            'align'        => 'left',
            'default'      => null,
        ],
        'settings'                => [
            'data_type' => 'text',
            'label'     => 'settings',
            'form'      => [
                'type'        => 'text',
                'placeholder' => 'user_agent',
                'class'       => 'form-control',
            ],
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
            'options'    => ['active', 'inactive','pending'],
            'form'       => [
                'type'        => 'radio',
                'options'     => ['active' => 'active', 'inactive' => 'inactive', 'pending' => 'pending'],
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