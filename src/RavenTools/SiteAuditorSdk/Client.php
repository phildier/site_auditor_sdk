<?php

namespace RavenTools\SiteAuditorSdk;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\BadResponseException;

use RuntimeException;
use Exception;

/**
 * Auditor client interface
 */
class Client {

	private $base_uri = 'http://auditor.raventools.com/api/v2';

	private $token = null;

	private $last_response = null;

	private $debug = false;

	/**
	 * Initialize a new Auditor client
	 */
	public function __construct($params = []) {
		if(array_key_exists('debug', $params) && $params['debug'] === true) {
			$this->debug = true;
		}

		if(array_key_exists('auth_token', $params) && !empty($params['auth_token'])) {
			$this->setAuthToken($params['auth_token']);
		}

		if(array_key_exists('base_uri', $params) && !empty($params['base_uri'])) {
			$this->setBaseUri($params['base_uri']);
		}

		if(array_key_exists('http_client', $params) && !empty($params['http_client'])) {
			$this->setHttpClient($params['http_client']);
		} else {
			$this->setHttpClient(new GuzzleClient([
				'base_uri' => $this->getBaseUri(),
				'debug' => $this->debug
			]));
		}
	}

	public function setAuthToken($token) {
		$this->token = $token;
	}

	public function getAuthToken() {
		if(is_null($this->token)) {
			throw new RuntimeException('Missing authentication token');
		}

		return $this->token;
	}

	public function setBaseUri($base_uri) {
		$this->base_uri = $base_uri;
	}

	public function getBaseUri() {
		return $this->base_uri;
	}

	public function setHttpClient(GuzzleClient $http_client) {
		$this->http_client = $http_client;
	}

	public function getHttpClient() {
		return $this->http_client;
	}

	public function setLastResponse(Response $response) {
		$this->last_response = $response;
	}

	public function getLastResponse() {
		return $this->last_response;
	}

	public function request($method, $path, $params = []) {

		$method = strtoupper($method);
		$http_client = $this->getHttpClient();

		if($this->getAuthToken()) {
			$params['headers'] = [
				'Authorization' => sprintf('Bearer %s', $this->getAuthToken())
			];
		}

		$params['query'] = $this->parseQueryParams($params);

		try {
			$response = $http_client->request($method, $path, $params);
			$this->setLastResponse($response);
		} catch(BadResponseException $e) {
			$this->setLastResponse($e->getResponse());
			throw $e;
		} catch(Exception $e) {
			error_log(get_class($e));
			error_log($e->getMessage());
			throw $e;
		}

		return $response;
	}

	public function decode(Response $response) {
		return json_decode($response->getBody(),true);
	}

	public function factory($type, $params = []) {
		if(!array_key_exists('client',$params) || !$params['client'] instanceof self) {
			$params['client'] = $this;
		}

		return new $type($params);
	}

	public function parseQueryParams($params = []) {
		$query_params = [];

		foreach(['offset','limit','order','search'] as $key) {
			if(array_key_exists($key,$params) && !empty($params[$key])) {
				$query_params[$key] =  $params[$key];
			}
		}

		return $query_params;
	}
}
