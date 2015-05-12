<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);

include($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

class ImportSite
{
	public function __construct()
	{
		$json = file_get_contents( 'export.json' );
    $pages = json_decode( $json);
    foreach ($pages as $page) {
      $date = DateTime::createFromFormat('Y-m-d', $page->last_updated);
      $post = array(
        'post_content' => $page->content,
        'post_title' => $page->title,
        'post_name' => $page->slug,
        'post_date' => $date->format('Y-m-d H:i:s'),
        'post_date_gmt' => $date->format('Y-m-d H:i:s'),
        'post_type' => 'page',
        'post_status' => 'publish'
      );
      wp_insert_post( $post, $error );
    }
	}
}

$import = new ImportSite();
