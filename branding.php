<?php 
namespace theme;

# this gets inserted into the site header via the '@header' action 
# hook in functions.php - I put in into a separate file so that it 
# would be easy to override in components in a child theme and so 
# that it'd be easier to control the sequence of header content b/c
# everything is added via the '@header' action located in header.php
?>

        <hgroup class="site-branding" itemprop="provider publisher" itemscope itemtype="http://schema.org/Organization">
            <h1 class="h site-title">
                <a accesskey="1" itemprop="url" rel="home" href="<?php echo home_url(); ?>">
                    <span itemprop="name"><?php bloginfo('name'); ?></span>
                </a>
            </h1>
            <h2 class="h site-description">
                <span itemprop="description"><?php bloginfo('description'); ?></span>
            </h2>
        </hgroup>

