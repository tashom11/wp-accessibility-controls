<?php
/**
 * Plugin Name: WP Accessibility Controls
 * Plugin URI: https://github.com/tashom11/wp-accessibility-controls
 * Description: Permet aux visiteurs de personnaliser les paramètres d'accessibilité (taille du texte, police, contraste, espacement) pour améliorer leur expérience de navigation.
 * Version: 1.0.0
 * Author: tashom11
 * Author URI: https://github.com/tashom11
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-accessibility-controls
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WP_ACCESSIBILITY_CONTROLS_VERSION', '1.0.0');
define('WP_ACCESSIBILITY_CONTROLS_PATH', plugin_dir_path(__FILE__));
define('WP_ACCESSIBILITY_CONTROLS_URL', plugin_dir_url(__FILE__));

class WP_Accessibility_Controls {

    private $default_settings = array(
        'dyslexia_mode' => false,
        'font_size' => 16,
        'line_height' => 'normal',
        'letter_spacing' => 'normal',
        'contrast' => 'normal',
        'cursor_size' => 'normal',
        'text_alignment' => 'left'
    );

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'add_accessibility_panel'), 999);
        add_action('wp_ajax_wpac_save_settings', array($this, 'save_user_settings'));
        add_action('wp_ajax_nopriv_wpac_save_settings', array($this, 'save_user_settings'));
        add_action('wp_ajax_wpac_reset_settings', array($this, 'reset_settings'));
        add_action('wp_ajax_nopriv_wpac_reset_settings', array($this, 'reset_settings'));
        add_action('init', array($this, 'apply_accessibility_styles'));
    }

    public function enqueue_scripts() {
        // Charger Lexend depuis Google Fonts
        wp_enqueue_style(
            'lexend-font',
            'https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap',
            array(),
            null
        );

        wp_enqueue_style(
            'wp-accessibility-controls-style',
            WP_ACCESSIBILITY_CONTROLS_URL . 'assets/css/style.css',
            array(),
            WP_ACCESSIBILITY_CONTROLS_VERSION
        );

        wp_enqueue_script(
            'wp-accessibility-controls-script',
            WP_ACCESSIBILITY_CONTROLS_URL . 'assets/js/script.js',
            array('jquery'),
            WP_ACCESSIBILITY_CONTROLS_VERSION,
            true
        );

        wp_localize_script('wp-accessibility-controls-script', 'wpacData', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wpac_nonce'),
            'settings' => $this->get_user_settings()
        ));
    }

    public function get_user_settings() {
        if (is_user_logged_in()) {
            $settings = get_user_meta(get_current_user_id(), 'wpac_settings', true);
        } else {
            $settings = isset($_COOKIE['wpac_settings']) ? json_decode(stripslashes($_COOKIE['wpac_settings']), true) : array();
        }
        
        return wp_parse_args($settings, $this->default_settings);
    }

    public function save_user_settings() {
        check_ajax_referer('wpac_nonce', 'nonce');
        
        $settings = isset($_POST['settings']) ? $_POST['settings'] : array();
        
        $sanitized_settings = array(
            'dyslexia_mode' => isset($settings['dyslexia_mode']) ? (bool) $settings['dyslexia_mode'] : false,
            'font_size' => isset($settings['font_size']) ? intval($settings['font_size']) : 16,
            'line_height' => sanitize_text_field($settings['line_height'] ?? 'normal'),
            'letter_spacing' => sanitize_text_field($settings['letter_spacing'] ?? 'normal'),
            'contrast' => sanitize_text_field($settings['contrast'] ?? 'normal'),
            'cursor_size' => sanitize_text_field($settings['cursor_size'] ?? 'normal'),
            'text_alignment' => sanitize_text_field($settings['text_alignment'] ?? 'left')
        );

        if (is_user_logged_in()) {
            update_user_meta(get_current_user_id(), 'wpac_settings', $sanitized_settings);
        } else {
            // Sauvegarde dans un cookie pour les visiteurs non connectés
            setcookie('wpac_settings', json_encode($sanitized_settings), time() + (365 * 24 * 60 * 60), '/');
        }

        wp_send_json_success($sanitized_settings);
    }

    public function reset_settings() {
        check_ajax_referer('wpac_nonce', 'nonce');

        if (is_user_logged_in()) {
            delete_user_meta(get_current_user_id(), 'wpac_settings');
        } else {
            setcookie('wpac_settings', '', time() - 3600, '/');
        }

        wp_send_json_success($this->default_settings);
    }

    public function apply_accessibility_styles() {
        $settings = $this->get_user_settings();
        
        if (empty($settings) || !isset($settings['font_size'])) {
            return;
        }

        add_action('wp_head', function() use ($settings) {
            echo '<style id="wp-accessibility-controls-css">';
            $this->generate_css($settings);
            echo '</style>';
        }, 999);
    }

    private function generate_css($settings) {
        // Définir les tableaux de configuration
        $sizes = array(
            'small' => '0.875rem',
            'normal' => '1rem',
            'large' => '1.25rem',
            'xlarge' => '1.5rem',
            'xxlarge' => '2rem'
        );
        
        $fonts = array(
            'arial' => 'Arial, sans-serif',
            'verdana' => 'Verdana, sans-serif',
            'times' => 'Times New Roman, serif',
            'comic' => 'Comic Sans MS, cursive',
            'dyslexic' => '"OpenDyslexic", sans-serif'
        );
        
        $heights = array(
            'tight' => '1.25',
            'normal' => '1.6',
            'relaxed' => '2'
        );
        
        $spacings = array(
            'tight' => '-0.05em',
            'normal' => '0',
            'wide' => '0.1em',
            'extra-wide' => '0.2em'
        );
        
        $contrasts = array(
            'low' => 'filter: contrast(0.8);',
            'normal' => '',
            'high' => 'filter: contrast(1.25) brightness(1.1);',
            'inverted' => 'filter: invert(1) hue-rotate(180deg);'
        );
        
        $cursors = array(
            'small' => '10px',
            'normal' => '16px',
            'large' => '24px'
        );
        
        $css = ':root {';
        
        // Taille de police
        if ($settings['font_size'] !== 'normal') {
            $font_size = isset($sizes[$settings['font_size']]) ? $sizes[$settings['font_size']] : '1rem';
            $css .= "--wpac-font-size: {$font_size};";
        }
        
        // Famille de police
        if ($settings['font_family'] !== 'default') {
            $font_family = isset($fonts[$settings['font_family']]) ? $fonts[$settings['font_family']] : 'inherit';
            $css .= "--wpac-font-family: {$font_family};";
        }
        
        // Hauteur de ligne
        if ($settings['line_height'] !== 'normal') {
            $line_height = isset($heights[$settings['line_height']]) ? $heights[$settings['line_height']] : '1.6';
            $css .= "--wpac-line-height: {$line_height};";
        }
        
        // Espacement des lettres
        if ($settings['letter_spacing'] !== 'normal') {
            $letter_spacing = isset($spacings[$settings['letter_spacing']]) ? $spacings[$settings['letter_spacing']] : '0';
            $css .= "--wpac-letter-spacing: {$letter_spacing};";
        }
        
        // Contraste
        if ($settings['contrast'] !== 'normal') {
            if (isset($contrasts[$settings['contrast']]) && $contrasts[$settings['contrast']]) {
                $css .= "--wpac-contrast: {$contrasts[$settings['contrast']]};";
            }
        }
        
        // Taille du curseur
        if ($settings['cursor_size'] !== 'normal') {
            $cursor_size = isset($cursors[$settings['cursor_size']]) ? $cursors[$settings['cursor_size']] : '16px';
            $css .= "--wpac-cursor-size: {$cursor_size};";
        }
        
        // Alignement du texte
        if ($settings['text_alignment'] !== 'left') {
            $css .= "--wpac-text-align: {$settings['text_alignment']};";
        }
        
        $css .= '}';
        
        // Application des styles
        $css .= 'body {';
        if (isset($settings['font_size']) && $settings['font_size'] !== 'normal') {
            $css .= 'font-size: var(--wpac-font-size);';
        }
        if (isset($settings['font_family']) && $settings['font_family'] !== 'default') {
            $css .= 'font-family: var(--wpac-font-family);';
        }
        if (isset($settings['line_height']) && $settings['line_height'] !== 'normal') {
            $css .= 'line-height: var(--wpac-line-height);';
        }
        if (isset($settings['letter_spacing']) && $settings['letter_spacing'] !== 'normal') {
            $css .= 'letter-spacing: var(--wpac-letter-spacing);';
        }
        if (isset($settings['contrast']) && $settings['contrast'] !== 'normal' && isset($contrasts[$settings['contrast']])) {
            $css .= $contrasts[$settings['contrast']];
        }
        if (isset($settings['text_alignment']) && $settings['text_alignment'] !== 'left') {
            $css .= 'text-align: var(--wpac-text-align);';
        }
        $css .= '}';
        
        // Curseur personnalisé
        if (isset($settings['cursor_size']) && $settings['cursor_size'] !== 'normal') {
            $css .= '* { cursor: url(' . WP_ACCESSIBILITY_CONTROLS_URL . 'assets/images/cursor.cur), auto !important; }';
        }
        
        echo $css;
    }

    public function add_accessibility_panel() {
        ?>
        <div id="wpac-panel" class="wpac-panel">
            <div class="wpac-panel-header">
                <h2><?php _e('Paramètres d\'accessibilité', 'wp-accessibility-controls'); ?></h2>
                <button class="wpac-close-btn" aria-label="<?php _e('Fermer', 'wp-accessibility-controls'); ?>">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="wpac-panel-content">
                <!-- Toggle Dyslexie en premier pour un accès facile -->
                <div class="wpac-control-group wpac-toggle-group">
                    <div class="wpac-toggle-wrapper">
                        <label class="wpac-toggle-label">
                            <span class="wpac-toggle-icon">🔤</span>
                            <?php _e('Mode Dyslexie', 'wp-accessibility-controls'); ?>
                        </label>
                        <label class="wpac-switch">
                            <input type="checkbox" id="wpac-dyslexia-mode">
                            <span class="wpac-slider"></span>
                        </label>
                    </div>
                    <p class="wpac-toggle-description"><?php _e('Active la police Lexend optimisée pour la lecture', 'wp-accessibility-controls'); ?></p>
                </div>

                <div class="wpac-control-group">
                    <label><?php _e('Taille du texte', 'wp-accessibility-controls'); ?></label>
                    <div class="wpac-slider-wrapper">
                        <input type="range" id="wpac-font-size" min="16" max="70" value="16" step="1">
                        <div class="wpac-slider-labels">
                            <span>16px</span>
                            <span id="wpac-font-size-value">16px</span>
                            <span>70px</span>
                        </div>
                    </div>
                </div>

                <div class="wpac-control-group">
                    <label><?php _e('Hauteur de ligne', 'wp-accessibility-controls'); ?></label>
                    <select id="wpac-line-height">
                        <option value="tight"><?php _e('Serré', 'wp-accessibility-controls'); ?></option>
                        <option value="normal" selected><?php _e('Normal', 'wp-accessibility-controls'); ?></option>
                        <option value="relaxed"><?php _e('Relâché', 'wp-accessibility-controls'); ?></option>
                    </select>
                </div>

                <div class="wpac-control-group">
                    <label><?php _e('Espacement des lettres', 'wp-accessibility-controls'); ?></label>
                    <select id="wpac-letter-spacing">
                        <option value="tight"><?php _e('Serré', 'wp-accessibility-controls'); ?></option>
                        <option value="normal" selected><?php _e('Normal', 'wp-accessibility-controls'); ?></option>
                        <option value="wide"><?php _e('Large', 'wp-accessibility-controls'); ?></option>
                        <option value="extra-wide"><?php _e('Très large', 'wp-accessibility-controls'); ?></option>
                    </select>
                </div>

                <div class="wpac-control-group">
                    <label><?php _e('Contraste', 'wp-accessibility-controls'); ?></label>
                    <select id="wpac-contrast">
                        <option value="low"><?php _e('Faible', 'wp-accessibility-controls'); ?></option>
                        <option value="normal" selected><?php _e('Normal', 'wp-accessibility-controls'); ?></option>
                        <option value="high"><?php _e('Élevé', 'wp-accessibility-controls'); ?></option>
                        <option value="inverted"><?php _e('Inversé', 'wp-accessibility-controls'); ?></option>
                    </select>
                </div>

                <div class="wpac-control-group">
                    <label><?php _e('Alignement du texte', 'wp-accessibility-controls'); ?></label>
                    <select id="wpac-text-alignment">
                        <option value="left" selected><?php _e('Gauche', 'wp-accessibility-controls'); ?></option>
                        <option value="center"><?php _e('Centre', 'wp-accessibility-controls'); ?></option>
                        <option value="right"><?php _e('Droite', 'wp-accessibility-controls'); ?></option>
                        <option value="justify"><?php _e('Justifié', 'wp-accessibility-controls'); ?></option>
                    </select>
                </div>

                <div class="wpac-actions">
                    <button class="wpac-btn wpac-btn-reset" id="wpac-reset">
                        <?php _e('Réinitialiser', 'wp-accessibility-controls'); ?>
                    </button>
                </div>
            </div>
        </div>

        <button id="wpac-toggle-btn" class="wpac-toggle-btn" aria-label="<?php _e('Ouvrir les paramètres d\'accessibilité', 'wp-accessibility-controls'); ?>" title="<?php _e('Paramètres d\'accessibilité', 'wp-accessibility-controls'); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                <path d="M12 9V5M12 19V15M9 12H5M19 12H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <path d="M15 9L17 7M9 15L7 17M9 15L11 17M17 7L15 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        <?php
    }
}

function wp_accessibility_controls_init() {
    new WP_Accessibility_Controls();
}
add_action('plugins_loaded', 'wp_accessibility_controls_init');

