# WP Accessibility Controls

Un plugin WordPress pour améliorer l'accessibilité de votre site web en permettant aux visiteurs de personnaliser les paramètres d'affichage en temps réel.

## 🎯 Fonctionnalités

Le plugin ajoute un panneau de contrôle d'accessibilité avec les options suivantes :

- **🔤 Mode Dyslexie** : Toggle ON/OFF pour activer la police Lexend (optimisée pour la dyslexie)
- **📏 Taille du texte** : Slider ajustable de 16px à 70px
- **📏 Hauteur de ligne** : 3 niveaux (Serré, Normal, Relâché)
- **🔤 Espacement des lettres** : 4 niveaux (Serré, Normal, Large, Très large)
- **🎨 Contraste** : 4 modes (Faible, Normal, Élevé, Inversé)
- **📝 Alignement du texte** : 4 options (Gauche, Centre, Droite, Justifié)

## 🔧 Installation

1. **Méthode manuelle** :
   - Téléchargez le plugin
   - Uploadez le dossier `wp-accessibility-controls` dans `/wp-content/plugins/`
   - Activez le plugin dans WordPress Admin → Extensions

2. **Via Git** :
   ```bash
   cd wp-content/plugins
   git clone https://github.com/tashom11/wp-accessibility-controls.git
   ```

## 📖 Utilisation

Une fois activé, le plugin affiche automatiquement un bouton flottant en bas à droite de votre site. Les visiteurs peuvent :

1. Cliquer sur le bouton d'accessibilité 🔘
2. Ouvrir le panneau de contrôle
3. Ajuster les paramètres selon leurs besoins
4. Les modifications s'appliquent immédiatement
5. Les paramètres sont sauvegardés pour les prochaines visites

### Persistance des paramètres

- **Tous les utilisateurs** : Les paramètres sont sauvegardés dans le localStorage du navigateur (valable indéfiniment)
- **Application immédiate** : Tous les changements sont appliqués en temps réel sans rechargement de page

### Police Lexend pour la Dyslexie

Le plugin utilise la police **Lexend** de Google Fonts, spécialement conçue pour améliorer la lisibilité des personnes dyslexiques. Elle est chargée automatiquement depuis Google Fonts CDN et ne nécessite aucune configuration supplémentaire.

## 🎨 Personnalisation

Vous pouvez personnaliser les styles du plugin en surchargeant les classes CSS suivantes :

```css
/* Bouton toggle */
.wpac-toggle-btn { }

/* Panel */
.wpac-panel { }

/* Contenu du panel */
.wpac-panel-content { }

/* Contrôles */
.wpac-control-group { }

/* Toggle Dyslexie */
.wpac-toggle-group { }

/* Slider */
.wpac-slider-wrapper { }
```

## 📱 Responsive

Le plugin est entièrement responsive et s'adapte à tous les écrans :
- Desktop : Panel en bas à droite
- Mobile : Panel en plein écran
- Tablette : Adaptation automatique

## ♿ Accessibilité

Le plugin respecte les standards d'accessibilité WCAG :
- Support du clavier (Tab, Enter, Escape)
- Attributs ARIA appropriés
- Contraste élevé
- Mode sombre automatique si activé par l'utilisateur
- Compatible avec les lecteurs d'écran

## 🔒 Sécurité

- Vérification des nonces pour toutes les requêtes AJAX
- Nettoyage des données d'entrée
- Échappement des sorties
- Protection CSRF

## 📋 Compatibilité

- **WordPress** : 5.0+
- **PHP** : 7.4+
- **Testé jusqu'à** : WordPress 6.4

## 🐛 Dépannage

Si le bouton n'apparaît pas :
1. Vérifiez que le plugin est activé
2. Vide le cache de votre navigateur
3. Videz le cache WordPress si vous utilisez un plugin de cache
4. Vérifiez la console JavaScript pour les erreurs

## 📝 Changelog

### Version 1.0.0
- Version initiale
- Panel d'accessibilité complet
- 7 paramètres configurables
- Persistance des paramètres
- Design responsive
- Support mode sombre

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :
- Signaler des bugs
- Proposer de nouvelles fonctionnalités
- Améliorer la documentation
- Soumettre des pull requests

## 📄 Licence

Ce plugin est sous licence GPL v2 ou ultérieure.

## 👤 Auteur

Développé pour améliorer l'accessibilité web et rendre Internet plus inclusif.

---

**Aidez à rendre le web plus accessible ! ♿**

