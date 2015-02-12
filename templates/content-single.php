<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <header>
      <?php if( get_post_type() == "post") get_template_part('templates/entry-meta');//if it's a post( and not a Page or event ) ?>
    </header>
    <div class="entry-content">
      <?php the_content(); ?>
      <div class="social-share">
      	<div class="addthis_native_toolbox"></div>
      	<script> gc.globalScripts.push("//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5149d8572d6ab06e");  </script>
      </div>
    </div>
    <footer>
      <?php wp_link_pages(array('before' => '<nav class="page-nav"><p>' . __('Pages:', 'roots'), 'after' => '</p></nav>')); ?>
    </footer>
    <?php comments_template('/templates/comments.php'); ?>
  </article>
<?php endwhile; ?>
