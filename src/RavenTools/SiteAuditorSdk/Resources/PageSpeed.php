<?php

namespace RavenTools\SiteAuditorSdk\Resources;

use RavenTools\SiteAuditorSdk\ResourceCollection;

class PageSpeed extends Common {

	protected $get_endpoint = "page_speed/{crawl_session_id}/{strategy}";
	protected $response_key = 'page_speed';

	public function get($params = []) {
		if(is_null($this->get_endpoint)) {
			throw new BadMethodCallException('method not supported on this resource');
		}

		$params = array_merge($this->data,$params);
		$uri = $this->substitute($this->get_endpoint,$params);
		$response = $this->client->request('GET', $uri, ['json' => $params]);

		$decoded = $this->client->decode($response);

		$params = (array) $decoded[$this->response_key];
		$params['client'] = $this->client;

		return new static($params);
	}
}
