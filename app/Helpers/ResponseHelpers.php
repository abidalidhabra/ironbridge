<?php

namespace App\Helpers;

class ResponseHelpers {
	
	public static function errorResponse($error, $code = 500)
	{
        return response()->json(['message'=> $error->getMessage().' on '.$error->getLine().' of '.$error->getFile()], $code);
	}

	public static function validationErrorResponse($error)
	{
		return response()->json(['message'=> $error->getMessage()], 422);
	}

	public static function successResponse($data)
	{
        return response()->json(['message'=> 'OK', 'data'=> $data]);
	}
}