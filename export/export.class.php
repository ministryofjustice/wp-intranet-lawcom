<?php header("Content-Type: text/html; charset=UTF-8"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<?php
include($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
require 'vendor/autoload.php';
use Goutte\Client;

class ExportSite
{
	public function __construct()
	{
		$file = fopen("internal_html.csv","r");
		$contents = fread($file, filesize("internal_html.csv"));
		$urls = explode("\r", $contents);
		$posts_url = "http://intranet-lawcom.dev/export/dump/archive.htm";

		foreach($urls as $url) {
			if($url != $posts_url) { // We have a page
				$scrape[] = $this->scrapePage($url);
			} else { // We have a post
				$scrape = array_merge($scrape, $this->scrapePost($url));
			}
		}
		$this->import($scrape);
	}

	protected function scrapePost($url)
	{
		$client = new Client();
		$crawler = $client->request('GET', $url);

		$posts = $crawler->filter('#content > ul > li')->each(function ($node) use (&$posts) {
			$content = preg_replace("/<strong>(.*?)<\/strong><br><br>|<strong>(.*?)<\/strong><br>|<strong>(.*?)<\/strong>/", "", $node->html(), 1);
			$content = preg_replace("/http:\/\/intranet.justice.gsi.gov.uk\/lawcommission\//", "", $content);
			$content = preg_replace_callback(
		  	"#(<\s*a\s+[^>]*href\s*=\s*[\"'])(?!http|mailto|javascript|\#)([^\"'>]+)([\"'>]+)#",
		  	function($matches) {
		  		$matches[2] = str_replace(".htm", "", $matches[2]);
		  		$matches[2] = str_replace("docs/", "wp-content/uploads/", $matches[2]);
		  		if(is_numeric($matches[2])) {
		  			$matches[2] .= "-2";
		  		}
		  		return $matches[1] . '/' . $matches[2] . $matches[3];
		  	},
		  	$content
		  );
		  $content = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $content);
			$content = mb_convert_encoding($content, 'HTML-ENTITIES', 'iso-8859-1');
			$content = utf8_encode($content);
			echo $content;

		  preg_match("/<strong>(.*?)<\/strong>/", $node->html(), $matches);
		  $split = explode("<br>", $matches[1]);

		  $date = htmlentities($split[0], null, 'utf-8');
			$date = str_replace("&nbsp;", " ", $date);
			$date = html_entity_decode($date);
			$date = strip_tags($date);
			$date = preg_replace("/ \(.*\)/", "", $date);
			$date = preg_replace("/(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday) /", "", $date);
			$date = str_replace("Apil", "April", $date);
			$date = preg_replace("/^14 December$/", "14 December 2010", $date);
			$date = DateTime::createFromFormat('j F Y', $date);

			if(!isset($split[1]) || empty($split[1])) {
				$split[1] = "Professor Elizabeth Cooke";
			}

		  return [
		  	"title" => $split[1],
		   	"content" => $content,
		   	"last_updated" => $date->format('Y-m-d H:i:s'),
		   	"post_type" => 'post'
		  ];

		});
		return $posts;
	}

	protected function scrapePage($url)
	{
		$client = new Client();
		$crawler = $client->request('GET', $url);

		// Generate slug
		$parse_url = pathinfo($url);
		$page['slug'] = str_replace("/dump/", "", $parse_url['filename']);
		if(is_numeric($page['slug'])) {
			$page['slug'] .= "-2";
		}

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
		  $data = preg_replace("/http:\/\/intranet.justice.gsi.gov.uk\/lawcommission\//", "", $data);
		  $data = preg_replace_callback(
		  	"#(<\s*a\s+[^>]*href\s*=\s*[\"'])(?!http|mailto|javascript|\#)([^\"'>]+)([\"'>]+)#",
		  	function($matches) {
		  		$matches[2] = str_replace(".htm", "", $matches[2]);
		  		$matches[2] = str_replace("docs/", "wp-content/uploads/", $matches[2]);
		  		if(is_numeric($matches[2])) {
		  			$matches[2] .= "-2";
		  		}
		  		return $matches[1] . '/' . $matches[2] . $matches[3];
		  	},
		  	$data
		  );
		  return $data;
		});
		$page['content'] = $content[0];
		$page['content'] = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $page['content']);
		$page['content'] = mb_convert_encoding($page['content'], 'HTML-ENTITIES', 'iso-8859-1');
		$page['content'] = utf8_encode($page['content']);

		// Generetae last updated date
		$last_updated = $content = $crawler->filter('.footer')->each(function ($node) {
			$start = ":";
			$data = $node->text();
		  $data = stristr($data, $start);
		  $data = substr($data, strlen($start));
		  $data = preg_replace( "/\r|\n/", "", $data );
		  $data = trim($data);
		  $date = DateTime::createFromFormat('j M Y', $data);
			return $date->format('Y-m-d H:i:s');
		});
		$page['last_updated'] = $last_updated[0];

		// Set post type
		$page['post_type'] = "page";

		return $page;
	}

	public function import($pages)
	{
    foreach ($pages as $page) {
      $post = array(
        'post_content' => $page['content'],
        'post_title' => $page['title'],
        'post_name' => $page['slug'],
        'post_date' => $page['last_updated'],
        'post_date_gmt' => $page['last_updated'],
        'post_type' => $page['post_type'],
        'post_status' => 'publish'
      );
      wp_insert_post( $post, $error );
    }
	}
}

$export = new ExportSite();

?>
</body>
</html>
