<?php

namespace RavenTools\SiteAuditorSdk\Resources;

class Site extends Common {

	protected $all_endpoint = "sites";
	protected $get_endpoint = "sites/{id}";
	protected $create_endpoint = "sites";
	protected $update_endpoint = "sites/{id}";
	protected $delete_endpoint = "sites/{id}";
	protected $crawl_endpoint = "sites/{id}/crawl";
	protected $response_key = 'site';

	public function crawl($resource_id = null) {

		if(is_null($resource_id)) {
			$resource_id = $this->id;
		}

		$params = [
			'id' => $resource_id,
			'method' => 'manually'
		];

		$uri = $this->substitute($this->crawl_endpoint, $params);
		$response = $this->client->request('POST', $uri, ['json' => $params]);

		return $this->client->decode($response);
	}
}
