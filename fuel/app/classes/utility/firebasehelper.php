<?php

	namespace Utility;

	use Fuel\Core\Fuel;

	/**
	 * Class FireBaseHelper
	 * FireBase関数
	 *
	 * @package FireBaseHelper
	 */
	class FireBaseHelper
	{

		/**
		 * @var \Firebase\FirebaseLib instance
		 */
		private $_instance = null;


		/**
		 * コンストラクタ
		 */
		public function __construct()
		{
			if ($this->_instance == null)
			{
				require_once APPPATH . 'vendor' . DS . 'autoload.php';
				\Autoloader::add_namespace('Firebase', APPPATH . 'vendor' . DS . 'ktamas77' . DS . 'firebase-php' . DS . 'src');
				\Config::load('firebase');
				$this->_instance = new \Firebase\FirebaseLib(\Config::get('default.host', getenv('FIREBASE_HOST')), \Config::get('default.token', getenv('FIREBASE_TOKEN')));
			}
		}


		/**
		 * デストラクタ
		 */
		public function __destruct()
		{
			//
		}


		/**
		 *　ルートを取得する
		 */
		public function getRoot()
		{
			return \Config::get('default.root', getenv('FIREBASE_ROOT'));
		}


		/**
		 * ROOTパスをセットする
		 * @return \Firebase\FirebaseLib
		 */
		public function setRoot()
		{
			$this->_instance->setBaseURI($this->getRoot());
		}


		/**
		 * 取得する
		 * @param null $namespace
		 * @return $this
		 */
		public function get($namespace = null)
		{
			return $this->_instance->get($this->getRoot() . DS . $namespace);
		}


		/**
		 * 作成する
		 * @param null $namespace
		 * @param array $data
		 * @return null
		 */
		public function create($namespace = null, array $data = [])
		{
			$this->_instance->set($this->getRoot() . DS . $namespace, $data);

			return $this->_instance;
		}


		/**
		 * 更新する
		 * @param null $namespace
		 * @param array $data
		 * @return null
		 */
		public function update($namespace = null, array $data = [])
		{
			$this->_instance->update($this->getRoot() . DS . $namespace, $data);

			return $this->_instance;
		}

		/**
		 * PUSHする
		 * @param null $namespace
		 * @param array $data
		 * @return null
		 */
		public function push($namespace = null, array $data = [])
		{
			$this->_instance->push($this->getRoot() . DS . $namespace, $data);

			return $this->_instance;
		}

		/**
		 * 削除する
		 * @param null $namespace
		 * @return null
		 */
		public function delete($namespace = null)
		{
			$this->_instance->delete($this->getRoot() . DS . $namespace);

			return $this->_instance;
		}
	}
