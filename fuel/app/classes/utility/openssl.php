<?
	/**
	 *
	 */
	namespace Utility;

	/**
	 * OPENSSL暗号化処理
	 * Class Common
	 * @package Util
	 */
	class OpenSSL
	{


		/**
		 * @var null
		 */
		protected static $method = null;


		/**
		 * @var string
		 */
		protected static $password = '#4r&rlF;:&t9JG32k?';


		/**
		 * @var string
		 */
		protected static $iv = '4698374298765432';


		/**
		 * @return array
		 */
		protected static function getMethods()
		{
			return array(
				'0'  => 'AES-128-CBC',
				'1'  => 'AES-128-CFB',
				'2'  => 'AES-128-CFB1',
				'3'  => 'AES-128-CFB8',
				'4'  => 'AES-128-CTR',
				'5'  => 'AES-128-ECB',
				'6'  => 'AES-128-OFB',
				'7'  => 'AES-128-XTS',
				'8'  => 'AES-192-CBC',
				'9'  => 'AES-192-CFB',
				'10' => 'AES-192-CFB1',
				'11' => 'AES-192-CFB8',
				'12' => 'AES-192-CTR',
				'13' => 'AES-192-ECB',
				'14' => 'AES-192-OFB',
				'15' => 'AES-256-CBC',
				'16' => 'AES-256-CFB',
				'17' => 'AES-256-CFB1',
				'18' => 'AES-256-CFB8',
				'19' => 'AES-256-CTR',
				'20' => 'AES-256-ECB',
				'21' => 'AES-256-OFB',
				'22' => 'AES-256-XTS',
				'23' => 'BF-CBC',
				'24' => 'BF-CFB',
				'25' => 'BF-ECB',
				'26' => 'BF-OFB',
				'27' => 'CAMELLIA-128-CBC',
				'28' => 'CAMELLIA-128-CFB',
				'29' => 'CAMELLIA-128-CFB1',
				'30' => 'CAMELLIA-128-CFB8',
				'31' => 'CAMELLIA-128-ECB',
				'32' => 'CAMELLIA-128-OFB',
				'33' => 'CAMELLIA-192-CBC',
				'34' => 'CAMELLIA-192-CFB',
				'35' => 'CAMELLIA-192-CFB1',
				'36' => 'CAMELLIA-192-CFB8',
				'37' => 'CAMELLIA-192-ECB',
				'38' => 'CAMELLIA-192-OFB',
				'39' => 'CAMELLIA-256-CBC',
				'40' => 'CAMELLIA-256-CFB',
				'41' => 'CAMELLIA-256-CFB1',
				'42' => 'CAMELLIA-256-CFB8',
				'43' => 'CAMELLIA-256-ECB',
				'44' => 'CAMELLIA-256-OFB',
				'45' => 'CAST5-CBC',
				'46' => 'CAST5-CFB',
				'47' => 'CAST5-ECB',
				'48' => 'CAST5-OFB',
				'49' => 'DES-CBC',
				'50' => 'DES-CFB',
				'51' => 'DES-CFB1',
				'52' => 'DES-CFB8',
				'53' => 'DES-ECB',
				'54' => 'DES-EDE',
				'55' => 'DES-EDE-CBC',
				'56' => 'DES-EDE-CFB',
				'57' => 'DES-EDE-OFB',
				'58' => 'DES-EDE3',
				'59' => 'DES-EDE3-CBC',
				'60' => 'DES-EDE3-CFB',
				'61' => 'DES-EDE3-CFB1',
				'62' => 'DES-EDE3-CFB8',
				'63' => 'DES-EDE3-OFB',
				'64' => 'DES-OFB',
				'65' => 'DESX-CBC',
				'66' => 'IDEA-CBC',
				'67' => 'IDEA-CFB',
				'68' => 'IDEA-ECB',
				'69' => 'IDEA-OFB',
				'70' => 'RC2-40-CBC',
				'71' => 'RC2-64-CBC',
				'72' => 'RC2-CBC',
				'73' => 'RC2-CFB',
				'74' => 'RC2-ECB',
				'75' => 'RC2-OFB',
				'76' => 'RC4',
				'77' => 'RC4-40',
				'78' => 'RC4-HMAC-MD5',
				'79' => 'SEED-CBC',
				'80' => 'SEED-CFB',
				'81' => 'SEED-ECB',
				'82' => 'SEED-OFB',
			);
		}


		/**
		 * __construct
		 */
		public function __construct()
		{
			//static::createIV();
		}


		/**
		 * __destruct
		 */
		public function __destruct()
		{
			//
		}


		/**
		 * @var null
		 */
		private static $openssl = null;


		/**
		 * @return null
		 */
		public static function getInstance()
		{
			if (\Utility\OpenSSL::$openssl == null)
			{
				\Utility\OpenSSL::$openssl = new \Utility\OpenSSL();
			}

			return \Utility\OpenSSL::$openssl;
		}


		/**
		 * @param $key
		 * @return null
		 */
		public static function setMethod($key = null)
		{
			$array = static::getMethods();
			if (!array_key_exists($key, $array))
			{
				$key = '1';
			}

			static::$method = $array[$key];

			return static::$method;
		}


		/**
		 * IVを生成
		 */
		public static function createIV()
		{
			$str_len = static::cipherIvLength(static::$method);
			$str = function () use ($str_len)
			{
				$code = '';
				for ($i = 0; $i < $str_len; $i++)
				{
					$code .= mt_rand(0, 9);
				}
			};

			static::$iv = $str();
		}


		/**
		 * 暗号化
		 * @param string $action
		 * @param null $input
		 * @param null $method
		 * @param null $password
		 * @param bool $raw_output
		 * @param null $iv
		 * @return null|string
		 */
		public static function run($action = 'encrypt', $input = null, $method = null, $password = null, $raw_output = false, $iv = null)
		{
			if (!$input)
			{
				return null;
			}

			if (!$method)
			{
				$method = static::setMethod('0');
			}

			if (!$password)
			{
				$password = static::$password;
			}

			if (!$iv)
			{
				$iv = static::$iv;
			}

			if (strtolower($action) === 'encrypt')
			{
				return openssl_encrypt($input, $method, $password, $raw_output, $iv);
			}
			else
			{
				return openssl_decrypt($input, $method, $password, $raw_output, $iv);
			}
		}


		/**
		 * IVの長さ取得
		 * @param null $method
		 * @return int
		 */
		public static function cipherIvLength($method = null)
		{
			//iv の長さは　メソッドによって異なるので、下記で調べることができる
			//echo openssl_cipher_iv_length('bf-cbc'), PHP_EOL;
			//echo openssl_cipher_iv_length('aes-128-cbc'), PHP_EOL;
			return openssl_cipher_iv_length($method);
		}


		/**
		 * @param bool $alias
		 * @return array
		 * 暗号化メソッド一覧取得
		 */
		public static function getCipherMethods($alias = false)
		{
			return openssl_get_cipher_methods($alias);
		}
	}
