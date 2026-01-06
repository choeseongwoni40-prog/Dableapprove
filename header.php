<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if (get_option('custom_head_code')) : ?>
    <?php echo get_option('custom_head_code'); ?>
    <?php endif; ?>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="site-container">
        <h1 class="site-title">
            <a href="<?php echo esc_url(home_url('/')); ?>" style="color: inherit; text-decoration: none;">
                <?php bloginfo('name'); ?>
            </a>
        </h1>
        <?php if (get_bloginfo('description')) : ?>
            <p class="site-description"><?php bloginfo('description'); ?></p>
        <?php endif; ?>
    </div>
</header>
