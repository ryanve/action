<?php 
namespace theme;

?>

                        <header class="entry-header">
                            <h1 class="entry-title">
                                <a itemprop="url" rel="bookmark" href="<?php the_permalink(); ?>">
                                    <span itemprop="headline name"><?php the_title(); ?></span>
                                </a>
                            </h1>
                            <dl class="byline meta-list"><?php call_user_func(function () {

                                ?><dt><?php _e('By'); ?></dt><dd class="vcard" itemprop="author"><?php 
                                    the_author_posts_link(); 
                                ?></dd><?php 

                                foreach ( array(
                                    'Published' => array( 
                                        'fn' => 'get_the_date'
                                      , 'class' => 'published'
                                      , 'rel' => 'index' 
                                    )
                                  , 'Modified' => array(
                                        'fn' => 'get_the_modified_date'
                                      , 'class' => 'updated'
                                      , 'rel' => null
                                    )
                                ) as $k => $v ) {
                                    \extract($v);
                                    $date = call_user_func( $fn ); # Uses: Settings > General > Date Format
                                    $ymd = call_user_func( $fn, 'Y-m-d' );
                                    $idx = get_year_link($y);
                                    $rel and $rel = ' rel="index"';
                                    $date = "<a$rel href='$idx'>$date</a>";
                                    $tag = "<time itemprop='date$k' class='$class' datetime='$ymd'>$date</time>";
                                    $tag = apply_filters( '@' . \strtolower($k) . '_tag', $tag, $date );
                                    echo "<dt>$k</dt><dd>$tag</dd>";
                                }
                            }); ?></dl>
                        </header>

