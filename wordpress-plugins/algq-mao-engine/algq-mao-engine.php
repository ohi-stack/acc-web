<?php
/**
 * Plugin Name: Algonquian MAO Engine
 * Plugin URI: https://algonquianrealestate.com/plugin/mao-engine
 * Description: Live WordPress underwriting plugin for deal intake, MAO calculation, and pipeline auto-underwriting.
 * Version: 1.0.0
 * Author: Onegodian
 * Text Domain: algq-mao-engine
 */

if (!defined('ABSPATH')) {
    exit;
}

define('ALGQ_MAO_VERSION', '1.0.0');
define('ALGQ_MAO_SLUG', 'algq-mao-engine');
define('ALGQ_MAO_PATH', plugin_dir_path(__FILE__));
define('ALGQ_MAO_URL', plugin_dir_url(__FILE__));

final class ALGQ_MAO_Engine {
    private static $instance = null;
    private $deals_table;
    private $underwriting_table;

    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        global $wpdb;
        $this->deals_table = $wpdb->prefix . 'algq_deals';
        $this->underwriting_table = $wpdb->prefix . 'algq_underwriting';

        register_activation_hook(__FILE__, array($this, 'activate'));
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_shortcode('algq_deal_intake_form', array($this, 'deal_intake_shortcode'));
        add_shortcode('algq_mao_calculator', array($this, 'calculator_shortcode'));
        add_shortcode('algq_pipeline_board', array($this, 'pipeline_shortcode'));
        add_shortcode('algq_mao_plugin_page', array($this, 'plugin_page_shortcode'));
        add_action('admin_post_nopriv_algq_submit_deal', array($this, 'handle_deal_submission'));
        add_action('admin_post_algq_submit_deal', array($this, 'handle_deal_submission'));
    }

    public function activate() {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $charset = $wpdb->get_charset_collate();

        dbDelta("CREATE TABLE {$this->deals_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            deal_uid VARCHAR(32) NOT NULL,
            property_address TEXT NOT NULL,
            seller_name VARCHAR(190) DEFAULT '',
            seller_email VARCHAR(190) DEFAULT '',
            seller_phone VARCHAR(80) DEFAULT '',
            asking_price DECIMAL(14,2) DEFAULT 0,
            arv DECIMAL(14,2) DEFAULT 0,
            repairs DECIMAL(14,2) DEFAULT 0,
            strategy VARCHAR(40) DEFAULT 'wholesale',
            status VARCHAR(80) DEFAULT 'Lead Captured',
            notes LONGTEXT,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY deal_uid (deal_uid)
        ) $charset;");

        dbDelta("CREATE TABLE {$this->underwriting_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            deal_id BIGINT UNSIGNED NOT NULL,
            arv DECIMAL(14,2) DEFAULT 0,
            repairs DECIMAL(14,2) DEFAULT 0,
            holding_costs DECIMAL(14,2) DEFAULT 0,
            closing_costs DECIMAL(14,2) DEFAULT 0,
            desired_profit DECIMAL(14,2) DEFAULT 0,
            assignment_fee DECIMAL(14,2) DEFAULT 0,
            mao DECIMAL(14,2) DEFAULT 0,
            estimated_spread DECIMAL(14,2) DEFAULT 0,
            risk_flag VARCHAR(40) DEFAULT 'Review',
            formula_snapshot LONGTEXT,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY deal_id (deal_id)
        ) $charset;");

        update_option('algq_mao_assumptions', array(
            'arv_multiplier' => 0.70,
            'holding_costs' => 0,
            'closing_cost_rate' => 0.03,
            'desired_profit' => 20000,
            'assignment_fee' => 10000,
            'auto_move_to_underwriting' => 1,
        ));

        $this->create_system_pages();
    }

    private function create_system_pages() {
        $pages = array(
            'MAO Engine' => array('plugin/mao-engine', '[algq_mao_plugin_page]'),
            'MAO Engine Getting Started' => array('plugin/mao-engine/start', '[algq_mao_plugin_page view="start"]'),
            'MAO Engine Documentation' => array('plugin/mao-engine/docs', '[algq_mao_plugin_page view="docs"]'),
            'MAO Calculator' => array('plugin/mao-engine/calculator', '[algq_mao_calculator]'),
            'Sell Your Property' => array('sell-your-property', '[algq_deal_intake_form]'),
            'Pipeline Board' => array('pipeline/board', '[algq_pipeline_board]'),
        );
        foreach ($pages as $title => $data) {
            if (!get_page_by_path($data[0])) {
                wp_insert_post(array('post_title' => $title, 'post_name' => basename($data[0]), 'post_content' => $data[1], 'post_status' => 'publish', 'post_type' => 'page'));
            }
        }
    }

    public function enqueue_assets() {
        wp_enqueue_style('algq-mao-engine', ALGQ_MAO_URL . 'assets/css/algq-mao-engine.css', array(), ALGQ_MAO_VERSION);
        wp_enqueue_script('algq-mao-engine', ALGQ_MAO_URL . 'assets/js/algq-mao-engine.js', array(), ALGQ_MAO_VERSION, true);
    }

    public function register_settings() {
        register_setting('algq_mao_settings', 'algq_mao_assumptions');
    }

    public function admin_menu() {
        add_menu_page('Algonquian Engine', 'Algonquian Engine', 'manage_options', 'algq-mao-engine', array($this, 'admin_dashboard'), 'dashicons-chart-area', 26);
        add_submenu_page('algq-mao-engine', 'Deals', 'Deals', 'manage_options', 'algq-mao-deals', array($this, 'admin_deals'));
        add_submenu_page('algq-mao-engine', 'Underwriting', 'Underwriting', 'manage_options', 'algq-mao-underwriting', array($this, 'admin_underwriting'));
        add_submenu_page('algq-mao-engine', 'Settings', 'Settings', 'manage_options', 'algq-mao-settings', array($this, 'admin_settings'));
    }

    public function admin_dashboard() {
        echo '<div class="wrap algq-admin"><h1>Algonquian MAO Engine</h1><p>Intake. Underwrite. Manage. Close.</p>' . $this->admin_summary_cards() . '</div>';
    }

    private function admin_summary_cards() {
        global $wpdb;
        $deals = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->deals_table}");
        $uw = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->underwriting_table}");
        return '<div class="algq-cards"><div><strong>Total Deals</strong><span>' . esc_html($deals) . '</span></div><div><strong>Underwritten</strong><span>' . esc_html($uw) . '</span></div></div>';
    }

    public function admin_deals() {
        global $wpdb;
        $rows = $wpdb->get_results("SELECT * FROM {$this->deals_table} ORDER BY created_at DESC LIMIT 100");
        echo '<div class="wrap"><h1>Deals</h1><table class="widefat"><thead><tr><th>Deal ID</th><th>Address</th><th>Seller</th><th>ARV</th><th>Status</th><th>Created</th></tr></thead><tbody>';
        foreach ($rows as $r) {
            echo '<tr><td>' . esc_html($r->deal_uid) . '</td><td>' . esc_html($r->property_address) . '</td><td>' . esc_html($r->seller_name) . '</td><td>$' . esc_html(number_format((float)$r->arv, 0)) . '</td><td>' . esc_html($r->status) . '</td><td>' . esc_html($r->created_at) . '</td></tr>';
        }
        echo '</tbody></table></div>';
    }

    public function admin_underwriting() {
        global $wpdb;
        $rows = $wpdb->get_results("SELECT u.*, d.deal_uid, d.property_address FROM {$this->underwriting_table} u JOIN {$this->deals_table} d ON d.id = u.deal_id ORDER BY u.created_at DESC LIMIT 100");
        echo '<div class="wrap"><h1>Underwriting</h1><table class="widefat"><thead><tr><th>Deal</th><th>Address</th><th>MAO</th><th>Spread</th><th>Risk</th><th>Created</th></tr></thead><tbody>';
        foreach ($rows as $r) {
            echo '<tr><td>' . esc_html($r->deal_uid) . '</td><td>' . esc_html($r->property_address) . '</td><td>$' . esc_html(number_format((float)$r->mao, 0)) . '</td><td>$' . esc_html(number_format((float)$r->estimated_spread, 0)) . '</td><td>' . esc_html($r->risk_flag) . '</td><td>' . esc_html($r->created_at) . '</td></tr>';
        }
        echo '</tbody></table></div>';
    }

    public function admin_settings() {
        $a = get_option('algq_mao_assumptions', array());
        echo '<div class="wrap"><h1>MAO Settings</h1><form method="post" action="options.php">';
        settings_fields('algq_mao_settings');
        $fields = array('arv_multiplier', 'holding_costs', 'closing_cost_rate', 'desired_profit', 'assignment_fee', 'auto_move_to_underwriting');
        echo '<table class="form-table">';
        foreach ($fields as $field) {
            echo '<tr><th>' . esc_html(ucwords(str_replace('_', ' ', $field))) . '</th><td><input name="algq_mao_assumptions[' . esc_attr($field) . ']" value="' . esc_attr($a[$field] ?? '') . '" class="regular-text"></td></tr>';
        }
        echo '</table>'; submit_button(); echo '</form></div>';
    }

    public function deal_intake_shortcode() {
        ob_start(); ?>
        <form class="algq-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="algq_submit_deal">
            <?php wp_nonce_field('algq_submit_deal', 'algq_nonce'); ?>
            <h2>Submit Property</h2>
            <label>Property Address<input required name="property_address"></label>
            <label>Seller Name<input name="seller_name"></label>
            <label>Seller Email<input type="email" name="seller_email"></label>
            <label>Seller Phone<input name="seller_phone"></label>
            <label>Asking Price<input type="number" step="0.01" name="asking_price"></label>
            <label>After Repair Value<input type="number" step="0.01" name="arv"></label>
            <label>Repair Estimate<input type="number" step="0.01" name="repairs"></label>
            <label>Strategy<select name="strategy"><option value="wholesale">Wholesale</option><option value="flip">Flip</option><option value="rental">Rental</option></select></label>
            <label>Notes<textarea name="notes"></textarea></label>
            <button type="submit">Create Deal & Auto-Underwrite</button>
        </form>
        <?php return ob_get_clean();
    }

    public function handle_deal_submission() {
        if (!isset($_POST['algq_nonce']) || !wp_verify_nonce($_POST['algq_nonce'], 'algq_submit_deal')) {
            wp_die('Security check failed.');
        }
        global $wpdb;
        $now = current_time('mysql');
        $deal_uid = 'ARE-' . gmdate('Ymd') . '-' . wp_rand(1000, 9999);
        $settings = get_option('algq_mao_assumptions', array());
        $status = !empty($settings['auto_move_to_underwriting']) ? 'Underwriting' : 'Lead Captured';
        $wpdb->insert($this->deals_table, array(
            'deal_uid' => $deal_uid,
            'property_address' => sanitize_text_field($_POST['property_address'] ?? ''),
            'seller_name' => sanitize_text_field($_POST['seller_name'] ?? ''),
            'seller_email' => sanitize_email($_POST['seller_email'] ?? ''),
            'seller_phone' => sanitize_text_field($_POST['seller_phone'] ?? ''),
            'asking_price' => (float) ($_POST['asking_price'] ?? 0),
            'arv' => (float) ($_POST['arv'] ?? 0),
            'repairs' => (float) ($_POST['repairs'] ?? 0),
            'strategy' => sanitize_text_field($_POST['strategy'] ?? 'wholesale'),
            'status' => $status,
            'notes' => sanitize_textarea_field($_POST['notes'] ?? ''),
            'created_at' => $now,
            'updated_at' => $now,
        ));
        $deal_id = (int) $wpdb->insert_id;
        $this->auto_underwrite($deal_id);
        wp_safe_redirect(add_query_arg(array('deal_created' => $deal_uid), wp_get_referer() ?: home_url('/')));
        exit;
    }

    private function auto_underwrite($deal_id) {
        global $wpdb;
        $deal = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->deals_table} WHERE id=%d", $deal_id));
        if (!$deal) { return; }
        $s = get_option('algq_mao_assumptions', array());
        $arv_multiplier = (float) ($s['arv_multiplier'] ?? 0.70);
        $closing_costs = ((float) $deal->arv) * (float) ($s['closing_cost_rate'] ?? 0.03);
        $holding = (float) ($s['holding_costs'] ?? 0);
        $profit = (float) ($s['desired_profit'] ?? 20000);
        $assignment = (float) ($s['assignment_fee'] ?? 10000);
        $mao = (((float) $deal->arv) * $arv_multiplier) - (float) $deal->repairs - $holding - $closing_costs - $profit;
        if ($deal->strategy === 'wholesale') { $mao -= $assignment; }
        $spread = max(0, ((float) $deal->asking_price) - $mao);
        $risk = $mao <= 0 ? 'High Risk' : ($spread > 50000 ? 'Review' : 'Acceptable');
        $wpdb->insert($this->underwriting_table, array(
            'deal_id' => $deal_id,
            'arv' => $deal->arv,
            'repairs' => $deal->repairs,
            'holding_costs' => $holding,
            'closing_costs' => $closing_costs,
            'desired_profit' => $profit,
            'assignment_fee' => $assignment,
            'mao' => $mao,
            'estimated_spread' => $spread,
            'risk_flag' => $risk,
            'formula_snapshot' => wp_json_encode($s),
            'created_at' => current_time('mysql'),
        ));
    }

    public function calculator_shortcode() {
        return '<div class="algq-calculator"><h2>MAO Calculator</h2><label>ARV<input id="algq_arv" type="number"></label><label>Repairs<input id="algq_repairs" type="number"></label><label>Holding Costs<input id="algq_holding" type="number"></label><label>Desired Profit<input id="algq_profit" type="number" value="20000"></label><button type="button" onclick="algqCalculateMao()">Calculate MAO</button><div id="algq_mao_result" class="algq-result"></div></div>';
    }

    public function pipeline_shortcode() {
        global $wpdb;
        $stages = array('Lead Captured', 'Underwriting', 'Offer Sent', 'Under Contract', 'Buyer Assigned', 'Closed');
        $html = '<div class="algq-pipeline">';
        foreach ($stages as $stage) {
            $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$this->deals_table} WHERE status=%s ORDER BY updated_at DESC LIMIT 20", $stage));
            $html .= '<div class="algq-stage"><h3>' . esc_html($stage) . '</h3>';
            foreach ($rows as $r) {
                $html .= '<div class="algq-deal-card"><strong>' . esc_html($r->deal_uid) . '</strong><p>' . esc_html($r->property_address) . '</p><span>$' . esc_html(number_format((float)$r->asking_price, 0)) . '</span></div>';
            }
            $html .= '</div>';
        }
        return $html . '</div>';
    }

    public function plugin_page_shortcode($atts = array()) {
        $atts = shortcode_atts(array('view' => 'overview'), $atts);
        if ($atts['view'] === 'docs') {
            return '<h1>MAO Engine Documentation</h1><p>Formula: MAO = (ARV x 70%) - Repairs - Holding Costs - Closing Costs - Desired Profit. Wholesale mode also subtracts assignment fee.</p><p>Shortcodes: [algq_deal_intake_form], [algq_mao_calculator], [algq_pipeline_board].</p>';
        }
        if ($atts['view'] === 'start') {
            return '<h1>Getting Started: MAO Engine</h1><ol><li>Activate plugin.</li><li>Configure assumptions.</li><li>Publish intake page.</li><li>Submit property.</li><li>Review auto-underwriting and pipeline status.</li></ol>';
        }
        return '<h1>Algonquian MAO Engine</h1><p>Version 1.0.0 | By Onegodian</p><p>Calculates Maximum Allowable Offer and creates underwriting outputs for acquisition decisions.</p>';
    }
}

ALGQ_MAO_Engine::instance();
