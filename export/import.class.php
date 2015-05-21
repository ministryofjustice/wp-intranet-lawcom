<?php

include($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

class ImportSite
{
	public function __construct()
	{
		$json = file_get_contents( 'export.json' );
    $pages = json_decode( $json );
    foreach ($pages as $page) {
      $post = array(
        'post_content' => $page->content,
        'post_title' => $page->title,
        'post_name' => $page->slug,
        'post_date' => $page->last_updated,
        'post_date_gmt' => $page->last_updated,
        'post_type' => $page->post_type,
        'post_status' => 'publish'
      );
      wp_insert_post( $post, $error );
    }
	}
}

$import = new ImportSite();
