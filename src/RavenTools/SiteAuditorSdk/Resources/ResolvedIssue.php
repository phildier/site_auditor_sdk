<?php

namespace RavenTools\SiteAuditorSdk\Resources;

class ResolvedIssue extends Common {

	protected $create_endpoint = "resolved_issues/{crawl_session_id}/{issue_name}/{issue_key}";
	protected $delete_endpoint = "resolved_issues/{crawl_session_id}/{issue_name}/{issue_key}";
	protected $response_key = "resolved_issue";
}
