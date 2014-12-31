<?php get_template_part('templates/head'); ?>
<body <?php body_class(); ?>>

  <!--[if lt IE 10]>
    <div class="alert alert-warning">
      <?php _e('You are using an <strong>outdated browser</strong>. Please <a href="http://outdatedbrowser.com/">upgrade your browser</a> by clicking <a href="http://outdatedbrowser.com/">here</a> to ensure you have the safest and best experience at GutsChurch.com. For any questions email <a href="mailto:admin@gutschurch.com">admin@gutschurch.com</a>', 'roots'); ?>
    </div>
  <![endif]-->

  <?php
    do_action('get_header');
    get_template_part('templates/header');
    echo hero();
    echo socialBar();
  ?>

  <div class="wrap container" role="document">
    <div id="content" class="content row">
      <main class="main" role="main">
        <?php include roots_template_path(); ?>
      </main><!-- /.main -->
      <?php if (roots_display_sidebar()) : ?>
        <aside class="sidebar" role="complementary">
          <?php include roots_sidebar_path(); ?>
        </aside><!-- /.sidebar -->
      <?php endif; ?>
    </div><!-- /.content -->
  </div><!-- /.wrap -->

  <?php get_template_part('templates/footer'); ?>

  <?php wp_footer(); ?>

</body>
</html>
