<?php

namespace RavenTools\SiteAuditorSdk\Resources;

use RavenTools\SiteAuditorSdk\Client;
use RavenTools\SiteAuditorSdk\ResourceCollection;
use RuntimeException;
use BadMethodCallException;
use JsonSerializable;

/**
 * Common resource functionality
 */
class Common implements JsonSerializable {

	/**
	 * Auditor SDK Client object
	 */
	protected $client = null;

	/**
	 * Resource properties
	 */
	protected $data = [];

	/**
	 * Endpoint used to list all of a particular resource
	 */
	protected $all_endpoint = null;

	/**
	 * Endpoint used to get a single resource
	 */
	protected $get_endpoint = null;

	/**
	 * Endpoint used to create a resource
	 */
	protected $create_endpoint = null;

	/**
	 * Endpoint used to update a resource
	 */
	protected $update_endpoint = null;

	/**
	 * Endpoint used to update a resource
	 */
	protected $delete_endpoint = null;

	/**
	 * Response key when querying for a single record
	 */
	protected $response_key = null;

	public function __construct($params = []) {
		if(array_key_exists('client',$params) && $params['client'] instanceof Client) {
			$this->client = $params['client'];
		} else {
			throw new RuntimeException(sprintf('Missing expected instance of %s', Client::class));
		}
		unset($params['client']);

		// initialize this resource's data
		$this->data = $params;
	}

	public function all($params = []) {
		if(is_null($this->all_endpoint)) {
			throw new BadMethodCallException('method not supported on this resource');
		}

		$params = array_merge($this->data, $params);
		$uri = $this->substitute($this->all_endpoint,$params);
		$response = $this->client->request('GET', $uri, ['json' => $params]);

		if($response === false) {
			return false;
		}

		return new ResourceCollection([
			'client' => $this->client,
			'response' => $response,
			'resource_type' => static::class
		]);
	}

	/**
	 * given an id parameter, fetches and returns a record
	 */
	public function get($resource_id = null) {
		if(is_null($this->get_endpoint)) {
			throw new BadMethodCallException('method not supported on this resource');
		}

		if(is_null($resource_id)) {
			$resource_id = $this->id;
		}

		$uri = $this->substitute($this->get_endpoint,['id' => $resource_id]);
		$response = $this->client->request('GET', $uri);

		$decoded = $this->client->decode($response);

		$params = (array) $decoded[$this->response_key];
		$params['client'] = $this->client;

		return new static($params);
	}

	/**
	 * creates a record.  uses the object's properties or a parameters array
	 */
	public function create($params = []) {
		if(is_null($this->create_endpoint)) {
			throw new BadMethodCallException('method not supported on this resource');
		}

		$params = array_merge($this->data, $params);
		$uri = $this->substitute($this->create_endpoint,$params);
		$response = $this->client->request('POST', $uri, ['json' => $params]);

		$decoded = $this->client->decode($response);

		$params = (array) $decoded[$this->response_key];
		$params['client'] = $this->client;

		return new static($params);
	}

	/**
	 * updates a record.  requires at least an id, either in the object's data
	 * or as a parameter
	 */
	public function update($params = []) {
		if(is_null($this->update_endpoint)) {
			throw new BadMethodCallException('method not supported on this resource');
		}

		$params = array_merge($this->data,$params);
		$uri = $this->substitute($this->update_endpoint,$params);
		$response = $this->client->request('PATCH', $uri, ['json' => $params]);

		$decoded = $this->client->decode($response);

		$params = (array) $decoded[$this->response_key];
		$params['client'] = $this->client;

		return new static($params);
	}

	/**
	 * deletes a record.  requires at least an id, either in the object's data
	 * or as a parameter
	 */
	public function delete($resource_id = null) {
		if(is_null($this->delete_endpoint)) {
			throw new BadMethodCallException('method not supported on this resource');
		}

		if(is_array($resource_id)) {
			$params = $resource_id;
		} elseif(!is_null($resource_id)) {
			$params = ['id' => $resource_id];
		}

		$params = array_merge($this->data,$params);
		$uri = $this->substitute($this->delete_endpoint,$params);
		$response = $this->client->request('DELETE', $uri, ['json' => $params]);

		return $this->client->decode($response);
	}

	/**
	 * given a uri and array of keys/values, replaces {key} with value and 
	 * returns the resulting uri
	 */
	public function substitute($uri, $params) {
		if(preg_match_all('#{([^}]+)}#', $uri, $matches, PREG_SET_ORDER)) {
			foreach($matches as $m) {
				$key = $m[1];
				$regex = sprintf("/{%s}/",$key);
				if(isset($params[$key])) {
					$uri = preg_replace($regex,$params[$key],$uri);
				}
			}
		}

		return $uri;
	}

	public function __get($key) {
		if(array_key_exists($key, $this->data)) {
			return $this->data[$key];
		}

		return null;
	}

	public function __set($key, $value) {
		$this->data[$key] = $value;
	}

	public function __toString() {
		return print_r($this->data, true);
	}

	public function to_array() {
		return $this->data;
	}

	public function jsonSerialize() {
		return $this->to_array();
	}
}
