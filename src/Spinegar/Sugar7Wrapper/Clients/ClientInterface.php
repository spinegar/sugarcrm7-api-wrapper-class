<?php namespace Spinegar\Sugar7Wrapper\Clients;

interface ClientInterface {
    public function connect();

    public function check();

    public function setUsername($value);

    public function setPassword($value);

    public function setUrl($url);

    public function setPlatform($platform);

    public function setClientOption($key, $value);

    public function get($endpoint, $paramters);

    public function post($endpoint, $paramters);

    public function put($endpoint, $paramters);

    public function delete($endpoint, $paramters);
}