<?php

namespace Tests\AppBundle\Controller\EnMarche;

use AppBundle\DataFixtures\ORM\LoadHomeBlockData;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Controller\ControllerTestTrait;

/**
 * @group functional
 * @group controller-2
 */
class FacebookControllerTest extends WebTestCase
{
    use ControllerTestTrait;

    public function testIndex()
    {
        $this->client->request(Request::METHOD_GET, '/profil-facebook');
        $this->assertResponseStatusCode(Response::HTTP_OK, $response = $this->client->getResponse());
    }

    public function testAuth()
    {
        $this->client->request(Request::METHOD_GET, '/profil-facebook/connexion');
        $this->assertResponseStatusCode(Response::HTTP_FOUND, $response = $this->client->getResponse());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->init([
            LoadHomeBlockData::class,
        ]);

        $this->client = $this->makeClient();
    }
}
