<?php
/*
Plugin Name: Theme to Browser (T2B) Control
Description: Display a different theme depending on the users browser. It is perfect for a quick way out of CSS annoyances and also for customization on mobile devices. Supports: Internet Explorer, FireFox, Opera, iPhone/iPod, iPad, Safari, BlackBerries, Playstation 3
Version: 1.0
Author: Federico Jacobi
Author URI: http://www.federicojacobi.com
*/

defined('ABSPATH') or die("No script kiddies please!");

class Theme2Browser {

	var $supported_browsers = array();
	var $user_agent = '';
	var $theme = '';
	var $theme_parent = '';

	function __construct() {
		add_action( 'setup_theme', array( $this, 'init' ) );
	}
	
	function init() {
		$options = get_option( 't2b_options' );
		
		$this->supported_browsers = apply_filters( 't2b-browsers', array (
		
			'ie' => array( 
				'regex' => '/(MSIE [0-9]?.[0-9]{1,2})|(Mozilla\/5\.0.*rv:11.[0-9]{1,2})/',
				'title' => 'Internet Explorer'
			),
			'firefox' => array( 
				'regex' => '|Firefox/([0-9\.]+)|',
				'title' => 'Firefox'
			),
			'opera' => array(
				'regex' => '/Opera|OPR\/(.[0-9]){1,5}/',
				'title' => 'Opera'
			),
			'chrome' => array(
				'regex' => '|Chrome/[0-9]{1,3}(.[0-9]{1,3}){1,5}|',
				'title' => 'Chrome'
			),	
			'iphone' => array(
				'regex' => '/\(iPhone|iPod/',
				'title' => 'iPhone'
			),
			'ipad' => array(
				'regex' => '/\(iPad/',
				'title' => 'iPad'
			), 
			'safari' => array(
				'regex' => '|Safari|',
				'title' => 'Safari'
			),	
			'blackberry' => array(
				'regex' => '/BlackBerry/',
				'title' => 'All BlackBerry'
			),
			'ps3' => array(
				'regex' => '/PLAYSTATION 3|PS3/',
				'title' => 'Playstation 3'
			)
		) );
		
		
		register_activation_hook( __FILE__, array( $this, 'onActivation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'onDeactivation' ) );
		add_action( 'admin_menu', array( $this, 'addMenu' ) );
		add_action( 'admin_init', array( $this, 'initSettings' ) );
		$this->T2B();
	}
	
	function T2B() {
		if ( ! is_array( $this->supported_browsers ) )
			return false;
	
		$this->user_agent = $_SERVER['HTTP_USER_AGENT'];
		if ( ! is_admin() ) {
			$option = get_option( 't2b_options' );
			
			foreach ( $this->supported_browsers as $id => $browser_data ) {
				if ( preg_match( $browser_data[ 'regex' ], $this->user_agent, $matched ) ) {
					$this->theme = $option[ $id ];
					break;
				}
			}
			
			if ( $this->theme && $this->theme != "t2b_default" ) {
				
					$theme = wp_get_theme( $this->theme );
				
					if ( $theme->exists() ) {
						$this->theme_parent = $this->theme;
						add_filter( 'template', array( $this, 'setTheme' ) );
						add_filter( 'stylesheet', array( $this, 'setCSS' ) );
					}
			}
			add_action( 'wp_footer', array( $this, 'debugInfo' ) );
		}
	}
	
	function addMenu() {
		add_theme_page(
			'T2B Control',
			"T2B Control",
			'manage_options',
			't2b_control',
			array( $this, 'doOptionsPage' )
		);
	}
	
	function initSettings() {
		register_setting( 'browsers', 't2b_options' );
		register_setting( 'debug', 't2b_options' );
		
		add_settings_section( 'browsers', 'Available browsers', array( $this, 'browser_section_text') , 't2b_control' );
		add_settings_section( 'debug', 'Debug Mode', null , 't2b_control' );
		
		foreach ( $this->supported_browsers as $id => $browser_data ) {
			add_settings_field( 't2b_' . $id, $browser_data[ 'title' ], array( $this, 'render_browser_option' ), 't2b_control', 'browsers', array( 'id' => $id ) );
		}
		
		add_settings_field( 't2b_debug', 'Debug Mode', array( $this, 'render_debug_option' ), 't2b_control', 'debug' );
	}
	
	function render_browser_option( $args ) {
		$options = get_option( 't2b_options' );
		
		$browser_slug = sanitize_text_field( $args[ 'id' ] );
		
		if ( isset( $options[ $browser_slug ] ) )
			$selected_theme = $options[ $browser_slug ];
		else
			$selected_theme = false;

		$themes = wp_get_themes();
	?>
		<select name="<?php echo "t2b_options[{$browser_slug}]" ?>" id="<?php echo "t2b_options[{$browser_slug}]" ?>" >
			<option value="0" <?php $selected_theme ? '' : selected( true ) ?> />Default theme</option>
			<?php
			foreach( $themes as $slug => $theme ) {
				?>
				<option value="<?php echo esc_attr( $slug ) ?>" <?php selected( $slug, $selected_theme ) ?> />
					<?php echo esc_html( $theme->name ); ?>
				</option>
				<?php
			}
		?>
		</select>
		<?php
		if ( $options[ 'debug' ] ) {
			echo " <code>". esc_html( $args[ 'regex' ] ) . "</code>";
			if ( preg_match( $args[ 'regex' ], $this->user_agent ) ) {
				echo " <strong><- Matches your current browser</strong>";
			}
		}
	}
	
	function render_debug_option() {
		$options = get_option( 't2b_options' );
		$debug = $options[ 'debug' ];
		?>
		<select name="t2b_options[debug]" id="t2b_options[debug]">
			<option value="0" <?php selected( $debug, 0 ) ?> >OFF</option>
			<option value="1" <?php selected( $debug, 1 ) ?> >ON</option>
		</select>
		<?php
	}
	
	function browser_section_text() {
	?>
		<p>Select the desired theme to show for the respective browser. </p>
	<?php
	}

	function doOptionsPage() {
		?>
		<div class="wrap">
			<?php					
				$plugin_data = get_plugin_data( __FILE__ );
			?>
			<h2>Theme to Browser Control a.k.a. T2B (v.<?php echo esc_html( $plugin_data[ 'Version' ] ) ?>)</h2>
			<?php echo apply_filters( 't2b-extra-text', '' ); ?>
			
			<form action="options.php" method="post">
				
				<?php settings_fields( 'browsers' ); ?>
				<?php do_settings_sections('t2b_control' ); ?>
				
				<?php submit_button(); ?>
				
			</form>
			<hr />
			<h2>Extras</h2>
			<p>Your browser's user-agent string: <code><?php echo $this->user_agent ?></code></p>
			<p>More user agent strings: <a href="http://www.useragentstring.com/pages/useragentstring.php" target="_blank">http://www.useragentstring.com</a></p>
			
			<h3>How to add your browser/variation/version</h3>
			<p>First you need some really basic knowledge of regular expressions and php.</p>
			<p>Because you are loading a completely different theme using filters in your theme is not an option. It kicks in too late.
			So you have to dive	into the plugin's code to do it, however, it is easy enough.</p>
			
			<p>Alternatively you could hook into <code>plugins_loaded</code> with a plugin and use the 
			<code>t2b-browsers</code> filter to make you own extension.</p>
			<p>
				Find the <code>$supported_browsers</code> array and add the proper regular expression to evaluate against the user agent string.
				For example, if you want to add Wii to your browser list add the following:
			</p>
<pre>
$this->supported_browsers = apply_filters( 't2b-browsers', array (
	'ie' => array( 
		'regex' => '|MSIE ([0-9]?.[0-9]{1,2})|',
		'title' => 'Internet Explorer'
	),
	'firefox' => array( 
		'regex' => '|Firefox/([0-9\.]+)|',
		'title' => 'Firefox'
	),
	.
	.
	.
	'ps3' => array(
		'regex' => '/PLAYSTATION 3|PS3/',
		'title' => 'Playstation 3'
	),
	// ADDING NINTENDO WII BROWSER EXCEPTION
	'wii' => array( 
		'regex' => '/Nintendo Wii/',
		'title' => 'Nintendo Wii'
	)
);
</pre>

			<p>Notice, if you want to add, let's say a version of IE you need to added BEFORE the current line, as they are evaluated
			in order. The first line in this example will catch ALL IE matches, version variations must come before that ! 
			If your line is too broad it'll catch too much, if it is to specific you might miss some cases.
			</p>
			
			<p>Use the debug option to get some theme information in your wp_footer. That way you'll know you are
			loading the proper theme depending on your setup.</p>
			<p>Supported browsers: 
				<?php
				$browsers = array();
				foreach ( $this->supported_browsers as $id => $browser_data ) {
					$browsers[] = $browser_data[ 'title' ];
				}
				echo esc_html( implode( ', ', $browsers ) );
				?>
			</p>
		</div>		
		<?
	}

	function debugInfo() {
		$options = get_option( 't2b_options' );
		$debug = $options[ 'debug' ];
		if ( ! $debug )
			return;
	?>
		<div>
			ThemeParent: <?php echo $this->theme_parent ?><br>
			ThemeCss: <?php echo $this->theme ?><br>
			User Agent: <?php echo $this->user_agent ?><br>
		</div>
	<?php
	}

	function setTheme() {
		return $this->theme_parent;
	}
	
	function setCSS() {
		return $this->theme;
	}
	
	function onActivation() {
	}
	
	function onDeactivation() {
	}
	
}
new Theme2Browser();