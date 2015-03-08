<?php
$apss_link_open_option=($options['dialog_box_options']=='1') ? "_blank": "";
$twitter_user=$options['twitter_username'];
$counter_enable_options=$options['counter_enable_options'];
$icon_set_value=$options['social_icon_set'];
$url =  get_permalink(); //$this->curPageURL();
$cache_period = ($options['cache_period'] != '') ? $options['cache_period']*60*60 : 24 * 60 * 60 ;
foreach( $options['social_networks'] as $key=>$value ){
	if( intval($value)=='1' ){
		switch( $key ){
			//counter available for facebook
			case 'facebook':
			$link = 'https://www.facebook.com/sharer/sharer.php?u='.$url;
			?>
			<div class='apss-facebook apss-single-icon'>
					<a title='Share on Facebook' target='<?php echo $apss_link_open_option; ?>' href='<?php echo $link; ?>'>
							<div class='apss-icon-block clearfix'>
									<i class='fa fa-facebook'></i>
									<span class='apss-social-text'><?php _e( 'Share on', APSS_TEXT_DOMAIN ); ?> <?php echo ucfirst( $key ); ?></span>
									<span class='apss-share'><?php _e( 'Share', APSS_TEXT_DOMAIN ); ?></span>
							</div>
							<?php if(isset($counter_enable_options) && $counter_enable_options=='1'){ ?>
		                    <div class='count apss-count' data-url='<?php echo $url; ?>' data-social-network='<?php echo $key; ?>' data-social-detail="<?php echo $url.'_'.$key; ?>">Loading...</div>
		                    <?php } ?>
					</a>
			</div>
			<?php
			break;

			//counter available for twitter
			case 'twitter':
			$url_twitter = $url;
			$url_twitter=urlencode($url_twitter);
			if(isset( $twitter_user) && $twitter_user !='' ){
				$twitter_user = 'via='.$twitter_user;
			}
			$link ="https://twitter.com/intent/tweet?text=$title&amp;url=$url_twitter&amp;$twitter_user";
			?>
			<div class='apss-twitter apss-single-icon'>
				<a title='Share on Twitter' target='<?php echo $apss_link_open_option; ?>' href="<?php echo $link; ?>">
					<div class='apss-icon-block clearfix'>
						<i class='fa fa-twitter'></i>
						<span class='apss-social-text'><?php _e( 'Share on', APSS_TEXT_DOMAIN ); ?> <?php echo ucfirst($key); ?></span><span class='apss-share'><?php _e( 'Tweet', APSS_TEXT_DOMAIN ); ?></span>
					</div>
					<?php if( isset( $counter_enable_options ) && $counter_enable_options=='1'){ ?>
					<div class='count apss-count' data-url='<?php echo $url; ?>' data-social-network='<?php echo $key; ?>' data-social-detail="<?php echo $url.'_'.$key; ?>">Loading...</div>
					<?php } ?>
				</a>
			</div>
			<?php
			break;

			//counter available for google plus
			case 'google-plus':
			$link = 'https://plus.google.com/share?url='.$url;
			?>
			<div class='apss-google-plus apss-single-icon'>
				<a title='Share on Google+' target='<?php echo $apss_link_open_option; ?>' href='<?php echo $link; ?>'>
					<div class='apss-icon-block clearfix'>
						<i class='fa fa-google-plus'></i>
						<span class='apss-social-text'><?php _e('Share on', APSS_TEXT_DOMAIN ); ?> Google Plus</span>
						<span class='apss-share'><?php _e( 'Share', APSS_TEXT_DOMAIN ); ?></span>
					</div>
					<?php if(isset($counter_enable_options) && $counter_enable_options=='1'){ ?>
					<div class='count apss-count' data-url='<?php echo $url;?>' data-social-network='<?php echo $key; ?>' data-social-detail="<?php echo $url.'_'.$key;?>">Loading...</div>
					<?php } ?>
				</a>
			</div>
			<?php
			break;

			//counter available for pinterest
			case 'pinterest':
			?>

			<div class='apss-pinterest apss-single-icon'>
				<a title='Share on Pinterest' href='javascript:void((function()%7Bvar%20e=document.createElement(&apos;script&apos;);e.setAttribute(&apos;type&apos;,&apos;text/javascript&apos;);e.setAttribute(&apos;charset&apos;,&apos;UTF-8&apos;);e.setAttribute(&apos;src&apos;,&apos;http://assets.pinterest.com/js/pinmarklet.js?r=&apos;+Math.random()*99999999);document.body.appendChild(e)%7D)());'>
					<div class='apss-icon-block clearfix'>
					<i class='fa fa-pinterest'></i>
					<span class='apss-social-text'><?php _e( 'Share on', APSS_TEXT_DOMAIN ); ?> <?php echo ucfirst($key); ?></span>
					<span class='apss-share'><?php _e( 'Share', APSS_TEXT_DOMAIN ); ?></span>
					</div>
					<?php if( isset( $counter_enable_options ) && $counter_enable_options=='1' ){ ?>
					<div class='count apss-count' data-url='<?php echo $url; ?>' data-social-network='<?php echo $key; ?>' data-social-detail="<?php echo $url.'_'.$key;?>">Loading...</div>
					<?php } ?>

				</a>
			</div>
			<?php
			//}
			break;
			
			//couter available for linkedin
			case 'linkedin':
			$link = "http://www.linkedin.com/shareArticle?mini=true&amp;ro=true&amp;trk=JuizSocialPostSharer&amp;title=".$title."&amp;url=".$url;
			?>
			<div class='apss-linkedin apss-single-icon'>
			<a title='Share on LinkedIn' target='<?php echo $apss_link_open_option; ?>' href='<?php echo $link; ?>'>
				<div class='apss-icon-block clearfix'><i class='fa fa-linkedin'></i>
					<span class='apss-social-text'><?php _e( 'Share on', APSS_TEXT_DOMAIN ); ?> <?php echo ucfirst($key); ?></span>
					<span class='apss-share'><?php _e( 'Share', APSS_TEXT_DOMAIN ); ?></span>
				</div>
				<?php if( isset( $counter_enable_options ) && $counter_enable_options=='1' ){ ?>
				<div class='count apss-count' data-url='<?php echo $url; ?>' data-social-network='<?php echo $key; ?>' data-social-detail="<?php echo $url.'_'.$key; ?>">Loading...</div>
				<?php } ?>
			</a>
			</div>
			<?php
			break;

			//there is no counter available for digg
			case 'digg':
			$link = "http://digg.com/submit?phase=2%20&amp;url=".$url."&amp;title=".$title;
			?>
			<div class='apss-digg apss-single-icon'>
			<a title='Share on Digg' target='<?php echo $apss_link_open_option; ?>' href='<?php echo $link; ?>'>
				<div class='apss-icon-block clearfix'>
					<i class='fa fa-digg'></i>
					<span class='apss-social-text'><?php _e( 'Share on', APSS_TEXT_DOMAIN ); ?> <?php echo ucfirst($key); ?></span>
					<span class='apss-share'><?php _e( 'Share', APSS_TEXT_DOMAIN ); ?></span>
				</div>
			</a>
			</div>

			<?php
			break;

			case 'email':
					if ( strpos( $options['apss_email_body'], '%%' ) || strpos( $options['apss_email_subject'], '%%' ) ) {
						$link = 'mailto:?subject='.$options['apss_email_subject'].'&amp;body='.$options['apss_email_body'];
						$link = preg_replace( array( '#%%title%%#', '#%%siteurl%%#', '#%%permalink%%#', '#%%url%%#' ), array( $title, get_site_url(), get_permalink(), $url ), $link );
					}
					else {
						$link = 'mailto:?subject='.$options['apss_email_subject'].'&amp;body='.$options['apss_email_body'].": ".$url;
					}
					?>
			<div class='apss-email apss-single-icon'>
				<a class='share-email-popup' title='Share it on Email' target='<?php echo $apss_link_open_option; ?>' href='<?php echo $link; ?>'>
					<div class='apss-icon-block clearfix'>
					<i class='fa  fa-envelope'></i>
					<span class='apss-social-text'><?php _e( 'Send email', APSS_TEXT_DOMAIN ); ?></span>
					<span class='apss-share'><?php _e( 'Mail', APSS_TEXT_DOMAIN ); ?></span>
					</div>
				</a>
			</div>

			<?php
			break;

			case 'print':
			?>
			<div class='apss-print apss-single-icon'>
				<a title='Print' href='javascript:void(0);' onclick='window.print();return false;'>
					<div class='apss-icon-block clearfix'><i class='fa fa-print'></i>
					<span class='apss-social-text'><?php _e( 'Print', APSS_TEXT_DOMAIN ); ?></span>
					<span class='apss-share'><?php _e( 'Print', APSS_TEXT_DOMAIN ); ?></span>
					</div>
				</a>
			</div>
			<?php 
			break;

			}
	}

}
?>