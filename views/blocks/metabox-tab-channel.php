<?php
/**
 * The post/page editor tab for channel preferences.
 *
 * @package   Muut
 * @copyright 2014 Muut Inc
 */
global $post;
$tab;

$meta_name = $tab['meta_name'];
$channel_settings = Muut_Post_Utility::getPostOption( $post->ID, 'channel_settings' );
$channel_defaults = muut()->getOption( 'channel_defaults' );

$hide_online = isset( $channel_settings['hide_online'] ) ? $channel_settings['hide_online'] : $channel_defaults['hide_online'];
$disable_uploads = isset( $channel_settings['disable_uploads'] ) ? $channel_settings['disable_uploads'] : $channel_defaults['disable_uploads'];
$channel_path = isset( $channel_settings['channel_path'] ) ? '/' . $channel_settings['channel_path'] : '';

?>
<p>
	<span class="checkbox_row"><input type="checkbox" name="<?php echo $tab['meta_name']; ?>[enabled-tab]" class="muut_enable_<?php echo $tab['name']; ?>" id="muut_enable_tab-<?php echo $tab['name']; ?>" <?php checked( $active_tab, $tab['name'] ); ?> value="1" /><label for="muut_enable_tab-<?php echo $tab['name']; ?>"><?php echo $tab['enable_text']; ?></label></span>
</p>
<div class="enabled_tab_wrapper">
	<p>
		<span class="checkbox_row"><input type="checkbox" name="<?php echo $meta_name; ?>[hide_online]" id="muut_channel_hide_online" value="1" <?php checked( $hide_online, '1' ); ?> /><label for="muut_channel_hide_online"><?php _e( 'Hide online users', 'muut' ); ?></label></span>
		<span class="checkbox_row"><input type="checkbox" name="<?php echo $meta_name; ?>[disable_uploads]" id="muut_channel_disable_uploads" value="1" <?php checked( $disable_uploads, '1' ); ?> /><label for="muut_channel_disable_uploads"><?php _e( 'Disable image uploads', 'muut' ); ?></label></span>
	</p>
	<?php if ( apply_filters( 'muut_show_channel_tab_channel_path_field', false ) ) { ?>
	<p>
		<label for="muut_channel_channel_path" class="text-input-label"><?php _e( 'Channel Path', 'muut' ); ?></label>
		<input type="text" name="<?php echo $meta_name; ?>[channel_path]" id="muut_channel_channel_path" value="<?php echo $channel_path; ?>" placeholder="<?php printf( __( '%sdefault%s', 'muut' ), '/(', ')' ); ?>" />
	</p>
	<?php } ?>
</div>
<div class="disabled_tab_wrapper">
	<?php do_action( 'muut_disabled_tab_content', $tab['name'] ); ?>
</div>