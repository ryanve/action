<?php 
namespace theme;
# This gets inserted into the site header via the '@header' action. I put
# it into a separate file so that it would be easy to override just this
# component in a child theme and to facilitate sequence via '@header'
#
# Consider
# - .site-title is common in WP but .site-name would be more systematic.
# - <hgroup> is obsolete | webmonkey.com/?p=61540 | html5doctor.com/?p=3208
?>

        <div class="site-branding hgroup" itemprop="provider publisher" itemscope itemtype="http://schema.org/Organization">
            <h1 class="site-name site-title">
                <a itemprop="url name" rel="home" href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a>
            </h1>
            <?php call_user_func(function() {
                $desc = apply_filters('@tagline', get_bloginfo('description'));
                if ( ! $desc) return;
                $type = 80 > \mb_strlen(\strip_tags($desc)) ? 'tagline subline' : 'subline';
                echo "<div class='site-description $type' itemprop='description'>$desc</div>\n";
            }); ?>
        </div>

