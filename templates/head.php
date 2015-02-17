<!doctype html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	
	<!-- Prevent text size adjustment on orientation change. -->
	<style>html { -webkit-text-size-adjust: 100%; }</style>
	
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
	
	<title><?php wp_title('|', true, 'right'); ?></title>
	
	<meta name="application-name" content="Guts Church"/>
	
	<!-- For IE10 & Metro: -->
	<meta name="msapplication-TileColor" content="#a20000">
	<meta name="msapplication-TileImage" content="<?php echo site_url(); ?>/logo/square_com/apple-touch-icon-144x144.png">
	<meta name="msapplication-square70x70logo" content="<?php echo site_url(); ?>/logo/square/mstile-70x70.png" />
	<meta name="msapplication-square150x150logo" content="<?php echo site_url(); ?>/logo/square/mstile-150x150.png" />
	<meta name="msapplication-wide310x150logo" content="<?php echo site_url(); ?>/logo/square/mstile-310x150.png" />
	<meta name="msapplication-square310x310logo" content="<?php echo site_url(); ?>/logo/square/mstile-310x310.png" />
	<meta name="msapplication-notification" content="frequency=30;polling-uri=http://notifications.buildmypinnedsite.com/?feed=http://gutschurch.com/feed&amp;id=1;polling-uri2=http://notifications.buildmypinnedsite.com/?feed=http://gutschurch.com/feed&amp;id=2;polling-uri3=http://notifications.buildmypinnedsite.com/?feed=http://gutschurch.com/feed&amp;id=3;polling-uri4=http://notifications.buildmypinnedsite.com/?feed=http://gutschurch.com/feed&amp;id=4;polling-uri5=http://notifications.buildmypinnedsite.com/?feed=http://gutschurch.com/feed&amp;id=5;cycle=1" />
	
	<!-- For iPad with high-resolution Retina display running iOS ≥ 7: -->
	<link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?php echo site_url(); ?>/logo/square/apple-touch-icon-152x152.png">
	
	<!-- For iPad with high-resolution Retina display running iOS ≤ 6: -->
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo site_url(); ?>/logo/square/apple-touch-icon-144x144.png">
	
	<!-- For iPhone with high-resolution Retina display running iOS ≥ 7: -->
	<link rel="apple-touch-icon-precomposed" sizes="120x120" href="<?php echo site_url(); ?>/logo/square/apple-touch-icon-120x120.png">
	
	<!-- For iPhone with high-resolution Retina display running iOS ≤ 6: -->
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo site_url(); ?>/logo/square/apple-touch-icon-114x114.png">
	
	<!-- For first- and second-generation iPad: -->
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo site_url(); ?>/logo/square/apple-touch-icon-72x72.png">
	
	<!-- For non-Retina iPhone, iPod Touch, and Android 2.1+ devices: -->
	<link rel="apple-touch-icon-precomposed" href="<?php echo site_url(); ?>/logo/square/apple-touch-icon-57x57.png">
	
	<?php wp_head(); ?>
	
	<link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo('name'); ?> Feed" href="<?php echo esc_url(get_feed_link()); ?>">
</head>
