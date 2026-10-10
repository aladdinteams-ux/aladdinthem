<?php
defined( 'ABSPATH' ) || exit;
function frpme_category_label($post_id=0) { $cats = get_the_category($post_id); return $cats ? $cats[0]->name : ''; }
function frpme_cards( $query ) {
    while ( $query->have_posts() ) { $query->the_post();
        $cats = get_the_category(); $slugs = wp_list_pluck( $cats, 'slug' ); $cover='basics';foreach($slugs as $slug){if(in_array($slug,array('basics','fibers','resins','execution','waterproofing','industrial'),true)){$cover=$slug;break;}}$icons=array('basics'=>'layers','fibers'=>'fiber','resins'=>'water','execution'=>'roof','waterproofing'=>'pool','industrial'=>'tank'); ?>
        <article class="blog-card" data-category="<?php echo esc_attr( implode( ',', $slugs ) ); ?>">
        <a class="blog-cover cover-<?php echo esc_attr($cover); ?>" href="<?php echo esc_url(get_permalink()); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
        <?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'frpm-card', array( 'loading'=>'lazy', 'decoding'=>'async' ) ); } else { ?><span>SG / FIELD NOTES</span><svg class="icon" aria-hidden="true"><use href="#i-<?php echo esc_attr($icons[$cover]); ?>"></use></svg><div class="cover-lines"></div><?php } ?></a>
        <div class="blog-card-body"><div class="blog-card-meta"><span><?php echo esc_html( frpme_category_label() ); ?></span><button class="blog-save" type="button" aria-pressed="false" aria-label="<?php echo esc_attr( 'نشان کردن ' . get_the_title() ); ?>"><svg class="icon" aria-hidden="true"><use href="#i-identity"></use></svg></button></div>
        <h3><a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_the_title()); ?></a></h3><p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p><details class="blog-reading"><summary>مطالعه راهنما <svg class="icon" aria-hidden="true"><use href="#i-arrow"></use></svg></summary><div><p><?php echo esc_html(wp_trim_words(get_the_excerpt(),45)); ?></p><a href="<?php echo esc_url(get_permalink()); ?>">مطالعه مقاله کامل <svg class="icon" aria-hidden="true"><use href="#i-arrow"></use></svg></a></div></details></div></article>
        <?php
    }
    wp_reset_postdata();
}
function frpme_pagination( $query ) {
    $links = paginate_links( array( 'total'=>$query->max_num_pages, 'current'=>max(1,(int)get_query_var('paged'),(int)get_query_var('page')), 'type'=>'list' ) );
    if ( $links ) { echo '<nav class="frpme-pagination" aria-label="' . esc_attr__( 'Pagination', 'frpmesh-editor' ) . '">' . wp_kses_post( $links ) . '</nav>'; }
}
function frpme_article_id(){return is_singular('post') ? get_queried_object_id() : get_the_ID();}
function frpme_related() {
    $post_id=frpme_article_id();
    $query = new WP_Query( array('post_type'=>'post','posts_per_page'=>3,'post__not_in'=>array($post_id),'category__in'=>wp_get_post_categories($post_id),'ignore_sticky_posts'=>true,'no_found_rows'=>true) );
    while ( $query->have_posts() ) { $query->the_post(); ?><a href="<?php echo esc_url(get_permalink()); ?>"><span><?php echo esc_html( frpme_category_label() ); ?></span><h3><?php echo esc_html(get_the_title()); ?></h3><p><?php echo esc_html( wp_trim_words(get_the_excerpt(),20) ); ?></p></a><?php }
    wp_reset_postdata();
}
function frpme_home_posts() {
    $query = new WP_Query( array('post_type'=>'post','posts_per_page'=>6,'ignore_sticky_posts'=>true,'no_found_rows'=>true) );
    ob_start();
    while ( $query->have_posts() ) { $query->the_post(); ?>
    <article class="home-article-card"><a class="home-article-link" href="<?php echo esc_url(get_permalink()); ?>"><div class="home-article-cover" aria-hidden="true"><span>SG / KNOWLEDGE</span><svg class="icon" aria-hidden="true"><use href="#i-layers"></use></svg><small><?php echo esc_html(number_format_i18n($query->current_post+1)); ?></small></div><div class="home-article-content"><span><?php echo esc_html(frpme_category_label()); ?></span><h3><?php echo esc_html(get_the_title()); ?></h3><p><?php echo esc_html(wp_trim_words(get_the_excerpt(),25)); ?></p><span class="home-article-read">مطالعه مقاله <svg class="icon" aria-hidden="true"><use href="#i-arrow"></use></svg></span></div></a></article>
    <?php }
    wp_reset_postdata(); return ob_get_clean();
}
function frpme_dynamic($kind,$component,$settings=array()) {
    if ('menu'===$kind) {
        $current=home_url('/'.(isset($GLOBALS['wp']->request)?ltrim($GLOBALS['wp']->request,'/'):''));$blog_context=is_home()||is_singular('post')||is_category()||is_tag()||is_author()||is_date();$locations=get_nav_menu_locations();$items=!empty($locations['primary']) ? wp_get_nav_menu_items($locations['primary']) : array();$out='';
        if ($items) {foreach($items as $item){$active=('custom'!==$item->type && (int)$item->object_id===get_queried_object_id())||(!is_search() && untrailingslashit($item->url)===untrailingslashit($current))||($blog_context && (int)$item->object_id===(int)get_option('page_for_posts') && (int)$item->object_id>0);$out.='<a href="'.esc_url($item->url).'"'.($active?' class="active" aria-current="page"':'').'>'.esc_html($item->title).'</a>';}}
        else {foreach(array('/'=>'خانه','/about/'=>'درباره ما','/services/'=>'خدمات','/gallery/'=>'نمونه‌کارها','/blog/'=>'وبلاگ','/contact/'=>'تماس') as $path=>$title){$url=frpme_link($path);$active=(!is_search() && untrailingslashit($url)===untrailingslashit($current))||('/blog/'===$path && $blog_context);$out.='<a href="'.esc_url($url).'"'.($active?' class="active" aria-current="page"':'').'>'.esc_html($title).'</a>';}}
        return $out;
    }
    if ('home-posts'===$kind) {return frpme_home_posts();}
    if ('blog-filters'===$kind) {
        $out='<button type="button" data-topic="all" aria-pressed="true">همه موضوعات</button>';
        foreach(get_categories(array('hide_empty'=>true)) as $term){$out.='<button type="button" data-topic="'.esc_attr($term->slug).'" aria-pressed="false">'.esc_html($term->name).'</button>';}
        return $out;
    }
    if ('blog-cards'===$kind) {
        $query= (is_home() || is_archive() || is_search()) ? $GLOBALS['wp_query'] : new WP_Query(array('post_type'=>'post','posts_per_page'=>isset($settings['frpme_post_count']) ? max(1,min(48,(int)$settings['frpme_post_count'])) : (int)get_option('posts_per_page'),'paged'=>max(1,(int)get_query_var('paged'),(int)get_query_var('page')),'ignore_sticky_posts'=>true));
        ob_start();frpme_cards($query);frpme_pagination($query);return ob_get_clean();
    }
    if ('post-title'===$kind) {return esc_html(get_the_title(frpme_article_id()));}
    if ('post-excerpt'===$kind) {return esc_html(get_the_excerpt(frpme_article_id()));}
    if ('post-category'===$kind) {return esc_html(frpme_category_label(frpme_article_id()));}
    if ('post-meta'===$kind) {
        $id=frpme_article_id();$author=(int)get_post_field('post_author',$id);return '<span><a rel="author" href="'.esc_url(get_author_posts_url($author)).'">'.esc_html(get_the_author_meta('display_name',$author)).'</a></span><time datetime="'.esc_attr(get_the_date(DATE_W3C,$id)).'">'.esc_html(get_the_date('',$id)).'</time><span id="article-read-time">زمان مطالعه</span>';
    }
    if ('post-content'===$kind) {
        if (!is_singular('post')) { return '<p>متن نوشته در صفحه واقعی مقاله نمایش داده می‌شود.</p>'; }
        global $post;$previous=$post;$current=get_post(frpme_article_id());if(!$current){return '';}$post=$current;setup_postdata($post);ob_start();try{if(has_post_thumbnail()){the_post_thumbnail('large',array('loading'=>'eager'));}the_content();wp_link_pages();if(comments_open() || get_comments_number()){comments_template();}return ob_get_clean();}finally{$post=$previous;if($post instanceof WP_Post){setup_postdata($post);}}
    }
    if ('related-posts'===$kind) {ob_start();frpme_related();return ob_get_clean();}
    return '';
}
