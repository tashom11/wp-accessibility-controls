/**
 * WP Accessibility Controls - Script
 */

(function($) {
    'use strict';

    const WPAC = {
        dynamicStyles: null,
        
        init: function() {
            this.loadSettings();
            this.createDynamicStyleSheet();
            this.bindEvents();
        },

        loadSettings: function() {
            let settings = {};
            
            // Charger depuis localStorage en priorité
            const localSettings = localStorage.getItem('wpac_settings');
            if (localSettings) {
                try {
                    settings = JSON.parse(localSettings);
                } catch (e) {
                    console.error('Erreur lors du chargement des paramètres depuis localStorage', e);
                }
            }
            
            // Si pas de localStorage, essayer les données PHP
            if (!localSettings && wpacData && wpacData.settings) {
                settings = wpacData.settings;
            }
            
            // Appliquer les paramètres par défaut
            const defaultSettings = {
                dyslexia_mode: false,
                font_size: 16,
                line_height: 'normal',
                letter_spacing: 'normal',
                contrast: 'normal',
                cursor_size: 'normal',
                text_alignment: 'left'
            };
            settings = $.extend({}, defaultSettings, settings);
            
            $('#wpac-dyslexia-mode').prop('checked', settings.dyslexia_mode || false);
            $('#wpac-font-size').val(settings.font_size || 16);
            $('#wpac-font-size-value').text((settings.font_size || 16) + 'px');
            $('#wpac-line-height').val(settings.line_height || 'normal');
            $('#wpac-letter-spacing').val(settings.letter_spacing || 'normal');
            $('#wpac-contrast').val(settings.contrast || 'normal');
            $('#wpac-cursor-size').val(settings.cursor_size || 'normal');
            $('#wpac-text-alignment').val(settings.text_alignment || 'left');
            
            // Appliquer immédiatement les styles sauvegardés
            this.applyStyles(settings);
        },

        createDynamicStyleSheet: function() {
            // Créer une balise style pour les modifications dynamiques
            if (!this.dynamicStyles) {
                this.dynamicStyles = $('<style id="wpac-dynamic-styles"></style>');
                $('head').append(this.dynamicStyles);
            }
        },

        bindEvents: function() {
            // Toggle panel
            $('#wpac-toggle-btn').on('click', function(e) {
                e.preventDefault();
                $('#wpac-panel').toggleClass('active');
                $(this).toggleClass('active');
            });

            // Close panel
            $('.wpac-close-btn').on('click', function(e) {
                e.preventDefault();
                $('#wpac-panel').removeClass('active');
                $('#wpac-toggle-btn').removeClass('active');
            });

            // Toggle dyslexie mode
            $('#wpac-dyslexia-mode').on('change', function() {
                const settings = {
                    dyslexia_mode: $(this).is(':checked'),
                    font_size: $('#wpac-font-size').val(),
                    line_height: $('#wpac-line-height').val(),
                    letter_spacing: $('#wpac-letter-spacing').val(),
                    contrast: $('#wpac-contrast').val(),
                    cursor_size: $('#wpac-cursor-size').val(),
                    text_alignment: $('#wpac-text-alignment').val()
                };
                
                // Appliquer immédiatement les styles
                WPAC.applyStyles(settings);
                
                // Puis sauvegarder en arrière-plan
                WPAC.saveSettings(settings);
            });

            // Slider de taille de texte
            $('#wpac-font-size').on('input', function() {
                const value = $(this).val();
                $('#wpac-font-size-value').text(value + 'px');
                
                const settings = {
                    dyslexia_mode: $('#wpac-dyslexia-mode').is(':checked'),
                    font_size: parseInt(value),
                    line_height: $('#wpac-line-height').val(),
                    letter_spacing: $('#wpac-letter-spacing').val(),
                    contrast: $('#wpac-contrast').val(),
                    cursor_size: $('#wpac-cursor-size').val(),
                    text_alignment: $('#wpac-text-alignment').val()
                };
                
                // Appliquer immédiatement les styles
                WPAC.applyStyles(settings);
                
                // Puis sauvegarder en arrière-plan
                WPAC.saveSettings(settings);
            });

            // Apply settings when control changes - APPLIQUER IMMÉDIATEMENT
            $('.wpac-control-group select').on('change', function() {
                const settings = {
                    dyslexia_mode: $('#wpac-dyslexia-mode').is(':checked'),
                    font_size: $('#wpac-font-size').val(),
                    line_height: $('#wpac-line-height').val(),
                    letter_spacing: $('#wpac-letter-spacing').val(),
                    contrast: $('#wpac-contrast').val(),
                    cursor_size: $('#wpac-cursor-size').val(),
                    text_alignment: $('#wpac-text-alignment').val()
                };
                
                // Appliquer immédiatement les styles
                WPAC.applyStyles(settings);
                
                // Puis sauvegarder en arrière-plan
                WPAC.saveSettings(settings);
            });

            // Reset settings
            $('#wpac-reset').on('click', function(e) {
                e.preventDefault();
                WPAC.resetSettings();
            });

            // Close panel on escape key
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('#wpac-panel').hasClass('active')) {
                    $('#wpac-panel').removeClass('active');
                    $('#wpac-toggle-btn').removeClass('active');
                }
            });

            // Prevent panel from closing when clicking inside
            $('#wpac-panel').on('click', function(e) {
                e.stopPropagation();
            });

            // Close panel when clicking outside
            $(document).on('click', function(e) {
                if ($('#wpac-panel').hasClass('active') && !$(e.target).closest('#wpac-panel, #wpac-toggle-btn').length) {
                    $('#wpac-panel').removeClass('active');
                    $('#wpac-toggle-btn').removeClass('active');
                }
            });
        },

        applyStyles: function(settings) {
            if (!this.dynamicStyles) {
                this.createDynamicStyleSheet();
            }
            
            let css = '';
            
            // Taille de police - utiliser directement les pixels
            const fontSize = settings.font_size + 'px';
            
            // Famille de police - Mode dyslexie active Lexend
            let fontFamily = 'inherit';
            if (settings.dyslexia_mode) {
                fontFamily = '"Lexend", sans-serif';
            }
            
            // Hauteur de ligne
            const lineHeights = {
                tight: '1.25',
                normal: '1.6',
                relaxed: '2'
            };
            const lineHeight = lineHeights[settings.line_height] || '1.6';
            
            // Espacement des lettres
            const letterSpacings = {
                tight: '-0.05em',
                normal: '0',
                wide: '0.1em',
                'extra-wide': '0.2em'
            };
            const letterSpacing = letterSpacings[settings.letter_spacing] || '0';
            
            // Contraste
            const contrasts = {
                low: 'filter: contrast(0.6) brightness(1.2);',
                normal: '',
                high: 'filter: contrast(1.5) brightness(1.15);',
                inverted: 'filter: invert(1) hue-rotate(180deg);'
            };
            const contrast = contrasts[settings.contrast] || '';
            
            // Text alignment
            const textAlign = ['left', 'center', 'right', 'justify'].includes(settings.text_alignment) ? settings.text_alignment : 'left';
            
            // Construire le CSS
            // Appliquer au body mais exclure le panel
            css += 'body {';
            css += 'font-size: ' + fontSize + ' !important; ';
            css += 'font-family: ' + fontFamily + ' !important; ';
            css += 'line-height: ' + lineHeight + ' !important; ';
            css += 'letter-spacing: ' + letterSpacing + ' !important; ';
            if (contrast) {
                css += contrast + ' ';
            }
            css += 'text-align: ' + textAlign + ' !important;';
            css += '}';
            
            // Exclure le panel des modifications de taille, police, etc.
            css += '#wpac-panel, #wpac-panel *, #wpac-toggle-btn {';
            css += 'font-size: 14px !important; ';
            css += 'font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif !important; ';
            css += 'line-height: 1.6 !important; ';
            css += 'letter-spacing: 0 !important; ';
            css += '}';
            
            // Exclure le panel du filtre de contraste
            css += '#wpac-panel, #wpac-panel *, #wpac-toggle-btn {';
            css += 'filter: none !important; ';
            css += '}';
            
            // Appliquer le CSS
            this.dynamicStyles.html(css);
        },

        saveSettings: function(settings) {
            // Si settings n'est pas fourni, les récupérer des selects
            if (!settings) {
                settings = {
                    dyslexia_mode: $('#wpac-dyslexia-mode').is(':checked'),
                    font_size: $('#wpac-font-size').val(),
                    line_height: $('#wpac-line-height').val(),
                    letter_spacing: $('#wpac-letter-spacing').val(),
                    contrast: $('#wpac-contrast').val(),
                    cursor_size: $('#wpac-cursor-size').val(),
                    text_alignment: $('#wpac-text-alignment').val()
                };
            }

            // Sauvegarder dans localStorage
            try {
                localStorage.setItem('wpac_settings', JSON.stringify(settings));
                console.log('Paramètres d\'accessibilité sauvegardés dans localStorage');
            } catch (e) {
                console.error('Impossible de sauvegarder dans localStorage', e);
            }
        },

        resetSettings: function() {
            // Réinitialiser les selects
            $('#wpac-dyslexia-mode').prop('checked', false);
            $('#wpac-font-size').val(16);
            $('#wpac-font-size-value').text('16px');
            $('#wpac-line-height').val('normal');
            $('#wpac-letter-spacing').val('normal');
            $('#wpac-contrast').val('normal');
            $('#wpac-cursor-size').val('normal');
            $('#wpac-text-alignment').val('left');
            
            // Construire les settings par défaut
            const defaultSettings = {
                dyslexia_mode: false,
                font_size: 16,
                line_height: 'normal',
                letter_spacing: 'normal',
                contrast: 'normal',
                cursor_size: 'normal',
                text_alignment: 'left'
            };
            
            // Appliquer immédiatement les styles par défaut
            this.applyStyles(defaultSettings);
            
            // Sauvegarder dans localStorage
            try {
                localStorage.setItem('wpac_settings', JSON.stringify(defaultSettings));
                console.log('Paramètres d\'accessibilité réinitialisés dans localStorage');
            } catch (e) {
                console.error('Impossible de sauvegarder dans localStorage', e);
            }
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        WPAC.init();
    });

})(jQuery);

