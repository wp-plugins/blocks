<?php

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

function wpcmsb_admin_save_button( $post_id ) {
	static $button = '';

	if ( ! empty( $button ) ) {
		echo $button;
		return;
	}

	$nonce = wp_create_nonce( 'wpcmsb-save-cms-block_' . $post_id );

	$onclick = sprintf(
		"this.form._wpnonce.value = '%s';"
		. " this.form.action.value = 'save';"
		. " return true;",
		$nonce );

	$button = sprintf(
		'<input type="submit" class="button-primary" name="wpcmsb-save" value="%1$s" onclick="%2$s" />',
		esc_attr( __( 'Save', 'cms-block' ) ),
		$onclick );

	echo $button;
}

?><div class="wrap">

<h2><?php
	if ( $post->initial() ) {
		echo esc_html( __( 'Add New CMS Block', 'cms-block' ) );
	} else {
		echo esc_html( __( 'Edit CMS Block', 'cms-block' ) );

		if ( current_user_can( 'wpcmsb_edit_cms_blocks' ) ) {
			echo ' <a href="' . esc_url( menu_page_url( 'wpcmsb-new', false ) ) . '" class="add-new-h2">' . esc_html( __( 'Add New', 'cms-block' ) ) . '</a>';
		}
	}
?></h2>

<?php do_action( 'wpcmsb_admin_notices' ); ?>

<?php
if ( $post ) :

	if ( current_user_can( 'wpcmsb_edit_cms_block', $post_id ) ) {
		$disabled = '';
	} else {
		$disabled = ' disabled="disabled"';
	}
?>

<form method="post" action="<?php echo esc_url( add_query_arg( array( 'post' => $post_id ), menu_page_url( 'wpcmsb', false ) ) ); ?>" id="wpcmsb-admin-form-element"<?php do_action( 'wpcmsb_post_edit_form_tag' ); ?>>
<?php
	if ( current_user_can( 'wpcmsb_edit_cms_block', $post_id ) ) {
		wp_nonce_field( 'wpcmsb-save-cms-block_' . $post_id );
	}
?>
<input type="hidden" id="post_ID" name="post_ID" value="<?php echo (int) $post_id; ?>" />
<input type="hidden" id="wpcmsb-locale" name="wpcmsb-locale" value="<?php echo esc_attr( $post->locale ); ?>" />
<input type="hidden" id="hiddenaction" name="action" value="save" />
<input type="hidden" id="active-tab" name="active-tab" value="<?php echo isset( $_GET['active-tab'] ) ? (int) $_GET['active-tab'] : '0'; ?>" />

<div id="poststuff">
<div id="post-body" class="metabox-holder columns-2">
<div id="post-body-content">
<div id="titlediv">
<div id="titlewrap">
	<label class="screen-reader-text" id="title-prompt-text" for="title"><?php echo esc_html( __( 'Enter title here', 'cms-block' ) ); ?></label>
<?php
	$posttitle_atts = array(
		'type' => 'text',
		'name' => 'post_title',
		'size' => 30,
		'value' => $post->initial() ? '' : $post->title(),
		'id' => 'title',
		'spellcheck' => 'true',
		'autocomplete' => 'off',
		'disabled' => current_user_can( 'wpcmsb_edit_cms_block', $post_id )
			? '' : 'disabled' );

	echo sprintf( '<input %s />', wpcmsb_format_atts( $posttitle_atts ) );
?>
</div><!-- #titlewrap -->

<div class="inside">
<?php
	if ( ! $post->initial() ) :
?>
	<p class="description">
	<label for="wpcmsb-shortcode"><?php echo esc_html( __( "Copy this shortcode and paste it into your post, page, or text widget content:", 'cms-block' ) ); ?></label>
	<span class="shortcode wp-ui-highlight"><input type="text" id="wpcmsb-shortcode" onfocus="this.select();" readonly="readonly" class="large-text code" value="<?php echo esc_attr( $post->shortcode() ); ?>" /></span>
	</p>
<?php
		if ( $old_shortcode = $post->shortcode( array( 'use_old_format' => true ) ) ) :
?>
	<p class="description">
	<label for="wpcmsb-shortcode-old"><?php echo esc_html( __( "You can also use this old-style shortcode:", 'cms-block' ) ); ?></label>
	<span class="shortcode old"><input type="text" id="wpcmsb-shortcode-old" onfocus="this.select();" readonly="readonly" class="large-text code" value="<?php echo esc_attr( $old_shortcode ); ?>" /></span>
	</p>
<?php
		endif;
	endif;
?>
</div>
</div><!-- #titlediv -->
</div><!-- #post-body-content -->

<div id="postbox-container-1" class="postbox-container">
<?php if ( current_user_can( 'wpcmsb_edit_cms_block', $post_id ) ) : ?>
<div id="submitdiv" class="postbox">
<h3><?php echo esc_html( __( 'Status', 'cms-block' ) ); ?></h3>
<div class="inside">
<div class="submitbox" id="submitpost">

<div id="minor-publishing-actions">

<div class="hidden">
	<input type="submit" class="button-primary" name="wpcmsb-save" value="<?php echo esc_attr( __( 'Save', 'cms-block' ) ); ?>" />
</div>

<?php
	if ( ! $post->initial() ) :
		$copy_nonce = wp_create_nonce( 'wpcmsb-copy-cms-block_' . $post_id );
?>
	<input type="submit" name="wpcmsb-copy" class="copy button" value="<?php echo esc_attr( __( 'Duplicate', 'cms-block' ) ); ?>" <?php echo "onclick=\"this.form._wpnonce.value = '$copy_nonce'; this.form.action.value = 'copy'; return true;\""; ?> />
<?php endif; ?>
</div><!-- #minor-publishing-actions -->

<div id="major-publishing-actions">

<?php
	if ( ! $post->initial() ) :
		$delete_nonce = wp_create_nonce( 'wpcmsb-delete-cms-block_' . $post_id );
?>
<div id="delete-action">
	<input type="submit" name="wpcmsb-delete" class="delete submitdelete" value="<?php echo esc_attr( __( 'Delete', 'cms-block' ) ); ?>" <?php echo "onclick=\"if (confirm('" . esc_js( __( "You are about to delete this CMS Block.\n  'Cancel' to stop, 'OK' to delete.", 'cms-block' ) ) . "')) {this.form._wpnonce.value = '$delete_nonce'; this.form.action.value = 'delete'; return true;} return false;\""; ?> />
</div><!-- #delete-action -->
<?php endif; ?>

<div class="save-cms-block textright">
	<?php wpcmsb_admin_save_button( $post_id ); ?>
</div>
</div><!-- #major-publishing-actions -->
</div><!-- #submitpost -->
</div>
</div><!-- #submitdiv -->
<?php endif; ?>

<div id="informationdiv" class="postbox">
<h3><?php echo esc_html( __( 'Information', 'cms-block' ) ); ?></h3>
<div class="inside">
<ul>
<li><?php echo wpcmsb_link( __( 'http://cmsblock.com/docs/', 'cms-block' ), __( 'Docs', 'cms-block' ) ); ?></li>
<li><?php echo wpcmsb_link( __( 'http://cmsblock.com/faq/', 'cms-block' ), __( 'FAQ', 'cms-block' ) ); ?></li>
<li><?php echo wpcmsb_link( __( 'http://cmsblock.com/support/', 'cms-block' ), __( 'Support', 'cms-block' ) ); ?></li>
</ul>
</div>
</div><!-- #informationdiv -->

</div><!-- #postbox-container-1 -->

<div id="postbox-container-2" class="postbox-container">
<div id="cms-block-editor">
<div class="keyboard-interaction"><?php echo sprintf( esc_html( __( '%s keys switch panels', 'cms-block' ) ), '<span class="dashicons dashicons-leftright"></span>' ); ?></div>

<?php

	$editor = new wpcmsb_Editor( $post );
	$panels = array();

	if ( current_user_can( 'wpcmsb_edit_cms_block', $post_id ) ) {
		$panels = array(
			'form-panel' => array(
				'title' => __( 'Block', 'cms-block' ),
				'callback' => 'wpcmsb_editor_panel_form' ));
	}

	$panels = apply_filters( 'wpcmsb_editor_panels', $panels );

	foreach ( $panels as $id => $panel ) {
		$editor->add_panel( $id, $panel['title'], $panel['callback'] );
	}

	$editor->display();
?>
</div><!-- #cms-block-editor -->

<?php if ( current_user_can( 'wpcmsb_edit_cms_block', $post_id ) ) : ?>
<p class="submit"><?php wpcmsb_admin_save_button( $post_id ); ?></p>
<?php endif; ?>

</div><!-- #postbox-container-2 -->

</div><!-- #post-body -->
<br class="clear" />
</div><!-- #poststuff -->
</form>

<?php endif; ?>

</div><!-- .wrap -->

<?php
	
	do_action( 'wpcmsb_admin_footer', $post );
