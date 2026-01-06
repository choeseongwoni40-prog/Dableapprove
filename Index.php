<?php get_header(); ?>

<main class="site-container">
    <?php if (have_posts()) : ?>
        <div class="post-list">
            <?php while (have_posts()) : the_post(); ?>
                <article class="post-item">
                    <h2 class="post-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                    <div class="post-meta">
                        작성일: <?php echo get_the_date(); ?>
                    </div>
                    <div class="post-excerpt">
                        <?php echo wp_trim_words(get_the_content(), 80, '...'); ?>
                    </div>
                    <a href="<?php the_permalink(); ?>" class="read-more">자세히 보기 →</a>
                </article>
            <?php endwhile; ?>
        </div>
        
        <?php
        the_posts_pagination(array(
            'mid_size' => 2,
            'prev_text' => '← 이전',
            'next_text' => '다음 →',
        ));
        ?>
    <?php else : ?>
        <p>게시글이 없습니다.</p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
