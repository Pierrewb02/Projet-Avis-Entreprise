#  AvisPro – Plateforme d’Analyse Automatisée des Avis Clients

AvisPro est une solution SaaS permettant aux entreprises d’analyser automatiquement la satisfaction de leurs clients grâce à un moteur d’analyse sémantique interne et une architecture Full‑Stack moderne.

---

##  Architecture du Projet

### 🔹 Backend – API REST (Laravel 11)
- Framework Laravel 11
- Authentification sécurisée via Sanctum
- ORM Eloquent pour la gestion des relations
- Migrations pour un travail collaboratif propre
- Analyse automatique des avis via un service interne (`AnalysisService`)

### 🔹 Frontend – SPA (Vue.js 3)
- Application Single Page Application
- Interface réactive et fluide
- Dashboard professionnel avec indicateurs visuels
- Composants dynamiques pour afficher les résultats de l’analyse

---

##  Moteur d’Analyse Sémantique

Un moteur interne analyse chaque commentaire pour produire un score de sentiment :

- Analyse basée sur un dictionnaire sémantique pondéré  
- Score compris entre 0 et 1  
- Conversion en badges visuels : **Positif / Neutre / Négatif**  
- Recalcul automatique lors de la modification d’un avis (PUT)

---

##  Gestion des Rôles & Sécurité (RBAC)

- Isolation stricte des données par entreprise  
- Middlewares pour protéger les routes sensibles  
- Accès administrateur réservé  
- Accès entreprise limité à ses propres avis et statistiques  

---

##  UX / UI

- Interface moderne inspirée de Material Design  
- Menu profil dynamique (Popover)  
- Visualisation claire des KPI  
- Dashboard réactif basé sur les données de l’IA  

---

##  Workflow Git & Collaboration

- Branches séparées pour le backend et le frontend  
- Historique clair des contributions  
- Utilisation de `.env.example` pour partager la structure sans exposer les secrets  
- Commits réguliers et structurés  

---

##  Perspectives d’Évolution

- Notifications en temps réel (email) en cas d’avis critique  
- Génération automatique de rapports PDF mensuels  
- Système d’avis vérifiés pour garantir l’authenticité  

---

##  Rapport du Projet

Le rapport complet est disponible ici :  
 **[Docs/Rapport-avispro.pdf](Docs/Rapport-avispro.pdf)**

---

##  Auteur

Projet réalisé par **JP et OSCARINE**, dans le cadre du développement d’une plateforme d’analyse d’avis clients.

