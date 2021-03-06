<?php

namespace AxelSpringer\WP\Akamai;

use AxelSpringer\WP\Bootstrap\Plugin\AbstractPlugin;
use \Timber\Timber;

class Plugin extends AbstractPlugin {

  public $purge_objects          = array();
  public $purge_post;
  public $base_url;

	private $akamai_prefix         = 'AKAMAI';
	private $akamai_env_vars       = array(
    'host',
    'client_token',
    'client_secret',
    'access_token'
  );
	private $akamai_section        = 'default';

	private $akamai_edge_defaults  = array(
    'wp_akamai_edge_max_age'        => '1d',
    'wp_akamai_dge_downstream_ttl'  => '1m',
    'wp_akamai_edge_options'        => '!no-store'
  );

  public function init() {

    // load options
    $this->setup->load_options( 'AxelSpringer\WP\Akamai\__OPTION__' );
    $this->settings = new Settings(
        __( __TRANSLATE__::SETTINGS_PAGE_TITLE ),
        __( __TRANSLATE__::SETTINGS_MENU_TITLE ),
        __PLUGIN__::SETTINGS_PAGE,
        __PLUGIN__::SETTINGS_PERMISSION,
        $this->setup->version
    );

    $this->base_url         = parse_url( get_bloginfo( 'url' ), PHP_URL_PATH ) . '/';
		$this->base_url         = apply_filters( 'wp_akamai_canonical_url', $this->base_url );

    // set specific library env
    $this->set_env_vars();

    // load hooks
    $this->load_hooks();
  }

  /**
   * Register Hooks
   *
   * @return void
   */
  public function load_hooks()
  {
    add_action( 'save_post', array( &$this, 'purge_on_post' ) );
    // save for later
		// add_action( 'comment_post', array( &$this, 'purge_on_comment' ) );

		add_action( 'wp', array( &$this, 'send_headers' ) );
		add_action( 'admin_notices', array( &$this, 'admin_notices' ) );

    // for local debug
		add_filter( 'http_request_timeout', array( &$this, 'wp9838c_timeout_extend' ) );
	}

  /**
   * Undocumented function
   *
   * @param [type] $time
   * @return void
   */
	public function wp9838c_timeout_extend( $time ) {
		// Default timeout is 5
		return 60;
	}

  /**
   * Undocumented function
   *
   * @return void
   */
	public function set_env_vars() {
		foreach ( $this->akamai_env_vars as $akamai_env_var ) {
			$_ENV[$this->akamai_prefix . '_' . strtoupper($akamai_env_var)] = $this->setup->options[$akamai_env_var];
		}
	}

  /**
   * Undocumented function
   *
   * @param [type] $post_id
   * @return void
   */
	public function purge_on_post( $post_id ) {
		if ( ! is_object( $post = get_post( $post_id ) ) || $post->post_status !== 'publish' ) {
			return true;
		}

    $this->purge_post = $post;
    $this->purge();
	}

  /**
   * Undocumented function
   *
   * @return void
   */
	public function send_headers() {
		$defaults   = $this->akamai_edge_defaults;

    if ( ! $this->setup->options['wp_akamai_hostnames']
      || count( $this->setup->options['wp_akamai_hostnames'] ) < 1 )
        return;

    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
      return;
    } elseif( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST ) {
      return;
    } elseif( defined('REST_REQUEST') && REST_REQUEST ) {
      return;
    } elseif ( is_admin()
      || post_password_required()
      || is_404()
      || post_password_required()
      || is_search() ) {
      return;
    }

    if ( ! $this->setup->options['wp_akamai_purge_pagemanager']
      && ( is_category() || is_tag() || is_front_page() || is_home() ) ) {
      return;
    } elseif ( ! $this->setup->options['wp_akamai_purge_feed'] && is_feed() ) {
      return;
    } elseif ( ! $this->setup->options['wp_akamai_purge_category'] && is_category() ) {
      return;
    } elseif ( ! $this->setup->options['wp_akamai_purge_archive'] && is_archive() ) {
      return;
    } elseif ( ! $this->setup->options['wp_akamai_purge_tag'] && is_tag() ) {
      return;
    }

		foreach( $defaults as $default_key => $default_value ) {
			if ( isset( $this->setup->options[$default_key] ) && ! empty( $this->setup->options[$default_key] ) ) {
				$defaults[$default_key] = $this->setup->options[$default_key];
			}
		}

		if ( ! headers_sent() )
			header( 'Edge-control: ' . $defaults['edge_options'] . ',max-age=' . $defaults['edge_max_age'] . ',downstream-ttl=' . $defaults['edge_downstream_ttl'] );
	}

  /**
   * Undocumented function
   *
   * @param [type] $host
   * @return void
   */
	public function purge() {

    $responses  = [];
    $success    = true;

    $hosts = apply_filters( 'wp_akamai_filter_hosts', $this->setup->options['wp_akamai_hostnames'] );

    foreach( array_filter( $hosts ) as $host ) {
      $body = json_encode( apply_filters( 'wp_akamai_filter_body', $this->get_purge_body( $host ) ) );
		  $auth = $this->get_purge_auth( $body );

      $responses[] = wp_remote_post( 'https://' . $auth->getHost() . $auth->getPath(), array(
			  'user-agent' => $this->get_user_agent(),
			  'headers' => array(
				  'Authorization' => $auth->createAuthHeader(),
				  'Content-Type' => 'application/json',
		  	  ),
			  'body' => $body
		  ) );
    }

    $responses = array_map( function ( $response ) use ( &$success ) {
      if ( wp_remote_retrieve_response_code( $response ) !== 201 ) {
        $success = false;
      }
      return json_decode( wp_remote_retrieve_body( $response ) );
    }, $responses );

    if ( ! $success ) {
      $instance = $this;
      add_filter( 'redirect_post_location', function ( $location ) use ( $instance, $responses ) {
				return $instance->add_error_query_arg( $location, $responses );
      }, 100 );
    } else {
      add_filter( 'redirect_post_location', array( &$this, 'add_success_query_arg' ) , 100 );
    }

    return $success;
	}

  /**
   * Undocumented function
   *
   * @param [type] $body
   * @return void
   */
	protected function get_purge_auth( $body ) {
		$auth = \Akamai\Open\EdgeGrid\Authentication::createFromEnv( $this->akamai_section );
		$auth->setHttpMethod( 'POST' );
		$auth->setPath( '/ccu/v3/invalidate/url' );
		$auth->setBody( $body );

		return $auth;
	}

  /**
   * Undocumented function
   *
   * @param [type] $host
   * @return void
   */
	protected function get_purge_body( $host ) {
    // purge post itself
		$this->purge_post();

    // purge front
    if ( $this->setup->options['wp_akamai_purge_front'] ) {
      $this->purge_front( $this->base_url );
    }

    // get hostname
		$wp_host = $this->get_hostname( $host );

    // purge tags
    if ( $this->setup->options['wp_akamai_purge_tags'] ) {
		  $this->purge_tags();
    }

    // purge categories
    if ( $this->setup->options['wp_akamai_purge_categories'] ) {
      $this->purge_categories();
    }

    // purge archives
		if ( $this->setup->options['wp_akamai_purge_archive'] ) {
			$this->purge_archive();
		}

    // purge feed
    if ( $this->setup->options['wp_akamai_purge_feed'] ) {
      $this->purge_feed();
    }

    // purge pagemanager
    if ( $this->setup->options['wp_akamai_purge_pagemanager'] ) {
      $this->purge_pagemanager();
    }

    // data
		$data = array(
			'hostname' => $wp_host,
			'objects'  => $this->purge_objects
		);

		return $data;
	}

  /**
   * Undocumented function
   *
   * @return void
   */
  protected function purge_post() {
    $permalink   = get_permalink( $this->purge_post->ID );

    $this->purge_objects[] = $this->get_post_url( $permalink );
  }

  /**
   * Undocumented function
   *
   * @return void
   */
  protected function purge_front() {
		$this->purge_objects[] = $this->base_url;
  }

  /**
   * Undocumented function
   *
   * @return void
   */
  protected function purge_tags() {
    $tags = get_the_tags( $this->purge_post->ID );

    if ( $tags !== false && ! ( $tags instanceof WP_Error ) ) {
      foreach ( $tags as $tag ) {
        $this->purge_objects[] = $this->get_post_url( get_tag_link( $tag ) );
      }
    }
  }

  /**
   * Undocumented function
   *
   * @return void
   */
  protected function purge_categories() {
    $categories = get_the_category( $this->purge_post->ID );

    if ( $categories !== false && ! ( $categories instanceof WP_Error ) ) {
			foreach ( $categories as $category ) {
				$url = $this->get_post_url( get_category_link( $category ) );
				$this->purge_objects[] = $url;
			}
		}
  }

  /**
   * Undocumented function
   *
   * @return void
   */
  protected function purge_archive() {
    $archive = get_month_link( get_post_time( 'Y', false, $this->purge_post ), get_post_time( 'm', false, $purge_post ) );

    if ( $archive !== false && ! ( $archive instanceof WP_Error ) ) {
			$this->purge_objects[] = $this->get_post_url( $archive );
		}
  }

  /**
   * Undocumented function
   *
   * @return void
   */
  protected function purge_feed() {
    $feeds = array( '', 'rss', 'xmli', 'rdf', 'atom' );

    foreach( $feeds as $feed ) {
      $this->purge_objects[] = $this->get_post_url( get_feed_link( $feed ) );
      $this->purge_objects[] = $this->get_post_url( add_query_arg( 'feed', $feed, trailingslashit( get_home_url() ) ) );
    }
  }

  /**
   * Undocumented function
   *
   * @return void
   */
  protected function purge_pagemanager() {
    global $wpdb;
    $table_name = 'asse_pagemanager_settings';

    if ( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name ) {
      return;
    }

    if ( ( $terms = wp_get_post_categories( $this->purge_post->ID ) ) instanceof WP_ERROR ) {
      return;
    }

    $pages = $wpdb->get_results(
      "
      SELECT `page_id` as `ID`, `page_type` as `type`, `settings`
      FROM $table_name
      WHERE `status`  = 1
      AND ( `page_type` = 'category' OR `page_type` = 'page' OR `page_type` = 'index' OR `page_type` = 'tag' OR `page_type` = 'index' )
      "
    , ARRAY_A );

    foreach ( $pages as $page ) {
      // map settings
      $settings = array_filter( unserialize( $page['settings'] ), function( $setting ) use ( $page ) {
        return in_array( $setting['type'], array( 'selectedposts', 'termposts' ) );
      } );

      // get url for category
      if ( $page['type'] === 'category' ) {
        $url = $this->get_post_url( get_category_link( $page['ID'] ) );
      }

      // get url for tag
      if ( $page['type'] === 'tag' ) {
        $url = $this->get_post_url( get_tag_link( $page['ID'] ) );
      }

      // search for id in terms
      if ( isset( $url ) // a bit ugly
        && in_array( intval( $page['ID'] ), $terms )
        && ! in_array( $url, $this->purge_objects ) ) {
          $this->purge_objects[] = $url;
      }

      // index
      if ( $page['type'] === 'index' ) {
        $url = $this->base_url;
      }

      // page
      if ( $page['type'] === 'page' ) {
        $url = $this->get_post_url( get_page_link( $page['ID'] ) );
      }

      // if set url, then evalute purging
      if ( isset( $url ) ) {
        $this->walk_pageblocks_settings( $settings, $terms, $url );
      }
    }

  }

  /**
   * Undocumented function
   *
   * @param [type] $blocks
   * @param [type] $terms
   * @param [type] $url
   * @return void
   */
  protected function walk_pageblocks_settings( $blocks, $terms, $url ) {
    // walk
    array_walk( $blocks, function( $block ) use ( $terms, $url ) {
      if ( $block['type'] === 'selectedposts' ) {
        if ( in_array( $this->purge_post->ID, explode( ',', $block['settings']['posts'] ) )
         && ! in_array( $url, $this->purge_objects ) ) {
          $this->purge_objects[] = $url; // refactor to function
        }
      }

      if ( $block['type'] === 'termposts' ) {
        $rel_terms = array_merge( array(), explode( ',', $block['settings']['terms'] ) );
        $rel_terms = array_filter( $rel_terms, function( $term ) use ( $terms ) {
          return in_array( $term, $terms );
        } );

        if ( ( count( $rel_terms ) > 0 || in_array( $this->purge_post->ID, explode( ',', $block['settings']['featured-posts'] ) ) )
          && ! in_array( $url, $this->purge_objects ) ) {
            $this->purge_objects[] = $url; // refactor to function
        }
      }
    } );
  }

  /**
   * Undocumented function
   *
   * @param [type] $post_url
   * @return void
   */
	protected function get_post_url( $post_url ) {
    $url = parse_url( $post_url );
    $post_url = $url['path'];

    if ( ! is_null( $url['query'] ) ) {
      $post_url .= '?' . $url['query'];
    }

    return $post_url;
	}

  /**
   * Undocumented function
   *
   * @return void
   */
	protected function get_user_agent() {
		return sprintf( 'WordPress/%s WP-Akamai/%s PHP/%s', get_bloginfo( 'version' ), $this->config->version, phpversion() );
	}

  /**
   * Undocumented function
   *
   * @param [type] $host
   * @return void
   */
	public function get_hostname( $host ) {
		if ( ! empty( $host ) ) {
			return $host;
		}

		$wp_url       = parse_url( get_bloginfo( 'wpurl' ) );
		$wp_host      = $wp_url['host'];

		return $wp_host;
	}


  /**
   * Add a parameter in case of error
   *
   * @param [type] $location
   * @param [type] $responses
   * @return void
   */
	public function add_error_query_arg( $location, $responses ) {
		remove_filter( 'redirect_post_location', array( $this, 'add_error_query_arg' ), 100 );

		return add_query_arg( array( 'wp-akamai-purge-error' => 'true' ), $location );
	}

  /**
   * Add a parameter in case of success
   *
   * @param [type] $location
   * @return void
   */
	public function add_success_query_arg( $location ) {
		remove_filter( 'redirect_post_location', array( &$this, 'add_success_query_arg' ), 100 );

		return add_query_arg( array( 'wp-akamai-purge-success' => 'true' ), $location );
	}

  /**
   * Show admin notice
   *
   * @return void
   */
	public function admin_notices() {
		if ( isset( $_GET['wp-akamai-purge-error'] ) ) {
			$timber_context = array(
			  'error'   => 'Unable to purge Akamai Cache.'
			);
			Timber::render( 'wp-framework-notice-error.twig', $timber_context );
		}
	}

  /**
   * Get options
   *
   * @return array
   */
  public function set_options() {
    $options = array(
      'hostnames'         => get_option( 'wp_akamai_hostnames' ),
		  'host'              => get_option( 'wp_akamai_host' ),
      'client_token'      => get_option( 'wp_akamai_client_token' ),
      'client_secret'     => get_option( 'wp_akamai_client_secret' ),
      'access_token'      => get_option( 'wp_akamai_access_token' ),
      'purge_front'       => get_option( 'wp_akamai_purge_front' ),
      'purge_categories'  => get_option( 'wp_akamai_purge_categories' ),
      'purge_tags'        => get_option( 'wp_akamai_purge_tags' ),
      'purge_archive'     => get_option( 'wp_akamai_purge_archive' ),
      'purge_pagemanager' => get_option( 'wp_akamai_purge_pagemanager' ),
      'purge_feed'        => get_option( 'wp_akamai_purge_feed' ),
      'edge_downstream_ttl' => get_option( 'wp_akamai_edge_downstream_ttl' ),
      'edge_max_age'        => get_option( 'wp_akamai_edge_max_age' ),
      'edge_options'        => get_option( 'wp_akamai_edge_options' )
    );

    $this->setup->options = $options;
  }

   /**
     * Activates the Bootstrap plugin
     *
     * @return bool
     */
    public static function activation()
    {
      // noop
		  return true;
    }

    /**
     * Do actions after init
     */
    public function after_init()
    {
        // noop
    }

    /**
     * Deactivates the Bootstrap plugin
     *
     * @return bool
     */
    public static function deactivation()
    {
        // noop
		return true;
    }

     /**
     * Enqueue required scripts
     *
     * @return
     */
    public function enqueue_scripts()
    {

    }

    /**
     * Enqueue shared styles and scripts
     *
     * @return
     */
    public function enqueue_admin_scripts()
    {
    }
}
