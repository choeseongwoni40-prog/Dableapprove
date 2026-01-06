<?php get_header(); ?>

<main class="site-container">
    <?php while (have_posts()) : the_post(); ?>
        <article <?php post_class('post-item'); ?>>
            <h1 class="post-title"><?php the_title(); ?></h1>
            <div class="entry-content">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
