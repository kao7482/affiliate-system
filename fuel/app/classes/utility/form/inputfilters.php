<?php

	namespace Utility\Form;

	/**
	 *
	 * Class InputFilters
	 * @package Utility\Form
	 */
	class InputFilters
	{


		/**
		 * @param $value
		 * @return mixed
		 * @throws \Utility\Error\HttpInvalidInputException
		 */
		public static function check_encoding($value)
		{
			if (is_array($value))
			{
				array_map(array('Utility\Form\InputFilters', 'check_encoding'), $value);

				return $value;
			}

			if (mb_check_encoding($value, \Fuel::$encoding))
			{
				return $value;
			}
			else
			{
				static::log_error('Invalid character encoding', $value);
				throw new \Utility\Error\HttpInvalidInputException('Invalid input data');
			}
		}


		/**
		 * @param $value
		 * @return mixed
		 * @throws \Utility\Error\HttpInvalidInputException
		 */
		public static function check_control($value)
		{
			if (is_array($value))
			{
				array_map(array('Utility\Form\InputFilters', 'check_control'), $value);

				return $value;
			}

			if (preg_match('/\A[\r\n\t[:^cntrl:]]*\z/u', $value) === 1)
			{
				return $value;
			}
			else
			{
				static::log_error('Invalid control characters', $value);
				throw new \Utility\Error\HttpInvalidInputException('Invalid input data');
			}
		}


		/**
		 * Standardize Newline with \n
		 *
		 * @param string|array $value
		 * @return string
		 */
		public static function standardize_newline($value)
		{
			if (is_array($value))
			{
				array_map(array('Utility\Form\InputFilters', 'standardize_newline'), $value);

				return $value;
			}

			if (strpos($value, "\r") !== false)
			{
				$value = str_replace(array("\r\n", "\r"), "\n", $value);
			}

			return $value;
		}


		/**
		 * @param $msg
		 * @param $value
		 * error log
		 */
		public static function log_error($msg, $value)
		{
			\Log::error(
				$msg . ': ' . \Input::uri() . ' ' . urlencode($value) . ' ' .
				\Input::ip() . ' "' . \Input::user_agent() . '"'
			);
		}
	}
