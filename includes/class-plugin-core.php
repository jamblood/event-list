<?php
namespace FutureEvents;

class PluginCore {
    public function __construct() {
        $this->define_constants();
        $this->init_hooks();
        $this->init_components();
    }

    private function define_constants() {
        define('FUTURE_EVENTS_VERSION', '1.2.0');
        define('FUTURE_EVENTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
        define('FUTURE_EVENTS_PLUGIN_URL', plugin_dir_url(__FILE__));
        define('FUTURE_EVENTS_TABLE_VERSION', '1.1');
    }

    private function init_hooks() {
        // Activation/Deactivation
        register_activation_hook(FUTURE_EVENTS_PLUGIN_DIR . 'future-events.php', [$this, 'activate_plugin']);
        register_deactivation_hook(FUTURE_EVENTS_PLUGIN_DIR . 'future-events.php', [$this, 'deactivate_plugin']);

        // Localization
        add_action('plugins_loaded', [$this, 'load_textdomain']);

        // Database upgrades
        add_action('init', [$this, 'maybe_create_tables']);
    }

    private function init_components() {
        // Core components
        new ShortcodeHandler(new RegistrationService());
        new AjaxHandler();
        new RegistrationService(); // Shared instance

        // Admin components (if needed)
        if(is_admin()) {
            // Potential future admin classes
        }
    }

    public function load_textdomain() {
        load_plugin_textdomain(
            'future-events',
            false,
            dirname(plugin_basename(FUTURE_EVENTS_PLUGIN_DIR . 'future-events.php')) . '/languages/'
        );
    }

    public function activate_plugin() {
        $this->maybe_create_tables(true);
        set_transient('future_events_activated', true, 5);
    }

    public function deactivate_plugin() {
        // Cleanup tasks if needed
    }

    public function maybe_create_tables($force = false) {
        global $wpdb;
        
        if($force || get_option('future_events_db_version') !== FUTURE_EVENTS_TABLE_VERSION) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            
            $charset_collate = $wpdb->get_charset_collate();
            $table_name = $wpdb->prefix . 'future_event_registrations';

            $sql = "CREATE TABLE $table_name (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                event_id BIGINT UNSIGNED NOT NULL,
                user_id BIGINT UNSIGNED NOT NULL,
                status VARCHAR(20) NOT NULL,
                custom LONGTEXT,
                registration_date DATETIME NOT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY user_event (user_id, event_id)
            ) $charset_collate;";

            dbDelta($sql);
            update_option('future_events_db_version', FUTURE_EVENTS_TABLE_VERSION);
        }
    }
}