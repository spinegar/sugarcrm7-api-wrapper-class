<?php namespace Spinegar\Sugar7Wrapper\Clients;

interface ClientInterface {
    public function connect();

    public function check();

    public function setUsername($value);

    public function setPassword($value);

    public function setUrl($url);

    public function setPlatform($platform);

    public function setClientOption($key, $value);

    public function get($endpoint, $parameters);

    public function post($endpoint, $parameters);

    public function put($endpoint, $parameters);

    public function delete($endpoint, $parameters);
}