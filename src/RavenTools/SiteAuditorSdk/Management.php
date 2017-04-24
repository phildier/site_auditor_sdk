<?php

namespace RavenTools\SiteAuditorSdk;

use RuntimeException;
use Exception;

/**
 * Auditor client interface
 */
class Management {

	protected $client = null;
	protected $client_name = null;
	protected $client_id = null;
	protected $client_secret = null;

	public function __construct($params = []) {
		if(array_key_exists('client',$params) && $params['client'] instanceof Client) {
			$this->setClient($params['client']);
		}

		if(array_key_exists('client_name',$params) && !empty($params['client_name'])) {
			$this->setClientName($params['client_name']);
		}

		if(array_key_exists('client_id',$params) && !empty($params['client_id'])) {
			$this->setClientId($params['client_id']);
		}

		if(array_key_exists('client_secret',$params) && !empty($params['client_secret'])) {
			$this->setClientSecret($params['client_secret']);
		}
	}

	public function setClient($client) {
		$this->client = $client;
	}

	public function getClient() {
		return $this->client;
	}

	public function setClientName($client_name) {
		$this->client_name = $client_name;
	}

	public function getClientName() {
		return $this->client_name;
	}

	public function setClientId($client_id) {
		$this->client_id = $client_id;
	}

	public function getClientId() {
		return $this->client_id;
	}

	public function setClientSecret($client_secret) {
		$this->client_secret = $client_secret;
	}

	public function getClientSecret() {
		return $this->client_secret;
	}

	public function createToken($params = []) {

		$params = array_merge($params, [
			'client_name' => $this->getClientName(),
			'client_id' => $this->getClientId(),
			'client_secret' => $this->getClientSecret(),
		]);

		$response = $this->getClient()->request('POST', 'getapitoken', ['json' => $params]);

		$decoded = $this->getClient()->decode($response);

		return $decoded;
	}
}
