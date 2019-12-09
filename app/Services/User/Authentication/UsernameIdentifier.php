<?php

namespace App\Services\User\Authentication;

class UsernameIdentifier {

    protected $username;
    protected $passwordField;
    protected $password;
    protected $usernameField;
    protected $credentials;

    public function setUsername($value)
    {
        $this->username = strtolower($value);
        return $this;
    }

    public function setPasswordField($value)
    {
        $this->passwordField = $value;
        return $this;
    }

    public function setPassword($value)
    {
        $this->password = $value;
        return $this;
    }

    public function getUsernameField()
    {
        return $this->usernameField;
    }

    public function getCredentials()
    {
        return $this->credentials;
    }

    public function init()
    {
        if (filter_var($this->username, FILTER_VALIDATE_EMAIL)) {
            $this->usernameField = 'email';
        }else if (is_numeric($this->username)) {
            $this->usernameField = 'mobile';
        }else{
            $this->usernameField = 'username';
        }

        if ($this->passwordField) {
            $this->credentials = [$this->usernameField=> $this->username, $this->passwordField=> $this->password];
        }else {
            $this->credentials = [$this->usernameField=> $this->username, 'password'=> $this->password];
        }
        return $this;
    }
}