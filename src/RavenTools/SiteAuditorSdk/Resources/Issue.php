<?php

namespace RavenTools\SiteAuditorSdk\Resources;

use RavenTools\SiteAuditorSdk\ResourceCollection;

class Issue extends Common {

	protected $all_endpoint = "issues/{crawl_session_id}";
	protected $table_endpoint = "issues/{crawl_session_id}/{issue_name}";
	protected $response_key = 'issue';

	public function table($params = []) {

		$params = array_merge($this->data, $params);

		$uri = $this->substitute($this->table_endpoint,$params);
		$response = $this->client->request('GET', $uri, $params);

		return new ResourceCollection([
			'client' => $this->client,
			'resource_type' => static::class,
			'response' => $response
		]);
	}
}
