<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class AppControllerTest extends WebTestCase{

    public function testErrorInitWithoutToken()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testErrorInitWithBadToken()
    {
        $client = static::createClient();
        $client->request('GET', '/', array(), array(), array('HTTP_X-AUTH-TOKEN' => 'nan'));
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testInit()
    {
        $client = static::createClient();
        $client->request('GET', '/', array(), array(), array('HTTP_X-AUTH-TOKEN' => 'test2'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(3, count($data['data']));
    }

    public function testErrorPlayWithoutToken()
    {
        $client = static::createClient();
        $client->request('POST', '/play');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testErrorPlayUserWithoutGame()
    {
        $client = static::createClient();
        $client->request('POST', '/play', array(), array(), array('HTTP_X-AUTH-TOKEN' => 'test'));
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(true, $data['data'] === 'Player has not any game.');
    }

    public function testErrorPlayLastGameIsOver()
    {
        $client = static::createClient();
        $client->request('POST', '/play', array(), array(), array('HTTP_X-AUTH-TOKEN' => 'test3'));
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(true, $data['data']==='Error, please create a new game before play a guess');
    }

    public function testErrorPlayWithoutGuess()
    {
        $client = static::createClient();
        $client->request('POST', '/play', array(), array(), array('HTTP_X-AUTH-TOKEN' => 'test2'));
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(true, $data['data']==='Error, guess param is avoid');
    }

    public function testErrorPlayBadGuess()
    {
        $client = static::createClient();
        $client->request('POST', '/play', array('guess' => 'NAN'), array(), array('HTTP_X-AUTH-TOKEN' => 'test2'));
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(true, $data['data']==='The guess sent is incorrect');
    }

    public function testPlayGuess()
    {
        $client = static::createClient();
        $client->request('POST', '/play', array('guess' => 'RED RED BLUE BLUE'), array(), array('HTTP_X-AUTH-TOKEN' => 'test2'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(2, count($data['data']['result']));
    }

    public function testErrorNewWithoutToken()
    {
        $client = static::createClient();
        $client->request('POST', '/new');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testNew()
    {
        $client = static::createClient();
        $client->request('POST', '/new', array(), array(), array('HTTP_X-AUTH-TOKEN' => 'test'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(0, substr($data['data'], 0, 3) === 'Game');
    }

    public function testErrorNewHasAnotherGameOpened()
    {
        $client = static::createClient();
        $client->request('POST', '/new', array(), array(), array('HTTP_X-AUTH-TOKEN' => 'test2'));
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(true, $data['data'] === 'User can not create a new game');
    }

    public function testErrorGameWithoutToken()
    {
        $client = static::createClient();
        $client->request('GET', '/game');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testGameHistoric()
    {
        $client = static::createClient();
        $client->request('GET', '/game', array(), array(), array('HTTP_X-AUTH-TOKEN' => 'test3'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(1, count($data['data']));
    }
}