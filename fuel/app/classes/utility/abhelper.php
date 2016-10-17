<?php

	namespace Utility;

	use Fuel\Core\Fuel;


	/**
	 * Class ABHelper
	 * [BACKEND]汎用関数
	 * 言語周り、タイム・ゾーン関連など
	 *
	 * @package Utility
	 */
	class ABHelper
	{


		/**
		 * コンストラクタ
		 */
		public function __construct()
		{
			//
		}


		/**
		 * デストラクタ
		 */
		public function __destruct()
		{
			//
		}


		/**
		 * instance
		 * @var null
		 */
		private static $helper = null;


		/**
		 * インスタンスを取得する
		 * @return null
		 */
		public static function getInstance()
		{
			if (\Utility\ABHelper::$helper == null)
			{
				\Utility\ABHelper::$helper = new \Utility\ABHelper();
			}

			return \Utility\ABHelper::$helper;
		}


		/**
		 * 性別のオプションを取得する
		 *
		 * @param bool $include_default
		 * @return array
		 */
		public static function getGenderAsArray($include_default = false)
		{
			$array = [];
			if ($include_default)
			{
				$array[null] = __('asianbet.msg.please_select', [], 'please select');
			}

			$array['male'] = __('asianbet.male', [], 'male');
			$array['female'] = __('asianbet.female', [], 'female');

			return $array;
		}


		/**
		 * マスクしたパスワードを取得する
		 * @param null $password
		 * @return string
		 */
		public static function getMaskedPassword($password = null)
		{
			$mask = '●';
			$masked_password = '';
			$password_length = strlen($password);
			if (empty($password))
			{
				$password_length = 7;
			}

			for ($i = 0; $i < $password_length; $i++)
			{
				$masked_password .= $mask;
			}

			return $masked_password;
		}


		/**
		 *　デバッグモードに設定する
		 */
		public static function setDebugMode()
		{
			//dbg mode
			$dbg_mode = \Input::get('dbg_mode', null);
			switch ($dbg_mode)
			{
				case 'on':
					\Session::set('dbg_mode', $dbg_mode);

					break;

				case 'off':
					\Session::delete('dbg_mode');

					break;

				case null:
				default:

					break;
			}

			return true;
		}


		/**
		 * デバッグモードに設定する
		 * @return mixed
		 */
		public static function getDebugMode()
		{
			$dbg_mode = \Session::get('dbg_mode', null) === 'on';
			\Fuel::$profiling = $dbg_mode;

			return $dbg_mode;
		}


		/**
		 * タイム・ゾーンを取得する
		 * @return mixed
		 */
		public static function getCurrentTimezone()
		{
			$default_timezone = \Config::get('default_timezone', 'Asia/Tokyo');
			$timezone = \Session::get('timezone', null);
			$timezone or $timezone = $default_timezone;

			return $timezone;
		}


		/**
		 * タイム・ゾーンを設定する
		 * @param null $timezone
		 * @return bool
		 */
		public static function setCurrentTimezone($timezone = null)
		{
			//server_gmt_offset
			$timezone or $timezone = static::getCurrentTimezone();
			\Session::set('timezone', $timezone);
			\Config::set('timezone', $timezone);

			return true;
		}


		/**
		 * タイム・ゾーン一覧を取得する
		 * @param null $key
		 * @return array
		 */
		public static function getTimezoneList($key = null)
		{
			$plus_diff = [];
			$minus_diff = [];
			$default_diff = [];
			$timezone_identifiers_list = null;
			$cache_identifier = 'timezone-list';

			try
			{
				$timezone_identifiers_list = \Cache::get($cache_identifier);
			}
			catch (\CacheNotFoundException $e)
			{
				//\Log::error($e);
			}
			catch (\Exception $e)
			{
				//\Log::error($e);
			}
			finally
			{

			}

			//キャッシュが存在する場合、キャッシュを使用する
			if (empty($timezone_identifiers_list))
			{
				try
				{
					\Cache::delete($cache_identifier);

					$timezone_identifiers_list = timezone_identifiers_list();
					if ($timezone_identifiers_list)
					{
						foreach ($timezone_identifiers_list as $timezone)
						{
							$offset = static::getTimezoneOffset(time(), $timezone, true);
							$diff = number_format($offset / 3600, 2, '.', '');

							if ($offset > 0)
							{
								if (!array_key_exists(intval($diff), $plus_diff) && intval($diff) !== 9)
								{
									$plus_diff[intval($diff)] = [
										'key'   => $timezone,
										'value' => $timezone . ' GMT ' . ($diff > 0 ? '+' : null) . $diff
									];
								}
							}
							else
							{
								if (!array_key_exists(intval($diff), $minus_diff))
								{
									$minus_diff[intval($diff)] = [
										'key'   => $timezone,
										'value' => $timezone . ' GMT ' . ($diff > 0 ? '+' : null) . $diff
									];
								}
							}
						}
					}

					$array = [];
					$default_timezone = \Config::get('default_timezone', 'Asia/Tokyo');
					$default_offset = static::getDefaultTimezoneOffset();
					$default_diff = number_format($default_offset / (60 * 60), 2, '.', '');
					$array[$default_timezone] = $default_timezone . ' GMT ' . ($default_diff > 0 ? '+' : null) . $default_diff;

					ksort($plus_diff);
					ksort($minus_diff);

					foreach ($minus_diff as $k => $v)
					{
						if ($v['key'] !== $default_timezone)
						{
							$array[$v['key']] = $v['value'];
						}
					}

					foreach ($plus_diff as $k => $v)
					{
						if ($v['key'] !== $default_timezone)
						{
							$array[$v['key']] = $v['value'];
						}
					}

					\Cache::set($cache_identifier, $array, 0);
				}
				catch (\Exception $e)
				{
					$timezone_identifiers_list = timezone_identifiers_list();
					\Log::error($e);
				}
				finally
				{

				}
			}

			if (!empty($key) && is_array($array) && array_key_exists($key, $array))
			{
				return $array[$key];
			}

			return $array;
		}


		/**
		 * デフォルトのタイム・ゾーンを取得する。
		 * @return int
		 */
		public static function getDefaultTimezoneOffset()
		{
			return 3600 * 9;
		}


		/**
		 * タイム・ゾーンの時差を取得する
		 * @param int $timestamp
		 * @param null $timezone
		 * @param bool $is_real
		 * @return int
		 */
		public static function getTimezoneOffset($timestamp = 0, $timezone = null, $is_real = false)
		{
			if (empty($timezone))
			{
				$timezone = static::getTimezone();
			}

			//Date::time()->format('mysql_date')
			//\Date::time()->format('mysql')
			//\Date::forge($timestamp)->get_timestamp(); $timestamp

			$datetime = \Date::forge($timestamp)->format('mysql');
//			$datetime = \Model_Base::getTimeToDateFormat($timestamp);
			$time = new \DateTime($datetime, new \DateTimeZone($timezone));

			$default_offset = static::getDefaultTimezoneOffset();

			return $is_real ? $time->getOffset() : $time->getOffset() - $default_offset;
		}


		/**
		 *　タイム・ゾーンを取得する
		 */
		public static function getTimezone()
		{
			$default_timezone = date_default_timezone_get();
			$ini_timezone = ini_get('date.timezone');
			if ($default_timezone !== $ini_timezone)
			{

			}

			return $ini_timezone;
		}


		/**
		 * 言語を設定する
		 * @param null $language
		 * @return null
		 */
		public static function setCurrentLanguage($language = null)
		{
			$language or $language = static::getCurrentLanguage();
			\Session::set('language', $language);
			\Config::set('language', $language);

			//locale
			$locale = \Config::get('locales.' . $language, \Config::get('locale', 'ja_JP'));
			\Config::set('locale', $locale);
		}


		/**
		 * 言語を取得する
		 * @return mixed
		 */
		public static function getCurrentLanguage()
		{
			$default_language = \Config::get('lang.default', \Config::get('language', 'ja'));
			$language = \Session::get('language', null);
			$language or $language = $default_language;

			return $language;
		}


		/**
		 * JSON　ENCODEの改良版。
		 * @param $data
		 * @return string
		 */
		public static function json_safe_encode($data)
		{
			return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
		}


		/**
		 * ランダムな文字列を取得する
		 * @return string
		 */
		public static function generateHashKey()
		{
			return sha1(uniqid(mt_rand(), true));
		}


		/**
		 * キャッシュIDを生成する。
		 * FUELPHPのキャッシュIDに指定できる文字列だけに自動的に変換することができる。
		 *
		 * @param null $key
		 * @param array $array
		 * @return mixed|null|string
		 * //Cache identifier can only contain alphanumeric characters, underscores, dashes & dots
		 */
		public static function createCacheIdentifier($key = null, $array = [])
		{
			$cache_identifier = $key;
			if (is_array($array) && !empty($array) && count($array))
			{
				$cache_identifier .= '-' . serialize($array);;
			}

			$cache_identifier = preg_replace('/[][}{)(!"#$%&\'~|\*+,\/@.\^<>`;:?_=\\\\]/i', '-', $cache_identifier);
			$cache_identifier = mb_ereg_replace("\---", '-', $cache_identifier);
			$cache_identifier = mb_ereg_replace("\--", '-', $cache_identifier);
			if (!preg_match("/^[a-zA-Z0-9-_.]+$/", $cache_identifier))
			{
				$cache_identifier = static::generateHashKey();
			}

			return $cache_identifier;
		}


		/**
		 * 配列を文字列に変換する
		 * もともと使いみちがない？
		 * 実装しなくて良い。
		 * @param null $obj
		 * @return array|null
		 */
		public static function arrayToString($obj = null)
		{
			//TODO AB
		}


		/**
		 * オブジェクトを配列に変換する
		 * @param null $obj
		 * @return array|null
		 */
		public static function objectToArray($obj = null)
		{
			if (false === is_object($obj) && false === is_array($obj))
			{
				return $obj;
			}

			if (true === is_object($obj))
			{
				$array = (array)$obj;
			}
			else
			{
				$array = $obj;
			}

			foreach ($array as &$a)
			{
				$a = static::objectToArray($a);
			}

			return $array;
		}


		/**
		 * 配列をオブジェクトに変換する
		 * @param array $array
		 * @return array|mixed
		 */
		public static function arrayToObject($array = [])
		{
			if (is_object($array))
			{
				return $array;
			}

			if (is_array($array))
			{
				return json_decode(json_encode($array));
			}

			return false;
		}


		/**
		 * GMAILの新着を確認する。
		 * @param null $username
		 * @param null $password
		 * @param string $specific_folder
		 * @return array|null
		 */
		public static function getGmail($username = null, $password = null, $specific_folder = 'INBOX')
		{
			$cache_identifier = 'gmail-check';
			$response = null;
//			\Cache::delete($cache_identifier);

			try
			{
				$response = \Cache::get($cache_identifier);
			}
			catch (\CacheNotFoundException $e)
			{
				//\Log::error($e);
			}
			catch (\Exception $e)
			{
				//\Log::error($e);
			}
			finally
			{

			}

			//キャッシュが存在する場合、キャッシュを使用する
			if (empty($response))
			{
				try
				{
					\Cache::delete($cache_identifier);
					$gmail = static::connectGmail($username, $password, $specific_folder);
					if (!empty($gmail))
					{
						$response = [
							'gmail_all_count'    => $gmail->countMessages(),
							'gmail_unread_count' => $gmail->countUnreadMessages(),
							'gmail'              => $gmail->getMessages(),
						];
					}

					\Cache::set($cache_identifier, $response, 60 * 1);
				}
				catch (\Exception $e)
				{
					$response = null;
					\Log::error($e);
				}
				finally
				{

				}
			}

			return $response;
		}


		/**
		 * GMAILに接続する
		 * @param null $username
		 * @param null $password
		 * @param string $specific_folder
		 * @return null|Gmail
		 */
		public static function connectGmail($username = null, $password = null, $specific_folder = 'INBOX')
		{
			$host = 'imap.googlemail.com';
			$encryption = 'ssl';

			if (empty($username))
			{
				$username = \Config::get('email.defaults.smtp.username', getenv('GOOGLE_USERNAME'));//"helpdesk.gmasia1000@gmail.com";
				$password = \Config::get('email.defaults.smtp.password', getenv('GOOGLE_PASSWORD'));//"12345678abcz";
			}

			if (empty($username) || empty($password))
			{
				return null;
			}

			$imap = new \Utility\Gmail($username, $password, $host, $port = 993, $encryption);
			//error
			if ($imap->isConnected() === false)
			{
//				die($imap->getError());
				return null;
			}

			$imap->selectFolder($specific_folder);

			return $imap;

//			$folders = $imap->getFolders();
//			foreach($folders as $folder)
//			{
//				var_dump($folder) ;
//			}
//			$allMessages = $imap->countMessages();
//			$unreadMessages = $imap->countUnreadMessages();
//			$emails = $imap->getMessages();
//			$imap->addFolder('archive');
//			$imap->moveMessage($emails[0]['id'], 'archive');
//			$imap->deleteMessage($emails[1]['id']);
		}


		/**
		 * 引用文章作成
		 * @param string $quotation
		 * @param string $message
		 * @return string
		 */
		public static function quoteMessage($quotation = '', $message = '')
		{
			$message = $quotation . $message;

			return str_replace(["\r\n", "\n\r", "\r"], PHP_EOL . $quotation, $message) . PHP_EOL;
		}


		/**
		 * 画像のアップロード
		 * @param array $login_auth
		 * @return array|null
		 * @throws \Exception
		 */
		public static function imgUpload($login_auth = [])
		{
			if (empty($login_auth))
			{
				throw new \InvalidArgumentException();
			}

			//TODO AB この先はABV2仕様に修正する必要がある。
			\Upload::process(
				[
					'path'          => DOCROOT . '/assets/img' . \Model_Upload::$upload_dir,//\Config::get('assets.url') . 'img' . DS . \Model_Upload::$upload_dir,//
					'auto_rename'   => true,
					'randomize'     => true,
					'ext_whitelist' => ['img', 'jpg', 'jpeg', 'gif', 'png'],
				]
			);

			$is_upload = count(\Upload::get_files()) > 0;
			if (false === $is_upload)
			{
				return [];
			}

			if (!\Upload::is_valid())
			{
				throw new \Exception(ValidateException);
			}

			if (!\Upload::save())
			{
				throw new \RuntimeException('upload is failed');
			}

			$result = [];
			$upload_files = \Upload::get_files();
			for ($i = 0; $i < count($upload_files); $i++)
			{
				$field = $upload_files[$i]['field'];
				$info = serialize($upload_files[$i]);
				$upload_files[$i] = [
					'field'             => $field,
					'info'              => $info,
					'folder'            => DOCROOT . \Model_Upload::$upload_dir,
					'created_id'        => $login_auth->id,
					'updated_id'        => $login_auth->id,
					'created_master_id' => $login_auth->master_id,
					'updated_master_id' => $login_auth->master_id,
				];

				$result[$field] = \Model_Upload::forge($upload_files[$i]);
				if (!$result[$field])
				{
					return null;
				}

				$result[$field]->save();
			}
			unset($upload_files);

			return $result;
		}


		/**
		 * FTP操作
		 * @param null $hostname
		 * @param null $username
		 * @param null $password
		 * @param int $port
		 * @return bool|null
		 * @throws \Exception
		 */
		public static function ftp($hostname = null, $username = null, $password = null, $port = 21)
		{
			if (empty($hostname) || empty($username) || empty($password) || empty($port))
			{
				return null;
			}

			// FTP接続設定
			$config = [
				'hostname' => $hostname,
				'username' => $username,
				'password' => $password,
				'timeout'  => 90,
				'port'     => $port,
				'passive'  => true,
				'ssl_mode' => false,
				'debug'    => false,
			];

			try
			{
				// FTP接続実行
				$ftp = \Ftp::forge($config);
				if (!$ftp->connect())
				{
					throw new \Exception();
				}

				return $ftp;
			}
			catch (Exception $e)
			{
				\Log::error($e);
			}
			finally
			{

			}

			return null;
		}


		/**
		 * EMAILを送信する
		 * @param null $from_addr
		 * @param null $from_name
		 * @param null $to_email
		 * @param null $subject
		 * @param null $body
		 * @param string $enc
		 * @param array $attachment
		 * @return bool
		 */
		public static function sendEmail($from_addr = null, $from_name = null, $to_email = null, $subject = null, $body = null, $enc = 'jis', $attachment = [])
		{
			if (!\Package::loaded('email'))
			{
				\Package::load('email');
			}

			if (empty($from_addr) || empty($from_name) || empty($to_email) || empty($subject) || empty($body))
			{
				return false;
			}

			try
			{
				$email = \Email::forge($enc);
				$email->from($from_addr, $from_name);
				$email->to($to_email);
				$email->subject($subject);
				$email->body(mb_convert_encoding($body, $enc));
				if (!empty($attachment) && count($attachment))
				{
					foreach ($attachment as $attach)
					{
						if (file_exists($attach))
						{
							$email->attach($attach);
						}
					}
				}

				$email->send();

				return true;
			}
				/*
				catch (\EmailValidationFailedException $e) {
					//
				}
				catch (\EmailSendingFailedException $e) {
					//
				}
				*/
			catch (Exception $e)
			{
				\Log::error($e);
			}
			finally
			{

			}

			unset($email);

			return false;
		}


		/**
		 * 配列を改行で連結して文字列として返す
		 * @param array $array
		 * @return array|string
		 */
		public static function arrayToTextAreaVal($array = [])
		{
			if (!is_array($array))
			{
				return $array;
			}

			if (0 === count($array))
			{
				return $array;
			}

			$val = '';
			foreach ($array as $str)
			{
				$val .= $str . "\n";
			}
			unset($array);

			return $val;
		}


		/**
		 * 文字列の値を配列にする
		 * @param null $text
		 * @return array|null
		 */
		public static function TextAreaValToArray($text = null)
		{
			if (empty($text))
			{
				return $text;
			}

			$array = explode("\n", $text); // とりあえず行に分割
			$array = array_map('trim', $array); // 各要素をtrim()にかける
			$array = array_filter($array, 'strlen'); // 文字数が0のやつを取り除く
			$array = array_values($array); // これはキーを連番に振りなおしてるだけ
			unset($text);

			return $array;
		}


		/**
		 * 文字列の値をエスケープ
		 * @param string $text
		 * @return string
		 */
		public static function escapeTextArea($text = "")
		{
			$text = htmlspecialchars($text);
			if (get_magic_quotes_gpc())
			{
				$text = stripslashes($text);
			}

			return $text;
		}


		/**
		 * 時間変換
		 * @param $unix
		 * @return string
		 */
		public static function timeLabel($unix = 0)
		{
			$unix = intval($unix);
			$unix = strtotime(substr(date("Y-m-d H:i:s", $unix), 0, 10) . " 00:00:00");

			$now = time();
			$timezone = static::getCurrentTimezone();
			$offset = static::getTimezoneOffset($now, $timezone);
			$now = $now + $offset;
			$diff_sec = $now - $unix;
			$date = '';
			$week = date('D', $unix);
			if ($diff_sec < 2764800)
			{
				$date = date("n/d", $unix);
			}
			else
			{
				if (date("Y") != date("Y", $unix))
				{
					$date = date("Y/n/d", $unix);
				}
				else
				{
					$date = date("n/d", $unix);
				}
			}

			return $date . " (" . $week . ")";
		}


		/**
		 * n分前　のようなことができる。
		 * @param $unix
		 * @param bool $is_countdown
		 * @return bool|float|int|string　
		 */
		public static function convertFuzzyTime($unix = 0, $is_countdown = false)
		{
			$now = time();
			$timezone = static::getCurrentTimezone();
			$offset = static::getTimezoneOffset($now, $timezone);
			$now = $now + $offset;

			if ($is_countdown)
			{
				$diff_sec = $unix - $now;
			}
			else
			{
				$diff_sec = $now - $unix;
			}

			if ($diff_sec < 60)
			{
				$time = $diff_sec;
				$unit = __('asianbet.second') . __('asianbet.ago');
			}
			elseif ($diff_sec < 3600)
			{
				$time = $diff_sec / 60;
				$unit = __('asianbet.minute') . __('asianbet.ago');
			}
			elseif ($diff_sec < 86400)
			{
				$time = $diff_sec / 3600;
				$unit = __('asianbet.hour') . __('asianbet.ago');
			}
			elseif ($diff_sec < 2764800)
			{
				$time = $diff_sec / 86400;
				$unit = __('asianbet.day') . __('asianbet.ago');
			}
			else
			{
				if (date("Y") != date("Y", $unix))
				{
					$time = date("Y" . __('asianbet.year') . "n" . __('asianbet.month') . "j" . __('asianbet.day'), $unix);
				}
				else
				{
					$time = date("n" . __('asianbet.month') . "j" . __('asianbet.day'), $unix);
				}

				return $time;
			}

			return (int)$time . $unit;
		}


		/**
		 * 旧論理のアクセスコントロール用。
		 *
		 * @param null $path
		 * @return bool
		 */
		public static function hasACL($path = null)
		{
			if (empty($path) || !is_string($path))
			{
				return false;
			}
			//暫定だけど、ここで意図せず false になるならプログラムミス。

			$slash = '/';
			$delimiter = '.';
			$path = str_replace($slash, $delimiter, $path);

			return \Auth::has_access($path);
		}


		/**
		 * ログイン情報のセット
		 * @return bool
		 */
		public static function setLoginAuth()
		{
			$session_array = ['id', 'username', 'first_name', 'last_name', 'email', 'tel', 'group_id', 'user_id', 'created_id', 'updated_id', 'entity_id', 'is_alias'];
			$id = \Auth::get('id', null);
			$user = \Model_User::findReal($id);
			if (empty($user))
			{
				return false;
			}

			$login_auth = [];
			$login_auth['entity_level'] = $user->entity->entity_level;
			$login_auth['entity_options'] = $user->entity->entity_options;
			foreach ($user as $k => $v)
			{
				if (in_array($k, $session_array))
				{
					$login_auth[$k] = $v;
				}
			}

			$entity_vendors = \Model_User::getEntityVendor($user);
			foreach ($entity_vendors as $entity_vendor)
			{
				$entity_vendor = \Model_EntityVendor::findRealWithStatus($entity_vendor);
				if (!empty($entity_vendor))
				{
					$login_auth['vendors'][$entity_vendor->vendor_id] = [$entity_vendor->vendor->name, $entity_vendor->balance];
				}
			}

//			$login_auth['group_name'] = static::getGroupName($login_auth['group'], true);//グループ名
//			$login_auth['is_agent'] = intval($login_auth['group']) === AGENT;
			//$login_auth = json_decode(json_encode($login_auth));
			$login_auth = static::arrayToObject($login_auth);
			\Session::set('login_auth', $login_auth);
			unset($session_array, $login_auth, $query);

			return true;
		}


		/**
		 * ログインチェック、ログイン情報の取得
		 * @param null $key_field
		 * @return mixed|null
		 */
		public static function getLoginAuth($key_field = null)
		{
			$id = \Auth::get('id', null);
			$login_auth = \Session::get('login_auth', null);
			if (!\Auth::check() || empty($id) || empty($login_auth))
			{
				return null;
			}

			$login = \Model_User::findReal($login_auth);
			if (empty($login))
			{
				return null;
			}

			if ($key_field)
			{
				return \Model_User::getValue($id, $key_field);
			}

			$update_interval = 600;
			$last_login = time();
			$previous_last_login = \Session::get('last_login', 0);
			if ($previous_last_login + $update_interval < $last_login)
			{
				\Session::set('last_login', $last_login);
				if (!empty($login))
				{
					$login->last_login = $last_login;
					$login->ip_address = \Input::ip();
					$login->user_agent = \Input::user_agent();
					$login->save();
				}
			}

			return $login_auth;
		}


		/**
		 * 配列をフィルタする
		 * @param array $param
		 * @return array
		 */
		public static function filterArray($param = [])
		{
			if (is_null($param))
			{
				$array = [];
			}
			else
			{
				if (is_numeric($param))
				{
					$array = [$param];
				}
				else
				{
					if (is_array($param))
					{
						$array = $param;
					}
					else
					{
						$array = explode(',', $param);
					}
				}
			}

			$array = array_filter($array, "strlen");
			$array = array_values($array);

			return $array;
		}


		/**
		 * 権限に合わせてアバターを返す
		 * @param null $group
		 * @param array $attr
		 * @param string $type
		 * @return string
		 */
		public static function getAvatar($group = null, $attr = [], $type = 'file')
		{
			if (empty($group))
			{
				return '';
			}

			if (!is_array($attr) || 0 === count($attr))
			{
				$attr = ['alt' => 'user image', 'class' => 'img-circle'];
			}

			$path = "/avatars/" . strtolower(\Auth::group('Ormgroup')->get_name($group)) . '.png';
			if ($type === 'path')
			{
				return \Config::get('asset.url', null) . DS . \Config::get('asset.img_dir', 'assets/img') . DS . $path;
//				return $path;
//				return \Asset::get_file($path, 'img');
			}

			return \Asset::img($path, $attr);
		}


		/**
		 * グループ名を取得する
		 * @param null $group
		 * @param bool $type
		 * @return string
		 */
		public static function getGroupName($group = null, $type = false)
		{
			if (empty($group))
			{
				return '';
			}

			$name = \Auth::group('Ormgroup')->get_name($group);
			if ($type === false)
			{
				return $name;
			}

			return __('asianbet.auth.' . \Str::lower($name));
		}


		/**
		 * CURLリクエストを生成する。
		 * @param null $url
		 * @param array $params
		 * @param string $method
		 * @param array $options
		 * @return \Fuel\Core\Response|mixed|null
		 */
		public static function getExternalData($url = null, $params = [], $method = 'post', $options = [])
		{
			if (empty($url))
			{
				return null;
			}

			$curl_options = [
				'SSL_VERIFYPEER' => false,
				'SSL_VERIFYHOST' => false,
				'RETURNTRANSFER' => true,
				'TIMEOUT'        => 60,
			];

			try
			{
				$curl = \Request::forge($url, 'curl');
				$curl->set_options(\Fuel\Core\Arr::merge($curl_options, $options));
				$curl->set_method($method);
				$curl->set_params($params);
				$response = $curl->execute()->response();
				$response = json_decode($response);

				return $response;
			}
			catch (\Exception $e)
			{
				\Log::error($e);
			}
			finally
			{
				//
			}

			return null;
		}


		/**
		 * TASKに追加する
		 * @param null $class
		 * @param null $command
		 * @param array $args
		 * @return bool|void
		 * @throws \FuelException
		 * @throws \PackageNotFoundException
		 */
		public static function addToTask($class = null, $command = null, array $args = [])
		{
			if (empty($class) || empty($command))
			{
				return false;
			}

			if (!\Package::loaded('background'))
			{
				\Package::load('background');
			}

			$background = \Background\Background::forge();
			$background->add_task($class . ':' . $command, $args);

			return $background->run();
		}


		/**
		 * サブドメインなどを除外したドメインを取得します。
		 *
		 * @param null $domain
		 * @return bool|null
		 */
		public static function getRealDomain($domain = null)
		{
			$domain or $domain = getenv('HTTP_HOST');
			if(empty($domain))
			{
				return null;
			}

			$array = array($domain);
			for ($i = 0; $i < count($array); $i++)
			{
				preg_match_all('/\.([a-z0-9\-_]*)/m', $array[$i], $array2);
				switch (count($array2[0]))
				{
					case 0:
						return null;

					case 1:
						break;

					case 2:
						preg_match_all('/\.(co|or|gr|ne|go|lg|ac|ed|ad)$/m', $array2[0][0], $array3);
						if (count($array3[0]) == 0)
						{
							$array[$i] = preg_replace('/^\./i', '', $array2[0][0] . $array2[0][1]);
						}
						break;

					default:
						$count = count($array2[0]);
						preg_match_all('/\.(co|or|gr|ne|go|lg|ac|ed|ad)$/m', $array2[0][($count - 2)], $array3);
						if (count($array3[0]) == 0)
						{
							$array[$i] = preg_replace('/^\./i', '', $array2[0][($count - 2)] . $array2[0][($count - 1)]);
						}
						else
						{
							$array[$i] = preg_replace('/^\./i', '', $array2[0][($count - 3)] . $array2[0][($count - 2)] . $array2[0][($count - 1)]);
						}
				}
			}

			return isset($array[0]) ? $array[0] : null;
		}
	}
