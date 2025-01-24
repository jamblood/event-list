<?php
/*
Plugin Name: Future Events
Description: Manage event registrations with accept/decline functionality
Version: 1.2.0
*/

namespace FutureEvents;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once __DIR__ . '/includes/class-plugin-core.php';
require_once __DIR__ . '/includes/class-shortcode-handler.php';
require_once __DIR__ . '/includes/class-ajax-handler.php';
require_once __DIR__ . '/includes/class-registration-service.php';

// Initialize plugin
new PluginCore();