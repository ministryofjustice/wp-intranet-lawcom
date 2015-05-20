
<div class="rtc"><?php the_field('remember_to_check'); ?></div>

<h2><?php the_field('news_heading'); ?></h2>

<div class="latest-messages">
<ul>
<?php

	$latest_messages_args = array(
		'post_type' => 'post',
		'posts_per_page' => 3,
		'date_query' => array(
        'after' => date('Y-m-d', strtotime('-7 days')) 
    )
	);
	$latest_messages_query = new WP_Query( $latest_messages_args );
	while ( $latest_messages_query->have_posts() ) : $latest_messages_query->the_post();
		?>
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
		<?php wp_reset_postdata(); ?>
		</ul>
		</div>

<h2><?php the_field('shortcuts_heading'); ?></h2>

<?php the_field('corporate_shortcuts'); ?>

<h2><?php the_field('archive_heading'); ?></h2>

<?php the_content(); ?>

