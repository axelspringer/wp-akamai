<?php

class AsseAkamaiSettingsSection {

	private $args;

	public function __construct( $args ) {
		$defaults = array(
			'id'			    => NULL,
			'title'			  => NULL,
			'page'			  => NULL,
			'description'	=> NULL,
			'icon'			  => NULL
		);
		$args = wp_parse_args( $args, $defaults );

		$this->args = $args;

		$this->register_section();
	}

	private function register_section() {
		global $wp_settings_sections;
		$wp_settings_sections[$this->args['page']][$this->args['id']] = array( 'id' => $this->args['id'], 'title' => $this->args['title'], 'callback' => array($this, 'output_callback'), 'icon' => $this->args['icon']);
	}

	public function output_callback() {
		?>
			<p><?php echo $this->args['description'] ?></p>
		<?php
	}

}
