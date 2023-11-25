##Qc Widgets - Tests fonctionnels

### My last pages -

Classe de test : LastModifiedPagesProviderImp

1) Page caché - affichée avec l'état - OK
2) Page expiré - affichée avec l'état - OK
3) Page n'est pas encore activée - affichée avec l'état - OK
4) Page créée juste en WS - ne pas affichée - OK
5) Page créée en WS et publier - affichée - OK
6) Page supprimée - n'est pas affichée - OK

### Last pages created in my groups

Classe pour test : LastCreatedPagesProviderImp

1) Page caché - crée par membre de groupe / user actuel - affichée avec l'état - OK
2) Page expiré - crée par membre de groupe / user actuel - affichée avec l'état - OK
3) Page n'est pas encore activée- crée par membre de groupe / user actuel - affichée avec l'état - OK
4) Page créée juste en WS- crée par membre de groupe / user actuel - ne pas affichée - OK
5) Page créée en WS et publier- crée par membre de groupe / user actuel - affichée - OK
6) Page supprimée - crée par membre de groupe / user actuel - n'est pas affichée - OK

### Recently modified content

Classe pour test : RecentlyModifiedContentProviderImp

1) Contenu caché - affichée avec l'état - OK
2) Contenu expiré - affichée avec l'état - OK
3) Contenu n'est pas encore - activée affichée avec l'état - OK
4) Contenu crée juste en WS - n'est pas affiché
5) Contenu crée en WS et publier - activée affichée avec l'état - OK
6) Contenu supprimé - n'est pas affiché - OK


### List of members/administrators

Classe de test : ListOfMembersProviderImp

1) Se connecter en tant qu'administrateur, le widget affiche la liste des administrateurs - OK
2) en tant qu'utilisateur, le widget affiche les membres de chaque groupe où il appartient l'utilisateur connecté - OK
3) Liste des administrateurs - Affichés juste pour les administrateurs - OK
4) Afficher une colonne pour La date de la dernière connexion pour chaque utilisateur - OK


### Pages without modification in the last x months

Classe de test : PagesWithoutModificationProviderImp

1) Changer le nombre de mois par l'option tsconfig numberOfMonths - OK
2) Modifier une page affichée dans le widget, puis actualisé le widget - la page ne doit pas s'afficher - OK

### Number of records by table

Classe de test : NumberOfRecordsByContentTypeProviderImp

1) Nombre des enregistrements activés OK
2) Nombre des enregistrements cachés : Ok - n'est pas considérés (Configuration tsconfig disponible)
3) Nombre des enregistrements en WS
4) Nombre des enregistrements supprimés - OK

### List of workspace previews

Classe de test : WorkspacePreviewProviderImp

1) Les administrateurs peuvent voir la totalité des liens WS
2) Chaque utilisateur ne peut voir que ses liens ou les lines de ses groupes

### Test
Tester les options TSConfig qui contrôlent l'affichage des données (disponibles dans le fichier : pageconfig.tsconfig)
