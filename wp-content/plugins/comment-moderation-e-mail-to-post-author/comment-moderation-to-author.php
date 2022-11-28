<?php
/*
Plugin Name: Comment Moderation E-mail only to Author
Plugin URI: http://status301.net/wordpress-plugins/comment-moderation-e-mail-to-post-author/
Description: Send comment moderation notifications **only** to the posts Author, not to the site Administration address (as configured on Settings > General) any more, unless the author in question has no moderation rights. There are no options, just activate and the site admin will no longer be bothered with notifications about posts from other authors. <strong>Happy with it? <em><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=Comment%20Moderation%20E-mail%20to%20Post%20Author&item_number=0%2e4&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8&lc=us">Buy me a coffee...</a></em> Thanks! :)</strong>
Version: 0.6
Author: RavanH
Author URI: http://status301.net/
License: GNU General Public License v3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
 * Filters wp_notify_moderator() recipients: $emails includes only author e-mail,
 * unless the authors e-mail is missing or the author has no moderator rights.
 *
 * @since 0.4
 *
 * @param array $emails     List of email addresses to notify for comment moderation.
 * @param int   $comment_id Comment ID.
 * @return array
 */
function comment_moderation_post_author_only( $emails, $comment_id )
{
	// Do we have multiple recipients at all?
	if ( is_array( $emails ) && count( $emails ) > 1 ) {
		// Most likely, the first element is the admin email and the second is the post author email.
		// But another filter might be responsible for additional emails so...
		$admin_email = get_option( 'admin_email' );
		$comment = get_comment( $comment_id );
		$post = get_post( $comment->comment_post_ID );
		$user = get_userdata( $post->post_author );
		
		// Make extra sure the admin email is NOT the same as the original post author email before removing it!
		if ( ! empty( $user->user_email ) && 0 !== strcasecmp( $user->user_email, $admin_email ) ) {
			$emails = array_diff( $emails, array( $admin_email ) );
		}
	}

	return $emails;
}

add_filter('comment_moderation_recipients', 'comment_moderation_post_author_only', 11, 2);
