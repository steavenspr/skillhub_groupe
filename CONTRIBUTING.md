# CONTRIBUTING.md

## Guide de contribution — SkillHub Bloc 03

Bienvenue ! Ce document formalise les règles de contribution au projet SkillHub pour le Bloc 03 (Cloud, DevOps, Architecture).

---

## 📋 Rôles de l'équipe

| Rôle | Responsabilités principales |
|---|---|
| **Tech Lead** | Gouvernance Git, revues PR, qualité code, cohérence front/back |
| **Cloud Architect** | Architecture cloud, diagrammes C4, rapport d'audit, budget |
| **DevOps Engineer** | Dockerisation, CI/CD, orchestration, sécurité infra |

---

## 🌿 Stratégie de branches

### Branches principales
- **`main`** : branche de production, code stable uniquement, protégée
- **`develop`** : branche d'intégration, accumule les fonctionnalités validées

### Branches de travail
- **`feature/<nom-court>`** : nouvelle fonctionnalité (ex. `feature/add-jwt-auth`)
- **`fix/<nom-court>`** : correction de bug (ex. `fix/enrollment-validation`)
- **`docker/*`** ou **`ci/*`** : tâches infra (ex. `docker/backend-multistage`)

### Flux de travail recommandé
1. Partir de `develop` à jour
2. Créer une branche `feature/*` ou `fix/*`
3. Faire des commits atomiques et testés localement
4. Pousser et ouvrir une Pull Request (PR)
5. Attendre la revue Tech Lead et les validations CI
6. Après approbation et merge, la branche est supprimée

### Protections de branches
- `main` : aucun commit direct, PR obligatoire, CI doit passer
- `develop` : aucun commit direct, PR + 1 revue Tech Lead minimum

---

## 📝 Convention des commits

Nous utilisons **Conventional Commits** pour clarifier l'historique Git.

### Format
```
<type>(<scope>): <description>

[optional body]

[optional footer]
```

### Types autorisés
- **feat** : nouvelle fonctionnalité (ex. `feat(api): add user profile endpoint`)
- **fix** : correction de bug (ex. `fix(auth): resolve jwt expiration handling`)
- **docs** : mise à jour documentation (ex. `docs(readme): add docker setup instructions`)
- **style** : formatage, pas de logique (ex. `style(frontend): fix linting errors`)
- **refactor** : refonte sans changement fonctionnel (ex. `refactor(api): simplify error handling`)
- **test** : ajout/modification tests (ex. `test(backend): add formation controller tests`)
- **docker** : Dockerfile, docker-compose, infra (ex. `docker: add multi-stage build`)
- **ci** : pipeline, workflows (ex. `ci: add GitHub Actions workflow`)
- **chore** : maintenance, dépendances (ex. `chore: update dependencies`)

### Scopes recommandés
- `api`, `backend`, `auth`, `formations`, `modules`, `enrollments`, `logs`
- `frontend`, `ui`, `routing`, `services`, `validation`
- `infra`, `docker`, `k8s`, `monitoring`

### Exemples valides
```
feat(api): implement JWT authentication middleware
fix(frontend): correct enrollment button state after subscription
docs(readme): clarify docker compose setup for windows
docker: add redis service to docker-compose.yml
ci: configure GitHub Actions for automated testing
chore: bump laravel version to 13.1.0
test(backend): add unit tests for formation controller
```

---

## 🔀 Processus de Pull Request (PR)

### Avant d'ouvrir une PR
1. Vérifier que votre code compile et fonctionne localement
2. Lancer les validations minimales :
   - **Backend** : `composer run test` + `php artisan optimize:clear`
   - **Frontend** : `npm run build` + `npm run lint`
3. Documenter les changements (README, code comments, etc.)

### Créer une PR
1. Titre court et explicite : respecter le format des commits
   - ✅ `feat(api): add GET /api/users endpoint`
   - ❌ `Fix stuff`, `Update`, `Various changes`
2. Description incluant :
   - Quelle est la fonctionnalité / le bug ?
   - Comment le changement le résout-il ?
   - **Pour les changements API** : décrire la route, le payload attendu, les codes d'erreur
   - Tests effectués localement
   - Impacts éventuels sur frontend/backend si applicable
3. Lier les issues si applicable : `Closes #42`

### Exemple de bonne description PR
```
## Description
Ajoute un endpoint API pour récupérer le profil utilisateur connecté.

## Type de changement
- [x] Nouvelle fonctionnalité
- [ ] Correction de bug
- [ ] Documentation

## API
- **Route** : `GET /api/profile`
- **Auth** : JWT obligatoire
- **Payload réponse** : `{ id, prenom, nom, email, role, date_creation }`
- **Codes d'erreur** : 401 si non authentifié

## Tests localement
- [x] Frontend peut appeler l'endpoint
- [x] Token JWT valide fonctionne
- [x] Erreur 401 sans token
- [x] Response validée contre le contrat OpenAPI

## Impacts
Mise à jour requise dans `frontend/src/services/authService.js`
```

### Révision de PR
- Le Tech Lead relira tout changement vers `develop` ou `main`
- Au minimum : vérifier la qualité, cohérence, absence de secrets
- Pour l'API : valider le contrat avec le frontend
- Demandes de modification doivent être claires et expliquées

### Critères d'acceptation pour merge
- ✅ CI passe (tests, lint, build)
- ✅ Au moins 1 revue Tech Lead approuvée
- ✅ Pas de conflits avec la branche cible
- ✅ Commits en Conventional Commits
- ✅ Aucun secret, pas de `.env`, pas de credentials
- ✅ Documentation pertinente mise à jour

---

## 🔐 Règles de sécurité

### Ne JAMAIS committer
- `.env` ou autres fichiers d'environnement (utilisez `.env.example` commenté)
- Credentials, clés API, tokens JWT générés
- Mots de passe en dur
- Fichiers `vendor/`, `node_modules/`, `dist/` générés
- Images Docker (poussez vers la registry uniquement)

### Vérifier avant de pousser
```bash
# Afficher les changements avant commit
git diff --cached

# Chercher les patterns dangereux
git diff --cached | grep -i "password\|secret\|api.key\|token"
```

### En cas de fuite accidentelle
1. Annuler le commit : `git reset --soft HEAD~1`
2. Supprimer le fichier sensible
3. Re-committer proprement
4. Signaler au Tech Lead immédiatement

---

## 📦 Révision de code — Points clés

Le Tech Lead effectuera les vérifications suivantes sur toute PR :

### Qualité générale
- [ ] Code lisible, noms explicites
- [ ] Pas de duplication inutile
- [ ] Pas de code commenté ou debug (`console.log`, `var_dump`)
- [ ] Erreurs gérées proprement

### Architecture
- [ ] Logique métier isolée des appels API / UI
- [ ] Séparation des responsabilités respectée
- [ ] Pas de "quick fixes" sans refonte

### Frontend/Backend cohérence
- [ ] Routes API correspondent aux appels frontend
- [ ] Payloads front/back alignés
- [ ] Codes d'erreur et validation cohérents
- [ ] Authentication / autorisation correctes

### Tests et documentation
- [ ] Tests existent et passent si applicable
- [ ] README/docs mis à jour si changement significatif
- [ ] Changements d'API documentés dans OpenAPI si applicable

### Sécurité
- [ ] Pas de secrets en dur
- [ ] Validations effectuées côté serveur
- [ ] JWT tokens utilisés correctement
- [ ] Inputs validés et échappés

---

## 🚀 Définition of Done (DoD)

Une tâche est considérée comme terminée si :

- ✅ Le code compile et s'exécute localement
- ✅ Les tests unitaires/fonctionnels passent
- ✅ Les changements d'API sont testés avec le frontend
- ✅ La documentation pertinente est mise à jour
- ✅ La PR est lisible, bien décrite, reviewable
- ✅ Aucun secret, pas de `.env`, pas de `node_modules`
- ✅ Approuvée par Tech Lead et CI passe
- ✅ Aucun conflit avec la branche cible

---

## 📚 Ressources supplémentaires

- **README racine** : vue globale du projet et stack
- **backend/README.md** : guide spécifique backend, API, routes
- **frontend/README.md** : guide spécifique frontend, pages, services
- **backend/docs/openapi.yaml** : contrat API (source de vérité)
- **CDC_Bloc03_SkillHub.pdf** : cahier des charges et livrables

---

## 💬 Questions / Problèmes ?

- **Doute sur une branche ou commit ?** Demande au Tech Lead
- **Conflit Git ?** Voir la section "Résolution de conflits" ci-dessous
- **Secret accidentellement poussé ?** Signale immédiatement

---

## 🔧 Résolution de conflits

### Cas : développement en parallèle sur `develop`

Vous avez fait un changement sur `feature/mon-feature`, mais quelqu'un d'autre a aussi modifié les mêmes fichiers sur `develop`.

**Étapes** :
1. Mettre à jour votre branche locale `develop` : `git fetch origin && git checkout develop && git pull origin develop`
2. Revenir à votre branche : `git checkout feature/mon-feature`
3. Rebaser ou merger : 
   - **Rebase** (linéaire, recommandé) : `git rebase develop`
   - **Merge** (historique préservé) : `git merge develop`
4. Résoudre les conflits manuellement dans l'éditeur
5. Ajouter les fichiers résolus : `git add <fichiers>`
6. Continuer le rebase : `git rebase --continue` (ou finaliser le merge)
7. Pousser : `git push --force-with-lease origin feature/mon-feature`

---

## 🎯 Checklist Tech Lead avant release

Avant de merger une PR vers `main` :

- [ ] Tous les commits sont en Conventional Commits
- [ ] Tests passent, couverture acceptable
- [ ] Documentation mise à jour (README, OpenAPI, etc.)
- [ ] Aucun secret dans le dépôt
- [ ] Changelog complété si applicable
- [ ] Version bumped si applicable (semantic versioning)
- [ ] Tag créé si release : `git tag -a v1.0.0 -m "Release 1.0.0"`

---

**Dernière mise à jour** : 21 avril 2026  
**Responsable** : Tech Lead Bloc 03  
**Prochaine révision** : mi-mai 2026

