<?php
	/**
	 *
	 */

	/**
	 *
	 */
	namespace Utility\Error;
	/**
	 * Class HttpInvalidInputException
	 * @package Utility\Error
	 */
	class HttpInvalidInputException extends \HttpException
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
