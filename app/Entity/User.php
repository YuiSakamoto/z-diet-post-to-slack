<?php

namespace ZDiet\Entity;

/**
 * Created by IntelliJ IDEA.
 * User: yui_tang
 * Date: 1/20/16
 * Time: 19:59
 */
class User
{

    public $userid;
    protected $name;
    protected $oauth_token;
    protected $oauth_token_secret;

    public function __construct($values)
    {
        $this->userid = $values[0];
        $this->name = $values[3];
        $this->oauth_token = $values[1];
        $this->oauth_token_secret = $values[2];
    }

    public function getUserId()
    {
        return $this->userid;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOauthToken()
    {
        return $this->oauth_token;
    }

    public function getOauthTokenSecret()
    {
        return $this->oauth_token_secret;
    }
}