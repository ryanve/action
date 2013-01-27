<?php 
namespace theme;

?>

                        <header class="entry-header">                        
                            <h1 class="entry-title">
                                <a itemprop="url" rel="bookmark" href="<?php the_permalink(); ?>">
                                    <span itemprop="headline name"><?php the_title(); ?></span>
                                </a>
                            </h1>
                            <dl class="byline"><?php \call_user_func(function () {

                                ?><dt><?php _e('By'); ?></dt><dd class="vcard" itemprop="author"><?php 
                                    the_author_posts_link(); 
                                ?></dd><?php 
                            
                                $time = array(
                                    'Published' => array( 'fn' => 'get_the_time', 'class' => 'published', 'rel' => 'index' )
                                  , 'Modified' => array( 'fn' => 'get_the_modified_time', 'class' => 'updated' )
                                );

                                foreach ( $time as $k => $v ) {
                                    $ymd = \call_user_func( $v['fn'], 'Y-m-d' );
                                    $nums = \explode( '-', $ymd );
                                    $urls = array('year', 'month', 'day');
                                    $j = \count($nums);
                                    while ( $j-- ) {
                                        $url = \call_user_func_array( 'get_' . $urls[$j] . '_link', \array_slice($nums, 0, $j + 1) );
                                        $urls[$j] = '<a class="' . $urls[$j] . '-link" href="' . $url . '">' . $nums[$j] . '</a>';
                                    }
                                    $urls = \implode('<span>-</span>', $urls);
                                    #$y   = $parts['year'];
                                    $lc = \strtolower($k);
                                    $class = $v['class'];
                                    $tag = "<time itemprop='date$k' class='$class' datetime='$ymd'>$urls</time>";
                                    $tag = apply_filters( '@' . $lc . '_tag', $tag, $ymd );
                                    #$archive = get_year_link($y);
                                    $rel = $v['rel'] ? " rel='$rel'" : '';
                                    echo "<dt>$k</dt><dd>$tag</dd>";
                                }
                            }); ?></dl>
                        </header>

