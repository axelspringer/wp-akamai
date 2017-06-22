<?php

class AsseAkamai {

  const VERSION           = '0.0.1';

  private $akamai_prefix         = 'AKAMAI';
  private $akamai_env_vars       = array( 'host', 'client_token', 'client_secret', 'access_token' );
  private $akamai_section        = 'default';

  private $akamai_edge_defaults  = array( 'edge_max_age' => '1d', 'edge_downstream_ttl' => '1m', 'edge_options' => '!no-store');

  protected $plugin_name;
  protected $settings;
  protected $options;

  public function __construct() {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    if ( ! is_plugin_active( 'asse-akamai/asse-akamai.php' ) ) {
      return;
    }

    $this->plugin_name    = 'asse_akamai';
    $this->settings       = new AsseAkamaiSettings( $this->plugin_name );
    $this->options        = $this->get_option();

    // we save multi-host for later
    $this->options['hostname'] = $this->options['hostnames'][0];

    $this->set_env_vars();
    $this->register_hooks();
  }

  public function register_hooks() {
    add_action( 'save_post', array( &$this, 'purge_on_post' ) );
    // add_action( 'comment_post', array( &$this, 'purge_on_comment' ) );

    add_action( 'send_headers', array( &$this, 'send_headers' ) );
    add_action( 'admin_notices', array( &$this, 'admin_notices' ) );

    add_filter( 'http_request_timeout', array( &$this, 'wp9838c_timeout_extend' ) );
  }

  public function wp9838c_timeout_extend( $time ) {
    // Default timeout is 5
    return 60;
  }

  public function set_env_vars() {
    foreach ( $this->akamai_env_vars as $akamai_env_var ) { // not nice, but leave for now
      $_ENV[$this->akamai_prefix . '_' . strtoupper($akamai_env_var)] = $this->options[$akamai_env_var];
    }
  }

  public function purge_on_post( $post_id ) {
    if ( ! is_object( $post = get_post( $post_id ) ) || $post->post_status !== 'publish' ) {
      return true;
    }

		$this->purge( $this->options, $post );
  }

  public function send_headers() {
    $defaults   = $this->akamai_edge_defaults;

    foreach( $defaults as $default_key => $default_value ) {
      if ( isset( $this->options[$default_key] ) && ! empty( $this->options[$default_key] ) ) {
        $defaults[$default_key] = $this->options[$default_key];
      }
    }

    if ( false === headers_sent() ) {
      header( 'Edge-control: ' . $defaults['edge_options'] . ',max-age=' . $defaults['edge_max_age'] . ',downstream-ttl=' . $defaults['edge_downstream_ttl'] );
    }
  }

  public function purge( $options, $post ) {
    $body = $this->get_purge_body( $this->options, $post );
		$auth = $this->get_purge_auth( $this->options, $body );

		$response = wp_remote_post( 'https://' . $auth->getHost() . $auth->getPath(), array(
			'user-agent' => $this->get_user_agent(),
			'headers' => array(
				'Authorization' => $auth->createAuthHeader(),
				'Content-Type' => 'application/json',
			),
			'body' => $body
		) );

    if ( wp_remote_retrieve_response_code( $response ) !== 201 ) {
      $instance = $this;
      add_filter( 'redirect_post_location', function ( $location ) use ( $instance, $response ) {
        $body = json_decode( wp_remote_retrieve_body( $response ) );

        return $instance->add_error_query_arg( $location, $body );
      }, 100 );
    } else {
      add_filter( 'redirect_post_location', array( &$this, 'add_success_query_arg') , 100 );
    }
  }

  protected function get_purge_auth( $options, $body ) {
		$auth = \Akamai\Open\EdgeGrid\Authentication::createFromEnv( $this->akamai_section );
		$auth->setHttpMethod( 'POST' );
		$auth->setPath( '/ccu/v3/invalidate/url' );
		$auth->setBody( $body );

		return $auth;
	}

  protected function get_purge_body( $options, $post ) {
    $base_url         = parse_url( get_bloginfo( 'wpurl' ), PHP_URL_PATH ) . '/';
		$base_url         = apply_filters( 'asse_akamai_canonical_url', $base_url );

		$post_permalink   = get_permalink( $post->ID );

    $purge_objects    = array(
      $this->get_post_url( $post_permalink )
    );

		if ( $options['purge_front'] ) {
			$purge_objects[] = $base_url;
		}

		$wp_host = $this->get_hostname( $options );

		$data = array(
			'hostname' => $wp_host,
			'objects'  => $purge_objects
		);

		return json_encode( $data );
	}

  protected function get_post_url( $post_url ) {
    $post_url = parse_url( $post_url, PHP_URL_PATH );
    if ( strpos( $post_url, '?' ) !== false ) {
      $post_url .= '?' . parse_url( $post_url, PHP_URL_QUERY );
    }

		return $post_url;
	}

  protected function get_user_agent() {
		return
			'WordPress/' . get_bloginfo( 'version' ) . ' ' .
			'Asse-Akamai-for-WordPress/' . self::VERSION . ' ' .
			'PHP/' . phpversion();
	}

  public function get_hostname( $options ) {
    if ( isset( $options['hostname'] ) ) {
      return $options['hostname'];
    }

		$wp_url       = parse_url( get_bloginfo( 'wpurl' ) );
		$wp_host      = $wp_url['host'];

		return $wp_host;
	}

  public function get_option() {
    $options = array(
      'hostnames'       => get_option( 'asse_akamai_hostnames' ),
      'host'            => get_option( 'asse_akamai_host' ),
      'client_token'    => get_option( 'asse_akamai_client_token' ),
      'client_secret'   => get_option( 'asse_akamai_client_secret' ),
      'access_token'    => get_option( 'asse_akamai_access_token' ),
      'purge_front'     => get_option( 'asse_akamai_purge_front' ),
      'purge_category'  => get_option( 'asse_akamai_purge_category' ),
      'purge_tags'      => get_option( 'asse_akamai_purge_tags' ),
      'edge_downstream_ttl' => get_option( 'asse_akamai_edge_downstream_ttl' ),
      'edge_max_age'        => get_option( 'asse_akamai_edge_max_age' ),
      'edge_options'        => get_option( 'asse_akamai_edge_options' )
    );

    return $options;
  }

  public function add_error_query_arg( $location, $response ) {
		remove_filter( 'redirect_post_location', array( $this, 'add_error_query_arg' ), 100 );

		return add_query_arg( array( 'asse-akamai-purge-error' => urlencode( $response->detail ) ), $location );
	}

	public function add_success_query_arg( $location ) {
		remove_filter( 'redirect_post_location', array( &$this, 'add_success_query_arg' ), 100 );

		return add_query_arg( array( 'asse-akamai-purge-success' => 'true' ), $location );
	}

	public function admin_notices() {
		if ( isset( $_GET['asse-akamai-purge-error'] ) ) {
			?>
			<div class="error notice is-dismissible">
				<p>
					<?php esc_html_e( 'Unable to purge cache: ' . $_GET['asse-akamai-purge-error'], 'asse-akamai' ); ?>
				</p>
			</div>
			<?php
		}
	}

  public static function activate() {
    add_option( 'asse_akamai_version', AsseAkamai::VERSION );
  }

  public static function deactivate() {

  }

}
