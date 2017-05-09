<?php

namespace RavenTools\SiteAuditorSdk\Resources;

class CrawlSession extends Common {

	protected $get_endpoint = "crawl_sessions/{id}";
	protected $delete_endpoint = "crawl_sessions/{id}";
	protected $previous_endpoint = "crawl_sessions/{id}/previous";
	protected $response_key = 'crawl';

	public function previous($resource_id = null) {
		if(is_null($resource_id)) {
			$resource_id = $this->id;
		}

		$params = [
			'id' => $resource_id,
		];

		$uri = $this->substitute($this->previous_endpoint, $params);
		$response = $this->client->request('POST', $uri);

		return $this->client->decode($response);
	}
}
