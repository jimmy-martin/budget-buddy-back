<?php

namespace App\Tests\Unit;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class RoleTest extends ApiTestCase
{
    private $client;
    private $headers = ['accept' => 'application/json'];

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testShowRoles(): void
    {
        $response = $this->client->request('GET', '/api/roles', [
            'headers' => $this->headers,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testCreateRole(): void
    {
        $json = [
            'title' => 'admin',
        ];

        $response = $this->client->request('POST', '/api/roles', [
            'headers' => $this->headers,
            'json' => $json,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSame(201, $response->getStatusCode());
    }

    public function testUpdateRole(): void
    {
        $json = [
            'title' => 'admin',
        ];

        $response = $this->client->request('PATCH', '/api/roles/1', [
            'headers' => $this->headers,
            'json' => $json,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testDeleteRole(): void
    {
        $response = $this->client->request('DELETE', '/api/roles/1', [
            'headers' => $this->headers,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSame(204, $response->getStatusCode());
        $this->assertEmpty($response->getContent());
    }
}
