<?php

require 'vendor/autoload.php';

use Goutte\Client;

class ExportSite
{
	public function __construct()
	{
		$files = $this->getHtmlFiles();
		foreach ($files as $file) {
			$scrape[] = $this->scrapePage('http://export-intranet-lawcom.dev/dump/' . $file);
		}
		$file = fopen("export.json", "w");
		fwrite($file, json_encode($scrape));
		fclose($file);
	}

	public function getHtmlFiles()
	{
		$files = scandir( "dump" );
		$html_files = array();
		foreach ( $files as $file ) {
			if( preg_match( '/^.*\.htm$/i', $file ) ) {
				$html_files[] = $file;
			}
		}
		return $html_files;
	}

	public function scrapePage($url)
	{
		$client = new Client();
		$crawler = $client->request('GET', $url);

		// Generate slug
		$parse_url = pathinfo($url);
		$page['slug'] = str_replace("/dump/", "", $parse_url['filename']);

		// Generate title
		$title = $crawler->filter('#content > h1')->each(function ($node) {
		   return $node->text();
		});
		$page['title'] = $title[0];

		// Generate content
		$content = $crawler->filter('#content')->each(function ($node) {
			$start = "<hr>";
			$end = "<!-- footer -->";
			$data = $node->html();
		  $data = stristr($data, $start);
		  $data = substr($data, strlen($start));
		  $stop = stripos($data, $end);
		  $data = substr($data, 0, $stop);
		  $data = preg_replace_callback(
		  	"#(<\s*a\s+[^>]*href\s*=\s*[\"'])(?!http|mailto)([^\"'>]+)([\"'>]+)#",
		  	function($matches) {
		  		$matches[2] = str_replace(".htm", "", $matches[2]);
		  		$matches[2] = str_replace("docs/", "wp-content/uploads/", $matches[2]);
		  		return $matches[1] . $matches[2] . $matches[3];
		  	},
		  	$data
		  );
		  return $data;
		});
		$page['content'] = $content[0];

		// Generetae last updated date
		$last_updated = $content = $crawler->filter('.footer')->each(function ($node) {
			$start = ":";
			$data = $node->text();
		  $data = stristr($data, $start);
		  $data = substr($data, strlen($start));
		  $data = preg_replace( "/\r|\n/", "", $data );
		  $data = trim($data);
		  $date = DateTime::createFromFormat('j-M-Y', '15-Feb-2009');
			return $date->format('Y-m-d');
		});
		$page['last_updated'] = $last_updated[0];

		return $page;
	}
}

$export = new ExportSite();
