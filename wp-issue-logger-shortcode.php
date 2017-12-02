<?php

//Shortcode to publish issues on a new page

function wpil_display_issues_shortcode()
{
	//The Query
	query_posts('post_type=issue');
	//The Loop
	if ( have_posts() ) : while ( have_posts() ) : the_post();
		echo	'<h3><a href="'; echo the_permalink(); echo '">'; echo the_title(); echo '</a></h3>';
		echo the_excerpt();
	endwhile; else:
	endif;
 
	//Reset Query
	wp_reset_query();
}
add_shortcode('wpil_issues', 'wpil_display_issues_shortcode');
