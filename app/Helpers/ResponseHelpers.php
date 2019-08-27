<?php

namespace App\Helpers;

class ResponseHelpers {
	
	public static function errorResponse($error)
	{
        return response()->json(['message'=> $error->getMessage().' on '.$error->getLine().' of '.$error->getFile()], 500);
	}

	public static function successResponse($data)
	{
        return response()->json(['message'=> 'OK', 'data'=> $data]);
	}
}