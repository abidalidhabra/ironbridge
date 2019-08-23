<?php

namespace App\Helpers;

class ExceptionHelpers {
	
	public static function getResourceResponse($error)
	{
        return response()->json(['message'=> $error->getMessage().' on '.$error->getLine().' of '.$error->getFile()]);
	}
}