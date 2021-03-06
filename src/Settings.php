<?php

namespace AxelSpringer\WP\Akamai;

use AxelSpringer\WP\Bootstrap\Settings\AbstractSettings;
use AxelSpringer\WP\Bootstrap\Settings\Page;
use AxelSpringer\WP\Bootstrap\Settings\Field;
use AxelSpringer\WP\Bootstrap\Settings\Section;

class Settings extends AbstractSettings {

  /**
   * Loading the settings for the plugin
   */
  public function load_settings()
  {
    // Zugang
		$args = array(
			'id'			    => 'wp_akamai_credentials',
			'title'			  => 'Zugang',
			'page'			  => $this->page,
			'description'	=> '',
		);
		$wp_akamai_credentials = new Section( $args );

    $args = array(
			'id'				    => 'wp_akamai_client_token',
			'title'				  => 'Client Token',
			'page'				  => $this->page,
			'section'			  => 'wp_akamai_credentials',
			'description'   => '',
			'type'				  => 'text', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->page,
		);
		$wp_akamai_client_token = new Field( $args );

    $args = array(
			'id'				    => 'wp_akamai_client_secret',
			'title'				  => 'Client Secret',
			'page'				  => $this->page,
			'section'			  => 'wp_akamai_credentials',
			'description'   => '',
			'type'				  => 'text', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->page,
		);
		$wp_akamai_client_secret = new Field( $args );

    $args = array(
			'id'				    => 'wp_akamai_access_token',
			'title'				  => 'Access Token',
			'page'				  => $this->page,
			'section'			  => 'wp_akamai_credentials',
			'description'   => '',
			'type'				  => 'text', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->page,
		);
		$wp_akamai_access_token = new Field( $args );

		$args = array(
			'id'			  => 'wp_akamai_settings',
			'title'			=> 'Einstellungen',
			'page'			=> $this->page,
			'description'	=> '',
		);
		$wp_akamai_settings = new Section( $args );

		$args = array(
			'id'				    => 'wp_akamai_hostnames',
			'title'				  => 'Hostnamen',
			'page'				  => $this->page,
			'section'			  => 'wp_akamai_settings',
			'description'   => 'Hostnamen die bei Akamai verwendet werden (z.B. www.techbook.de)',
			'type'				  => 'text', // text, textarea, password, checkbox
			'multi'				  => true,
			'option_group'	=> $this->page,
		);
		$wp_akamai_hostnames = new Field( $args );

    $args = array(
			'id'				    => 'wp_akamai_host',
			'title'				  => 'Host',
			'page'				  => $this->page,
			'section'			  => 'wp_akamai_credentials',
			'description'   => '',
			'type'				  => 'text', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->page,
		);
		$wp_akamai_host = new Field( $args );

    $args = array(
			'id'				    => 'wp_akamai_purge_front',
			'title'				  => 'Frontpage mit bereinigen',
			'page'				  => $this->page,
			'section'			  => 'wp_akamai_settings',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'option_group'	=> $this->page,
		);
		$wp_akamai_purge_front = new Field( $args );

    $args = array(
			'id'				    => 'wp_akamai_purge_categories',
			'title'				  => 'Kategorien mit bereinigen',
			'page'				  => $this->page,
			'section'			  => 'wp_akamai_settings',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'option_group'	=> $this->page,
		);
		$wp_akamai_purge_categories = new Field( $args );

    $args = array(
			'id'				    => 'wp_akamai_purge_tags',
			'title'				  => 'Tags mit bereinigen',
			'page'				  => $this->page,
			'section'			  => 'wp_akamai_settings',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'option_group'	=> $this->page,
		);
		$wp_akamai_purge_tags = new Field( $args );

    $args = array(
			'id'				    => 'wp_akamai_purge_archive',
			'title'				  => 'Archiv bereinigen',
			'page'				  => $this->page,
			'section'			  => 'wp_akamai_settings',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'option_group'	=> $this->page,
		);
		$wp_akamai_purge_archive = new Field( $args );

    $args = array(
			'id'				    => 'wp_akamai_purge_pagemanager',
			'title'				  => 'PageManager bereinigen',
			'page'				  => $this->page,
			'section'			  => 'wp_akamai_settings',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'option_group'	=> $this->page,
		);
		$wp_akamai_purge_pagemanager = new Field( $args );

    $args = array(
			'id'				    => 'wp_akamai_purge_feed',
			'title'				  => 'Feed bereinigen',
			'page'				  => $this->page,
			'section'			  => 'wp_akamai_settings',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'option_group'	=> $this->page,
		);
		$wp_akamai_purge_feed = new Field( $args );

    $args = array(
			'id'			  => 'wp_akamai_edge_settings',
			'title'			=> 'Edge',
			'page'			=> $this->page,
			'description'	=> '',
		);
		$wp_akamai_edge_settings = new Section( $args );

    $args = array(
			'id'				    => 'wp_akamai_edge_max_age',
			'title'				  => 'Edge Max Age',
			'page'				  => $this->page,
			'section'			  => 'wp_akamai_edge_settings',
			'description'   => 'Standardeinstellung: 1d (1 Tag)',
			'type'				  => 'text', // text, textarea, password, checkbox
			'option_group'	=> $this->page,
		);
		$wp_akamai_edge_max_age = new Field( $args );

    $args = array(
			'id'				    => 'wp_akamai_edge_downstream_ttl',
			'title'				  => 'Edge Downstream TTL',
			'page'				  => $this->page,
			'section'			  => 'wp_akamai_edge_settings',
			'description'   => 'Standardeinstellung: 1m (1 Minute)',
			'type'				  => 'text', // text, textarea, password, checkbox
			'option_group'	=> $this->page,
		);
		$wp_akamai_edge_downstream_ttl = new Field( $args );

    $args = array(
			'id'				    => 'wp_akamai_edge_options',
			'title'				  => 'Edge Options',
			'page'				  => $this->page,
			'section'			  => 'wp_akamai_edge_settings',
			'description'   => 'Standardeinstellung: !no-store (Nicht im Proxy cachen)',
			'type'				  => 'text', // text, textarea, password, checkbox
			'option_group'	=> $this->page,
		);
		$wp_akamai_edge_options = new Field( $args );
  }
}
