<?php namespace Spinegar\SugarRestClient\Clients;

use GuzzleHttp\Client;

/**
 * SugarCRM 7 Rest Client
 *
 * @package   SugarCRM 7 Rest Wrapper
 * @category  Libraries
 * @author  Sean Pinegar
 * @license MIT License
 * @link   https://github.com/spinegar/sugarcrm7-api-wrapper-class
 */

class Guzzle implements ClientInterface {

  /**
  * Variable: $url
  * Description:  A Sugar Instance. 
  */
  private $url;

  /**
  * Variable: $username
  * Description:  A SugarCRM User. 
  */
  private $username;

  /**
  * Variable: $password
  * Description:  The password for the $username SugarCRM account
  */
  private $password;

  /**
  * Variable: $platform
  * Description:  A Sugar Instance. 
  */
  private $platform = 'api';

  /**
  * Variable: $token
  * Description:  OAuth 2.0 token
  */
  protected $token;

  /**
  * Variable: $client
  * Description:  Guzzle Client
  */
  protected $client;

  /**
  * Variable: $options
  * Description:  Guzzle Client Options
  */
  private $options = array();

  /**
  * Function: __construct()
  * Parameters: none
  * Description: Construct Class
  * Returns: VOID
  */
  function __construct(){}

  /**
  * Function: __destruct()
  * Parameters: none
  */
  function __destruct(){}

  /**
   * Function: getGuzzleConfig
   * Parameters: none
   * Description: Returns a composited config for creating Guzzle client.
   * Returns: array.
   */
  function getGuzzleConfig()
  {
    // Note from thomasez: We can drop this if we drop the specific parameters
    // above and just set the options directly from within setUrl & co.
    $config = $this->options;
    if ($this->url)
        $config['base_uri'] = $this->url;
    return $config;
  }

  /**
   * Function: getClient
   * Parameters: none
   * Description: Return a client. Creates a client if it has not been created
   *              already.
   * Returns: client
   */
  function getClient()
  {
    if ($this->client) {
        return $this->client;
    }

    $this->client = new Client($this->getGuzzleConfig());

    return $this->client;
  }

  /**
   * Function: getNewAuthToken
   * Parameters: none
   * Description: Retrieve access token from OAuth server
   * Returns: token on success, otherwise null
   */
  public function getNewAuthToken()
  {
    $response = $this->getClient()->post('oauth2/token', array(
        'json' => array(
            'grant_type' => 'password',
            'client_id' => 'sugar',
            'client_secret' => '',
            'username' => $this->username,
            'password' => $this->password,
            'platform' => $this->platform,
        )
    ));

    $result = json_decode($response->getBody(), true);
    return $result['access_token'];
  }

  /**
  * Function: connect()
  * Parameters: none
  * Description: Authenticate and set the oAuth 2.0 token
  * Returns: TRUE on login success, otherwise FALSE
  */
  public function connect()
  {
    $token = $this->getNewAuthToken();

    if (!$token) return false;

    return self::setToken($token);
  }

  /**
  * Function: check()
  * Parameters: none
  * Description: check if token is set
  * Returns: TRUE on login success, otherwise FALSE
  */
  public function check()
  {
    if(!$this->token) return false;

    return $this->token;
  }

  /**
  * Function: setClientOption()
  * Parameters: $key = Guzzle option, $value = Value
  * Description:  Set Default options for the Guzzle client.
  * Returns:  returns FALSE is falsy, otherwise TRUE
  */
  public function setClientOption($key, $value)
  {
    return $this->options[$key] = $value;
  }

  /**
  * Function: setUrl()
  * Parameters: $value = URL for the REST API
  * Description:  Set $url
  * Returns:  returns FALSE is falsy, otherwise TRUE
  */
  public function setUrl($value)
  {
    return $this->url = $value;
  }

  /**
  * Function: getUrl()
  * Description:  Set $url
  * Returns:  returns a value if successful, otherwise FALSE
  */
  public function getUrl()
  {
    return $this->url;
  }

  /**
  * Function: setUsername()
  * Parameters:   $value = Username for the REST API User
  * Description:  Set $username
  * Returns:  returns FALSE is falsy, otherwise TRUE
  */
  public function setUsername($value)
  {
    return $this->username = $value;
  }

  /**
  * Function: setPassword()
  * Parameters:   none
  * Description:  Set $password
  * Returns:  returns FALSE is falsy, otherwise TRUE
  */
  public function setPassword($value)
  {
    return $this->password = $value;
  }

  /**
  * Function: setPlatform()
  * Parameters:  $value = URL for the REST API
  * Description:  Set $platform
  * Returns:  returns FALSE is falsy, otherwise TRUE
  */
  public function setPlatform($value)
  {
    return $this->platform = $value;
  }

  /**
  * Function: getPlatform()
  * Description:  Set $platform
  * Returns:  returns a value if successful, otherwise FALSE
  */
  public function getPlatform()
  {
    return $this->platform;
  }

    /**
     * Function: getToken()
     * Parameters:   none
     * Description:  Set $token
     * Returns:  returns FALSE is falsy, otherwise TRUE
     */
    public function getToken()
    {
        return $this->token;
    }

  /**
  * Function: setToken()
  * Parameters:   none
  * Description:  Set $token
  * Returns:  returns FALSE is falsy, otherwise TRUE
  */
  public function setToken($value)
  {
    return $this->token = $value;
  }

  /**
  * Function: request()
  * Parameters:
  *   $method = endpoint per API specs
  *   $endpoint = endpoint per API specs
  *   $parameters = Parameters per API specs
  *   $decode_json = Boolen for trigging a decode of the response json.
  * Description:  Calls the API via the request function.
  * Returns:  Returns an Array or response object if successful, otherwise FALSE
  */
  public function request($method, $endpoint, $parameters = array(), $decode_json = true)
  {
    // Move to getClient?
    if(!self::check()) self::connect();

    $parameters['headers']['OAuth-Token'] = $this->token;

    try {
        $response = $this->getClient()->request($method, $endpoint, $parameters);
    } catch (\GuzzleHttp\Exception\ClientException $e) {
        // If we are here without a token we'd better not retry since something
        // else is wrong.
        if ($e->getCode() == 401 && $this->token) {
          $token = $this->getNewAuthToken();
            if ($token) {
              $this->setToken($token);
              // Time to retry.
              return $this->request($method, $endpoint, $parameters, $decode_json);
            }
        }
        // Not a 401, gotta throw.
        throw $e;
    }

    if(!$response)
      return false;

    if ($decode_json)
        return json_decode($response->getBody(), true);

    return $response;
  }

  /**
  * Function: get()
  * Parameters:
  *   $endpoint = endpoint per API specs
  *   $parameters = Parameters per API specs
  * Description:  Calls the API via HTTP GET
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  public function get($endpoint, $parameters = array())
  {
    $result = $this->request('GET', $endpoint,
        array('query' => $parameters));
    return $result;
  }

  /**
  * Function: getFile()
  * Parameters:
  *   $endpoint = endpoint per API specs
  *   $destinationFile = destination file including folders and file extension (e.g. /var/www/html/somefile.zip)
  *   $parameters = Parameters per API specs
  * Description:  Calls the API via HTTP GET
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  public function getFile($endpoint, $destinationFile, $parameters = array())
  {
    $res = $this->request('GET', $endpoint,
        array(
            'query' => $parameters,
            'sink' => $destinationFile,
            'decode_content' => false
        ), false);

    if($res->getStatusCode() !== 200) return false;

    return true;
  }

  /**
  * Function: postFile()
  * Parameters:
  *   $endpoint = endpoint per API specs
  *   $parameters = Parameters per API specs
  * Description:  Calls the API via HTTP POST without JSON and with special header
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  public function postFile($endpoint, $parameters = array())
  {
    $output = [];

    foreach ( $parameters as $key => $value ) {
      if ( ! is_array( $value ) && $key!== '') {
          $output[] = [
              'name'     => $key,
              'contents' => $value
          ];
          continue;
      }
    }

    $parameters = ['multipart' => $output];

    $res = $this->request('POST', $endpoint, $parameters, false);

    if($res->getStatusCode() !== 200) return false;

    return true;
  }

  /**
  * Function: post()
  * Parameters:
  *   $endpoint = endpoint per API specs
  *   $parameters = Parameters per API specs
  * Description:  Calls the API via HTTP POST
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  public function post($endpoint, $parameters = [])
  {
    return $this->request('POST', $endpoint,
        array('json' => $parameters));
  }
  
  /**
  * Function: put()
  * Parameters:
  *   $endpoint = endpoint per API specs
  *   $parameters = Parameters per API specs
  * Description:  Calls the API via HTTP PUT
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  public function put($endpoint, $parameters = [])
  {
    return $this->request('PUT', $endpoint,
        array('json' => $parameters));
  }

 /**
  * Function: delete()
  * Parameters:
  *   $endpoint = endpoint per API specs
  * Description:  Calls the API via HTTP DELETE
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  public function delete($endpoint, $parameters = [])
  {
    return $this->request('DELETE', $endpoint);
  }
}
