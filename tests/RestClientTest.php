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
    private $platform = 'api';

    private $testGeneratedRecords = [];

    function setUp(): void
    {
        parent::setUp();

        $this->client = new Rest();
        $this->client->setUrl($this->host)
            ->setUsername($this->username)
            ->setPassword($this->password)
            ->setPlatform($this->platform);

    }

    function tearDown(): void
    {
        parent::tearDown();

        forEach($this->testGeneratedRecords as &$record) {
            $res = $this->client->delete($record->module, $record->id);

            if($res) {
                print('teardown successful for ' . json_encode($record));
            } else {
                print('teardown unsuccessful for ' . json_encode($record));
            }
        }

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

        $this->testGeneratedRecords[] = (object) array('module' => 'Cases', 'id' => $res['id']);
    }

    public function testDeleteCase()
    {
        $name = 'Unit Test Case ' . time();

        $post = $this->client->create('Cases', array(
            'name' => $name,
            'status' => 'Assigned'
        ));

        $this->assertTrue(array_key_exists('id', $post));
        $this->assertTrue($name === $post['name']);
        $this->assertTrue('Assigned' === $post['status']);

        $delete = $this->client->delete('Cases', $post['id']);
        $this->assertTrue($delete['id'] === $post['id']);
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

        $this->testGeneratedRecords[] = (object) array('module' => 'Cases', 'id' => $res['id']);
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

        $this->testGeneratedRecords[] = (object) array('module' => 'Cases', 'id' => $res['id']);
    }

    public function testFavoriteCase()
    {
        $res = $this->client->create('Cases', array(
            'name' => 'Unit Test Case'
        ));

        $case = $this->client->favorite('Cases', $res['id']);

        $this->assertTrue($case['my_favorite']);

        $this->testGeneratedRecords[] = (object) array('module' => 'Cases', 'id' => $res['id']);
    }

    public function testUnfavoriteCase()
    {
        $name = 'Unit Test Case ' . time();

        $case = $this->client->create('Cases', array(
            'name' => $name
        ));

        $favorite = $this->client->favorite('Cases', $case['id']);

        $this->assertTrue($favorite['my_favorite']);

        $unfavorite = $this->client->unfavorite('Cases', $case['id']);

        $this->assertFalse($unfavorite['my_favorite']);

        $this->testGeneratedRecords[] = (object) array('module' => 'Cases', 'id' => $case['id']);
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

        $this->testGeneratedRecords[] = (object) array('module' => 'Cases', 'id' => $case['id']);
        $this->testGeneratedRecords[] = (object) array('module' => 'Accounts', 'id' => $account['id']);
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

        $this->testGeneratedRecords[] = (object) array('module' => 'Cases', 'id' => $case['id']);
        $this->testGeneratedRecords[] = (object) array('module' => 'Accounts', 'id' => $account['id']);
    }

    public function testCountOfRecords()
    {
        $res = $this->client->countRecords('Cases');

        $this->assertTrue(is_array($res));
        $this->assertTrue(array_key_exists('record_count', $res));
        $this->assertTrue(is_numeric($res['record_count']));
    }

    public function testPostEndpoint()
    {
        $name = 'Unit Test Case ' . time();

        $res = $this->client->postEndpoint('Cases', array(
            'name' => $name,
            'status' => 'Assigned'
        ));

        $this->assertTrue(array_key_exists('id', $res));
        $this->assertTrue($name === $res['name']);
        $this->assertTrue('Assigned' === $res['status']);

        $this->testGeneratedRecords[] = (object) array('module' => 'Cases', 'id' => $res['id']);
    }

    public function testGetEndpoint()
    {
        $name = 'Unit Test Case ' . time();

        $post = $this->client->postEndpoint('Cases', array(
            'name' => $name,
            'status' => 'Assigned'
        ));

        $this->assertTrue(array_key_exists('id', $post));
        $this->assertTrue($name === $post['name']);
        $this->assertTrue('Assigned' === $post['status']);

        $get = $this->client->getEndpoint('Cases/' . $post['id']);

        $this->assertTrue(array_key_exists('id', $get));
        $this->assertTrue($name === $get['name']);
        $this->assertTrue('Assigned' === $get['status']);

        $this->testGeneratedRecords[] = (object) array('module' => 'Cases', 'id' => $post['id']);
    }

    public function testPutEndpoint()
    {
        $name = 'Unit Test Case ' . time();

        $post = $this->client->postEndpoint('Cases', array(
            'name' => $name,
            'status' => 'Assigned'
        ));

        $this->assertTrue(array_key_exists('id', $post));
        $this->assertTrue($name === $post['name']);
        $this->assertTrue('Assigned' === $post['status']);

        $put = $this->client->putEndpoint('Cases/' . $post['id'],  array(
            'name' => $name,
            'status' => 'Assigned',
            'description' => 'Updated desc'
        ));

        $this->assertTrue(array_key_exists('id', $put));
        $this->assertTrue($name === $put['name']);
        $this->assertTrue('Assigned' === $put['status']);
        $this->assertTrue('Updated desc' === $put['description']);

        $this->testGeneratedRecords[] = (object) array('module' => 'Cases', 'id' => $post['id']);
    }

    public function testDeleteEndpoint()
    {
        $name = 'Unit Test Case ' . time();

        $post = $this->client->postEndpoint('Cases', array(
            'name' => $name,
            'status' => 'Assigned'
        ));

        $this->assertTrue(array_key_exists('id', $post));
        $this->assertTrue($name === $post['name']);
        $this->assertTrue('Assigned' === $post['status']);

        $delete = $this->client->deleteEndpoint('Cases/' . $post['id']);

        $this->assertTrue(array_key_exists('id', $delete));
        $this->assertTrue($delete['id'] === $post['id']);
    }

    public function testSetAndGetUrl()
    {
        $this->client->setUrl('https://someurl.com/');
        $url = $this->client->getUrl();

        $this->assertTrue($url === 'https://someurl.com/');
    }
}
