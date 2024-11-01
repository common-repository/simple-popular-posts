<?php
/*
Plugin Name: Simple Popular Posts Widget
Plugin URI: http://www.jimmysun.net/teknologi/wordpress/wordpress-plugin-simple-popular-posts
Description: Creates a very simple and basic widget for your sidebar to display most popular posts on your blog based on the number of comments only.
Author: Jimmy Sun
Version: 1.0
Author URI: http://www.jimmysun.net/
*/

add_action("plugins_loaded", "initialize_simple_popular");

function simple_popular($arg) {
	global $wpdb;

	if ($arg == 1)
		$list = true;
	else
		$list = false;

	$settings = get_option("widget_simple_popular");
	if (!is_array( $settings ))
		{
		$settings = array('NumOfPosts' => '5', 'ShowNumOfComments' => 'checked', 'WidgetTitle' => 'Popular Posts');
		}

	//retrieve from the database
	$posts = $wpdb->get_results("SELECT ID, post_title, comment_count FROM " . $wpdb->posts . " WHERE post_type = 'post' && post_status = 'publish' && comment_count > 0 ORDER BY comment_count DESC LIMIT " . $settings['NumOfPosts']);
	?>

	<!--displays the widget title, you can edit here to match your theme-->
	<div class="widget"><div class="widget-title"><h3><?php echo $settings['WidgetTitle']; ?></h3></div>

	<ul>

	<?php

	//display the posts
	if (!empty($posts)) {
	foreach ($posts as $links) {
		if ($settings['ShowNumOfComments'] == 'checked')
			$NumOfComments = ' (' . $links->comment_count . ')';
			echo "\t" . '<li><a href="' . get_permalink($links->ID) . '">' . $links->post_title . '</a>' . $NumOfComments . '</li>' . "\n";
		}
	}
	else
		_e('No posts to display', 'simple_popular_posts');
	?>
	</ul></div>

	<?php
}

function initialize_simple_popular(){
	//initialize the widget
    register_sidebar_widget("Simple Popular Posts", "simple_popular");
	register_widget_control('Simple Popular Posts', 'simple_popular_control', 200, 300 );
}

function simple_popular_control() {
	$settings = get_option("widget_simple_popular");

	//if no settings yet
	if (!is_array( $settings ))
		{
			$settings = array('NumOfPosts' => '5', 'ShowNumOfComments' => 'checked', 'WidgetTitle' => 'Popular Posts');
	  	}

	//submit the settings
	if ($_POST['simple_popular-Submit'])
	  {
		$settings['WidgetTitle'] = htmlspecialchars($_POST['simplepopular-WidgetTitle']);
		$settings['NumOfPosts'] = htmlspecialchars($_POST['simplepopular-NumOfPosts']);
		$settings['ShowNumOfComments'] = htmlspecialchars($_POST['simplepopular-ShowNumOfComments']);

		update_option("widget_simple_popular", $settings);
	  }

	//create the widget panel
	?>
	<p>
		<label for="simplepopular-WidgetTitle"><?php _e('Title: ', 'simple_popular_posts'); ?> </label>
		<input type="text" id="simplepopular-WidgetTitle" name="simplepopular-WidgetTitle" value="<?php echo $settings['WidgetTitle'];?>" />
	</p>

	<p>
		<label for="simplepopular-NumOfPosts"><?php _e('Number of posts to display: ', 'simple_popular_posts'); ?></label>
		<input type="text" id="simplepopular-NumOfPosts" name="simplepopular-NumOfPosts" value="<?php echo $settings['NumOfPosts'];?>" size="5" />
	</p>

	<p>
		<input type="checkbox" id="simplepopular-ShowNumOfComments" name="simplepopular-ShowNumOfComments" value="checked" <?php echo $settings['ShowNumOfComments'];?> />
		<label for="simplepopular-ShowNumOfComments"><?php _e('Show Number of Comments ', 'simple_popular_posts'); ?></label>
		<input type="hidden" id="simple_popular-Submit" name="simple_popular-Submit" value="1" />
	</p>

	<?php
}

$plugin = plugin_basename(__FILE__);

function simple_popular_posts_donate( $links ) {
 // Add links to the plugin page
 $settings_link = '<a href="http://www.jimmysun.net/teknologi/wordpress/wordpress-plugin-simple-popular-posts">Donate</a>';
 array_unshift( $links, $settings_link );
 return $links;
}

add_filter("plugin_action_links_$plugin", 'simple_popular_posts_donate' );?>