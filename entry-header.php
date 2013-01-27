<?php 
namespace theme;

?>

                        <header class="entry-header">                        
                            <h1 class="entry-title">
                                <a itemprop="url" rel="bookmark" href="<?php the_permalink(); ?>">
                                    <span itemprop="headline name"><?php the_title(); ?></span>
                                </a>
                            </h1>
                            <div class="byline"><?php \call_user_func(function () {

                                ?><address class="vcard" itemprop="author"><?php 
                                    the_author_posts_link(); 
                                ?></address><?php 
                            
                                $time = array(
                                    'Published' => array( 'fn' => 'get_the_time', 'class' => 'published', 'rel' => 'index' )
                                  , 'Modified' => array( 'fn' => 'get_the_modified_time', 'class' => 'updated' )
                                );

                                foreach ( $time as $k => $v ) {
                                    $ymd = \call_user_func( $v['fn'], 'Y-m-d' );
                                    #$d   = \call_user_func( \str_replace( '_time', '_date', $v['fn'] ) );
                                    $y   = \array_shift( \explode( '-', $ymd ) );
                                    $lc = \strtolower($k);
                                    $class = $v['class'];
                                    $tag = "<time itemprop='date$k' class='$class' datetime='$ymd' title='$k: $ymd'>$y</time>";
                                    $tag = apply_filters( '@' . $lc . '_tag', $tag, $ymd, $y );
                                    $archive = get_year_link($y);
                                    $rel = $v['rel'] ? " rel='$rel'" : '';
                                    echo "<span data-meta-label='$k' data-time-label='$k'><a$rel href='$archive'>$tag</a></span>";
                                }
                            }); ?></div>
                        </header>

