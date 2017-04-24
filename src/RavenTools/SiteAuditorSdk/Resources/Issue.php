<?php

namespace RavenTools\SiteAuditorSdk\Resources;

class Issue extends Common {

	protected $all_endpoint = "issues/{crawl_session_id}";
	protected $table_endpoint = "issues/{crawl_session_id}/{issue_name}";
	protected $response_key = 'issue';

	public function table($params = []) {

		$params = array_merge($this->data, $params);

		$uri = $this->substitute($this->table_endpoint,$params);
		$response = $this->client->request('GET', $uri, $params);

		$decoded = $this->client->decode($response);

		$issue_records = [];
		foreach($decoded['records'] as $row) {
			$row['client'] = $this->client;
			$issue_records[] = new IssueRecord($row);
		}

		return $issue_records;
	}
}
