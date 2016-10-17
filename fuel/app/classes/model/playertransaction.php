<?php

	class Model_PlayerTransaction extends \Model_Base
	{


		protected static $_table_name = 'player_transactions';

		protected static $_parent_key = null;

		protected static $_primary_key = ['id'];


		/**
		 * テーブルプロパティ
		 *
		 * @var array
		 */
		protected static $_properties = [
			'id'              => [
				'data_type' => 'bigint',
				'label'     => 'id',
				'form'      => ['type' => false],
				'table'     => true,
				'sort'      => true,
				'search'    => true,
				'align'     => 'left',
				'default'   => null,
			],
            'system_id'              => [
                'data_type' => 'bigint',
                'label'     => 'system_id',
                'validation' => [
                    'required'
                ],
                'form'      => ['type' => false],
                'table'     => true,
                'sort'      => true,
                'search'    => true,
                'align'     => 'left',
                'default'   => null,
            ],
			'amount'            => [
				'data_type'  => 'decimal',
				'label'      => 'name',
				'validation' => [
					'required'
				],
				'form'       => [
					'type'        => 'text',
					'placeholder' => 'name',
					'class'       => 'form-control',
				],
				'table'      => true,
				'sort'       => true,
				'search'     => true,
				'align'      => 'right',
				'default'   => null,
			],
            'player_id'              => [
                'data_type' => 'bigint',
                'label'     => 'player_id',
                'validation' => [
                    'required'
                ],
                'form'      => ['type' => false],
                'table'     => true,
                'sort'      => true,
                'search'    => true,
                'align'     => 'left',
                'default'   => null,
            ],
            'external_tran_id'    => [
                'data_type' => 'varchar',
                'label'     => 'external_tran_id',
                'validation' => [
                    'required'
                ],
                'form'      => ['type' => false],
                'table'     => true,
                'sort'      => true,
                'search'    => true,
                'align'     => 'left',
                'default'   => null,
            ],
            'details'              => [
                'data_type' => 'text',
                'label'     => 'details',
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
			'status'          => [
				'data_type'  => 'enum',
				'label'      => 'status',
				'validation' => [
					'required',
				],
				'options'    => ['active', 'inactive', 'pending'],
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
				'default'   => 'active',
			],
			'created_id'      => [
				'data_type' => 'int',
				'label'     => 'created_id',
				'form'      => ['type' => false],
				'table'     => false,
				'sort'      => false,
				'search'    => false,
				'align'     => 'center',
				'default'   => null,
			],
			'created_at'      => [
				'data_type' => 'int',
				'label'     => 'created_at',
				'form'      => ['type' => false],
				'sort'      => true,
				'search'    => false,
				'default'   => null,
			],
			'updated_id'      => [
				'data_type' => 'int',
				'label'     => 'updated_id',
				'form'      => ['type' => false],
				'table'     => false,
				'sort'      => false,
				'search'    => false,
				'align'     => 'center',
				'default'   => null,
			],
			'updated_at'      => [
				'data_type' => 'int',
				'label'     => 'updated_at',
				'form'      => ['type' => false],
				'table'     => false,
				'sort'      => false,
				'search'    => false,
				'align'     => 'center',
				'default'   => null,
			],
			'deleted_id'      => [
				'data_type' => 'int',
				'label'     => 'deleted_id',
				'form'      => ['type' => false],
				'table'     => false,
				'sort'      => false,
				'search'    => false,
				'align'     => 'center',
				'default'   => null,
			],
			'deleted_at'      => [
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


		/**
		 * OWNER　プロパティ | 独自実装
		 * 現状使用していません。
		 *
		 * @var null
		 */
		protected static $_owner = null;


		/**
		 * 現状は使用していません。
		 * @param $property
		 * @return mixed
		 */
		public function & __get($property)
		{
			return parent::__get($property);
		}


		/**
		 * 現状は使用していません。
		 * @var array
		 */
		protected static $_belongs_to = array();


		/**
		 * 現状は使用していません。
		 * @var array
		 */
		protected static $_has_one = array();


		/**
		 * 現状は使用していません。
		 * @var array
		 */
		protected static $_has_many = array();


		/**
		 * 現状は使用していません。
		 * @var array
		 */
		protected static $_many_many = array();


		/**
		 * 現状は使用していません。
		 * @var array
		 */
		protected static $_eav = array();


		/**
		 *現状は使用していません。
		 */
		public function _event_after_load()
		{
			$this->created_at = $this->timeConvertByVal($this->created_at);
			$this->updated_at = $this->timeConvertByVal($this->updated_at);
			$this->deleted_at = $this->timeConvertByVal($this->deleted_at);
		}


		/**
		 * クラスを取得する
		 * @return string
		 */
		public static function getClass()
		{
			return __CLASS__;//get_class():
		}


		/**
		 * 現状は使用していません。
		 * @param array $additional_condition
		 * @return array
		 */
		public static function getQueryCondition($additional_condition = [])
		{
			$where = parent::getQueryCondition($additional_condition);

			return $where;
		}


		/**
		 * 現状は使用していません。
		 * @param $factory
		 * @return \Fuel\Core\Validation
		 */
		public static function validate($factory)
		{
			$validate = parent::validate($factory);

			switch ($factory)
			{
				case 'status':
				case 'delete':
				case 'create':
				case 'update':
					return $validate;
					break;

				default:
					break;
			}

			return $validate;
		}
		//////////##########		↑↑↑↑↑ここまで共通↑↑↑↑↑		##########//////////
	}
