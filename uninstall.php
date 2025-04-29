<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$keys = [
    'date_icon_fixer_enabled',
    'date_icon_fixer_format',
    'date_icon_fixer_enable_time',
    'date_icon_fixer_disable_weekend',
    'date_icon_fixer_min_date',
    'date_icon_fixer_max_date',
    'date_icon_fixer_locale'
];

foreach ($keys as $key) {
    delete_option($key);
}
