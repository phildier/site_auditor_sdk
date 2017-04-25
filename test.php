<?php

include('vendor/autoload.php');

use RavenTools\SiteAuditorSdk\Client;
use RavenTools\SiteAuditorSdk\Management;
use RavenTools\SiteAuditorSdk\Resources\Site;
use RavenTools\SiteAuditorSdk\Resources\CrawlSession;
use RavenTools\SiteAuditorSdk\Resources\Issue;
use RavenTools\SiteAuditorSdk\Resources\ResolvedIssue;
use RavenTools\SiteAuditorSdk\Resources\Usage;

$auth_token = load_var("auth_token.txt");
$client_id = load_var("client_id.txt");
$client_secret = load_var("client_secret.txt");

$client = new Client([
	'auth_token' => $auth_token,
	'base_uri' => 'http://standalone.site:8088/api/v2/',
	'debug' => true
]);

$command = "list";
if(count($argv) > 1) {
	$command = $argv[1];
}

switch($command) {
case "createtoken":
	$management = new Management([
		'client' => $client,
		'client_name' => 'raventools',
		'client_id' => $client_id,
		'client_secret' => $client_secret
	]);

	$token = $management->createToken([
		'user_id' => 1,
		'name' => 'Test McTesty',
		'email' => 'test@example.com',
		'package' => 'API-Agency'
	]);

	print_r($token);

	break;

case "listsites":
	$sites = $client->factory(Site::class)->all();

	echo "sites: ".count($sites)."\n";

	foreach($sites as $site) {
		echo $site;
	}

	break;

case "getsite":
	if(!isset($argv[2])) {
		echo "id argument required\n";
		exit(1);
	}
	$id = $argv[2];

	echo $client->factory(Site::class)->get($id);

	break;

case "crawlsite":
	if(!isset($argv[2])) {
		echo "id argument required\n";
		exit(1);
	}
	$id = $argv[2];

	print_r($client->factory(Site::class)->crawl($id));

	break;

case "createsite":
	if(!isset($argv[2])) {
		echo "url argument required\n";
		exit(1);
	}
	$url = $argv[2];

	$site = $client->factory(Site::class)->create([
		'url' => $url,
		'max_urls' => 10
	]);

	echo $site;

	break;

case "updatesite":
	if(!isset($argv[2])) {
		echo "id argument required\n";
		exit(1);
	}
	$id = $argv[2];

	$site = $client->factory(Site::class)->get($id);
	$site->crawl_interval = 'weekly';

	echo $site->update();

	break;

case "deletesite":
	if(!isset($argv[2])) {
		echo "id argument required\n";
		exit(1);
	}
	$id = $argv[2];

	$response = $client->factory(Site::class)->delete($id);

	print_r($response);

	break;

case "getsession":
	if(!isset($argv[2])) {
		echo "id argument required\n";
		exit(1);
	}
	$id = $argv[2];

	$response = $client->factory(CrawlSession::class)->get($id);

	echo $response;

	break;

case "deletesession":
	if(!isset($argv[2])) {
		echo "id argument required\n";
		exit(1);
	}
	$id = $argv[2];

	$response = $client->factory(CrawlSession::class)->delete($id);

	echo $response;

	break;

case "listissues":
	if(!isset($argv[2])) {
		echo "id argument required\n";
		exit(1);
	}
	$id = $argv[2];

	$response = $client->factory(Issue::class)->all([
		'crawl_session_id' => $id
	]);

	foreach($response as $key => $issue) {
		echo $issue;
	}

	break;

case "getissuetable":
	if(!isset($argv[2])) {
		echo "id argument required\n";
		exit(1);
	}
	$id = $argv[2];

	if(!isset($argv[3])) {
		echo "issue_name argument required\n";
		exit(1);
	}
	$issue_name = $argv[3];

	$issues = $client->factory(Issue::class)->table([
		'crawl_session_id' => $id,
		'issue_name' => $issue_name,
		'order' => 'duplicate_url desc'
	]);

	foreach($issues as $issue) {
		echo $issue;
	}

	break;

case "createresolvedissue":
	if(!isset($argv[2])) {
		echo "id argument required\n";
		exit(1);
	}
	$id = $argv[2];

	if(!isset($argv[3])) {
		echo "issue_name argument required\n";
		exit(1);
	}
	$issue_name = $argv[3];

	if(!isset($argv[4])) {
		echo "issue_key argument required\n";
		exit(1);
	}
	$issue_key = $argv[4];

	$response = $client->factory(ResolvedIssue::class)->create([
		'crawl_session_id' => $id,
		'issue_name' => $issue_name,
		'issue_key' => $issue_key
	]);

	echo $response;

	break;

case "deleteresolvedissue":
	if(!isset($argv[2])) {
		echo "id argument required\n";
		exit(1);
	}
	$id = $argv[2];

	if(!isset($argv[3])) {
		echo "issue_name argument required\n";
		exit(1);
	}
	$issue_name = $argv[3];

	if(!isset($argv[4])) {
		echo "issue_key argument required\n";
		exit(1);
	}
	$issue_key = $argv[4];

	$response = $client->factory(ResolvedIssue::class)->delete([
		'crawl_session_id' => $id,
		'issue_name' => $issue_name,
		'issue_key' => $issue_key
	]);

	print_r($response);

	break;

case "getusage":
	$response = $client->factory(Usage::class)->get();

	echo $response;

	break;
}

function load_var($filename) {
	if(file_exists($filename)) {
		return trim(file_get_contents($filename));
	}
}
