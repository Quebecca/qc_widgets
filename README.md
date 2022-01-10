Qc Widgets
==============================================================
*La [version française](#documentation-qc-widget) de la documentation suit le texte anglais*

## About
This extension provides a set of widgets for the TYPO3 Backend. Each widget display lists of records related to the current logged in user.
The amount of records to display can be changed in User or Group TSconfig. The list of widgets is:

- List of *my* group members
- Last created Pages in *my* groups
- Pages that haven't been touched for X months
- My last modified pages
- My last modified content elements
- List of my workspace preview links

As usual, you can select which widgets are available to editors by managing the BE group rights to each of the widgets.

## Details about the provided widgets :

## List of my groups members 
This widget is used to display details of users who belong to the same groups as the logged in user, 
if the current user is an administrator in this case the widget will display the list of administrators in the system.

## Last created Pages in my groups
This widget allows to display the list of the last pages created by the members who belong to the same groups as the user, 
the widget will display by default the last 25 pages created, by a click on the UID of a page displayed, the user will be redirected to the page module.
To control the amount displayed by this widget, the following tsconfig attribute can be used:
    `listOfLastCreatedPagesLimit = 0`

if the value entered is less than or equal to 0, the default value is '25'.

## My last modified pages
This widget allows to display the list of the last pages modified by the current user, 
the details of the pages displayed in this widget is similar to the details displayed by the widget `List of last created pages by my group’s members`
To control the amount displayed by this widget, the following tsconfig attribute can be used:
    `listOfLastModifiedPagesLimit = 0`

if the value entered is less than or equal to 0, the default value is '25'.


## List of my workspace preview links
This widget is used to display the list of the last 25 preview links created by members who belong to the same groups as the current user.
To control the amount displayed by this widget, the following tsconfig attribute can be used:
    `ListOfWorkspaceProviderLinksLimit = 0`

if the value entered is less than or equal to 0, the default value is '25'.

-----------
[Version française]
# Documentation Qc Widgets

## À propos
Cette extension fournit un ensemble de widgets, chaque widget permet d’afficher une table qui sert à afficher des détails liés à l’utilisateur actuel connecté.
Pour contrôler la quantité affichée par les widgets de cette extension, cette dernière supporte trois options tsconfig, qui servent à limiter la quantité affichée.   

### La liste des membres de mes groupes
Ce widget sert à afficher les détails des utilisateurs qui appartient aux mêmes groupes que l’utilisateur connecté, 
si l’utilisateur actuel est un administrateur dans ce cas le widget va afficher la liste des administrateurs dans le système. 

### Les derniers pages créées par mes groupes
Ce widget permet d’afficher la liste des dernières pages crées par les membres qui appartient aux mêmes groups que l’utilisateur, 
le widget va afficher par défaut les dernières 25 pages crées, par une clique sur le UID d’une page affichée, 
l’utilisateur sera redirigé vers le module page.
Pour contrôler la quantité affichée par ce widget, l’attribue tsconfig suivant peut être utilisé :
    `listOfLastCreatedPagesLimit = 0`

Si la valeur saisi est inférieur ou égale 0, la valeur par défaut est ‘25’.

### Mes dernières pages modifiées
Ce widget permet d’afficher la liste des dernières pages modifiées par l’utilisateur actuel, 
les détails des pages affichées dans ce widget est similaire aux détails afficher
par le widget `List of last created pages by my group’s members`
Pour contrôler la quantité affichée par ce widget, l’attribue tsconfig suivant peut être utilisé :
    `listOfLastModifiedPagesLimit = 0`

Si la valeur saisi est inférieur ou égale 0, la valeur par défaut est ‘25’.

### La liste de mes liens d'aprerçu de l'espace de travail
Ce widget sert à afficher la liste des derniers 25 liens d’aperçu crées par les membres qui appartient aux mêmes groupes que l’utilisateur actuel.
Pour contrôler la quantité affichée par ce widget, l’attribue tsconfig suivant peut être utilisé :
    `ListOfWorkspaceProviderLinksLimit = 0`

Si la valeur saisi est inférieur ou égale 0, la valeur par défaut est ‘25’.
