<?php

use Adjust\Dataceed\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
	public function testSendLead()
	{
		// Mock the Client to bypass the actual HTTP request
		$client = $this->getMockBuilder(Client::class)
					   ->onlyMethods(['makeApiRequest'])
					   ->setConstructorArgs(['fake_access_token'])
					   ->getMock();

		// Configure the stub.
		$client->method('makeApiRequest')
			   ->willReturn(['success' => true]);

		// Define your parameters as you would for a real request
		$params = [
			'action' 	=> 'test action',
			'email' 	=> 'test@example.com',
			'lang' 		=> 'fr',
			// add other parameters as needed
		];

		// Call sendLead and assert that the response is as expected
		$response = $client->sendLead($params);
		$this->assertIsArray($response);
		$this->assertEquals(['success' => true], $response);
	}
}