<?php

namespace Adjust\Dataceed;

class Client
{
	private $accessToken;
	private $apiBaseUrl = 'https://process.dataceed.com/api/';
	
	protected $cookiesList = [
		'traffic_' => '_dc_trffc_source'
	];
	
	public $checkCookies = true;

	public function __construct($accessToken)
	{
		$this->accessToken = $accessToken;
	}
	
	public function sendLead($params)
	{
		return $this->makeApiRequest('lead', $this->getParams($params));
	}
	
	protected function getParams($params)
	{
		if($this->checkCookies) {
			foreach($this->cookiesList as $prefix => $cookieName) {
				if(!empty($_COOKIE[$cookieName])) {
					$cookie = json_decode($_COOKIE[$cookieName], true);
					foreach($this->flattenArray($cookie) as $key => $value) {
						$params[$prefix.$key] = $value;
					}
				}
			}
		}
		
		return $params;
	}

	protected function makeApiRequest($endpoint, $params)
	{
		
		$url = $this->apiBaseUrl . $endpoint . '?access_token=' . $this->accessToken;
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
		curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

		$response = curl_exec($curl);
		$error = curl_error($curl);
		$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($error) {
			throw new \Exception("CURL Error: $error");
		}

		if ($statusCode >= 400) {
			// Handle HTTP errors
			throw new \Exception("HTTP Error: Received status code $statusCode");
		}

		return json_decode($response, true);
	}
	
	private function flattenArray($array, $prefix = '')
	{
		$result = [];
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$flattened = $this->flattenArray($value, $prefix . $key . '_');
				$result = array_merge($result, $flattened);
			} else {
				$result[$prefix . $key] = $value;
			}
		}
		return $result;
	}
}