<?php

namespace RavenTools\SiteAuditorSdk;

use GuzzleHttp\Psr7\Response;
use ArrayAccess;
use Iterator;

class ResourceCollection implements ArrayAccess, Iterator {

	private $client = null;
	private $resource_type = null;
	private $resources = [];
	private $total = 0;
	private $filtered = 0;

	public function __construct($params = []) {
		if(array_key_exists('client',$params) && $params['client'] instanceof Client) {
			$this->client = $params['client'];
		} else {
			throw new Exception('client is required');
		}

		if(array_key_exists('resource_type',$params) && !empty($params['resource_type'])) {
			$this->resource_type = $params['resource_type'];
		} else {
			throw new Exception('resource_type is required');
		}

		if(array_key_exists('response',$params) && $params['response'] instanceof Response) {
			$this->response = $params['response'];
			$this->parseResponse();
		} else {
			throw new Exception('response is required');
		}
	}

	public function setTotal($total) {
		$this->total = $total;
	}

	public function getTotal() {
		return $this->total;
	}

	public function setFiltered($filtered) {
		$this->filtered = $filtered;
	}

	public function getFiltered() {
		return $this->filtered;
	}

	public function offsetExists($offset) {
		return array_key_exists($offset, $this->resources);
	}

	public function offsetGet($offset) {
		return $this->resources[$offset];
	}

	public function offsetSet($offset, $value) {
		$this->resources[$offset] = $value;
	}

	public function offsetUnset($offset) {
		unset($this->resources[$offset]);
	}

	public function current() {
		return current($this->resources);
	}

	public function key() {
		return key($this->resources);
	}

	public function next() {
		next($this->resources);
	}

	public function rewind() {
		reset($this->resources);
	}

	public function valid() {
		return (current($this->resources) !== false);
	}

	private function parseResponse() {
		$decoded = $this->client->decode($this->response);

		$this->resources = [];
		foreach($decoded['records'] as $record) {
			$params = array_merge([
					'client' => $this->client
				],
				(array) $record
			);

			$type = $this->resource_type;
			$this->resources[] = new $type($params);
		}

		$this->setTotal($decoded['total']);
		$this->setFiltered($decoded['filtered']);
	}
}
