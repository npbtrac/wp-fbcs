<?php
/**
 * Created by PhpStorm.
 * User: tracnguyen
 * Date: 2/15/18
 * Time: 11:06 PM
 */

namespace Enpii\WpPlugin\Fbcs\Base;


use Enpii\WpPlugin\Fbcs\Fbcs;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

class Main extends BaseObject {
	/**
	 * @var null|Facebook
	 */
	public $fb_sdk = null;
	public $fb_default_access_token = null;

	public function __construct( $config = null ) {
		$default_access_token = null;
		if ( ! empty( Fbcs::instance()->options['fb_app_id'] ) && ! empty( Fbcs::instance()->options['fb_app_secret'] ) ) {
			try {
				$this->fb_sdk = new Facebook( [
					'app_id'                => Fbcs::instance()->options['fb_app_id'],
					'app_secret'            => Fbcs::instance()->options['fb_app_secret'],
					'default_access_token'  => $this->get_fb_default_access_token(),
					'default_graph_version' => 'v2.10',
				] );
			} catch ( FacebookSDKException $e ) {
				die( $e->getMessage() );
			}
		}
	}

	/**
	 * Get FB default access token using app_id and app_secret
	 *
	 * @return string
	 */
	public function get_fb_default_access_token() {
		if ( $this->fb_default_access_token ) {
			return $this->fb_default_access_token;
		}
		if ( ! empty( Fbcs::instance()->options['fb_app_id'] ) && ! empty( Fbcs::instance()->options['fb_app_secret'] ) ) {
			$request_access_token_url = 'https://graph.facebook.com/oauth/access_token?client_id=' . Fbcs::instance()->options['fb_app_id'] . '&client_secret=' . Fbcs::instance()->options['fb_app_secret'] . '&grant_type=client_credentials';
			try {
				$response = wp_remote_request( $request_access_token_url );
				if ( ! empty( $response['response'] ) && ! empty( $response['response']['code'] ) ) {
					if ( 200 === intval( $response['response']['code'] ) ) {
						$body = json_decode( $response['body'], true );

						$this->fb_default_access_token = $body['access_token'];
					}
				}
			} catch ( \Exception $e ) {
				echo 'Getting FB default access token failed for url (' . $request_access_token_url . ') : ' . $e->getMessage() . "\n";
				echo $e->getTraceAsString();
			}
		}

		return $this->fb_default_access_token;
	}

	/**
	 * Get FB Access Token
	 *
	 * @return \Facebook\Authentication\AccessToken|null
	 */
	public function get_fb_access_token() {
		$access_token = null;

		if ( $this->fb_sdk ) {
			try {
				$helper       = $this->fb_sdk->getCanvasHelper();
				$access_token = $helper->getAccessToken();
			} catch ( \Exception $e ) {
				if ( $e instanceof FacebookResponseException ) {
					// When Graph returns an error
					echo 'Graph returned an error: ' . $e->getMessage() . "\n";
					echo $e->getTraceAsString();
				} else {
					// When validation fails or other local issues.
					echo 'Facebook SDK returned an error: ' . $e->getMessage() . "\n";
					echo $e->getTraceAsString();
				}
			}
		}

		return $access_token;
	}

	/**
	 * Get remote response
	 *
	 * @param $request_url
	 *
	 * @return array|mixed|null|object result returned only on successful response, otherwise, null returned
	 */
	public function get_remote_request( $request_url ) {
		try {
			$response = wp_remote_request( $request_url );
			if ( ! empty( $response['response'] ) && ! empty( $response['response']['code'] ) ) {
				if ( 200 === intval( $response['response']['code'] ) ) {
					return $response['body'];
				}
			}
		} catch ( \Exception $e ) {
			echo 'Remote request failed for url (' . $request_url . ') : ' . $e->getMessage() . "\n";
			echo $e->getTraceAsString();
		}

		return null;
	}

	/**
	 * Get comment count and object id of url on FB
	 *
	 * @param $url
	 *
	 * @return array
	 */
	public function get_fb_url_info( $url ) {
		$request_url = 'https://graph.facebook.com/?ids=' . $url;
		if ( $body = $this->get_remote_request( $request_url ) ) {
			$response = json_decode( $body, true );

			return ( isset( $response[ $url ] ) && isset( $response[ $url ]['share'] ) && isset( $response[ $url ]['og_object'] ) ) ? [
				$response[ $url ]['share']['comment_count'],
				$response[ $url ]['og_object']['id']
			] : [ 0, 0 ];
		}

		return [ 0, 0 ];
	}

	/**
	 * Request remote command from FB
	 * Data return with successful attempt only
	 *
	 * @param $command
	 *
	 * @return \Facebook\FacebookResponse|null
	 */
	public function get_fb_remote_request( $command ) {
		try {
			// Get the \Facebook\GraphNodes\GraphUser object for the current user.
			// If you provided a 'default_access_token', the '{access-token}' is optional.
			$response = $this->fb_sdk->get( $command );
			if ( 200 === $response->getHttpStatusCode() ) {
				return $response;
			}
		} catch ( \Exception $e ) {
			if ( $e instanceof FacebookResponseException ) {
				// When Graph returns an error
				echo 'Graph returned an error: ' . $e->getMessage() . "\n";
				echo $e->getTraceAsString();
			} else {
				// When validation fails or other local issues.
				echo 'Facebook SDK returned an error: ' . $e->getMessage() . "\n";
				echo $e->getTraceAsString();
			}
		}

		return null;
	}

	/**
	 * Get list of comments from FB using object_id
	 *
	 * @param $fb_object_id
	 *
	 * @return null
	 */
	public function get_fb_comments( $fb_object_id ) {
		$response = $this->get_fb_remote_request( '/comments?id=' . $fb_object_id );
		if ( $response ) {
			return $response->getDecodedBody()['data'];
		}

		return null;
	}

	public function get_fb_sub_comments( $fb_object_id, $fb_comment_id ) {
		$response = $this->get_fb_remote_request( '/comments?id=' . $fb_object_id . '_' . $fb_comment_id );
		if ( $response ) {
			return $response->getDecodedBody()['data'];
		}

		return null;
	}

	public function get_fb_comment_details( $fb_comment_id ) {
		$response = $this->get_fb_remote_request( '/' . $fb_comment_id );
		if ( $response ) {
			return $response->getDecodedBody();
		}

		return null;
	}

	public function get_fb_avatar_url( $fb_user_id, $type = 'large' ) {
		$response = $this->get_fb_remote_request( '/' . $fb_user_id . '/picture?type=' . $type . '&redirect=0' );
		if ( $response ) {
			return $response->getDecodedBody()['data']['url'];
		}

		return null;
	}

	/**
	 * Add FB comment box at the end of content
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function add_fb_comment_box( $content ) {
		return $content . $this->get_fb_comment_box();
	}

	/**
	 * Get FB Comments Plugin box content
	 *
	 * @return string
	 */
	public function get_fb_comment_box() {
		$options = Fbcs::instance()->options;
		$result  = '';

		$flag_allow = is_singular() && $options['insert_after_content'];
		if ( $flag_allow ) {

			$custom_fields = get_post_custom();
			if ( ! empty( $custom_fields ) ) {
				foreach ( $custom_fields as $field_key => $field_values ) {
					foreach ( $field_values as $key => $value ) {
						$post_meta[ $field_key ] = $value;
					}
				}
			}
			if ( ! isset( $post_meta['_disable_fbcs'] ) ) {
				$post_meta['_disable_fbcs'] = "off";
			}

			if ( ! empty( $options['onlyfb'] ) ) {
				$comments_id = "comments";
			} else {
				$comments_id = "fbcs_box";
			}

			$comment_title = $options['title_text'] ? '<h3>' . esc_html( $options['title_text'] ) . '</h3>' : '';
			$result        .= "<div id=\"" . $comments_id . "\" class=\"comments-area\">" . $comment_title;
			$result        .= "<div class=\"fb-comments\" data-href=\"" . get_permalink() . "\" data-num-posts=\"" . esc_attr( $options['num'] ) . "\" data-width=\"" . esc_attr( $options['width'] ) . "\" data-colorscheme=\"" . esc_attr( $options['scheme'] ) . "\" data-notify=\"true\" data-order_by=\"" . esc_attr( $options['order'] ) . "\"></div>";
			$result        .= "</div>";
		}

		return $result;
	}

	/**
	 * Add FB graph info to the head
	 */
	public function fb_graph_info() {
		if ( ! empty( Fbcs::instance()->options['fb_app_id'] ) ) {
			echo '<meta property="fb:app_id" content="' . Fbcs::instance()->options['fb_app_id'] . '"/>';
		}
		if ( ! empty( Fbcs::instance()->options['fb_moderators'] ) ) {
			echo '<meta property="fb:admins" content="' . Fbcs::instance()->options['fb_moderators'] . '"/>';
		}
	}

	/**
	 * Add FB script to enable FB connect
	 */
	public function fb_init_top() {
		$language  = Fbcs::instance()->options['language'];
		$fb_app_id = Fbcs::instance()->options['fb_app_id'];
		echo <<<JSSCRIPT
<script type="text/javascript">
    (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/{$language}/sdk.js#xfbml=1&version=v2.4&appID={$fb_app_id}";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>
JSSCRIPT;
	}

	public function fb_init_top_ajax() {
		echo <<<JSSCRIPT
<script type="text/javascript">
    jQuery(window).load(function () {
        FB.Event.subscribe('comment.create', comment_add);
        FB.Event.subscribe('comment.remove', comment_remove);

        jQuery("[id=comments]").each(function () {
            jQuery("[id=comments]:gt(0)").hide();
        });
    });
</script>
JSSCRIPT;
	}

	/**
	 * Hook to the wp_footer
	 * Add callbacks for FB to call when new comment created or deleted
	 */
	public function fb_comments_ajax() {
		$ajax_url = admin_url( 'admin-ajax.php' );
		$post_id  = ! empty( $GLOBALS['post']->ID ) ? $GLOBALS['post']->ID : 0;
		echo <<<JSSCRIPT
<script type="text/javascript">
	var post_id = {$post_id};
    var comment_add = function (response) {
        jQuery.ajax({
            type: 'POST',
            url: '{$ajax_url}',
            data: {
                'action': 'fbcs_fb_comment_created', 'post_id': post_id, fb_comment_data: response
            },
            success: function (response) {
                console.log('comment.create fired' + response);
            },
            error: function (exception) {
                console.log('Exception:' + exception);
            }
        });
        return false;
    };

    var comment_remove = function (response) {
        jQuery.ajax({
            type: 'POST',
            url: '{$ajax_url}',
            data: {
                'action': 'fbcs_fb_comment_removed', 'post_id': post_id, fb_comment_data: response
            },
            success: function () {
                console.log('comment.remove fired');
            },
            error: function (exception) {
                console.log('Exception:' + exception);
            }
        });
        return false;
    };
</script>
JSSCRIPT;
	}

	/**
	 * Do ajax to add a comment when receive callback from FB
	 *
	 */
	public function ajax_fb_comment_created() {
		$post_id         = isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : 0;
		$fb_comment_data = isset( $_REQUEST['fb_comment_data'] ) ? $_REQUEST['fb_comment_data'] : [];

		list( $fbcs_count, $fb_object_id ) = $this->get_fb_url_info( get_permalink( $post_id ) );

		$fb_comment = $this->get_fb_comment_details( $fb_comment_data['commentID'] );

		$parent_comment_id = 0;
		if ( ! empty( $fb_comment_data['parentCommentID'] ) ) {
			$fb_parent_comment_id = $this->get_fb_comment_id_from_original( $fb_comment_data['parentCommentID'] );
			$args                 = array(
				'number'     => 1,
				'meta_key'   => Fbcs::COMMENT_META_KEY_FB_COMMENT_ID,
				'meta_value' => $fb_parent_comment_id,
				'meta_query' => '',
				'date_query' => null, // See WP_Date_Query
			);
			/* @var \WP_Comment[] $wp_comments */
			$wp_comments = get_comments( $args );

			foreach ( $wp_comments as $wp_comment ) {
				$parent_comment_id = $wp_comment->comment_ID;
			}
		}
		$this->save_fb_comment( $post_id, $fb_comment, $parent_comment_id );
		wp_die( ' Comment added ' );
	}

	/**
	 * Do ajax to remove a comment when receive callback from FB
	 *
	 */
	public function ajax_fb_comment_removed() {
		$post_id         = isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : 0;
		$fb_comment_data = isset( $_REQUEST['fb_comment_data'] ) ? $_REQUEST['fb_comment_data'] : [];

		list( $fbcs_count, $fb_object_id ) = $this->get_fb_url_info( get_permalink( $post_id ) );

		if ( ! empty( $fb_comment_data['commentID'] ) ) {
			$this->delete_fb_comment( $fb_comment_data['commentID'] );
		}
		wp_die( ' Comment removed ' );
	}

	/**
	 * Do ajax to Sync FB comments of latest posts
	 *
	 */
	public function ajax_sync_latest_posts() {
		$limit          = isset( $_REQUEST['limit'] ) ? $_REQUEST['limit'] : - 1;
		$offset         = isset( $_REQUEST['offset'] ) ? $_REQUEST['offset'] : 0;
		$posts_per_page = isset( $_REQUEST['posts_per_page'] ) ? (int) $_REQUEST['posts_per_page'] : 1;

		list( $found_posts, $offset ) = $this->sync_latest_posts( $posts_per_page, $offset );

		$result_data = [
			'found_posts' => $found_posts,
			'offset'      => $offset,
		];

		wp_die( wp_json_encode( $result_data ) );
	}

	/**
	 * Sync FB comments of latest posts
	 *
	 * @param int $posts_per_page limit of posts count per turn
	 * @param int $limit limit of posts count in total
	 */
	public function sync_latest_posts( $posts_per_page = 48, $offset = 0 ) {
		$args = array(
			'posts_per_page'      => $posts_per_page,
			'offset'              => $offset,
			'ignore_sticky_posts' => true,
			'post_status'         => 'publish',
			'post_type'           => 'any',
		);

		$the_query = new \WP_Query( $args );
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$this->sync_post( get_the_ID() );
		}

		wp_reset_postdata();

		return [ $the_query->found_posts, $offset ];
	}

	/**
	 * Sync all comments belong to a post
	 *
	 * @param $post_id
	 */
	public function sync_post( $post_id ) {
		list( $fbcs_count, $fb_object_id ) = $this->get_fb_url_info( get_permalink( $post_id ) );

		$fb_comments = null;
		if ( $fb_object_id ) {
			$fb_comments = $this->get_fb_comments( $fb_object_id );
		}

		if ( is_array( $fb_comments ) ) {
			foreach ( $fb_comments as $key => $fb_comment ) {
				list( $save_result, $comment_id, $fb_comment_id ) = $this->save_fb_comment( $post_id, $fb_comment );

				if ( $save_result ) {
					$this->sync_sub_comments( $post_id, $comment_id, $fb_object_id, $fb_comment_id );
				}
			}
		}
	}

	/**
	 * Sync sub-comments of a parent comments
	 *
	 * @param $post_id
	 * @param $comment_id
	 * @param $fb_object_id
	 * @param $fb_comment_id
	 */
	public function sync_sub_comments( $post_id, $comment_id, $fb_object_id, $fb_comment_id ) {

		$fb_comments = null;
		if ( $fb_object_id ) {
			$fb_comments = $this->get_fb_sub_comments( $fb_object_id, $fb_comment_id );
		}

		if ( is_array( $fb_comments ) ) {
			foreach ( $fb_comments as $key => $fb_comment ) {
				$this->save_fb_comment( $post_id, $fb_comment, $comment_id );
			}
		}
	}

	/**
	 * Save a FB comment to WP database
	 *
	 * @param $post_id
	 * @param $fb_comment
	 * @param int $comment_parent
	 *
	 * @return array [ $save_result bool successful or not, $comment_id int ID of the comment inserted or updated, $fb_comment_id int ID of that FB's comment ]
	 */
	public function save_fb_comment( $post_id, $fb_comment, $comment_parent = 0 ) {
		$comment_id = null;

		$fb_commented_time = $fb_comment['created_time'];
		$fb_author_id      = $fb_comment['from']['id'];
		$fb_author_name    = $fb_comment['from']['name'];
		$fb_author_url     = 'www.facebook.com/' . $fb_author_id;
		$fb_author_email   = '';
		$fb_comment_parts  = explode( '_', $fb_comment['id'] );
		$fb_comment_id     = isset( $fb_comment_parts[1] ) ? $fb_comment_parts[1] : 0;

		$args          = [
			'number'        => 1,
			'no_found_rows' => 1,
			'meta_query'    => [
				'relation' => 'AND',
				[
					'key'     => Fbcs::COMMENT_META_KEY_FB_COMMENT_ID,
					'value'   => $fb_comment_id,
					'compare' => '=='
				]
			]
		];
		$comment_query = new \WP_Comment_Query( $args );
		if ( ! empty( $comment_query->comments ) ) {
			$comment_id = $comment_query->comments[0]->comment_ID;
		}

		$data = [
			'comment_post_ID'      => $post_id,
			'comment_author'       => $fb_author_name,
			'comment_author_email' => $fb_author_email,
			'comment_author_url'   => $fb_author_url,
			'comment_content'      => $fb_comment['message'],
			'comment_type'         => '',
			'comment_parent'       => $comment_parent,
			'user_id'              => 0,
			'comment_approved'     => 1,
			'comment_date_gmt'     => date( 'Y-m-d H:i:s', strtotime( $fb_commented_time ) ),
			'comment_date'         => get_date_from_gmt( date( 'Y-m-d H:i:s', strtotime( $fb_commented_time ) ), 'Y-m-d H:i:s' ),
		];

		if ( $comment_id ) {
			$data['comment_ID'] = intval( $comment_id );
			$save_result        = wp_update_comment( $data );
		} else {
			$save_result = $comment_id = wp_insert_comment( $data );
		}

		if ( $save_result ) {
			update_comment_meta( $comment_id, Fbcs::COMMENT_META_KEY_FB_COMMENT_ID, $fb_comment_id );
			update_comment_meta( $comment_id, Fbcs::COMMENT_META_KEY_FB_AUTHOR_AVATAR_URL, $this->get_fb_avatar_url( $fb_author_id ) );
		}

		return [ ! ! $save_result, $comment_id, $fb_comment_id ];
	}

	/**
	 * Delete WP comment that accompanied with a certain FB comment
	 *
	 * @param string $fb_comment_id original comment ID given by FB in format 17..9_17993...34
	 */
	public function delete_fb_comment( $fb_comment_id ) {
		$fb_comment_id = $this->get_fb_comment_id_from_original( $fb_comment_id );

		$args = array(
			'number'     => 1,
			'meta_key'   => Fbcs::COMMENT_META_KEY_FB_COMMENT_ID,
			'meta_value' => $fb_comment_id,
			'meta_query' => '',
			'date_query' => null, // See WP_Date_Query
		);
		/* @var \WP_Comment[] $wp_comments */
		$wp_comments = get_comments( $args );

		foreach ( $wp_comments as $wp_comment ) {
			wp_delete_comment( $wp_comment->comment_ID );
		}
	}

	/**
	 * Get the FB comment ID from original one given by FB in format 17..9_17993...34
	 *
	 * @param $fb_comment_id
	 *
	 * @return int
	 */
	public function get_fb_comment_id_from_original( $fb_comment_id ) {
		$fb_comment_parts = explode( '_', $fb_comment_id );

		return isset( $fb_comment_parts[1] ) ? $fb_comment_parts[1] : $fb_comment_parts[0];
	}
}