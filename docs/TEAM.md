# TEAM.md — Composition et Rôles

## Équipe SkillHub Bloc 03

### Membres

| Nom | Rôle | Responsabilités | Contact/GitHub |
|---|---|---|---|
| Steavens | Tech Lead | Gouvernance Git, revues PR, qualité code, cohérence front/back | @steavenspr |
| Mahery | DevOps Engineer | Dockerisation, CI/CD, orchestration, sécurité infra | @Mahery23 |
| Nicia | Cloud Architect | Architecture cloud, rapport d'audit, C4, budget, scalabilité | - |

---

## Livrables par rôle

### Tech Lead — Steavens

Responsable de la qualité technique globale et de la gouvernance du projet.

Livrables principaux :
- CONTRIBUTING.md : formalisation des règles Git et du workflow
- Revues de Pull Requests : validation code, cohérence, sécurité
- Arbitrage des choix techniques : frontend/backend, conflits d'architecture
- Maintien de la documentation : README, guides, conventions
- Vérification de la qualité : tests, linting, documentation

### DevOps Engineer — Mahery

Responsable de l'industrialisation et de l'automatisation.

Livrables principaux :
- Dockerfiles backend et frontend (multi-stage)
- docker-compose.yml : orchestration complète de la stack
- Pipeline CI/CD : linting, tests, build, push d'images
- Healthchecks et orchestration : limites ressources, restart policies
- Sécurité infra : secrets management, images taggées, workflow sécurisé

### Cloud Architect — Nicia

Responsable de la stratégie cloud et de l'architecture.

Livrables principaux :
- Rapport d'audit cloud : analyse des besoins, comparaison offres
- Diagrammes C4 : Context (C1) et Container (C2)
- Plan budgtaire : coûts N1 (500 users) et N2 (10k users)
- Plan sécurité : IAM, chiffrement, secrets, RTO/RPO, RGPD
- Recommandation cloud : plateforme choisie et justification

---

## Conventions de collaboration

### Répartition du travail

- **Backend/API** : tout le monde peut y toucher, revue par Steavens
- **Frontend** : tout le monde peut y toucher, revue par Steavens
- **Docker/CI/CD** : lead par Mahery, revue par Steavens
- **Rapport cloud** : lead par Nicia, validation par Steavens

### Critères de validation avant merge

Avant que toute PR soit mergée vers `develop` ou `main`, Steavens valide :

1. Respect des Conventional Commits
2. Tests qui passent (backend + frontend)
3. Pas de secrets committés
4. Documentation mise à jour si applicable
5. Code lisible et maintenable
6. Cohérence API/Frontend si changement d'interface

### Fréquence de réunion

- Hebdomadaire : point d'avancement sur les livrables Bloc 03
- Au besoin : sync rapide sur blocages techniques

---

## Contrats de communication

### Pour Steavens (Tech Lead)

Si tu bloques :
- PR sans revue → demande à qui tu veux que la fasse (Mahery ou Nicia peuvent aussi)
- Conflit sur choix technique → arbitrage à la prochaine réunion
- Doute sur un commit → tu ouvres l'issue, on en discute

### Pour Mahery (DevOps)

Si tu as besoin :
- De l'API modifiée pour Docker → ouvre une PR ou demande à Steavens
- De secrets d'env → on détermine ensemble la stratégie (GitHub Secrets, etc.)
- Feedback sur une PR frontend → ping Steavens qui tranche

### Pour Nicia (Cloud Architect)

Si tu as besoin :
- De comprendre comment ça va s'exécuter → demande à Mahery (DevOps)
- De valider une décision tech → Steavens
- De clarifier un besoin fonctionnel → backend/frontend devs

---

## Points de repère documentaires

| Document | Maintenu par | Consulter avant de |
|---|---|---|
| CONTRIBUTING.md | Steavens | Commit / PR |
| README.md racine | Steavens | Starter du projet |
| backend/README.md | Steavens + dev backend | Coder sur l'API |
| frontend/README.md | Steavens + dev frontend | Coder sur l'UI |
| docker-compose.yml | Mahery | Lancer en local |
| Dockerfile* | Mahery | Modifier l'infra |
| .github/workflows/* | Mahery | Modifier la CI/CD |
| Rapport cloud | Nicia | Comprendre la stratégie infra |

---

Dernière mise à jour : 21 avril 2026

