<?php
declare(strict_types=1);

use Spinegar\SugarRestClient\Rest;
use PHPUnit\Framework\TestCase;

class RestClientTest extends TestCase
{
    //use your host, username, and password
    private $host = 'https://host/rest/v11/';
    private $username = '';
    private $password = '';

    function setUp(): void
    {
        parent::setUp();

        $this->client = new Rest();
        $this->client->setUrl($this->host)
            ->setUsername($this->username)
            ->setPassword($this->password)
            ->connect();

    }

    public function testMe()
    {
        $res = $this->client->me();

        $this->assertTrue($res['current_user']['user_name'] === $this->username);
    }

    public function testGetCases()
    {
        $res = $this->client->search('Cases');

        $this->assertTrue(is_array($res));
        $this->assertArrayHasKey('records', $res);
        $this->assertTrue(is_array($res['records']));

        $record = $res['records'][0];

        $this->assertArrayHasKey('my_favorite', $record);
        $this->assertArrayHasKey('following', $record);
        $this->assertArrayHasKey('id', $record);
        $this->assertArrayHasKey('name', $record);
        $this->assertArrayHasKey('date_entered', $record);
        $this->assertArrayHasKey('date_modified', $record);
        $this->assertArrayHasKey('modified_user_id', $record);
        $this->assertArrayHasKey('modified_by_name', $record);
        $this->assertArrayHasKey('created_by', $record);
        $this->assertArrayHasKey('created_by_name', $record);
        $this->assertArrayHasKey('doc_owner', $record);
        $this->assertArrayHasKey('user_favorites', $record);
        $this->assertArrayHasKey('description', $record);
        $this->assertArrayHasKey('deleted', $record);
        $this->assertArrayHasKey('assigned_user_id', $record);
        $this->assertArrayHasKey('assigned_user_name', $record);
        $this->assertArrayHasKey('team_count', $record);
        $this->assertArrayHasKey('team_name', $record);
    }

    public function testGetCasesNameFieldOnly()
    {
        $res = $this->client->search('Cases', array(
            'fields' => 'name'
        ));

        $this->assertTrue(is_array($res));
        $this->assertArrayHasKey('records', $res);
        $this->assertTrue(is_array($res['records']));

        $record = $res['records'][0];

        $this->assertTrue(array_key_exists('id', $record));
        $this->assertTrue(array_key_exists('name', $record));
        $this->assertTrue(array_key_exists('date_modified', $record));
        $this->assertTrue(array_key_exists('date_modified', $record));

        $this->assertFalse(array_key_exists('my_favorite', $record));
        $this->assertFalse(array_key_exists('following', $record));
        $this->assertFalse(array_key_exists('date_entered', $record));
        $this->assertFalse(array_key_exists('modified_user_id', $record));
        $this->assertFalse(array_key_exists('modified_by_name', $record));
        $this->assertFalse(array_key_exists('created_by', $record));
        $this->assertFalse(array_key_exists('created_by_name', $record));
        $this->assertFalse(array_key_exists('doc_owner', $record));
        $this->assertFalse(array_key_exists('user_favorites', $record));
        $this->assertFalse(array_key_exists('description', $record));
        $this->assertFalse(array_key_exists('deleted', $record));
        $this->assertFalse(array_key_exists('assigned_user_id', $record));
        $this->assertFalse(array_key_exists('assigned_user_name', $record));
        $this->assertFalse(array_key_exists('team_count', $record));
        $this->assertFalse(array_key_exists('team_name', $record));
    }

    public function testCreateCase()
    {
        $res = $this->client->create('Cases', array(
            'name' => 'Unit Test Case',
            'status' => 'Assigned'
        ));

        $this->assertTrue(array_key_exists('id', $res));
        $this->assertTrue('Unit Test Case' === $res['name']);
        $this->assertTrue('Assigned' === $res['status']);
    }

    public function testGetCaseById()
    {
        $res = $this->client->create('Cases', array(
            'name' => 'Unit Test Case'
        ));

        $case = $this->client->retrieve('Cases', $res['id']);

        $this->assertTrue(array_key_exists('id', $res));
        $this->assertTrue($res['id'] === $case['id']);
        $this->assertTrue(array_key_exists('name', $res));
        $this->assertTrue('Unit Test Case' === $res['name']);
        $this->assertTrue($res['name'] === $case['name']);
    }

    public function testUpdateCase()
    {
        $res = $this->client->create('Cases', array(
            'name' => 'Unit Test Case'
        ));

        $this->client->update('Cases', $res['id'], array(
            'name' => 'Unit Test Case Updated'
        ));

        $case = $this->client->retrieve('Cases', $res['id']);

        $this->assertTrue(array_key_exists('id', $res));
        $this->assertTrue($res['id'] === $case['id']);
        $this->assertTrue(array_key_exists('name', $res));
        $this->assertTrue('Unit Test Case Updated' === $case['name']);
    }

    public function testFavoriteCase()
    {
        $res = $this->client->create('Cases', array(
            'name' => 'Unit Test Case'
        ));

        $case = $this->client->favorite('Cases', $res['id']);

        $this->assertTrue($case['my_favorite']);
    }

    public function testUnfavoriteCase()
    {
        $case = $this->client->create('Cases', array(
            'name' => 'Unit Test Case',
            'my_favorite' => true
        ));

        $this->assertTrue($case['my_favorite']);

        $case = $this->client->update('Cases', $case['id'], array(
            'my_favorite' => false
        ));

        $this->assertFalse($case['my_favorite']);
    }

    public function testRelateCaseToAccount()
    {
        $case = $this->client->create('Cases', array(
            'name' => 'Unit Test Case'
        ));

        $account = $this->client->create('Accounts', array(
            'name' => 'Unit Test Account'
        ));

        $this->client->relate('Accounts', $account['id'], 'cases', $case['id']);

        $cases = $this->client->related('Accounts', $account['id'], 'cases');

        $this->assertTrue(is_array($cases));
        $this->assertArrayHasKey('records', $cases);
        $this->assertTrue(is_array($cases['records']));

        $record = $cases['records'][0];

        $this->assertArrayHasKey('id', $record);
        $this->assertTrue($record['id'] === $case['id']);
    }

    public function testUnrelateCaseToAccount()
    {
        $case = $this->client->create('Cases', array(
            'name' => 'Unit Test Case'
        ));

        $account = $this->client->create('Accounts', array(
            'name' => 'Unit Test Account'
        ));

        $this->client->relate('Accounts', $account['id'], 'cases', $case['id']);

        $cases = $this->client->related('Accounts', $account['id'], 'cases');

        $this->assertTrue(is_array($cases));
        $this->assertArrayHasKey('records', $cases);
        $this->assertTrue(is_array($cases['records']));

        $record = $cases['records'][0];

        $this->assertArrayHasKey('id', $record);
        $this->assertTrue($record['id'] === $case['id']);

        $this->client->unrelate('Accounts', $account['id'], 'cases', $case['id']);

        $cases = $this->client->related('Accounts', $account['id'], 'cases');

        $this->assertTrue(is_array($cases));
        $this->assertArrayHasKey('records', $cases);
        $this->assertTrue(is_array($cases['records']));

        $this->assertTrue(count($cases['records']) === 0);
    }

    public function testCountOfRecords()
    {
        $res = $this->client->countRecords('Cases');

        $this->assertTrue(is_array($res));
        $this->assertTrue(array_key_exists('record_count', $res));
        $this->assertTrue(is_numeric($res['record_count']));
    }

    public function testMultipleRequests()
    {
        $results = $this->client->send(function($client){
            return [
                $client->countRecords('Cases'),
                $client->countRecords('Cases'),
                $client->countRecords('Cases'),
            ];
        });

        foreach($results as $res) {
            $this->assertTrue(is_array($res));
            $this->assertTrue(array_key_exists('record_count', $res));
            $this->assertTrue(is_numeric($res['record_count']));
        }
    }
}
