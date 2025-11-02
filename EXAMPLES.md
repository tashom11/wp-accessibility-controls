# WP Accessibility Controls - Exemples d'utilisation

## Personnalisation des paramètres par défaut

Vous pouvez filtrer les paramètres par défaut du plugin dans votre thème ou un autre plugin :

```php
add_filter('wpac_default_settings', function($defaults) {
    $defaults['font_size'] = 'large';
    $defaults['contrast'] = 'high';
    return $defaults;
});
```

## Ajouter des polices personnalisées

Pour ajouter vos propres polices au menu déroulant :

```php
add_filter('wpac_font_families', function($fonts) {
    $fonts['custom-font'] = 'MaPolice, sans-serif';
    return $fonts;
});
```

## Personnaliser les styles CSS

Surchargez les styles du plugin dans votre thème enfant :

```css
/* Dans le fichier style.css de votre thème enfant */

/* Changer la couleur du bouton toggle */
.wpac-toggle-btn {
    background: #d63638 !important;
}

/* Personnaliser le panel */
.wpac-panel-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Modifier l'apparence des sélecteurs */
.wpac-control-group select {
    background-image: url('data:image/svg+xml...');
    border-radius: 8px;
}
```

## Intégrer avec d'autres plugins d'accessibilité

Le plugin peut fonctionner en complément d'autres outils d'accessibilité :

```php
// S'assurer que les styles sont appliqués après d'autres plugins
add_action('wp_enqueue_scripts', function() {
    wp_dequeue_style('other-accessibility-plugin');
}, 20);
```

## Personnaliser les textes traduits

Tous les textes du plugin sont internationalisables :

```php
// Traduire les labels dans votre langue
add_filter('gettext', function($translated_text, $text, $domain) {
    if ($domain === 'wp-accessibility-controls') {
        if ($text === 'Paramètres d\'accessibilité') {
            return 'Mes Réglages d\'Accessibilité';
        }
    }
    return $translated_text;
}, 10, 3);
```

## Désactiver certaines fonctionnalités

Vous pouvez masquer certaines options pour simplifier l'interface :

```php
add_action('wp_footer', function() {
    ?>
    <style>
        /* Masquer l'option de taille de curseur */
        #wpac-cursor-size,
        #wpac-cursor-size ~ br {
            display: none !important;
        }
    </style>
    <?php
}, 999);
```

## Ajouter des notifications personnalisées

Informez les utilisateurs après qu'ils aient modifié un paramètre :

```javascript
jQuery(document).ready(function($) {
    $(document).on('change', '.wpac-control-group select', function() {
        console.log('Paramètre modifié :', $(this).attr('id'));
        // Ajouter votre code personnalisé ici
    });
});
```

## Intégration avec les préférences système

Détecter les préférences d'accessibilité du système :

```php
add_action('wp_head', function() {
    ?>
    <script>
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.documentElement.classList.add('reduced-motion');
        }
    </script>
    <style>
        .reduced-motion .wpac-panel {
            transition: none;
        }
    </style>
    <?php
});
```

## Sauvegarder les paramètres dans la base de données

Par défaut, les utilisateurs connectés ont leurs paramètres sauvegardés automatiquement. Pour les visiteurs anonymes, vous pouvez utiliser des cookies personnalisés ou du localStorage.

## Tests d'accessibilité

Le plugin est testé avec :
- NVDA (Windows)
- JAWS (Windows)
- VoiceOver (macOS/iOS)
- Narrateur Windows
- TalkBack (Android)

## Support des navigateurs

Testé et fonctionnel sur :
- Chrome/Edge (dernières versions)
- Firefox (dernières versions)
- Safari (dernières versions)
- Opéra (dernières versions)

