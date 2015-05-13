<?php use Roots\Sage\Titles; global $post; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta name="DC.title" content="Law Commission: <?= Titles\title(); ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta name="Keywords" content="">
    <meta name="DC.subject.keyword" content="">
    <meta name="Description" content="">
    <meta name="DC.description" content="">
    <meta name="DC.creator" content="Law Commission">
    <meta name="DC.contributor" content="">
    <meta name="DC.identifier" content="<?= $post->post_name; ?>">
    <meta name="DC.date.created" content="<?= get_the_date( 'Y-m-d', get_the_ID() ); ?>">
    <meta name="DC.date.modified" content="<?php echo get_the_modified_date( 'Y-m-d' ); ?>">
    <meta name="DC.publisher" content="Law Commission">
    <meta name="eGMS.subject.category" scheme="GCL" content="Crime, Law, Justice and Rights">
    <meta name="DC.format" content="Text/HTML">
    <meta name="DC.language" content="eng">
    <meta name="DC.coverage" content="England, Wales">
    <meta name="DC.rights.copyright" content="Crown Copyright">
    <?php wp_head(); ?>
  </head>
