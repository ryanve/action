<?php 
namespace theme;

?>

                        <header class="entry-header">
                            <h1 class="entry-title">
                                <a rel="bookmark" href="<?php the_permalink(); ?>" title="<?php esc_attr_e('Permalink &raquo;'); ?>"><?php the_title(); ?></a>
                            </h1>
                            <div class="byline">
                                <address class="author vcard"><?php the_author_posts_link(); ?></address>
                                <time datetime="<?php the_time('Y-m-d'); ?>" title="<?php esc_attr_e('date published'); ?>"><?php the_date(); ?></time>
                            </div>
                        </header><!-- .entry-header -->

