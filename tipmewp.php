<?php
defined('ABSPATH') or die("I don't think so...");
/*
Plugin Name: TipMeWP
Plugin URI: http://www.cruising.com/tipmewp/
Description: Add a tip button to posts automatically using ChangeTip and Twitter.  If a user has a twitter URL defined in the "Website" setting in the profile, then a tip button will appear so that anyone can tip them for a blog post or bbpress posting.  In short, it creates a tweet that the ChangeTip bot pics up and delivers with just one click.
Version: 0.1
Author: Phonebook
Author URI: http://www.cruising.com/tipmewp/
License: GPLv2 or later
*/

/*
Enter your twitter URL
WordPress and BBPress: Website section of Contact Info
No warranties, express or implied.  Use at your own risk per GPL.

*/
function createTipLink ($theTipSiteURL, $theLink, $theText, $forChangeTip, $imgLink) {
	//Create the link to do a tip
	//$theTipSiteURL = the link to start the tipping, with an @ etc
	//$theLink = the person's twitter etc handle link
	//$theText = the text to use for the tweet
	//$forChangeTip = how do we get Change tips attention, e.g.  @ChangeTip
	//$imgLink: link to the tip button
	//Sample tweet URL for Make A Wish, we create it automatically:
	//https://twitter.com/intent/tweet?text=Hey%20%40example1%2C%20here%20is%2025%20cents%20for%20your%20good%20Cruising.com%20post%20%40ChangeTip%20Thanks&source=webclient
	$theLink2 = $theTipSiteURL.$theLink."%20".$theText.$forChangeTip;
	$theLink = "<a href='$theLink2' target='_blank'><img src= '$imgLink' alt='Tip button'></a>";
	return $theLink;
}

function stripTipLink ($data,$theSiteString) {
	//Just split it and sanitize it.
	//$data = the entire URL: e.g. http://twitter.com/example1
	//$theSiteString = string to look for.  e.g. twitter.com/

	$theLink = end(explode($theSiteString, $data));
	$theLink = rtrim($theLink);  // remove whitespace - tab, newline, space etc
	$theLink = rtrim($theLink, '/');  // Remove trailing slash, if there.
	$theLink = rawurlencode($theLink);
	return $theLink;
}


function insert_ChangeTip( $content ) {
    global $post;  // Current post we see?
    
      
    $whichImageTemp = 1;  //default value.
    $defaultTipAmtTemp = "25 cents";
    
    
    $options = (array) get_option( 'wptip_settings' );
    $defaultTipAmt = $options['wpTip_text_field_0'] ?: $defaultTipAmtTemp;
    $whichImage = $options['wpTip_select_field_1'] ?: $whichImageTemp;
    

	$imgLink = plugins_url( 'no-twitter.png', __FILE__ ); //Get the link to the button
	$custom_content = "<img src= '$imgLink' alt='No twitter URL entered in web site section of profile so tipping not available for this user.'>";
    
    //Config options:
	//$theSite = get_site_url();  // Could us site URL for the tweet/message etc
    $theSite = get_bloginfo('name');  // site Name for Tweet/Message 
	$theText = rawurlencode("Hey, here is a " .$defaultTipAmt. " tip for your good info on ".$theSite);   //Message for the Tweet/Message
	$imgLink = plugins_url( 'tipme'.$whichImage.'.png', __FILE__ ); //Get the link to the button
    
    //Get the author of the ID so we can look up their tip URL
    $user_info = get_userdata($post->post_author);   //get UserID to look up userdata 
	$data = $user_info->user_url;  //Get URL

	//Check to see if it is twitter etc, if so, create the appropriate link
	if(strpos($user_info->user_url, "twitter") !== FALSE) {
		$theLink = stripTipLink ($data, "twitter.com/");
	    $custom_content = createTipLink ("http://twitter.com/home?status=%40",$theLink, $theText,"%20%40ChangeTip",$imgLink);	 
    }
    
    //We can add others later:
    if(strpos($user_info->user_url, "facebook") !== FALSE) {
	    // not available
    }
    if(strpos($user_info->user_url, "reddit") !== FALSE) {
	    // could post to a sub-reddit
    }
   
    
    return $custom_content;
}
function insert_TipPost ($content) {
		$custom_content = insert_ChangeTip( $content ) ;
		$custom_content = $custom_content . $content . $custom_content;
		return $custom_content;
}
function insert_TipBBPress ($content) {
		$custom_content = insert_ChangeTip( $content ) ;
		echo $custom_content;
		//return $custom_content;
}
add_filter( 'the_content', 'insert_TipPost' );
add_filter( 'bbp_theme_after_reply_author_details', 'insert_TipBBPress' ); 

include 'admin/settings.php';
?>
