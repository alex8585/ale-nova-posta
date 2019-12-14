
<?php 
global  $wpshop_helper;

$is_show_excerpt = true;
$is_show_category = true;
?>

<div class="posts-container posts-container--big">
    <div class='tags-content' tags="<?php echo $tags ?>"> </div>
    <?php if ( $posts->have_posts() ) : ?>
        <?php  while ( $posts->have_posts() ): ?>
            <?php $posts->the_post(); ?>
            <?php
                $thumb = get_the_post_thumbnail( $post->ID, 'thumb-big', array( 'itemprop' => 'image' ) );
            ?>

            <div id="post-<?php the_ID(); ?>" <?php post_class( 'content-card content-card--big' ); ?> itemscope itemtype="http://schema.org/BlogPosting">

                <?php if ( ! empty( $thumb ) ) : ?>
                    <div class="content-card__image">
                        <?php if ( $is_show_category ) : ?>
                            <span class="entry-category"><?php echo wpshop_category() ?></span>
                        <?php endif; ?>
                        <a href="<?php the_permalink() ?>">
                            <?php echo $thumb; ?>
                        </a>
                    </div>
                    <?php get_template_part( 'template-parts/boxes/content-card', 'meta' ) ?>
                <?php endif; ?>

                <?php
                echo '<div class="content-card__title" itemprop="name"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark" itemprop="url"><span itemprop="headline">';
                the_title();
                echo '</span></a></div>';
                ?>

                <?php if ( empty( $thumb ) ) get_template_part( 'template-parts/boxes/content-card', 'meta' ); ?>
                
                
                
                <?php if ( $is_show_excerpt ) { ?>
                    <div class="content-card__excerpt" itemprop="articleBody">
                        <?php echo do_excerpt( get_the_excerpt(), 14 ); ?>
                    </div><!-- .entry-content -->
                <?php } ?>

                <?php if ( ! $is_show_excerpt ) { ?>
                    <meta itemprop="articleBody" content="<?php echo esc_attr( get_the_excerpt() ) ?>">
                <?php } ?>
                <meta itemprop="author" content="<?php echo esc_attr( get_the_author() ) ?>">
                <meta itemscope itemprop="mainEntityOfPage" itemType="https://schema.org/WebPage" itemid="<?php echo esc_attr( get_the_permalink() ) ?>" content="<?php echo esc_attr( get_the_title()) ?>">
                <meta itemprop="dateModified" content="<?php echo esc_attr( get_the_modified_time( 'Y-m-d' ) ) ?>">
                <meta itemprop="datePublished" content="<?php echo esc_attr( get_the_time( 'c' ) ) ?>">
                <?php echo $wpshop_helper->get_microdata_publisher() ?>

            </div><!-- #post-## -->

        <?php endwhile;?>   
        
    <?php else : ?>

        <?php get_template_part( 'template-parts/content', 'none' ); ?>

    <?php endif; ?>

    <?php
        $GLOBALS['wp_query']->max_num_pages = $posts->max_num_pages;
        the_posts_pagination(); 
        wp_reset_query();
    ?>
</div>