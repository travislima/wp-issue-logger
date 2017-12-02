<?php
/** The template for displaying the issues archive pages
 * 
 * 
  */

get_header();
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="https://pingendo.com/assets/bootstrap/bootstrap-4.0.0-beta.1.css" type="text/css">
</head>
      
<div class="wrap">
<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
    <?php if ( have_posts() ) : ?>
		<header class="page-header">
			<?php
				the_archive_title( '<h1 class="page-title">','</h1>' );
				the_archive_description( '<div class="taxonomy-description">', '</div>' );
			?>
			<h1>All Issues</h1>
		
		</header><!-- .page-header -->
	<?php endif; ?>
            
    <div class="p-5">
    <div class="container">
        <?php
        if(have_posts()) :
        
            /* Start the Loop */
            while(have_posts()) : the_post();  ?>

                <div class="row mb-0 pb-0">
                    <div class="col-md-12">
                        <h3> <?php the_title( '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a>' ); ?> </h3>
                    </div>
                </div>
                
               <?php
              
            endwhile; 
        endif; ?>
    </div>
    </div>

  <!--
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
    -->

    </main><!-- #main -->
	</div><!-- #primary -->
</div> <!-- .wrap -->


<?php
    get_footer();
?>