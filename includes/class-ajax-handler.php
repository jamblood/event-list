<?php
namespace FutureEvents;

class AjaxHandler {
    public function __construct() {
        add_action('wp_ajax_handle_event_registration', [$this, 'handle_registration']);
        add_action('wp_ajax_nopriv_handle_event_registration', [$this, 'handle_registration']);
    }

    public function handle_registration() {
        try {
            // Verify nonce first
            check_ajax_referer('future_events_nonce', 'security');

            // Validate required parameters
            if (!isset($_POST['event_id'], $_POST['status'])) {
                throw new \Exception(__('Missing required parameters', 'future-events'));
            }

            // Validate decline reason requirement
            $status = sanitize_text_field($_POST['status']);
            $custom_reason = isset($_POST['custom_reason']) 
                ? sanitize_textarea_field($_POST['custom_reason'])
                : '';

            if ($status === 'decline' && empty(trim($custom_reason))) {
                throw new \Exception(__('A reason is required for declining', 'future-events'));
            }

            // Process registration
            $registration_service = new RegistrationService();
            $result = $registration_service->handle_registration(
                absint($_POST['event_id']),
                $status,
                $custom_reason
            );

            if (!$result) {
                throw new \Exception(__('Failed to update registration', 'future-events'));
            }

            // Return success with clean data
            wp_send_json_success([
                'message' => __('Registration updated successfully', 'future-events'),
                'status' => $status,
                'event_id' => absint($_POST['event_id'])
            ]);

        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 400);
        }
    }
}