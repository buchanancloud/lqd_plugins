<?php
/**
 * Plugin Name: LqD
 * Description: A WordPress plugin to create a sticky pop-up bar at the bottom of the viewport.
 */

// Add an admin menu item for this plugin
add_action('admin_menu', 'lqd_menu');
function lqd_menu() {
    add_menu_page('LqD Settings', 'LqD', 'manage_options', 'lqd_settings', 'lqd_settings_page');
}

// Display the settings page
function lqd_settings_page() {
    ?>
    <div class="wrap">
        <h2>LqD Settings</h2>
        <form id="lqd-settings-form" method="post" action="options.php">
            <?php
            settings_fields('lqd_settings');
            do_settings_sections('lqd_settings');
            ?>
            <input type="submit" value="Save Changes">
        </form>
        <script>
            (function() {
                document.getElementById('lqd-settings-form').addEventListener('submit', function() {
                    return window.confirm('Are you sure you want to save these settings?');
                });
            })();
        </script>
    </div>
    <?php
}

// Initialize the plugin's settings
add_action('admin_init', 'lqd_settings_init');
function lqd_settings_init() {
    // Register existing settings
    register_setting('lqd_settings', 'lqd_height');
    register_setting('lqd_settings', 'lqd_bg_color');
    register_setting('lqd_settings', 'lqd_columns');
    register_setting('lqd_settings', 'lqd_toggle_color');
    register_setting('lqd_settings', 'lqd_toggle_text_color');
    register_setting('lqd_settings', 'lqd_minimized_height');
  
    // Register new settings
    register_setting('lqd_settings', 'lqd_start_minimized');
    register_setting('lqd_settings', 'lqd_auto_slide_delay');
    register_setting('lqd_settings', 'lqd_width'); // New setting for the width of the vertical bar

    add_settings_section('lqd_section', 'LqD Settings', null, 'lqd_settings');

    // Existing settings fields
    add_settings_field('lqd_height', 'Height of Horizontal Bar', 'lqd_height_callback', 'lqd_settings', 'lqd_section');
    add_settings_field('lqd_bg_color', 'Background Color', 'lqd_bg_color_callback', 'lqd_settings', 'lqd_section');
    add_settings_field('lqd_columns', 'Number of Columns', 'lqd_columns_callback', 'lqd_settings', 'lqd_section');
    add_settings_field('lqd_toggle_color', 'Toggle Button Color', 'lqd_toggle_color_callback', 'lqd_settings', 'lqd_section');
    add_settings_field('lqd_toggle_text_color', 'Toggle Text Color', 'lqd_toggle_text_color_callback', 'lqd_settings', 'lqd_section');
    add_settings_field('lqd_minimized_height', 'Minimized Height of Horizontal Bar', 'lqd_minimized_height_callback', 'lqd_settings', 'lqd_section');
  
    // New settings fields
    add_settings_field('lqd_start_minimized', 'Start Minimized', 'lqd_start_minimized_callback', 'lqd_settings', 'lqd_section');
    add_settings_field('lqd_auto_slide_delay', 'Auto Slide Delay (ms)', 'lqd_auto_slide_delay_callback', 'lqd_settings', 'lqd_section');
    add_settings_field('lqd_width', 'Width of Vertical Bar', 'lqd_width_callback', 'lqd_settings', 'lqd_section'); // New setting for the width of the vertical bar

    // Multiple columns setting
    for ($i = 1; $i <= 3; $i++) {
        register_setting('lqd_settings', "lqd_column_content_$i");
        add_settings_field("lqd_column_content_$i", "Column $i Content", 'lqd_column_content_callback', 'lqd_settings', 'lqd_section', array('column' => $i));
    }
}

// Callback functions for existing settings
function lqd_height_callback() {
    echo '<input type="text" name="lqd_height" value="' . get_option('lqd_height', '50') . '" />';
}
function lqd_bg_color_callback() {
    echo '<input type="text" name="lqd_bg_color" value="' . get_option('lqd_bg_color', 'darkblue') . '" />';
}
function lqd_columns_callback() {
    echo '<select name="lqd_columns">';
    for ($i = 1; $i <= 3; $i++) {
        $selected = (get_option('lqd_columns', '1') == $i) ? 'selected' : '';
        echo "<option value='$i' $selected>$i</option>";
    }
    echo '</select>';
}
function lqd_toggle_color_callback() {
    echo '<input type="text" name="lqd_toggle_color" value="' . get_option('lqd_toggle_color', 'black') . '" />';
}
function lqd_toggle_text_color_callback() {
    echo '<input type="text" name="lqd_toggle_text_color" value="' . get_option('lqd_toggle_text_color', 'white') . '" />';
}
function lqd_minimized_height_callback() {
    echo '<input type="text" name="lqd_minimized_height" value="' . get_option('lqd_minimized_height', '50') . '" />';
}

// Callback functions for new settings
function lqd_start_minimized_callback() {
    $checked = get_option('lqd_start_minimized', '0') === '1' ? 'checked' : '';
    echo "<input type='checkbox' name='lqd_start_minimized' value='1' $checked />";
}
function lqd_auto_slide_delay_callback() {
    echo '<input type="text" name="lqd_auto_slide_delay" value="' . get_option('lqd_auto_slide_delay', '0') . '" />';
}
function lqd_width_callback() { // Callback function for the width setting
    echo '<input type="text" name="lqd_width" value="' . get_option('lqd_width', '50') . '" />';
}

function lqd_column_content_callback($args) {
    $column = $args['column'];
    wp_editor(get_option("lqd_column_content_$column", ''), "lqd_column_content_$column");
}

// Enqueue styles and scripts
add_action('wp_enqueue_scripts', 'lqd_enqueue_scripts');
function lqd_enqueue_scripts() {
    wp_enqueue_style('lqd-style', plugins_url('lqd_files/lqd_style.css', __FILE__));
    wp_enqueue_script('lqd-script', plugins_url('lqd_files/lqd_script.js', __FILE__), array('jquery'), null, true);

    // Localize script with new settings
    $script_data = array(
        'minimizedHeight' => get_option('lqd_minimized_height', '50'),
        'originalHeight' => get_option('lqd_height', '50'),
        'startMinimized' => get_option('lqd_start_minimized', '0'),
        'autoSlideDelay' => get_option('lqd_auto_slide_delay', '0'),
        'width' => get_option('lqd_width', '50'), // New setting for the width of the vertical bar
    );
    wp_localize_script('lqd-script', 'lqdData', $script_data);
}

// Output the horizontal footer bar
add_action('wp_footer', 'lqd_footer_bar');
function lqd_footer_bar() {
    $height = get_option('lqd_height', '50'); // Height for the horizontal footer bar
    $bg_color = get_option('lqd_bg_color', 'darkblue');
    $columns = get_option('lqd_columns', '1');
    $toggleColor = get_option('lqd_toggle_color', 'black');
    $toggleTextColor = get_option('lqd_toggle_text_color', 'white');
    echo "<div class='lqd-bar' style='height: ${height}px; background-color: ${bg_color};'>";
    for ($i = 1; $i <= $columns; $i++) {
        $content = do_shortcode(get_option("lqd_column_content_$i", "Column $i Content"));
        echo "<div class='lqd-column'>$content</div>";
    }
    echo "<div class='lqd-toggle' style='background-color: ${toggleColor}; color: ${toggleTextColor};'>-</div>";
    echo '</div>';
}

// Output the vertical bar
add_action('wp_footer', 'lqd_vertical_bar');
function lqd_vertical_bar() {
    $width = get_option('lqd_width', '50'); // Width of the vertical bar
    $bg_color = get_option('lqd_bg_color', 'darkblue');
    $columns = get_option('lqd_columns', '1');
    $toggleColor = get_option('lqd_toggle_color', 'black');
    $toggleTextColor = get_option('lqd_toggle_text_color', 'white');
    echo "<div class='lqd-vertical-bar' style='width: ${width}px; background-color: ${bg_color};'>";
    for ($i = 1; $i <= $columns; $i++) {
        $content = do_shortcode(get_option("lqd_column_content_$i", "Column $i Content"));
        echo "<div class='lqd-column'>$content</div>";
    }
    echo "<div class='lqd-toggle' style='background-color: ${toggleColor}; color: ${toggleTextColor};'>-</div>";
    echo '</div>';
}
?>