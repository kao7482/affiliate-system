<?php

	/**
	 * Class Model_Currency
	 * [BACKEND]通貨
	 *
	 * オンライン用のテーブルです。現状は使用していません。
	 */
	class Model_Currency extends \Model_Base
	{


		/**
		 * テーブル名
		 *
		 * @var string
		 */
		protected static $_table_name = 'currencies';


		/**
		 * リレーション(belongs_to)の親キー
		 * リレーションがある場合、親モデルに対してのキーを設定する。
		 * が、これは旧論理。特に必要ない。
		 * ※もともと\Orm\Model_Softにこのようなプロパティは存在しない。
		 * @var null
		 */
		protected static $_parent_key = null;


		/**
		 * DBのPKを定義する。
		 * ※もともと\Orm\Model_のプロパティ？。
		 *
		 * @var array
		 */
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
			'name'            => [
				'data_type'  => 'varchar',
				'label'      => 'name',
				'validation' => [
					'required',
					'min_length' => [2],
					'max_length' => [32]
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
				'default'   => null,
			],
			'exchange_rate'         => [
				'data_type'  => 'decimal',
				'label'      => 'exchange_rate',
				'validation' => [
					'required',
				],
				'form'       => [
					'type'        => 'text',
					'placeholder' => 'exchange_rate',
					'class'       => 'form-control',
				],
				'table'      => true,
				'sort'       => true,
				'search'     => true,
				'align'      => 'left',
				'default'   => null,
			],
			'status'          => [
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
