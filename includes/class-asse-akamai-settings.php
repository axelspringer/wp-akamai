<?php

class AsseAkamaiSettings {

  protected $plugin_title;
  protected $plugin_menu_title;
  protected $plugin_permission;
  protected $plugin_slug;
  protected $hook_suffix;

  public function __construct( $slug ){
    $this->plugin_slug = $slug;

    $this->init();

	  add_action(	'admin_menu',			  array( &$this, 'add_admin_menu' ) );
	  add_action( 'admin_init',			  array( &$this, 'register_settings' ) );
	  add_action( 'admin_notices', 		  array( &$this, 'theme_settings_admin_notices' ) );
    add_action( 'admin_enqueue_scripts',  array( &$this, 'enqueue_admin_scripts' ) );
  }

  private function init() {
    $this->plugin_title       = __( 'ASSE Akamai', 'asse-akamai' );
		$this->plugin_menu_title  = __( 'Akamai', 'asse-akamai' );
    $this->plugin_slug        = $this->plugin_slug . '_settings_page';

    $this->plugin_permission  = 'manage_options';
  }

	public function register_settings() {

    // Zugang
		$args = array(
			'id'			    => 'asse_akamai_credentials',
			'title'			  => 'Zugang',
			'page'			  => $this->plugin_slug,
			'description'	=> '',
		);
		$asse_akamai_credentials = new AsseAkamaiSettingsSection( $args );

    $args = array(
			'id'				    => 'asse_akamai_client_token',
			'title'				  => 'Client Token',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_akamai_credentials',
			'description'   => '',
			'type'				  => 'text', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
		);
		$asse_akamai_client_token = new AsseAkamaiSettingsField( $args );

    $args = array(
			'id'				    => 'asse_akamai_client_secret',
			'title'				  => 'Client Secret',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_akamai_credentials',
			'description'   => '',
			'type'				  => 'text', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
		);
		$asse_akamai_client_secret = new AsseAkamaiSettingsField( $args );

    $args = array(
			'id'				    => 'asse_akamai_access_token',
			'title'				  => 'Access Token',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_akamai_credentials',
			'description'   => '',
			'type'				  => 'text', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
		);
		$asse_akamai_access_token = new AsseAkamaiSettingsField( $args );

		$args = array(
			'id'			  => 'asse_akamai_settings',
			'title'			=> 'Einstellungen',
			'page'			=> $this->plugin_slug,
			'description'	=> '',
		);
		$asse_akamai_settings = new AsseAkamaiSettingsSection( $args );

		$args = array(
			'id'				    => 'asse_akamai_hostnames',
			'title'				  => 'Hostnamen',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_akamai_settings',
			'description'   => 'Hostnamen die bei Akamai verwendet werden (z.B. www.techbook.de)',
			'type'				  => 'text', // text, textarea, password, checkbox
			'multi'				  => true,
			'option_group'	=> $this->plugin_slug,
		);
		$asse_akamai_hostnames = new AsseAkamaiSettingsField( $args );

    $args = array(
			'id'				    => 'asse_akamai_host',
			'title'				  => 'Host',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_akamai_credentials',
			'description'   => '',
			'type'				  => 'text', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
		);
		$asse_akamai_host = new AsseAkamaiSettingsField( $args );

    $args = array(
			'id'				    => 'asse_akamai_purge_front',
			'title'				  => 'Frontpage mit bereinigen',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_akamai_settings',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'option_group'	=> $this->plugin_slug,
		);
		$asse_akamai_purge_front = new AsseAkamaiSettingsField( $args );

    $args = array(
			'id'				    => 'asse_akamai_purge_categories',
			'title'				  => 'Kategorien mit bereinigen',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_akamai_settings',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'option_group'	=> $this->plugin_slug,
		);
		$asse_akamai_purge_categories = new AsseAkamaiSettingsField( $args );

    $args = array(
			'id'				    => 'asse_akamai_purge_tags',
			'title'				  => 'Tags mit bereinigen',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_akamai_settings',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'option_group'	=> $this->plugin_slug,
		);
		$asse_akamai_purge_tags = new AsseAkamaiSettingsField( $args );

    $args = array(
			'id'			  => 'asse_akamai_edge_settings',
			'title'			=> 'Edge',
			'page'			=> $this->plugin_slug,
			'description'	=> '',
		);
		$asse_akamai_edge_settings = new AsseAkamaiSettingsSection( $args );

    $args = array(
			'id'				    => 'asse_akamai_edge_max_age',
			'title'				  => 'Edge Max Age',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_akamai_edge_settings',
			'description'   => 'Standardeinstellung: 1d (1 Tag)',
			'type'				  => 'text', // text, textarea, password, checkbox
			'option_group'	=> $this->plugin_slug,
		);
		$asse_akamai_edge_max_age = new AsseAkamaiSettingsField( $args );

    $args = array(
			'id'				    => 'asse_akamai_edge_downstream_ttl',
			'title'				  => 'Edge Downstream TTL',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_akamai_edge_settings',
			'description'   => 'Standardeinstellung: 1m (1 Minute)',
			'type'				  => 'text', // text, textarea, password, checkbox
			'option_group'	=> $this->plugin_slug,
		);
		$asse_akamai_edge_downstream_ttl = new AsseAkamaiSettingsField( $args );

    $args = array(
			'id'				    => 'asse_akamai_edge_options',
			'title'				  => 'Edge Options',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_akamai_edge_settings',
			'description'   => 'Standardeinstellung: !no-store (Nicht im Proxy cachen)',
			'type'				  => 'text', // text, textarea, password, checkbox
			'option_group'	=> $this->plugin_slug,
		);
		$asse_akamai_edge_options = new AsseAkamaiSettingsField( $args );
	}

	public function add_admin_menu() {
		$theme_page = add_options_page( $this->plugin_title, $this->plugin_menu_title, $this->plugin_permission, $this->plugin_slug, array( $this, 'settings_page' ) );
	}

	public function settings_page() {
		?>
		<div class="wrap afbia-settings-page">
			<h2><span class='hidden-xs'><?= esc_html($this->plugin_menu_title) ?></span></h2>
			<form action="options.php" method="post">
			<?php
				global $wp_settings_sections, $wp_settings_fields;
				settings_fields( $this->plugin_slug );
				$page = $this->plugin_slug;
			?>
			<div class="container-fluid settings-container">
				<div class="row container-row">
					<div class="col-xs-12 col-sm-4 col-md-3 navigation-container">
						<ul class="navigation">
						<?php

							if ( isset( $wp_settings_sections[$page] ) ) {
								foreach ( (array) $wp_settings_sections[$page] as $section ) {
									echo '<li class="nav-item">';
										echo '<a href="#'.$section['id'].'">';
											if($section['icon'])
												echo '<i class="fa fa-'.$section['icon'].'"></i> ';

											echo '<span class="hidden-xs">' . $section['title'] . '</span>';

										echo '</a>';
									echo '</li>';
								}
							}

						?>
						</ul>
					</div>
					<div class="col-xs-12 col-sm-8 col-md-9 content-container">
						<?php

							if ( isset( $wp_settings_sections[$page] ) ) {
								foreach ( (array) $wp_settings_sections[$page] as $section ) {
									echo '<div class="section" id="section-'.$section['id'].'">';
									if ( $section['icon'] ) {
										$icon = "<i class='fa fa-{$section['icon']}'></i>";
									} else {
										$icon = null;
									}
									if ( $section['title'] )
										echo "<h2>$icon {$section['title']}</h2>\n";
									if ( $section['callback'] )
										call_user_func( $section['callback'], $section );

									do_action("afb_settings_section_" . $section['id']);

									if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) ) {
										echo '</div>';
										continue;
									}
									echo '<table class="form-table">';
										do_settings_fields( $page, $section['id'] );
									echo '</table>';
									echo '
				<p class="submit">
					<input name="Submit" type="submit" class="button-primary" value="'.esc_attr(__('Save Changes')).'" />
				</p>';
									echo '</div>';
								}
							}

						?>
					</div>
				</div>
			</div>
			</form>


			<div class="credits-container">
				<div class="row">
					<div class="col-xs-12">
            Version <?= get_option( 'asse_akamai_version' ) ?>
					</div>
				</div>
			</div>
		</div><!-- wrap -->
		<?php
	}

  public function enqueue_admin_scripts() {
		wp_register_style( 'asse_akamai_admin_style', ASSE_AKAMAI_PLUGIN_URL . 'admin/admin.css', false, get_option('asse_akamai_version') );
    wp_register_script( 'asse_akamai_admin_script' , ASSE_AKAMAI_PLUGIN_URL . 'admin/admin.min.js', array( 'jquery', 'wp-util'), get_option('asse_akamai_version'), true );

    wp_enqueue_style( 'asse_akamai_admin_style' );
		wp_enqueue_script( 'asse_akamai_admin_script' );
	}

	public function theme_settings_admin_notices(){
		if( isset( $_GET['page'] ) && $_GET['page'] !== 'theme_settings' ){
			return;
		}

		if( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] === true){
			add_settings_error( $this->plugin_slug, $this->plugin_slug, 'Erfolgreich aktualisiert.' , 'updated' );
		}

		settings_errors( $this->plugin_slug );
  }

}
