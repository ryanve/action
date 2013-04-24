<?php 
namespace theme;
# this gets inserted into the site header via the '@header' action. I put
# it into a separate file so that it would be easy to override just this
# component in a child theme and to facilitate sequence via '@header'
?>

        <hgroup class="site-branding" itemprop="provider publisher" itemscope itemtype="http://schema.org/Organization">
            <h1 class="site-title">
                <a itemprop="url" rel="home" href="<?php echo home_url(); ?>">
                    <span itemprop="name"><?php bloginfo('name'); ?></span>
                </a>
            </h1>
            <h2 class="site-description">
                <span itemprop="description"><?php bloginfo('description'); ?></span>
            </h2>
        </hgroup>

