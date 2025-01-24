<?php
namespace FutureEvents;

class ShortcodeHandler {
    public function __construct() {
        // Self-register on instantiation
        add_shortcode('future_events', [$this, 'render_shortcode']);
    }

    public function render_shortcode() {
        if (!class_exists('Tribe__Events__Pro__Main')) {
            return '<p>The Events Calendar Pro plugin is not active.</p>';
        }

        $events = $this->get_future_events();
        ob_start();
        
        if ($events->have_posts()) {
            echo '<ul class="future-events-list">';
            while ($events->have_posts()) {
                $events->the_post();
                $this->render_event_item(get_the_ID());
            }
            echo '</ul>';
        } else {
            echo '<p>No future events found.</p>';
        }

        wp_reset_postdata();
        return ob_get_clean();
    }

    private function get_future_events() {
        return new \WP_Query([
            'post_type' => \Tribe__Events__Main::POSTTYPE,
            'posts_per_page' => -1,
            'meta_query' => [[
                'key' => '_EventStartDate',
                'value' => date('Y-m-d H:i:s'),
                'compare' => '>=',
                'type' => 'DATETIME'
            ]],
            'orderby' => 'meta_value',
            'order' => 'ASC'
        ]);
    }

    private function render_event_item($event_id) {
        $status = $this->registration_service->get_user_status($event_id);
        $status_text = $this->registration_service->get_status_text($status);
        $status_class = $this->registration_service->get_status_class($status);
        
        echo sprintf(
            '<li data-event-id="%s">%s%s</li>',
            esc_attr($event_id),
            $this->get_event_link($event_id),
            $this->get_status_controls($event_id, $status, $status_text, $status_class)
        );
    }

    private function get_event_link($event_id) {
        return sprintf('<a href="%s">%s - %s</a>',
            esc_url(get_permalink($event_id)),
            esc_html(get_the_title()),
            esc_html(tribe_get_start_date($event_id, false, 'Y-m-d H:i:s'))
        );
    }

    private function get_status_controls($event_id, $status, $text, $class) {
        if ($status) {
            return sprintf('<span class="status-text %s">%s</span>', $class, $text);
        }
        
        return sprintf(
            '<button class="accept-event" data-event-id="%s">Accept</button>
             <button class="decline-event" data-event-id="%s">Decline</button>',
            esc_attr($event_id),
            esc_attr($event_id)
        );
    }
}