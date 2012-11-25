<?php 
namespace theme;

# this gets inserted into the site header via the '@header' action 
# hook in functions.php - I put in into a separate file so that it 
# would be easy to override in components in a child theme and so 
# that it'd be easier to control the sequence of header content b/c
# everything is added via the '@header' action located in header.php
?>

        <hgroup id="branding">
            <h1 class="site-title"><a accesskey="1" rel="home" title="<?php _e('Home'); ?>" href="<?php echo home_url(); ?>"><span><?php bloginfo('name'); ?></span></a></h1>
            <h2 class="site-description"><span><?php bloginfo('description'); ?></span></h2>
        </hgroup><!-- #branding -->
