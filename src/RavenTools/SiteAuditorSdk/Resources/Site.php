<?php

namespace RavenTools\SiteAuditorSdk\Resources;

class Site extends Common {

	protected $all_endpoint = "sites";
	protected $get_endpoint = "sites/{id}";
	protected $create_endpoint = "sites";
	protected $update_endpoint = "sites/{id}";
	protected $delete_endpoint = "sites/{id}";
	protected $response_key = 'site';
}
