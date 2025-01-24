<?php
namespace FutureEvents;

class RegistrationService {
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'future_event_registrations';
    }

    public function handle_registration($event_id, $status, $custom = '') {
        global $wpdb;
        
        // Validate user context
        $user_id = get_current_user_id();
        if (!$user_id) {
            error_log('Registration attempt without valid user ID');
            return false;
        }

        // Validate event existence
        if (!get_post($event_id)) {
            error_log("Invalid event ID: $event_id");
            return false;
        }

        // Check existing registration
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT id, status FROM {$this->table_name}
            WHERE event_id = %d AND user_id = %d",
            $event_id, $user_id
        ));

        // Update or create logic
        if ($existing) {
            return $wpdb->update(
                $this->table_name,
                [
                    'status' => $status,
                    'custom' => $custom,
                    'registration_date' => current_time('mysql')
                ],
                ['id' => $existing->id],
                ['%s', '%s', '%s'],
                ['%d']
            );
        }

        // Create new registration
        return $wpdb->insert($this->table_name, [
            'event_id' => $event_id,
            'user_id' => $user_id,
            'status' => $status,
            'custom' => $custom,
            'registration_date' => current_time('mysql')
        ]);
    }

    public function get_registration_status($event_id) {
        global $wpdb;
        
        $user_id = get_current_user_id();
        if (!$user_id) return null;

        return $wpdb->get_var($wpdb->prepare(
            "SELECT status FROM {$this->table_name}
            WHERE event_id = %d AND user_id = %d",
            $event_id, $user_id
        ));
    }
}