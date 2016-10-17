<?php
	/**
	 *
	 */
	namespace Utility\Error;

	/**
	 * Class SoapException
	 * @package Utility\Error
	 */
	class SoapException extends \RequestException
	{


		/**
		 * return a response object for the handle method
		 */
		public function response()
		{
			$uri = \Config::get('routes.error/403','error/403');
			$response = \Request::forge($uri)->execute()->response();

			return $response;
		}
	}
