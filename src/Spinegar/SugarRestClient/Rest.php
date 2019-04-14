<?php namespace Spinegar\SugarRestClient;

/**
 * SugarCRM Rest Client
 *
 * @package   SugarCRM Rest Client
 * @category  Libraries
 * @author  Sean Pinegar
 * @license MIT License
 * @link   https://github.com/spinegar/sugarcrm7-api-wrapper-class
 */

class Rest {

  protected $client;

  /**
  * Function: __construct()
  * Parameters:   none    
  * Description:  Construct Class
  * Returns:  VOID
  */
  public function __construct()
  {
    $this->client = new Clients\Guzzle;
  }

  /**
   * Function: setClientOption()
   * Parameters:   $key = Guzzle option, $value = Value  
   * Description:  Set Default options for the Guzzle client.
   * Returns:  returns $this
   */
  public function setClientOption($key, $value)
  {
    $this->client->setClientOption($key, $value);
   
    return $this;
  }

  /**
  * Function: setUrl()
  * Parameters:   $value = URL for the REST API    
  * Description:  Set $url
  * Returns:  returns $this
  */
  public function setUrl($value)
  {
    $this->client->setUrl($value);

    return $this;
  }

  /**
  * Function: getUrl()
  * Description:  Get $url
  * Returns:  returns value if true otherwise FALSE
  */
  public function getUrl()
  {
    return $this->client->getUrl();
  }

  /**
  * Function: setUsername()
  * Parameters:   $value = Username for the REST API User    
  * Description:  Set $username
  * Returns:  returns $this
  */
  public function setUsername($value)
  {
    $this->client->setUsername($value);

    return $this;
  }

  /**
  * Function: setPassword()
  * Parameters:   none    
  * Description:  Set $password
  * Returns:  returns $this
  */
  public function setPassword($value)
  {
    $this->client->setPassword($value);

    return $this;
  }

  /**
  * Function: setPlatform()
  * Parameters:   $value = platform for the REST API    
  * Description:  Set $platform
  * Returns:  returns $this
  */
  public function setPlatform($value)
  {
    $this->client->setPlatform($value);

    return $this;
  }

  /**
  * Function: getPlatform()
  * Description:  Get $platform
  * Returns:  returns value if true otherwise FALSE
  */
  public function getPlatform()
  {
    return $this->client->getPlatform();
  }

  /**
  * Function: create()
  * Parameters:   $module = Record Type
  *   $fields = Record field values    
  * Description:  This method creates a new record of the specified type
  * Returns:  returns Array if successful, otherwise FALSE
  */
  public function create($module, $fields)
  {
    $endpoint = $module;

    return $this->client->post($endpoint, $fields);
  }

  /**
  * Function: search()
  * Parameters:  $module - The module to work with
  *   $parameters = [
  *     q - Search the records by this parameter, if you don't have a full-text search engine enabled it will only search the name field of the records.  (Optional)
  *     max_num - A maximum number of records to return (Optional)
  *     offset -  How many records to skip over before records are returned (Optional)
  *     fields -  Comma delimited list of what fields you want returned. The field date_modified will always be added  (Optional)
  *     order_by -  How to sort the returned records, in a comma delimited list with the direction appended to the column name after a colon. Example: last_name:DESC,first_name:DESC,date_modified:ASC (Optional)
  *     favorites - Only fetch favorite records (Optional)
  *     deleted - Show deleted records in addition to undeleted records (Optional)
  *   ]
  * Description:  Search records in this module
  * Returns:  returns Object if successful, otherwise FALSE
  */
  public function search($module, $parameters = array())
  {
    $endpoint = $module;

    return $this->client->get($endpoint, $parameters);
  }

  /**
   * Function: filter()
   * Parameters: $module = Module type
   *   $parameters = Filter Criteria
   * Description:   Filter records in this module
   * Returns: returns a list of module beans if successful, otherwise false
   */
  public function filter($module, $parameters = array())
  {
    $endpoint = $module . '/filter';

    return $this->client->get($endpoint, $parameters);
  }

  /**
   * Function: countRecords()
   * Parameters: $module = Module type
   *   $parameters = Filter Criteria
   * Description:   Count records in this module
   * Returns: returns the records quantity
   */
  public function countRecords($module, $parameters = array())
  {
    $endpoint = $module . '/count';

    return $this->client->get($endpoint, $parameters);
  }

   /**
  * Function: delete()
  * Parameters: $module = Record Type
  *   $record = The record to delete
  * Description:  This method deletes a record of the specified type
  * Returns:  returns Object if successful, otherwise FALSE
  */
   public function delete($module, $record)
   {
    $endpoint = $module . '/' . $record;

    return $this->client->delete($endpoint);
  }

  /**
  * Function: retrieve()
  * Parameters: $module = Record Type
  *   $record = The record to retrieve
  * Description:  This method retrieves a record of the specified type
  * Returns:  Returns a single record
  */
  public function retrieve($module, $record)
  {
    $endpoint = $module . '/' . $record;

    return $this->client->get($endpoint);
  }

  /**
  * Function: update()
  * Parameters: $module = Record Type
  *   $record = The record to update
  *   $fields = Record field values
  * Description:  This method updates a record of the specified type
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  public function update($module, $record, $fields)
  {
    $endpoint = $module . '/' . $record;

    return $this->client->put($endpoint, $fields);
  }

  /**
  * Function: favorite()
  * Parameters: $module = Record Type
  *   $record = The record to favorite
  * Description:  This method favorites a record of the specified type
  * Returns:  Returns TRUE if successful, otherwise FALSE
  */
  public function favorite($module, $record)
  {
    $endpoint = $module . '/' . $record . '/favorite';

    return $this->client->put($endpoint);
  }

  /**
  * Function: unfavorite()
  * Parameters: $module = Record Type
  *   $record = The record to unfavorite
  * Description:  This method unfavorites a record of the specified type
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  public function unfavorite($module, $record)
  {
    $endpoint = $module . '/' . $record . '/favorite';

    return $this->client->delete($endpoint);
  }

  /**
  * Function: files()
  * Parameters: $module = Record Type
  *   $record = The record  we are working with
  * Description:  Gets a listing of files related to a field for a module record.
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  public function files($module, $record)
  {
    $endpoint = $module . '/' . $record . '/file';

    return $this->client->get($endpoint);
  }

  /**
  * Function: download()
  * Parameters: $module = Record Type
  *   $record = The record  we are working with
  *   $field = Field associated to the file
  *   $destinationFile = destination file including folders and file extension (e.g. /var/www/html/someFile.zip)
  * Description:  Gets the contents of a single file related to a field for a module record.
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  public function download($module, $record, $field, $destinationFile)
  {
    $endpoint = $module . '/' . $record . '/file/' . $field;
    return $this->client->getFile($endpoint, $destinationFile);
  }

  /**
  * Function: upload()
  * Parameters: $module = Record Type
  *   $record = The record  we are working with
  *   $field = Field associated to the file
  *   $sourceFilePath = local path of file to be uploaded (e.g. /var/www/html/path/of/local/file.ext)
  * Description:  Saves a file. The file can be a new file or a file override.
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  public function upload($module, $record, $field, $sourceFilePath)
  {
    $endpoint = $module . '/' . $record . '/file/' . $field;

    $parameters = array(
      "format"          => "sugar-html-json",
      "delete_if_fails" => true,
      "oauth_token"     => $this->client->getToken(),
      "$field"          => fopen($sourceFilePath, 'r')
    );

    return $this->client->postFile($endpoint, $parameters);
  }

  /**
  * Function: deleteFile()
  * Parameters: $module = Record Type
  *   $record = The record  we are working with
  *   $field = Field associated to the file
  * Description:  Saves a file. The file can be a new file or a file override.
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  public function deleteFile($module, $record, $field)
  {
    $endpoint = $module . '/' . $record . '/file/' . $field;

    return $this->client->delete($endpoint);
  }

  /**
  * Function: related()
  * Parameters: $module = Record Type
  *   $record = The record we are working with
  *   $link = The link for the relationship
  *   $parameters = Request arguments, see the documentation for GET /<module>/:record/link/:link_name on https://<your sugarcrm domain>/rest/v10/help
  * Description:  This method retrieves a list of records from the specified link
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  public function related($module, $record, $link, $parameters = array())
  {
    $endpoint = $module . '/' . $record . '/link/' . $link;

    return $this->client->get($endpoint, $parameters);
  }

  /**
  * Function: relate()
  * Parameters: $module = Record Type
  *   $record = The record we are working with
  *   $link = The link for the relationship
  *   $related_record = the record to relate to
  *   $fields = Relationship data
  * Description:  This method relates 2 records
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  public function relate($module, $record, $link, $related_record, $fields=array())
  {
    $endpoint = $module . '/' . $record . '/link/' . $link . '/' . $related_record;

    return $this->client->post($endpoint, $fields);
  }

  /**
  * Function: unrelate()
  * Parameters: $module = Record Type
  *   $record = The record to unfavorite
  * Description:  This method removes the relationship for 2 records
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  public function unrelate($module, $record, $link, $related_record)
  {
    $endpoint = $module . '/' . $record . '/link/' . $link . '/' . $related_record;

    return $this->client->delete($endpoint);
  }

  /**
  * Function: updateRelationship()
  * Parameters: $module = Record Type
  *   $record = The record we are working with
  *   $link = The link for the relationship
  *   $related_record = the record to relate to
  *   $fields = Relationship data
  * Description:  This method updates relationship data
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  public function updateRelationship($module, $record, $link, $related_record, $fields=array())
  {
    $endpoint = $module . '/' . $record . '/link/' . $link . '/' . $related_record;

    return $this->client->put($endpoint,  $fields);
  }

  /**
  * Function: getEndpoint()
  * Parameters: $endpoint = API Endpoint
  * Parameters: $parameters = parameters to pass to the endpoint
  * Description:  Call a get endpoint
  * Returns:  Returns ARRAY if successful, otherwise FALSE
  */
  public function getEndpoint($endpoint, $parameters = array())
  {
    return $this->client->get($endpoint, $parameters);
  }

  /**
  * Function: postEndpoint()
  * Parameters: $endpoint = API Endpoint
  * Parameters: $parameters = parameters to pass to the endpoint
  * Description:  Call a post endpoint
  * Returns:  Returns ARRAY if successful, otherwise FALSE
  */
  public function postEndpoint($endpoint, $parameters = array())
  {
    return $this->client->post($endpoint, $parameters);
  }

  /**
  * Function: putEndpoint()
  * Parameters: $endpoint = API Endpoint
  * Parameters: $parameters = parameters to pass to the endpoint
  * Description:  Call a put endpoint
  * Returns:  Returns ARRAY if successful, otherwise FALSE
  */
  public function putEndpoint($endpoint, $parameters = array())
  {
    return $this->client->put($endpoint, $parameters);
  }

  /**
  * Function: deleteEndpoint()
  * Parameters: $endpoint = API Endpoint
  * Parameters: $parameters = parameters to pass to the endpoint
  * Description:  Call a delete endpoint
  * Returns:  Returns ARRAY if successful, otherwise FALSE
  */
  public function deleteEndpoint($endpoint, $parameters = array())
  {
    return $this->client->delete($endpoint, $parameters);
  }
  
    /**
  * Function: me()
  * Parameters: 
  * Description:  This method retrieves current user
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  public function me()
  {
    $endpoint = 'me';

    return $this->client->get($endpoint);
  }
}
