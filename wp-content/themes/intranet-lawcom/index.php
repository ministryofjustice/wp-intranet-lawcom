<?php get_template_part('templates/page', 'header'); ?>

<?php if (!have_posts()) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'sage'); ?>
  </div>
  <?php get_search_form(); ?>
<?php endif; ?>
<div class="latest-messages">
<ul>
<?php while (have_posts()) : the_post(); ?>
<li>
				<div class="">
					<div class="meta">
						<time class="published" datetime="<?php echo get_the_time( 'c' ); ?>"><strong><?php echo get_the_date(); ?></strong></time>
					</div>
						<h3>
							<?php the_title(); ?>
						</h3>

						<?php the_content(); ?>			
			</div>
			</li>
 
<?php endwhile; ?>
</ul>
</div>
<?php the_posts_navigation(); ?>
