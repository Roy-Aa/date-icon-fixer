<?php

/**
 * Plugin Name: Date Icon Fixer
 * Plugin URI: https://royaardenburg.nl
 * Description: Replaces all input[type="date"] fields on the frontend with Flatpickr to remove the annoying Firefox calendar icon.
 * Version: 1.0
 * Author: Roy Aardenburg
 * Author URI: https://royaardenburg.nl
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: date-icon-fixer
 * Domain Path: /languages
 */

// Flatpickr is used under the MIT License
// https://github.com/flatpickr/flatpickr
// Load plugin styles and scripts

function dif_enqueue_assets()
{
    $plugin_url = plugin_dir_url(__FILE__);

    if (get_option('date_icon_fixer_enabled')) {
        wp_enqueue_style('flatpickr-css', $plugin_url . 'assets/css/flatpickr.min.css');

        $theme = get_option('date_icon_fixer_theme');
        if (!empty($theme)) {
            wp_enqueue_style('flatpickr-theme', $plugin_url . 'assets/css/themes/' . esc_attr($theme) . '.css');
        }

        wp_enqueue_script('flatpickr-js', $plugin_url . 'assets/js/flatpickr.min.js', [], null, true);
        wp_enqueue_script('init-flatpickr', $plugin_url . 'assets/js/init-flatpickr.js', ['flatpickr-js'], null, true);

        $settings = [
            'dateFormat'     => get_option('date_icon_fixer_format', 'd-m-Y'),
            'disableWeekend' => (bool) get_option('date_icon_fixer_disable_weekend'),
            'placeholder' => get_option('date_icon_fixer_placeholder', 'DD-MM-YYYY'),
            'minDate'        => get_option('date_icon_fixer_min_date'),
            'maxDate'        => get_option('date_icon_fixer_max_date'),
            'weeksOnly'      => (bool) get_option('date_icon_fixer_weeks_only'),
            'disableDates'   => get_option('date_icon_fixer_disable_dates'),
            'disableRanges'  => get_option('date_icon_fixer_disable_ranges'),
            'enableOnly'     => get_option('date_icon_fixer_enable_only'),
            'multipleDates'  => (bool) get_option('date_icon_fixer_multiple_dates'),
            'weekNumbers'    => (bool) get_option('date_icon_fixer_enable_week_dates'),
        ];

        wp_localize_script('init-flatpickr', 'DateIconFixerSettings', $settings);
    }
}
add_action('wp_enqueue_scripts', 'dif_enqueue_assets');

function dif_enqueue_admin_assets()
{
    $plugin_url = plugin_dir_url(__FILE__);

    wp_enqueue_style('date-icon-fixer-admin-style', $plugin_url . 'assets/css/date-icon-fixer.css');
}
add_action('admin_enqueue_scripts', 'dif_enqueue_admin_assets');

add_action('admin_init', function () {
    register_setting('date_icon_fixer_settings', 'date_icon_fixer_enabled');
    register_setting('date_icon_fixer_settings', 'date_icon_fixer_format');
    register_setting('date_icon_fixer_settings', 'date_icon_fixer_placeholder');
    register_setting('date_icon_fixer_settings', 'date_icon_fixer_disable_weekend');
    register_setting('date_icon_fixer_settings', 'date_icon_fixer_min_date');
    register_setting('date_icon_fixer_settings', 'date_icon_fixer_max_date');
    register_setting('date_icon_fixer_settings', 'date_icon_fixer_theme');
    register_setting('date_icon_fixer_settings', 'date_icon_fixer_weeks_only');
    register_setting('date_icon_fixer_settings', 'date_icon_fixer_disable_dates');
    register_setting('date_icon_fixer_settings', 'date_icon_fixer_disable_ranges');
    register_setting('date_icon_fixer_settings', 'date_icon_fixer_enable_only');
    register_setting('date_icon_fixer_settings', 'date_icon_fixer_multiple_dates');
    register_setting('date_icon_fixer_settings', 'date_icon_fixer_enable_week_dates');
});

add_action('admin_menu', function () {
    add_menu_page(
        'Date Icon Fixer',
        'Date Icon Fixer',
        'manage_options',
        'date-icon-fixer',
        'date_icon_fixer_settings_page',
        'dashicons-calendar-alt',
        1000
    );
});

add_action('admin_bar_menu', function ($wp_admin_bar) {
    if (!current_user_can('manage_options')) return;

    $enabled = get_option('date_icon_fixer_enabled');

    $icon_svg = $enabled
        ? '<svg xmlns="http://www.w3.org/2000/svg" fill="#00D992" width="20" height="20" viewBox="0 0 24 24"><path d="M20.285 2l-11.285 11.281-5.285-5.281-3.715 3.715 9 9 15-15z"/></svg>'
        : '<svg xmlns="http://www.w3.org/2000/svg" fill="#ff4d4d" width="20" height="20" viewBox="0 0 24 24"><path d="M12 10.586l4.95-4.95 1.414 1.414L13.414 12l4.95 4.95-1.414 1.414L12 13.414l-4.95 4.95-1.414-1.414L10.586 12 5.636 7.05l1.414-1.414z"/></svg>';

    $wp_admin_bar->add_node([
        'id'    => 'flatpickr-status',
        'title' => 'Flatpickr:' . $icon_svg,
        'href'  => admin_url('admin.php?page=date-icon-fixer'),
        'meta'  => [
            'class' => 'flatpickr-icon-only',
            'title' => $enabled ? 'Flatpickr is actief' : 'Flatpickr is uitgeschakeld',
        ],
    ]);
}, 100);

add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=date-icon-fixer') . '">Settings</a>';
    $links[] = $settings_link;
    return $links;
});

add_action('admin_init', function () {
    if (
        isset($_GET['reset-date-icon-fixer']) &&
        $_GET['reset-date-icon-fixer'] === '1' &&
        current_user_can('manage_options')
    ) {
        delete_option('date_icon_fixer_enabled');
        delete_option('date_icon_fixer_format');
        delete_option('date_icon_fixer_placeholder');
        delete_option('date_icon_fixer_disable_weekend');
        delete_option('date_icon_fixer_min_date');
        delete_option('date_icon_fixer_max_date');
        delete_option('date_icon_fixer_theme');
        delete_option('date_icon_fixer_weeks_only');
        delete_option('date_icon_fixer_disable_dates');
        delete_option('date_icon_fixer_disable_ranges');
        delete_option('date_icon_fixer_enable_only');
        delete_option('date_icon_fixer_multiple_dates');
        delete_option('date_icon_fixer_enable_week_dates');

        wp_redirect(admin_url('admin.php?page=date-icon-fixer&reset-success=1'));
        exit;
    }
});

function date_icon_fixer_settings_page()
{ ?>

    <div class="dif-wrap">
        <div class="dif-head">
            <div class="dif-text-svg">
                <svg fill="#00D992" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M24 2v22h-24v-22h3v1c0 1.103.897 2 2 2s2-.897 2-2v-1h10v1c0 1.103.897 2 2 2s2-.897 2-2v-1h3zm-2 6h-20v14h20v-14zm-2-7c0-.552-.447-1-1-1s-1 .448-1 1v2c0 .552.447 1 1 1s1-.448 1-1v-2zm-14 2c0 .552-.447 1-1 1s-1-.448-1-1v-2c0-.552.447-1 1-1s1 .448 1 1v2zm1 11.729l.855-.791c1 .484 1.635.852 2.76 1.654 2.113-2.399 3.511-3.616 6.106-5.231l.279.64c-2.141 1.869-3.709 3.949-5.967 7.999-1.393-1.64-2.322-2.686-4.033-4.271z" />
                </svg>
                <h1>Date Icon Fixer <span class="dif-version">v1.0</span></h1>
            </div>
            <p>A plugin to replace the annoying Firefox calendar icon with a Flatpickr date picker.</p>
        </div>

        <div class="dif-text-svg">
            <svg fill="#00D992" width="24" height="24" clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="m12.002 2.005c5.518 0 9.998 4.48 9.998 9.997 0 5.518-4.48 9.998-9.998 9.998-5.517 0-9.997-4.48-9.997-9.998 0-5.517 4.48-9.997 9.997-9.997zm0 1.5c-4.69 0-8.497 3.807-8.497 8.497s3.807 8.498 8.497 8.498 8.498-3.808 8.498-8.498-3.808-8.497-8.498-8.497zm0 6.5c-.414 0-.75.336-.75.75v5.5c0 .414.336.75.75.75s.75-.336.75-.75v-5.5c0-.414-.336-.75-.75-.75zm-.002-3c.552 0 1 .448 1 1s-.448 1-1 1-1-.448-1-1 .448-1 1-1z" fill-rule="nonzero" />
            </svg>
            <h2>What is Flatpickr?</h2>
        </div>
        <p>Flatpickr is a lightweight and powerful datetime picker.</p>
        <p>Lean, UX-driven, and extensible, yet it doesn’t depend on any libraries. Minimal UI but many themes.</p>

        <div class="dif-text-svg" style="margin-top: 20px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="#00D992" width="24" height="24" viewBox="0 0 24 24">
                <path d="M24 14.187v-4.374c-2.148-.766-2.726-.802-3.027-1.529-.303-.729.083-1.169 1.059-3.223l-3.093-3.093c-2.026.963-2.488 1.364-3.224 1.059-.727-.302-.768-.889-1.527-3.027h-4.375c-.764 2.144-.8 2.725-1.529 3.027-.752.313-1.203-.1-3.223-1.059l-3.093 3.093c.977 2.055 1.362 2.493 1.059 3.224-.302.727-.881.764-3.027 1.528v4.375c2.139.76 2.725.8 3.027 1.528.304.734-.081 1.167-1.059 3.223l3.093 3.093c1.999-.95 2.47-1.373 3.223-1.059.728.302.764.88 1.529 3.027h4.374c.758-2.131.799-2.723 1.537-3.031.745-.308 1.186.099 3.215 1.062l3.093-3.093c-.975-2.05-1.362-2.492-1.059-3.223.3-.726.88-.763 3.027-1.528zm-4.875.764c-.577 1.394-.068 2.458.488 3.578l-1.084 1.084c-1.093-.543-2.161-1.076-3.573-.49-1.396.581-1.79 1.693-2.188 2.877h-1.534c-.398-1.185-.791-2.297-2.183-2.875-1.419-.588-2.507-.045-3.579.488l-1.083-1.084c.557-1.118 1.066-2.18.487-3.58-.579-1.391-1.691-1.784-2.876-2.182v-1.533c1.185-.398 2.297-.791 2.875-2.184.578-1.394.068-2.459-.488-3.579l1.084-1.084c1.082.538 2.162 1.077 3.58.488 1.392-.577 1.785-1.69 2.183-2.875h1.534c.398 1.185.792 2.297 2.184 2.875 1.419.588 2.506.045 3.579-.488l1.084 1.084c-.556 1.121-1.065 2.187-.488 3.58.577 1.391 1.689 1.784 2.875 2.183v1.534c-1.188.398-2.302.791-2.877 2.183zm-7.125-5.951c1.654 0 3 1.346 3 3s-1.346 3-3 3-3-1.346-3-3 1.346-3 3-3zm0-2c-2.762 0-5 2.238-5 5s2.238 5 5 5 5-2.238 5-5-2.238-5-5-5z" />
            </svg>
            <h2>Date Icon Fixer Settings</h2>
        </div>
        <p>Replace the annoying Firefox calendar icon with a Flatpickr date picker.</p>

        <form method="post" action="options.php">
            <?php
            settings_fields('date_icon_fixer_settings');
            do_settings_sections('date_icon_fixer_settings');

            $enabled         = get_option('date_icon_fixer_enabled');
            $date_format     = get_option('date_icon_fixer_format', 'd-m-Y');
            $disable_weekend = get_option('date_icon_fixer_disable_weekend');
            $min_date        = get_option('date_icon_fixer_min_date');
            $max_date        = get_option('date_icon_fixer_max_date');
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <h3>Activate Flatpickr</h3>
                    </th>
                    <td>
                        <label class="dif-switch">
                            <input type="checkbox" name="date_icon_fixer_enabled" value="1" <?php checked(1, $enabled, true); ?> />
                            <span class="dif-slider round"></span>
                        </label>
                        <label style="margin-left: 10px;">Enable Flatpickr replacement for input[type='date'] fields.</label>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <h3>Date Format</h3>
                    </th>
                    <td>
                        <select name="date_icon_fixer_format">
                            <option value="d-m-Y" <?php selected($date_format, 'd-m-Y'); ?>>24-04-2025</option>
                            <option value="m-d-Y" <?php selected($date_format, 'm-d-Y'); ?>>04-24-2025</option>
                            <option value="Y-m-d" <?php selected($date_format, 'Y-m-d'); ?>>2025-04-24</option>
                            <option value="d/m/Y" <?php selected($date_format, 'd/m/Y'); ?>>24/04/2025</option>
                            <option value="m/d/Y" <?php selected($date_format, 'm/d/Y'); ?>>04/24/2025</option>
                            <option value="Y/m/d" <?php selected($date_format, 'Y/m/d'); ?>>2025/04/24</option>
                            <option value="d.m.Y" <?php selected($date_format, 'd.m.Y'); ?>>24.04.2025</option>
                            <option value="m.d.Y" <?php selected($date_format, 'm.d.Y'); ?>>04.24.2025</option>
                            <option value="Y.m.d" <?php selected($date_format, 'Y.m.d'); ?>>2025.04.24</option>
                        </select>
                        <p class="description">Choose a date format for the calendar.</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <h3>Date Placeholder</h3>
                    </th>
                    <td>
                        <input type="text" name="date_icon_fixer_placeholder" value="<?php echo esc_attr(get_option('date_icon_fixer_placeholder', 'DD-MM-YYYY')); ?>" placeholder="e.g. DD-MM-YYYY" />
                        <p class="description">This text will appear as a placeholder in date inputs.</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <h3>Disable Weekends</h3>
                    </th>
                    <td>
                        <label class="dif-switch">
                            <input type="checkbox" name="date_icon_fixer_disable_weekend" value="1" <?php checked(1, $disable_weekend, true); ?> />
                            <span class="dif-slider round"></span>
                        </label>
                        <label style="margin-left: 10px;">Prevent users from selecting Saturdays and Sundays.</label>
                    </td>
                </tr>


                <tr>
                    <th scope="row">
                        <h3>Minimum Date</h3>
                    </th>
                    <td>
                        <input type="text" name="date_icon_fixer_min_date" value="<?php echo esc_attr($min_date); ?>" placeholder="e.g. 01-01-2025" />
                        <p class="description">Choose a minimum date for the calendar.</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <h3>Maximum Date</h3>
                    </th>
                    <td>
                        <input type="text" name="date_icon_fixer_max_date" value="<?php echo esc_attr($max_date); ?>" placeholder="e.g. 31-12-2025" />
                        <p class="description">Choose a maximum date for the calendar.</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <h3>Change Theme</h3>
                    </th>
                    <td>
                        <select name="date_icon_fixer_theme">
                            <option value="">Default</option>
                            <option value="dark" <?php selected(get_option('date_icon_fixer_theme'), 'dark'); ?>>Dark</option>
                            <option value="light" <?php selected(get_option('date_icon_fixer_theme'), 'light'); ?>>Light</option>
                            <option value="confetti" <?php selected(get_option('date_icon_fixer_theme'), 'confetti'); ?>>Confetti</option>
                            <option value="airbnb" <?php selected(get_option('date_icon_fixer_theme'), 'airbnb'); ?>>Airbnb</option>
                            <option value="material_blue" <?php selected(get_option('date_icon_fixer_theme'), 'material_blue'); ?>>Blue</option>
                            <option value="material_green" <?php selected(get_option('date_icon_fixer_theme'), 'material_green'); ?>>Green</option>
                            <option value="material_orange" <?php selected(get_option('date_icon_fixer_theme'), 'material_orange'); ?>>Orange</option>
                            <option value="material_red" <?php selected(get_option('date_icon_fixer_theme'), 'material_red'); ?>>Red</option>
                        </select>
                        <p class="description">Choose a theme for the calendar.</p>
                        <p class="description">For custom themes you must add your own CSS or create your own theme.</p>
                        <p class="description"><span class="dif-text-heading">- </span>Path: `/plugins/date-icon-fixer/assets/css/themes/...`</p>
                        <p class="description"><span class="dif-text-heading">- </span>Wrapper class: `flatpickr-calendar`</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <h3>Enable Week Numbers</h3>
                    </th>
                    <td>
                        <label class="dif-switch">
                            <input type="checkbox" name="date_icon_fixer_enable_week_dates" value="1" <?php checked(1, get_option('date_icon_fixer_enable_week_dates'), true); ?> />
                            <span class="dif-slider round"></span>
                        </label>
                        <label style="margin-right: 10px;">Show week numbers in the calendar view.</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <h3>Weeks Only</h3>
                    </th>
                    <td>
                        <label class="dif-switch">
                            <input type="checkbox" name="date_icon_fixer_weeks_only" value="1" <?php checked(1, get_option('date_icon_fixer_weeks_only'), true); ?> />
                            <span class="dif-slider round"></span>
                        </label>
                        <label style="margin-right: 10px;">Only allow selecting weeks (not specific days).</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <h3>Disable Specific Dates</h3>
                    </th>
                    <td>
                        <input type="text" name="date_icon_fixer_disable_dates" value="<?php echo esc_attr(get_option('date_icon_fixer_disable_dates')); ?>" placeholder="25-12-2025,01-01-2025" />
                        <p class="description">Comma-separated dates to disable.</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <h3>Disable Range of Dates</h3>
                    </th>
                    <td>
                        <input type="text" name="date_icon_fixer_disable_ranges" value="<?php echo esc_attr(get_option('date_icon_fixer_disable_ranges')); ?>" placeholder="01-07-2025 to 10-07-2025" />
                        <p class="description">Use "start to end" format. Example: `01-07-2025 to 10-07-2025`</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <h3>Enable Only Specific Dates</h3>
                    </th>
                    <td>
                        <input type="text" name="date_icon_fixer_enable_only" value="<?php echo esc_attr(get_option('date_icon_fixer_enable_only')); ?>" placeholder="01-05-2025,10-05-2025" />
                        <p class="description">Comma-separated dates to enable, all others will be disabled.</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <h3>Allow Multiple Dates Selection</h3>
                    </th>
                    <td>
                        <label class="dif-switch">
                            <input type="checkbox" name="date_icon_fixer_multiple_dates" value="1" <?php checked(1, get_option('date_icon_fixer_multiple_dates'), true); ?> />
                            <span class="dif-slider round"></span>
                        </label>
                        <label style="margin-right: 10px;">Enable selecting multiple dates.</label>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save Changes', 'dif-button-primary', 'submit', true); ?>
        </form>

        <p>
            <a href="<?php echo esc_url(admin_url('admin.php?page=date-icon-fixer&reset-date-icon-fixer=1')); ?>"
                class="dif-button-dateicon-fixer"
                onclick="return confirm('Are you sure you want to reset all settings to default?');">
                Reset to Default Settings
            </a>
        </p>

        <?php if (isset($_GET['reset-success'])) : ?>
            <div class="dif-success-message">
                <svg fill="#00D992" width="24" height="24" clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="m12.002 2.005c5.518 0 9.998 4.48 9.998 9.997 0 5.518-4.48 9.998-9.998 9.998-5.517 0-9.997-4.48-9.997-9.998 0-5.517 4.48-9.997 9.997-9.997zm0 1.5c-4.69 0-8.497 3.807-8.497 8.497s3.807 8.498 8.497 8.498 8.498-3.808 8.498-8.498-3.808-8.497-8.498-8.497zm0 6.5c-.414 0-.75.336-.75.75v5.5c0 .414.336.75.75.75s.75-.336.75-.75v-5.5c0-.414-.336-.75-.75-.75zm-.002-3c.552 0 1 .448 1 1s-.448 1-1 1-1-.448-1-1 .448-1 1-1z" fill-rule="nonzero" />
                </svg>
                <p>Settings have been reset to default.</p>
            </div>
        <?php endif; ?>

        <div class="dif-footer">
            <p>Plugin by <a href="https://royaardenburg.nl" target="_blank">Roy Aardenburg</a></p>
            <p>Plugin is open source and available on <a href="https://github.com/Roy-Aa/date-icon-fixer">GitHub</a>.</p>
        </div>
    </div>
    <style>
        body {
            background: #0A0A0A;
            padding: 0px;
        }

        #wpbody-content {
            margin-top: 20px;
        }

        #wpbody-content::selection {
            color: #080f11;
            background: #00D992;
        }

        #wpadminbar #wp-admin-bar-flatpickr-status>a {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 8px;
            height: 32px;
            gap: 8px;
        }

        #wpadminbar #wp-admin-bar-flatpickr-status svg {
            display: block;
        }
    </style>
<?php
}
