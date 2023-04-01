<?php

namespace App\Api;

use Exception;

class ApiException extends Exception
{
	public function __construct($message = '', $code = 0, public array $data = [], Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}