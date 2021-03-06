<?php

class Arconix_FAQ {

    /**
     * Constructor
     *
     * @since 1.0
     * @version 1.4.0
     */
    function __construct() {
        
    }

    /**
     * Get our FAQ data
     *
     * @param  array   $args
     * @param  boolean $echo Echo or Return the data
     * @return mixed   FAQ information for display
     * @since 1.2.0
     * @version 1.4.2
     */
    function loop($args, $echo = false) {
        $defaults = array(
            'order' => 'ASC',
            'orderby' => 'title',
            'posts_per_page' => -1,
            'nopaging' => true,
            'group' => '',
        );
        # Merge incoming args with the function defaults
        $args = wp_parse_args($args, $defaults);
        # Container
        $return = '';
        # Get the taxonomy terms assigned to all FAQs
        $terms = get_terms('group');
        # If there are any terms being used, loop through each one to output the relevant FAQ's, else just output all FAQs
        if (!empty($terms)) {
            foreach ($terms as $term) {
                # If a user sets a specific group in the params, that's the only one we care about
                $group = $args['group'];
                if (isset($group) and $group != '' and $term->slug != $group)
                    continue;
                # Set up our standard query args.
                $query_args = array(
                    'post_type' => 'faq',
                    'order' => $args['order'],
                    'orderby' => $args['orderby'],
                    'posts_per_page' => $args['posts_per_page'],
                    'nopaging' => $args['nopaging'],
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'group',
                            'field' => 'slug',
                            'terms' => array($term->slug),
                            'operator' => 'IN'
                        )
                    )
                );
                # New query just for the tax term we're looping through
                $q = new WP_Query($query_args);
                if ($q->have_posts()) {
                    $return .= '<h3 id="faq-term-' . $term->slug . '">' . $term->name . '</h3>';
                    # If the term has a description, show it
                    if ($term->description)
                        $return .= '<p class="arconix-faq-term-description">' . $term->description . '</p>';
                    # Loop through the rest of the posts for the term
                    $return .= '<ol id="faq-' . $term->slug . '" class="panel-group">';
                    while ($q->have_posts()) : $q->the_post();
                        # Grab our metadata
                        $rtt = get_post_meta(get_the_id(), '_acf_rtt', true);
                        $lo = get_post_meta(get_the_id(), '_acf_open', true);
                        $open = empty($lo) ? '' : ' in';
                        $link = 'faq-' . sanitize_title(get_the_title());
                        $return .= '<li class="panel" id="' . $link . '">';
                        $return .= '<div class="panel-heading"><a href="#faq-' . get_the_ID() . '" data-toggle="collapse" data-parent="#faq-' . $term->slug . '">' . get_the_title() . '</a></div>';
                        $return .= '<div id="faq-' . get_the_ID() . '" class="panel-body collapse ' . $open . '">';
                        $return .= '<div class="panel-inner">';
                        $return .= apply_filters('the_content', get_the_content());
                        if ($rtt) {
                            $rtt_text = __('Return to Top', 'acf');
                            $rtt_text = apply_filters('arconix_faq_return_to_top_text', $rtt_text);
                            $return .= '<a href="#' . $link . '">' . $rtt_text . '</a>';
                        }
                        $return .= '</div>';
                        $return .= '</div>';
                        $return .= '</li>';
                    endwhile;
                    $return .= '</ol>';
                } # end have_posts()
                wp_reset_postdata();
            } # end foreach
        } # End if( $terms )
        else {
            # Set up our standard query args.
            $q = new WP_Query(array(
                'post_type' => 'faq',
                'order' => $args['order'],
                'orderby' => $args['orderby'],
                'posts_per_page' => $args['posts_per_page'],
                'nopaging' => $args['nopaging'],
            ));
            if ($q->have_posts()) {
                $return .= '<ol id="faq" class="panel-group">';
                while ($q->have_posts()) : $q->the_post();
                    # Grab our metadata
                    $rtt = get_post_meta(get_the_id(), '_acf_rtt', true);
                    $lo = get_post_meta(get_the_id(), '_acf_open', true);
                    $open = empty($lo) ? '' : ' in';
                    $link = 'faq-' . sanitize_title(get_the_title());
                    $return .= '<li class="panel" id="' . $link . '">';
                    $return .= '<div class="panel-heading"><a href="#faq-' . get_the_ID() . '" data-toggle="collapse" data-parent="#faq">' . get_the_title() . '</a></div>';
                    $return .= '<div id="faq-' . get_the_ID() . '" class="panel-body collapse ' . $open . '">';
                    $return .= '<div class="panel-inner">';
                    $return .= apply_filters('the_content', get_the_content());
                    if ($rtt) {
                        $rtt_text = __('Return to Top', 'acf');
                        $rtt_text = apply_filters('arconix_faq_return_to_top_text', $rtt_text);
                        $return .= '<a href="#' . $link . '">' . $rtt_text . '</a>';
                    }
                    $return .= '</div>';
                    $return .= '</div>';
                    $return .= '</li>';
                endwhile;
                $return .= '</ol>';
            } # end have_posts()
            wp_reset_postdata();
        }

        # Allow complete override of the FAQ content
        $return = apply_filters('arconix_faq_return', $return);

        if ($echo === true)
            echo $return;
        else
            return $return;
    }

}
