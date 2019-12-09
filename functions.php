<?php


/*** Child Theme Function  ***/
function grafik_qodef_child_theme_enqueue_scripts() {
	wp_register_style( 'childstyle', get_stylesheet_directory_uri() . '/style.css'  );
	wp_enqueue_style( 'childstyle' );
}


add_action('wp_enqueue_scripts', 'grafik_qodef_child_theme_enqueue_scripts', 11);







////////////////////
//
//  MARK CHANGE
//
///////////////////////
//Blue Sage is an outlier. That's all. It's using a shortcode.
add_action('wp_enqueue_scripts','blue_sage_css');
function blue_sage_css() {
	if(stripos(get_permalink(),'blue-sage'))
	{
		$stylesheet_uri = get_stylesheet_directory_uri();
		wp_enqueue_style('blue_sage_css',$stylesheet_uri.'/overlays/css/blue_sage.css');
		wp_enqueue_script('blue_sage_autoptimize',$stylesheet_uri.'/overlays/js/autoptimize.js');
		wp_enqueue_script('blue_sage_jquery',$stylesheet_uri.'/overlays/js/jquery.js');
	}
}
add_shortcode('blue_sage_creek_overlay','blue_sage_creek_overlay');
function blue_sage_creek_overlay()
{
	include_once('overlays/blue_sage_data_tag_filled.php');
	return;
}
//500 means the very end so it goes underneath the Google Maps API declaration
add_action('wp_footer', 'before_closing_tag', 500);
function before_closing_tag() 
{
	//Child Theme Url (Grafix-Child)
	$stylesheet_uri = get_stylesheet_directory_uri();
	//the array key is the post "slug", a more immutable characteristic. Consider using post name if the need arises
	$portfolio_item_data = array(
		'bluewater' => array(
			'overlay_file' => 'overlays/bluewater.php',
			'overlay_image' => $stylesheet_uri . '/overlays/images/bluewater.png',
			'xml_data_location_staging' => 'http://staging.lanohadevelopment.flywheelsites.com/development/bluewater',
			'xml_data_location_production' => 'http://lanohadevelopment.com/development/bluewater'
		),
		'the-sanctuary' => array(
			'overlay_file' => 'overlays/the_sanctuary.php',
			'overlay_image' => $stylesheet_uri . '/overlays/images/the_sanctuary.png',
			'xml_data_location_staging' => 'http://staging.lanohadevelopment.flywheelsites.com/development/the-sanctuary',
			'xml_data_location_production' => 'http://lanohadevelopment.com/development/the-sanctuary'
		),
		'the-prairies' => array(
			'overlay_file' => 'overlays/the_prairies.php',
			'overlay_image' => $stylesheet_uri . '/overlays/images/the_prairies.png',
			'xml_data_location_staging' => 'http://staging.lanohadevelopment.flywheelsites.com/development/the-prairies',
			'xml_data_location_production' => 'http://lanohadevelopment.com/development/the-prairies'
		)
	);
	//I could have put the WP_ENQUEUE_SCRIPT in just one line without the action, but it will need to get the post type & post_slug in order to determine the overlay JS file to insert into the Google Maps div that the WPBakery Page Builder is looking at!
	global $post;
	$post_slug = trim($post->post_name);
	//$overlay_include_path is a PHP file
	//Residence Node gives different values based on the slug of the current post
	//If we're dealing with a post who's title/slug is in the keys of the large array above
	if(in_array($post_slug,array_keys($portfolio_item_data)))
	{
		$residence_node = $portfolio_item_data[$post_slug];
		//Overlay File is the JavaScript in the PHP file
		$overlay_include_path = $residence_node['overlay_file'];
		//Copy the Production XML feed into the overlays subfolder. The sole purpose of this is to get around the Cross Origin Domain Limitation
		//
		//IF STAGING
		if(stripos(get_permalink(),'staging'))
		{
			$xml_data_path = $residence_node['xml_data_location_staging'];
		}
		//IF PRODUCTION
		else
		{
			$xml_data_path = $residence_node['xml_data_location_production'];
		}
		$src_image = $residence_node['overlay_image'];
		include_once($overlay_include_path);
	}
}
////////////////////
//
//  END MARK CHANGE
//
////////////////////





////////////////////
//
//  JOB #2) GENERATE XML FOR GRAFIX-CHILD THEME
//
////////////////////
function all_lots_for_development( $query ) {
if (is_tax()) {
        $query->set( 'posts_per_page', -1 );
    }
}
add_action( 'pre_get_posts', 'all_lots_for_development' );