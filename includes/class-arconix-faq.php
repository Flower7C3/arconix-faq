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
        $terms = get_terms('group', array(
            'orderby' => 'slug',
            'order' => 'ASC',
        ));
        # If there are any terms being used, loop through each one to output the relevant FAQ's, else just output all FAQs
        if (!empty($terms)) {
            foreach ($terms as $term) {
                # If a user sets a specific group in the params, that's the only one we care about
                $group = $args['group'];
                if (isset($group) and $group != '' and $term->slug != $group)
                    continue;
                # Set up our standard query args.
                $q = new WP_Query(array(
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
                ));
                if ($q->have_posts()) {
                    $parentID = 'faq-' . $term->slug;
                    $return .= '<h3 id="' . $parentID . '-header">' . $term->name . '</h3>';
                    # If the term has a description, show it
                    if ($term->description)
                        $return .= '<p class="arconix-faq-term-description">' . $term->description . '</p>';
                    $return .= self::theLoop($q, $parentID);
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
                $return = self::theLoop($q);
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

    private function theLoop($q = null, $parentID = 'faq') {
        $return = '';
        $return .= '<ol id="' . $parentID . '" class="panel-group">';
        while ($q->have_posts()) {
            $q->the_post();
            # Grab our metadata
            $lo = get_post_meta(get_the_id(), '_acf_open', true);
            $open = empty($lo) ? '' : ' in';
            $itemID = $parentID . '-item-' . sanitize_title(get_the_title());
            $itemLink = $parentID . '-item-' . get_the_ID();
            # generate return
            $return .= '<li class="panel" id="' . $itemID . '">';
            $return .= '<div class="panel-heading"><a href="#' . $itemLink . '" data-toggle="collapse" data-parent="#' . $parentID . '">' . get_the_title() . '</a></div>';
            $return .= '<div id="' . $itemLink . '" class="panel-body collapse ' . $open . '">';
            $return .= '<div class="panel-inner">';
            $return .= apply_filters('the_content', get_the_content());
            if (get_post_meta(get_the_id(), '_acf_rtt', true)) {
                $rtt_text = __('Return to Top', 'acf');
                $rtt_text = apply_filters('arconix_faq_return_to_top_text', $rtt_text);
                $return .= '<a href="#' . $itemID . '">' . $rtt_text . '</a>';
            }
            $return .= '</div>';
            $return .= '</div>';
            $return .= '</li>';
        }
        $return .= '</ol>';
        return $return;
    }

}
