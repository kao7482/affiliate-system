<?php
	//TODO 使い方調査

	/**
	 * GODADDYのDNSを切り替えることができる？
	 * 現状使っていない。
	 *
	 * The main class for sending and parsing server requests to the
	 * GoDaddy® TotalDNS management system. Eventually this class
	 * could split into multiple classes representing the various
	 * components such as the Service, Account, Zone, and Record(s).
	 */
	class GoDaddy
	{
		/**
		 * Class variables
		 */
		private $_config;
		private $_curlHandle;
		private $_lastResponse;

		/**
		 * Initialize the configuration array with configuration defaults.
		 */
		public function __construct($config = array())
		{
			// Apply default configuration settings
			$this->_config = array_merge(array(
				'cookie_file'                 => tempnam(sys_get_temp_dir(), 'Curl'),
				'auto_remove_cookie_file'     => false,
				'auto_logout'                 => false,
				'godaddy_dns_default_url'     => 'https://dns.godaddy.com/default.aspx',
				'godaddy_dns_zonefile_url'    => 'https://dns.godaddy.com/ZoneFile.aspx?zoneType=0&sa=&zone=',
				'godaddy_dns_zonefile_ws_url' => 'https://dns.godaddy.com/ZoneFile_WS.asmx',
				'hostip_api_url'              => 'http://api.hostip.info/',
			), $config);
		}

		/**
		 * Destroy the curl handle and unlink the cookies file.
		 */
		public function __destruct()
		{
			if ($this->_config['auto_logout'])
			{
				$this->logout();
			}
			if ($this->_curlHandle)
			{
				curl_close($this->_curlHandle);
			}
			if ($this->_config['auto_remove_cookie_file'] && file_exists($this->_config['cookie_file']))
			{
				unlink($this->_config['cookie_file']);
			}
		}

		/**
		 * Static convienence method which uses this class to implement the DNS
		 * Update API to allow compatible DDNS clients to set GoDaddy DNS records
		 * using HTTP requests.
		 *
		 * See: http://dyn.com/support/developers/api/perform-update/
		 *      http://dyn.com/support/developers/api/return-codes/
		 */
		public static function ddns($defaults = array())
		{
			// Merge the default request values with those passed into the function argument
			$defaults = array_merge(array(
				'username'           => '',
				'password'           => '',
				'hostname'           => '',
				'myip'               => '',
				'offline'            => false,
				'offline_ip'         => '127.0.0.1',
				'detect_external_ip' => false,
			), $defaults);

			// Get request values from HTTP data, falling back to the defaults above
			$username = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] :
				(isset($_REQUEST['user']) ? $_REQUEST['user'] : $defaults['username']);

			$password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] :
				(isset($_REQUEST['pw']) ? $_REQUEST['pw'] : $defaults['password']);

			$hostname = isset($_REQUEST['hostname']) ? $_REQUEST['hostname'] : $defaults['hostname'];

			$myip = isset($_REQUEST['myip']) ? $_REQUEST['myip'] :
				(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : $defaults['myip']);

			$offline = isset($_REQUEST['offline']) ? (strtoupper($_REQUEST['offline']) == 'YES') : $defaults['offline'];

			if ($hostname)
			{
				if ($username && $password)
				{
					// Instantiate the GoDaddyDNS class
					$dns = new self($defaults);
					if ($dns->authenticate($username, $password, $hostname))
					{
						if ($offline)
						{
							// Use offline IP
							$myip = $defaults['offline_ip'];
						}
						elseif ($defaults['detect_external_ip'] && $dns->isPrivateIp($myip))
						{
							if (($externalip = $dns->getPublicIp($myip)))
							{
								// Use detected external IP
								$myip = $externalip;
							}
						}
						// Attempt to update the hostname's A record
						if (($result = $dns->setRecord($hostname, $myip, 'A')))
						{
							echo (($result['last_ip'] == $result['new_ip']) ? 'nochg' : 'good') . ' ' . $result['new_ip'];
						}
						else
						{
							header('HTTP/1.1 500 Internal Server Error');
							echo '911';
						}
					}
					else
					{
						header('HTTP/1.1 403 Forbidden');
						echo 'badauth';
					}
				}
				else
				{
					header('WWW-Authenticate: Basic realm="Dynamic DNS"');
					header('HTTP/1.1 401 Unauthorized');
					echo 'noauth';
				}
			}
			else
			{
				header('HTTP/1.1 500 Internal Server Error');
				echo 'nohost';
			}
		}

		/**
		 * Login to the user's account, returning an error if the credentials are
		 * invalid or the login fails.
		 *
		 * A optional FQDN or TLD can be specified to log the user directly into
		 * a specific zone record (eliminates an additional page request).
		 */
		public function authenticate($username, $password, $hostname = null)
		{
			if ($hostname)
			{
				list($host, $domain) = $this->_splitHostname($hostname);
				$loginUrl = $this->_config['godaddy_dns_zonefile_url'] . $domain;
			}
			else
			{
				$loginUrl = $this->_config['godaddy_dns_default_url'];
			}
			$this->_lastResponse = $this->_fetchURL($loginUrl);
			if (!$this->isLoggedIn($username))
			{
				// User is not already logged in, build and submit a login request
				$postUrl = curl_getinfo($this->_curlHandle, CURLINFO_EFFECTIVE_URL);

				$post = array(
					'Login$userEntryPanel2$LoginImageButton.x' => 0,
					'Login$userEntryPanel2$LoginImageButton.y' => 0,
					'Login$userEntryPanel2$UsernameTextBox'    => $username,
					'Login$userEntryPanel2$PasswordTextBox'    => $password,
					'__EVENTARGUMENT'                          => $this->_getField('__EVENTARGUMENT'),
					'__EVENTTARGET'                            => $this->_getField('__EVENTTARGET'),
					'__VIEWSTATE'                              => $this->_getField('__VIEWSTATE'),
				);
				$this->_lastResponse = $this->_fetchURL($postUrl, $post);
				if (!$this->isLoggedIn($username, $this->_lastResponse))
				{
					// Invalid username/password or unknown response received
					return false;
				}
			}

			return true;
		}
		/**
		 * Check to see if the expected user is logged in.
		 */

//     
		public function isLoggedIn($username)
		{
			if (preg_match('#Welcome:&nbsp;<span id="ctl00_lblUser" .*?>(.*)</span>#', $this->_lastResponse, $match))
			{
				if (strtolower($match[1]) == strtolower($username) || $match[2] == $username)
				{
					return true;
				}
				else
				{
					// An unexpected user was logged in
					$this->logout();
				}
			}

			return false;
		}

		/**
		 * Log the user out.
		 */
		public function logout()
		{
			if (preg_match('#<a [^>]+href="(.*?)"[^>]*>Log Out</a>#', $this->_lastResponse, $match))
			{
				$this->_lastResponse = $this->_fetchURL($match[1]);
				if (preg_match('#<img src="([^"]+)" height="1" width="1" />#', $this->_lastResponse, $match))
				{
					$this->_lastResponse = $this->_fetchURL($match[1]);

					return true;
				}
			}

			return false;
		}

		/**
		 * Update a host record for the specified FQDN.
		 *
		 * Notes: The only type of records currently supported are "A" records.
		 *
		 *        Support for creating new records, updating other information
		 *        (such as the TTL), or submitting batch edit requests (instead
		 *        of handling them one at a time) could be added in the future.
		 */
		public function setRecord($hostname, $data, $type = 'A')
		{
			list($host, $domain) = $this->_splitHostname($hostname);
			switch (strtoupper($type))
			{
				case 'A':
					if (!($record = $this->_findRecord($host, $domain, $type)))
					{
						// Host record not found
						return false;
					}
					elseif ($record['data'] != $data)
					{
						// A record is out of date, build the query for updating it
						$post = array(
							'sInput' => '<PARAMS><PARAM name="type" value="arecord" /><PARAM name="fieldName" value="data" /><PARAM name="fieldValue" value="' . $data . '" /><PARAM name="lstIndex" value="' . $record['index'] . '" /></PARAMS>',
						);
						$calloutResponse = $this->_fetchURL($this->_config['godaddy_dns_zonefile_ws_url'] . '/EditRecordField', http_build_query($post, '', '&'));
						if (strpos($calloutResponse, 'SUCCESS') === false)
						{
							return false;
						}
						// Commit the updates
						$post = array(
							'sInput' => '<PARAMS><PARAM name="domainName" value="' . $domain . '" /><PARAM name="zoneType" value="0" /><PARAM name="aRecEditCount" value="1" /><PARAM name="aRecDeleteCount" value="0" /><PARAM name="aRecEdit0Index" value="' . $record['index'] . '" /><PARAM name="cnameRecEditCount" value="0" /><PARAM name="cnameRecDeleteCount" value="0" /><PARAM name="mxRecEditCount" value="0" /><PARAM name="mxRecDeleteCount" value="0" /><PARAM name="txtRecEditCount" value="0" /><PARAM name="txtRecDeleteCount" value="0" /><PARAM name="srvRecEditCount" value="0" /><PARAM name="srvRecDeleteCount" value="0" /><PARAM name="aaaaRecEditCount" value="0" /><PARAM name="aaaaRecDeleteCount" value="0" /><PARAM name="soaRecEditCount" value="0" /><PARAM name="soaRecDeleteCount" value="0" /><PARAM name="nsRecEditCount" value="0" /><PARAM name="nsRecDeleteCount" value="0" /></PARAMS>',
						);
						$calloutResponse = $this->_fetchURL($this->_config['godaddy_dns_zonefile_ws_url'] . '/SaveRecords', http_build_query($post, '', '&'));
						if (strpos($calloutResponse, 'SUCCESS') === false)
						{
							return false;
						}
					}

					// The update succeeded or no changes were needed
					return array(
						'last_ip' => $record['data'],
						'new_ip'  => $data,
					);
				case 'CNAME':
				case 'MX':
				case 'TXT':
				case 'SRV':
				case 'AAAA':
				case 'NS':
				default:
					// Other record types are currently unsupported
					throw new Exception('Unknown record type encountered: ' . $type);
			}
		}

		/**
		 * Find and return the details about a host record, return false if nothing is found.
		 *
		 * Note: The only type of records currently supported are "A" records.
		 */
		private function _findRecord($host, $domain, $type = 'A')
		{
			$currentZone = $this->_getField('ctl00$cphMain$hdnCurrentZone');
			if (strtolower($currentZone) != strtolower($domain))
			{
				// Request zone details if not already loaded - could keep a separate cache of each zone's records in the future
				$this->_lastResponse = $this->_fetchUrl($this->_config['godaddy_dns_zonefile_url'] . $domain);
			}
			if (!preg_match("#Undo{$type}Edit\('tbl{$type}Records_([0-9]+)?', '({$host})', '([^']+)?', '([^']+)?', '([^']+)?', '([^']+)?', '([^']+)?'\);#is", $this->_lastResponse, $match))
			{
				// Host details not found or user doesn't have permission to edit the zone
				return false;
			}

			return array_combine(array('match', 'index', 'host', 'data', 'ttl', 'host_td', 'points_to', 'rec_modified'), $match);
		}

		/**
		 * Uses the hostip.info API to detect and return the remote IP.
		 * Useful if this script is running from within a LAN.
		 */
		public function getPublicIp()
		{
			if (($response = $this->_fetchUrl($this->_config['hostip_api_url'])) &&
				($apiResult = simplexml_load_string($response)) &&
				($ip = $apiResult->xpath('gml:featureMember/Hostip/ip'))
			)
			{
				return (string)$ip[0];
			}
			else
			{
				return false;
			}
		}

		/**
		 * Checks to see whether an IP address is private (non-routable) or public.
		 */
		public function isPrivateIp($ip)
		{
			return (strpos($ip, '10.') === 0 ||
				strpos($ip, '127.') === 0 ||
				strpos($ip, '169.254.') === 0 ||
				strpos($ip, '172.16.') === 0 ||
				strpos($ip, '192.168.') === 0);
		}

		/**
		 * Determine the host and domain name components given a hostname - could add some sanity testing.
		 */
		private function _splitHostname($hostname)
		{
			$len = strlen($hostname);
			$tldPos = strrpos($hostname, '.', 0);
			$sldPos = strrpos($hostname, '.', ($tldPos - $len - 1));
			if ($sldPos !== false)
			{
				$host = substr($hostname, 0, $sldPos);
				$domain = substr($hostname, $sldPos + 1);
			}
			else
			{
				$host = '@';
				$domain = $hostname;
			}

			return array($host, $domain);
		}

		/**
		 * Connect to the remote server using CURL.
		 */
		private function _fetchURL($url, $post = null, $referer = '', $agent = 'Mozilla/5.0 (compatible; PHP; cURL)', $language = 'en', $timeout = 30)
		{
			// Initialize CURL
			if (!$this->_curlHandle)
			{
				if (!function_exists('curl_init'))
				{
					die('CURL is not loaded or compiled into this version of PHP.');
				}
				if (!is_writable($this->_config['cookie_file']))
				{
					die('Cookie jar file is not writable: ' . $this->_config['cookie_file']);
				}
				$this->_curlHandle = curl_init();
				curl_setopt_array($this->_curlHandle, array(
					CURLOPT_CONNECTTIMEOUT => $timeout,
					CURLOPT_TIMEOUT        => $timeout,
					CURLOPT_HEADER         => false,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_AUTOREFERER    => true,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_SSL_VERIFYHOST => false,
					CURLOPT_COOKIEJAR      => $this->_config['cookie_file'],
					CURLOPT_COOKIEFILE     => $this->_config['cookie_file'],
				));
			}
			// Set the options
			curl_setopt($this->_curlHandle, CURLOPT_URL, $url);
			curl_setopt($this->_curlHandle, CURLOPT_REFERER, $referer);
			curl_setopt($this->_curlHandle, CURLOPT_USERAGENT, $agent);
			$extraHeaders = array(
				'Accept-Language: ' . $language,
			);
			curl_setopt($this->_curlHandle, CURLOPT_HTTPHEADER, $extraHeaders);
			if ($post)
			{
				curl_setopt($this->_curlHandle, CURLOPT_POST, true);
				curl_setopt($this->_curlHandle, CURLOPT_POSTFIELDS, $post);
			}
			else
			{
				curl_setopt($this->_curlHandle, CURLOPT_HTTPGET, true);
			}

			// Execute the request, returning the results
			return curl_exec($this->_curlHandle);
		}

		/**
		 * Parse and return a named field's value from the last response.
		 */
		private function _getField($name)
		{
			if (preg_match_all('#<input[^>]+>#is', $this->_lastResponse, $matches, PREG_SET_ORDER))
			{
				foreach ($matches as $match)
				{
					$fieldHtml = $match[0];
					if ($this->_getFieldAttribute('name', $fieldHtml) == $name)
					{
						return $this->_getFieldAttribute('value', $fieldHtml);
					}
				}
			}

			return false;
		}

		/**
		 * Get the attribute from a field's html.
		 */
		private function _getFieldAttribute($attribute, $fieldHtml)
		{
			if (preg_match('#' . $attribute . '=["\']([^"\']+)?["\']#is', $fieldHtml, $match))
			{
				return $match[1];
			}

			return false;
		}
	}