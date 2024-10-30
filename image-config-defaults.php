<?php
/**
 * Plugin Name: Image Config Defaults
 * Plugin URI:  https://github.com/BenediktBergmann/WordPress-ImageConfigDefaults-Plugin
 * Description: Adds default configuration to images when added to a blog post within gutenberg editor. It will load the configuration of the option page (image_default_align and image_default_link_type).
 * Version:     1.2.5
 * Author:      Benedikt Bergmann
 * Author URI:  https://benediktbergmann.eu
 * Text Domain: Image-Defaults
 * License:     GPL3
 */

	add_action( 'admin_head-post.php', 'imageConfigDefaults_SetImageDefaultSettings' );
	add_action( 'admin_head-post-new.php', 'imageConfigDefaults_SetImageDefaultSettings' );
	function imageConfigDefaults_SetImageDefaultSettings() {
		$alignment = get_option( 'image_default_align' );
		switch ($alignment) {
			case "left":
			case "right":
			case "center":
				break;
			default:
				$alignment = "none";
				break;
		}

		$link = get_option( 'image_default_link_type' );
		switch ($link) {
			case "file":
			case "attachment":
				$link = "attachment";
				break;
			case "media":
			case "media file":
			case "media-file":
				$link = "media";
				break;
			default:
				$link = "none";
				break;
		}

		$options = get_option( 'imageConfigDefaults_plugin_options' );
		$caption = $options['caption'];

	?>
		<script>
		function imageConfigDefaults_setImageDefaultSettings(settings, name) {
			if (name !== "core/image" || !settings.attributes) {
				return settings;
			}

			if(!settings.attributes.linkDestination){
				settings.attributes.linkDestination = {};
			}
			settings.attributes.linkDestination.default = "<?php echo $link ?>";

			if(!settings.attributes.align){
				settings.attributes.align = {};
			}
			settings.attributes.align.default = "<?php echo $alignment ?>";

			if(!settings.attributes.caption){
				settings.attributes.caption = {};
			}
			settings.attributes.caption.default = "<?php echo $caption ?>";

			return settings;
		}

		wp.hooks.addFilter(
			"blocks.registerBlockType",
			"imageConfigDefaults/setImageDefaultSettings",
			imageConfigDefaults_setImageDefaultSettings
		);
		</script>
	<?php
	}

	/* Create Settings page */
	function imageConfigDefaults_add_settings_page() {
		add_options_page( 'Image Config Default Plugin Settings', 'Image Config Default', 'manage_options', 'imageConfigDefaults', 'imageConfigDefaults_render_plugin_settings_page' );
	}
	add_action( 'admin_menu', 'imageConfigDefaults_add_settings_page' );

	function imageConfigDefaults_render_plugin_settings_page() {
		?>
		<h2>Image Config Default Plugin Settings</h2>
		<form action="options.php" method="post">
			<?php 
			settings_fields( 'imageConfigDefaults_plugin_options' );
			do_settings_sections( 'imageConfigDefaults_plugin' ); ?>
			<input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
		</form>
		<?php
	}

	function imageConfigDefaults_register_settings() {
		register_setting( 'imageConfigDefaults_plugin_options', 'imageConfigDefaults_plugin_options');
		register_setting( 'imageConfigDefaults_plugin_options', 'image_default_align');
		register_setting( 'imageConfigDefaults_plugin_options', 'image_default_link_type');
		add_settings_section( 'default_settings', 'Image default settings', 'imageConfigDefaults_plugin_section_text', 'imageConfigDefaults_plugin' );
	
		add_settings_field( 'imageConfigDefaults_plugin_setting_align', 'image_default_align', 'imageConfigDefaults_plugin_setting_align', 'imageConfigDefaults_plugin', 'default_settings' );
		add_settings_field( 'imageConfigDefaults_plugin_setting_link_type', 'image_default_link_type', 'imageConfigDefaults_plugin_setting_link_type', 'imageConfigDefaults_plugin', 'default_settings' );
		add_settings_field( 'imageConfigDefaults_plugin_setting_caption', 'Default caption', 'imageConfigDefaults_plugin_setting_caption', 'imageConfigDefaults_plugin', 'default_settings' );
	}
	add_action( 'admin_init', 'imageConfigDefaults_register_settings' );

	function imageConfigDefaults_plugin_section_text() {
		?>
			<p>Here you can set all the options for using the Image config Defaults plugin</p>
		<?php
	}
	
	function imageConfigDefaults_plugin_setting_caption() {
		$options = get_option( 'imageConfigDefaults_plugin_options' );
		?>
			<input id="imageConfigDefaults_plugin_setting_caption" name="imageConfigDefaults_plugin_options[caption]" type="text" value="<?php echo esc_attr( $options['caption'] ); ?>" />
		<?php
	}

	function imageConfigDefaults_plugin_setting_align() {
		$align = get_option( 'image_default_align' );
		?>
			<select name="image_default_align" id="imageConfigDefaults_plugin_setting_align">
				<option value="none" <?php selected($align, "none"); ?>>None</option>
				<option value="left" <?php selected($align, "left"); ?>>Left</option>	
				<option value="right" <?php selected($align, "right"); ?>>Right</option>
				<option value="center" <?php selected($align, "center"); ?>>Center</option>
			</select>
        <?php
	}

	function imageConfigDefaults_plugin_setting_link_type() {
		$linktype = get_option( 'image_default_link_type' );
		?>
			<select name="image_default_link_type" id="imageConfigDefaults_plugin_setting_link_type">
				<option value="none" <?php selected($linktype, "none"); ?>>None</option>
				<option value="media-file" <?php if ( $linktype == "media" || $linktype == "media file" || $linktype == "media-file" ) echo 'selected="selected"'; ?>>Media file</option>	
				<option value="attachment" <?php if ( $linktype == "attachment" || $linktype == "file") echo 'selected="selected"'; ?>>Attachment</option>
			</select>
        <?php
	}

	/* Add settings link to plugin page */
	add_filter( 'plugin_action_links_image-config-defaults/image-config-defaults.php', 'imageConfigDefaults_settings_link' );
	function imageConfigDefaults_settings_link( $links ) {
		// Build and escape the URL.
		$url = esc_url( add_query_arg(
			'page',
			'imageConfigDefaults',
			get_admin_url() . 'options-general.php'
		) );
		// Create the link.
		$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
		// Adds the link to the end of the array.
		array_push(
			$links,
			$settings_link
		);
		return $links;
	}
?>