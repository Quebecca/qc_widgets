Qc Widgets
==============================================================
*La [version française](#documentation-qc-widgets) de la documentation suit le texte anglais*

## About
This extension provides a set of widgets for the TYPO3 Dasboard Backend module. Most widgets display lists of records related to the current logged in user.
The amount of records to display (default 25) can be changed in User or Group TSconfig. The list of widgets is:

- List of *my* group members
- Last created Pages in *my* groups
- Pages without modification
- My last modified pages
- My last modified content elements
- List of my Workspace preview links

![Four of the widgets](Documentation/Images/qc-widgets.png)

All the pages widget share the same information: uid, Title, Status, Creation date, Last modification date and Slug. A click on the UID or Title of the page will redirect to the Page Module, on the clicked page.

As usual, you can select which widgets are available to editors by managing the BE group rights for each widget.

## Details about the provided widgets :

### List of my groups members 
This widget display details (username, email, real name and last login) of users who belong to the same groups as the logged in user. 
If the current user is a TYPO3 administrator, the widget will display the list of administrators.

### Last created Pages in my groups
This widget displays the list of the recent pages created by members who belong to the same groups as the user. 

###  Pages without modification
This widget shows pages that haven't been touched for X months. Default is 3 months. Gives an overview of "neglected" pages. 

### My last modified pages
This widget allows to display the list of the pages last modified by the current user. 

### My last modified pages
Related to the logged in user. His recent work on pages.

### My last modified content elements
Related to the logged in user. His recent work on tt_content.

### List of my Workspace preview links
This widget displays a list (default 25) of Workspace preview links created by members who belong to the same groups as the current user. The columns are Workspace title, status (expired or active), creation date, expiration date and key (the key in links like `?ADMCMD_prev=8a636e5d5545c1bb1dec5a5f77a96ca4`.

## User and Group TSconfig

```php
mod{
    qcWidgets{
        //All limits have a default set to 25 if not set.
        lastCreatedPages{
            limit = 0
        }
        lastModifiedPages{
            limit = 0
        }
        workspaceProviderLinks{
            limit = 0
        }
        pagesWithoutModification{
            limit = 0
            numberOfMonths = 3
        }
        recentlyModifiedContent{
            limit = 0
        }
        listOfmembers{
        // When value is 0 (zero), the Widget will dig into sub-groups
            dontLookintoSubgroups = 1
        }
    }
}
```

-----------
[Version française]
# Documentation Qc Widgets

## À propos
Cette extension fournit un ensemble de "widgets" pour le module "Dashboard" de TYPO3. La plupart liste des enregistrements liés à l’utilisateur connecté.
La quantité d'enregistrements affichés est de 25 par défaut et peut être modifié par TSconfig de Groupe ou d'Utilisateur. Les widgets fournis sont:

- Liste  des utilisateur de mon groupe
- Dernières pages créées dans mes groupes
- Pages sans modification (depuis x mois)
- Mes dernières pages modifiées 
- Mes derniers contenus(tt_content) modifiés
- Liste des liens de prévisiualisation d'espaces de travail (Workspace)

Tous les Widgets de pages affichent les mêmes informations: uid, titre, état, date de création, date de modification and slug(url). Un clic sur le uid ou le titre ouvre le module Page sur l'enregistrement cliqué.

Comme d'habitude, on peut sélectionner quels widgets sont disponibles dans les droits d'utilisateur ou de groupe.

## Détails sur les widget de l'extension

### Liste  des utilisateur de mon groupe
Ce widget sert à afficher les détails (utilisateur, courriel, nom réel, dernière connexion) des utilisateurs qui appartient aux mêmes groupes que l’utilisateur connecté. Si l’utilisateur est un administrateur TYPO3, le widget va afficher la liste des administrateurs. 

### Dernières pages créées dans mes groupes
Ce widget permet d’afficher la liste des dernières pages crées par les membres qui appartient aux mêmes groups que l’utilisateur.

###  Pages sans modification (depuis x mois)
Liste les pages sans modification depuis "x" mois. Le nombre de mois par défaut est de 3. Permet de repérer les pages "délaissées".

### Mes dernières pages modifiées
Ce widget permet d’afficher la liste des dernières pages modifiées par l’utilisateur connecté.

###  Mes derniers contenus(tt_content) modifiés
Ce widget permet d’afficher la liste des derniers contenus (tt_content) modifiées par l’utilisateur connecté.

### Liste des liens de prévisiualisation d'espaces de travail (Workspace)
Ce widget permet d'afficher la liste des derniers 25 liens d’aperçu crées par les membres qui appartient aux mêmes groupes que l’utilisateur connecté. Les colonnes sont : Titre de l'Espace de travail, état (expiré ou actif), date de création, date d'expiration et la clé qu'on retrouve d'asn l'url (ex:  `?ADMCMD_prev=8a636e5d5545c1bb1dec5a5f77a96ca4`).


## TSconfig utilisateur ou groupe

```php
mod{
    qcWidgets{
        //Toutes les limites sont à 25 par défaut.
        lastCreatedPages{
            limit = 0
        }
        lastModifiedPages{
            limit = 0
        }
        workspaceProviderLinks{
            limit = 0
        }
        pagesWithoutModification{
            limit = 0
            numberOfMonths = 3
        }
        recentlyModifiedContent{
            limit = 0
        }
        listOfmembers{
        // Si cette valeur est à 0 (zéro), le widget va rechercher les sous-groupes
            dontLookintoSubgroups = 1
        }
    }
}
```
