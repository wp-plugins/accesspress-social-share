<?php 
defined( 'ABSPATH' ) or die( "No script kiddies please!" );
/*
Plugin name: AccessPress Social Share
Plugin URI: https://accesspressthemes.com/wordpress-plugins/accesspress-social-share/
Description: A plugin to add various social media shares to a site with dynamic configuration options.
Version: 1.0.0
Author: AccessPress Themes
Author URI: http://accesspressthemes.com
Text Domain:apss-share
Domain Path: /languages/
License: GPLv2 or later
*/

//Decleration of the necessary constants for plugin
if( !defined( 'APSS_IMAGE_DIR' ) ) {
	define( 'APSS_IMAGE_DIR', plugin_dir_url( __FILE__ ) . 'images' );
}

if( !defined( 'APSS_JS_DIR' ) ) {
	define( 'APSS_JS_DIR', plugin_dir_url( __FILE__ ) . 'js' );
}

if( !defined( 'APSS_CSS_DIR' ) ) {
	define( 'APSS_CSS_DIR', plugin_dir_url( __FILE__ ) . 'css' );
}


if( !defined( 'APSS_LANG_DIR' ) ) {
	define( 'APSS_LANG_DIR', basename(dirname(__FILE__)) . '/languages/' );
}

if( !defined( 'APSS_VERSION' ) ) {
	define( 'APSS_VERSION', '1.0.0' );
}

if(!defined('APSS_TEXT_DOMAIN')){
	define( 'APSS_TEXT_DOMAIN', 'apss-share' );
}

if(!defined('APSS_SETTING_NAME')){
define( 'APSS_SETTING_NAME','apss_share_settings' );
}

//Decleration of the class for necessary configuration of a plugin

if( !class_exists( 'APSS_Class' ) ){
	class APSS_Class{
		function __construct() {
			register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) ); //load the default setting for the plugin while activating
			add_action( 'init', array( $this, 'plugin_text_domain' ) ); //load the plugin text domain
			add_action('init',array( $this,'session_init')); //start the session if not started yet.
			add_action('admin_enqueue_scripts', array($this, 'register_admin_assets')); //registers all the assets required for wp-admin
			add_filter( 'the_content', array($this, 'apss_the_content_filter' )); // add the filter function for display of social share icons in frontend
			add_filter( 'the_excerpt', array($this, 'apss_the_content_filter' )); // add the filter function for display of social share icons in frontend
			add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_assets' ) ); // registers all the assets required for the frontend
			add_action( 'admin_menu', array( $this, 'add_apss_menu' ) ); //register the plugin menu in backend
			add_action('admin_post_apss_save_options', array( $this, 'apss_save_options')); //save the options in the wordpress options table.
			add_action('admin_post_apss_restore_default_settings',array($this,'apss_restore_default_settings'));//restores default settings.
			add_action('admin_post_apss_clear_cache',array($this,'apss_clear_cache'));//clear the cache of the social share counter.
		}

		//called when plugin is activated
		function plugin_activation(){
			if( !get_option( APSS_SETTING_NAME ) ){
			include( 'inc/backend/activation.php' );
			}
		}

		//loads the text domain for translation
		function plugin_text_domain(){
			load_plugin_textdomain( APSS_TEXT_DOMAIN, false, APSS_LANG_DIR);
		}

		//add plugins menu in backend
		function add_apss_menu(){
			add_menu_page( 'AccessPress Social Share', 'AccessPress Social Share', 'manage_options', 'apss-share', array( $this, 'main_page' ), APSS_IMAGE_DIR . '/apss-icon.png' );
			add_submenu_page( 'apss-share', __( 'Social Icons Settings', 'apss-share' ), __( 'Social Icons Settings', 'apss-share' ), 'manage_options', 'apss-share', array( $this, 'main_page' ) );
		}

		//plugins backend admin page
		function main_page() {
			include('inc/backend/main-page.php');
		}

		//for saving the plugin settings
		function apss_save_options(){
			if ( isset( $_POST['apss_add_nonce_save_settings'] ) && isset( $_POST['apss_submit_settings'] ) && wp_verify_nonce( $_POST['apss_add_nonce_save_settings'], 'apss_nonce_save_settings') ){  
			include( 'inc/backend/save-settings.php' );
			}
            else
            {
                die('No script kiddies please!');
            }
		}

		//starts the session with the call of init hook
        function session_init(){
            if( !session_id() )
            {
                session_start();
            }
        }

        //returns the current page url
        function curPageURL() {
            $pageURL = 'http';
            if ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ) {
                $pageURL .= "s";
            }
            $pageURL .= "://";
            if ( $_SERVER["SERVER_PORT"] != "80" ) {
                $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            }
            return $pageURL;
        }

        //function to return the content filter for the posts and pages
		function apss_the_content_filter( $content ) {
			$options = get_option( APSS_SETTING_NAME );
			$apss_link_open_option=($options['dialog_box_options']=='1') ? "_blank": "";
			$twitter_user=$options['twitter_username'];
			$counter_enable_options=$options['counter_enable_options'];
			$api_link='';
			$icon_set_value=$options['social_icon_set'];
			$url=  $this->curPageURL();
			$text= get_the_title();
			$cache_period = ($options['cache_period'] != '') ? $options['cache_period']*60*60 : 24 * 60 * 60 ;
			$counter_share='';
			$count_share='';
			$counter_tweets='';
			$counter_googleplus='';
			$counter_pinterest='';
			$counter_linkedin='';
			$counter_digg='';
			foreach( $options['social_networks'] as $key=>$value ){
				if( intval($value)=='1' ){
					switch($key){
						case 'facebook':
						if($counter_enable_options ==='1'){
						$count_share=$this->get_fb( $url, $cache_period );
						$counter_share="<div class='count'>$count_share</div>";
						}
						$api_link .= "<div class='apss-facebook apss-single-icon'><a title='Share on Facebook' target='$apss_link_open_option' href='https://www.facebook.com/sharer/sharer.php?u=$url'><div class='apss-icon-block'><i class='fa fa-facebook'></i><span class='apss-social-text'>Share on Facebook</span><span class='apss-share'>share</span></div>$counter_share</a></div>";
						break;

						case 'twitter':
						if( $counter_enable_options ==='1' ){
						$count_tweets=$this->get_tweets( $url, $cache_period );
						$counter_tweets="<div class='count'>$count_tweets</div>";
						}
						if(isset( $twitter_user) && $twitter_user !='' ){
							$url=$url."@$twitter_user";
						}else{
							$url=$url;
						}
						$api_link .= "<div class='apss-twitter apss-single-icon'><a title='Share on Twitter' target='$apss_link_open_option' href='"."https://twitter.com/intent/tweet?source=webclient&amp;original_referer=$url&amp;text=$text&amp;url=$url'"."><div class='apss-icon-block'><i class='fa fa-twitter'></i><span class='apss-social-text'>Share on Twitter</span><span class='apss-share'>tweet</span></div>$counter_tweets</a></div>";
						break;

						case 'google-plus':
						if($counter_enable_options ==='1'){
						$count_googleplus=$this->get_plusones( $url, $cache_period );
						$counter_googleplus="<div class='count'>$count_googleplus</div>";
						}
						$link = 'https://plus.google.com/share?url='.$url;
						$api_link .="<div class='apss-google-plus apss-single-icon'><a title='Share on Google+' target='$apss_link_open_option' href='$link'><div class='apss-icon-block'><i class='fa fa-google-plus'></i><span class='apss-social-text'>Share on Google Plus</span><span class='apss-share'>share</span></div>$counter_googleplus</a></div>";
						break;

						case 'pinterest':
						$count=0;
						if($counter_enable_options ==='1'){
						$count_pinterest=$this->get_pinterest( $url, $cache_period );
						$counter_pinterest="<div class='count'>$count_pinterest</div>";
						}
						global $post;
						if(has_post_thumbnail()){
						$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
						$link = 'http://pinterest.com/pin/create/bookmarklet/?media='.$image[0].'&amp;url='.$url.'&amp;title='.get_the_title().'&amp;description='.$post->post_excerpt;
						$api_link .="<div class='apss-pinterest apss-single-icon'><a title='Share on Pinterest' target='$apss_link_open_option' href='$link'><div class='apss-icon-block'><i class='fa fa-pinterest'></i><span class='apss-social-text'>Share on $key</span><span class='apss-share'>share</span></div>$counter_pinterest</a></div>";
						}else{
							
						}
						break;
						
						case 'linkedin':
						if($counter_enable_options ==='1'){
						$count=$this->get_linkedin( $url, $cache_period );
						$counter_linkedin="<div class='count'>$count</div>";
						}
						$link = "http://www.linkedin.com/shareArticle?mini=true&amp;ro=true&amp;trk=JuizSocialPostSharer&amp;title=".$text."&amp;url=".$url;
						$api_link .="<div class='apss-linkedin apss-single-icon'><a title='Share on LinkedIn' target='$apss_link_open_option' href='$link'><div class='apss-icon-block'><i class='fa fa-linkedin'></i><span class='apss-social-text'>Share on $key</span><span class='apss-share'>share</span></div>$counter_linkedin</a></div>";
						break;

						case 'digg':
						if( $counter_enable_options ==='1' ){
						$count_digg=0;
						$counter_digg="<div class='count'>$count_digg</div>";
						}
						$link = "http://digg.com/submit?phase=2%20&amp;url=".$url."&amp;title=".$text;
						$api_link .="<div class='apss-digg apss-single-icon'><a title='Share on Digg' target='$apss_link_open_option' href='$link'><div class='apss-icon-block'><i class='fa fa-digg'></i><span class='apss-social-text'>Share on $key</span><span class='apss-share'>share</span></div>$counter_digg</a></div>";
						break;
						
						case 'email':
								if ( strpos( $options['apss_email_body'], '%%' ) || strpos( $options['apss_email_subject'], '%%' ) ) {
									$link = 'mailto:?subject='.$options['apss_email_subject'].'&amp;body='.$options['apss_email_body'];
									$link = preg_replace( array( '#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#', '#%%url%%#' ), array( get_the_title(), get_site_url(), get_permalink(), $url ), $link );
								}
								else {
									$link = 'mailto:?subject='.$options['apss_email_subject'].'&amp;body='.$options['apss_email_body'].": ".$url;
								}
								$api_link .="<div class='apss-email apss-single-icon'><a  title='Share it on Email' target='$apss_link_open_option' href='$link'><div class='apss-icon-block'><i class='fa  fa-envelope'></i><span class='apss-social-text'>Send a mail</span><span class='apss-share'>mail</span></div><div class='count'></div></a></div>";
						break;

						case 'print':
						$api_link .="<div class='apss-print apss-single-icon'><a title='Print' href='javascript:void(0);' onclick='window.print();return false;'><div class='apss-icon-block'><i class='fa fa-print'></i><span class='apss-social-text'>Print</span><span class='apss-share'>print</span></div><div class='count'></div></a></div>";
						break;

						default:
						echo "should not reach here";
				
						}
				}

			}	
			
				 $share_shows_in_options=$options['share_options'];
				 if(in_array('posts', $share_shows_in_options)){
				 	if($options['share_positions']=='below_content'){
					return $content."<div class='apss-social-share apss-theme-$icon_set_value clearfix' >$api_link</div>";
					}

					if($options['share_positions']=='above_content'){
					return "<div class='apss-social-share apss-theme-$icon_set_value clearfix'>$api_link</div>".$content;
					}

					if($options['share_positions']=='on_both'){
					return "<div class='apss-social-share apss-theme-$icon_set_value clearfix'>$api_link</div>".$content."<div class='apss-social-share apss-theme-$icon_set_value clearfix'>$api_link</div>";
					}

				 }else if(in_array('pages', $share_shows_in_options) && is_page()){
				 	if($options['share_positions']=='below_content'){
					return $content."<div class='apss-social-share apss-theme-$icon_set_value clearfix'>$api_link</div>";
					}

					if($options['share_positions']=='above_content'){
					return "<div class='apss-social-share apss-theme-$icon_set_value clearfix'>$api_link</div>".$content;
					}

					if($options['share_positions']=='on_both'){
					return "<div class='apss-social-share apss-theme-$icon_set_value clearfix'>$api_link</div>".$content."<div class='apss-social-share apss-theme-$icon_set_value clearfix'>$api_link</div>";
					}

				 }else if(in_array('archives', $share_shows_in_options) && is_archive()){

				 	if($options['share_positions']=='below_content'){
					return $content."<div class='apss-social-share apss-theme-$icon_set_value clearfix'>$api_link</div>";
					}

					if($options['share_positions']=='above_content'){
					return "<div class='apss-social-share apss-theme-$icon_set_value clearfix'>$api_link</div>".$content;
					}

					if($options['share_positions']=='on_both'){
					return "<div class='apss-social-share apss-theme-$icon_set_value clearfix'>$api_link</div>".$content."<div class='apss-social-share apss-theme-$icon_set_value clearfix'>$api_link</div>";
					}


				 }else if(in_array('categories', $share_shows_in_options) && is_category()){

				 	if($options['share_positions']=='below_content'){
					return $content."<div class='apss-social-share apss-theme-$icon_set_value clearfix'>$api_link</div>";
					}

					if($options['share_positions']=='above_content'){
					return "<div class='apss-social-share apss-theme-$icon_set_value clearfix'>$api_link</div>".$content;
					}

					if($options['share_positions']=='on_both'){
					return "<div class='apss-social-share apss-theme-$icon_set_value clearfix'>$api_link</div>".$content."<div class='apss-social-share apss-theme-$icon_set_value clearfix'>$api_link</div>";
					}

				 }else{
				 	return $content;
				 }


		}

		function register_admin_assets(){
			/**
			 * Backend CSS
			 * */
			if(isset($_GET['page']) && $_GET['page']=='apss-share'){
			wp_enqueue_style( 'aps-admin-css', APSS_CSS_DIR . '/backend.css',false,APSS_VERSION ); //registering plugin admin css
			wp_enqueue_style( 'fontawesome-css', APSS_CSS_DIR . '/font-awesome.min.css',false,APSS_VERSION );

			/**
			 * Backend JS
			 * */
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'apss-admin-js', APSS_JS_DIR . '/backend.js', array('jquery', 'jquery-ui-sortable', 'wp-color-picker'),APSS_VERSION );//registering plugin's admin js
			}
		}

		 /**
         * Registers Frontend Assets
         * */
        function register_frontend_assets() {
        	wp_enqueue_style( 'apss-font-awesome',APSS_CSS_DIR.'/font-awesome.min.css',array(),APSS_VERSION );
        	wp_enqueue_style( 'apss-font-opensans','http://fonts.googleapis.com/css?family=Open+Sans',array(),false);
            wp_enqueue_style( 'apss-frontend-css', APSS_CSS_DIR . '/frontend.css', array( 'apss-font-awesome' ), APSS_VERSION );
        }
        
        /**
         * Funciton to print array in pre format
         * */
         function print_array($array)
         {
            echo "<pre>";
            print_r($array);
            echo "</pre>";
         }

         //function to restore the default setting of a plugin
         function apss_restore_default_settings(){
         	$nonce = $_REQUEST['_wpnonce'];
	        if(!empty($_GET) && wp_verify_nonce( $nonce, 'apss-restore-default-settings-nonce' ))
	        {
         	//restore the default plugin activation settings from the activation page.
         		include( 'inc/backend/activation.php' );
		      	$_SESSION['apss_message'] = __( 'Settings restored Successfully.', APSS_TEXT_DOMAIN ); 
				wp_redirect( admin_url().'admin.php?page=apss-share' );
	          exit;
	      }else{
	      	 die( 'No script kiddies please!' );
	      }

         }



         ////////////////////////////////////for count //////////////////////////////////////////////////////

         //for facebook url share count
         function get_fb( $url, $cache_period ) {
         	$facebook_count=get_transient('apss_fb_count');
         	if(false===$facebook_count){
		        $json_string = $this->get_json_values( 'http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls='.$url );
		        $json = json_decode( $json_string, true );
		        $facebook_count = isset( $json[0]['total_count'] )?intval( $json[0]['total_count'] ):0;
		        set_transient('apss_fb_count', $facebook_count, $cache_period);
		        return $facebook_count;
         	}else{
         		return $facebook_count;
         	}
         }

         //for twitter url share count
         function get_tweets( $url, $cache_period ) {
         $tweet_count = get_transient('apss_tweets_count');
         if(false===$tweet_count){
         	$json_string = $this->get_json_values( 'http://urls.api.twitter.com/1/urls/count.json?url=' . $url );
         	$json = json_decode( $json_string, true );
         	$tweet_count = isset( $json['count'])?intval($json['count'] ):0;
         	set_transient('apss_tweets_count', $tweet_count, $cache_period);
         	return $tweet_count;
         }else{
         	return $tweet_count;
         }
         
         }

          //for google plus url share count
         function get_plusones( $url, $cache_period )  {
         	$plusones_count = get_transient('apss_google_plus_count');
         	if(false === $plusones_count){
	         $curl = curl_init();
	         curl_setopt( $curl, CURLOPT_URL, "https://clients6.google.com/rpc");
	         curl_setopt( $curl, CURLOPT_POST, true);
	         curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false);
	         curl_setopt( $curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"'.rawurldecode($url).'","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
	         curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true);
	         curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ));
	         $curl_results = curl_exec ( $curl );
	         curl_close ( $curl );
	         $json = json_decode( $curl_results, true );
	         $plusones_count = isset( $json[0]['result']['metadata']['globalCounts']['count'])?intval( $json[0]['result']['metadata']['globalCounts']['count'] ):0;
	         set_transient('apss_google_plus_count', $plusones_count, $cache_period );
	         return $plusones_count;
         	}else{
         	 return $plusones_count; 	
         	}	
         }

         //for pinterest url share count
         function get_pinterest( $url, $cache_period ){
         	$pinterest_count = get_transient('apss_pin_count');
         	if(false===$pinterest_count){
         	 $json_string = $this->get_json_values( 'http://api.pinterest.com/v1/urls/count.json?url='.$url );
         	 $json_string = preg_replace( '/^receiveCount\((.*)\)$/', "\\1", $json_string );
         	 $json = json_decode( $json_string, true );
         	 $pinterest_count = isset( $json['count'])? intval( $json['count'] ) : 0;
         	 set_transient('apss_pin_count', $pinterest_count, $cache_period );
         	 return $pinterest_count;
         	}else{
         		return $pinterest_count;
         	}	
         	
         }

          //for linkedin url share count
         function get_linkedin( $url, $cache_period ) {
         $linkedin_count=get_transient('apss_linkedin_count');
         	if(false===$linkedin_count){	
		         $json_string = $this->get_json_values( "https://www.linkedin.com/countserv/count/share?url=$url&format=json" );
		         $json = json_decode( $json_string, true );
		         $linkedin_count = isset( $json['count'])?intval($json['count'] ):0;
		         set_transient('apss_linkedin_count', $linkedin_count, $cache_period);
		         return $linkedin_count;
         	}else{
         		return $linkedin_count;
         	}
         }


         //function to return json values from social media urls
         private function get_json_values( $url ){
         $args=array( 'timeout'     => 10 );
         $response = wp_remote_get( $url, $args );
         return $response['body']; 
         }

         ////////////////////////////////////for count ends here/////////////////////////////////////////////

         /**
         * Clears the social share counter cache 
         */
        function apss_clear_cache() {
            if (!empty($_GET) && wp_verify_nonce($_GET['_wpnonce'], 'apss-clear-cache-nonce')) {
                $transient_array = array('apss_tweets_count', 'apss_linkedin_count', 'apss_fb_count', 'apss_pin_count', 'apss_google_plus_count', 'apss_stumble_count', 'apss_delicious_count', 'apss_reddit_count');
                foreach ($transient_array as $transient) {
                    delete_transient($transient);
                }
                $_SESSION['apss_message'] = __( 'Cache cleared Successfully', APSS_TEXT_DOMAIN );
                wp_redirect( admin_url() . 'admin.php?page=apss-share' );
            }
        }    

 	}//APSS_Class termination


    $apss_object = new APSS_Class();

}
