<?php

namespace App\Exceptions;

use Exception;

class AppNotUpdatedException extends Exception {
	
	public $exceptionCode;


    /**
     * @return mixed
     */
    public function getExceptionCode()
    {
        return $this->exceptionCode;
    }

    /**
     * @param mixed $exceptionCode
     *
     * @return self
     */
    public function setExceptionCode($exceptionCode)
    {
        $this->exceptionCode = $exceptionCode;

        return $this;
    }
}