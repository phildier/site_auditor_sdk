<?php

namespace RavenTools\SiteAuditorSdk\Resources;

class CrawlSession extends Common {

	protected $get_endpoint = "crawl_sessions/{id}";
	protected $delete_endpoint = "crawl_sessions/{id}";
	protected $response_key = 'crawl';
}
