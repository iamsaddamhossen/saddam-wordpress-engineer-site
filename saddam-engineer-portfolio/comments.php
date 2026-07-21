<?php
/** Styled comments. */
if ( post_password_required() ) { return; }
?>
<section id="comments" class="sp-comments">
	<?php if ( have_comments() ) : ?>
		<h2><?php echo esc_html( get_comments_number() ); ?> <?php esc_html_e( 'comments', 'sptheme' ); ?></h2>
		<ol class="sp-comment-list"><?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true, 'avatar_size' => 42 ) ); ?></ol>
		<?php the_comments_pagination(); ?>
	<?php endif; ?>
	<?php comment_form( array( 'class_form' => 'sp-comment-form', 'title_reply' => __( 'Join the discussion', 'sptheme' ), 'label_submit' => __( 'Post comment', 'sptheme' ) ) ); ?>
</section>
