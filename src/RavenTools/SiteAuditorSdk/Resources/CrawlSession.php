<?php

namespace RavenTools\SiteAuditorSdk\Resources;

class CrawlSession extends Common {

	protected $get_endpoint = "crawl_sessions/{id}";
	protected $delete_endpoint = "crawl_sessions/{id}";
	protected $previous_endpoint = "crawl_sessions/{id}/previous";
	protected $history_endpoint = "crawl_sessions/history/{site_id}";
	protected $response_key = 'crawl';

	public function previous($resource_id = null) {
		if(is_null($resource_id)) {
			$resource_id = $this->id;
		}

		$params = [
			'id' => $resource_id,
		];

		$uri = $this->substitute($this->previous_endpoint, $params);
		$response = $this->client->request('GET', $uri);

		return $this->client->decode($response);
	}

	public function history($resource_id) {
		$params = [
			'site_id' => $resource_id,
		];

		$uri = $this->substitute($this->history_endpoint, $params);
		$response = $this->client->request('GET', $uri);

		return $this->client->decode($response);
	}
}
