# PanoDrone
Visualisation sphères et placement de points d'intérets

__But__: Permettre de visualiser et d'enrichir les photos sphères prisent avec son drone.

Pour ce faire, deux logiciels sont utilisés :

- [Photo Sphères Viewer](https://photo-sphere-viewer.js.org/) de Damien Sorel pour l'affichage et le marquage de point d'intérêt
- [Cute File Browser](https://tutorialzine.com/2014/09/cute-file-browser-jquery-ajax-php) de Nick Anastasov pour parcourir les photos

## Principe de fonctionnement : 

Cute File Browser permet de se déplacer dans l'arborescence des photos sphères, puis lors du clique sur la tuile info de la photosphère on passe la main à Photo Sphères View qui permet alors de naviguer visuellement dans la sphère et afficher les marqueurs.

Cute File Browser est légérement modifié, il scan les fichiers .jpg, si un fichier .xml du même nom est présent il se servira alors du champs titre et du champs legende pour afficher cela sur la tuile de présentation de la sphère.
Photo Sphère Viewer est utilisé soit pour visualiser les sphères ainsi que les points d'intérêts soit pour créer ou mettre à jour ces derniers. Pour ce faire il recupére ou écrit les infos marqueurs dans le fichier .xml

## Pré-requis :
Un hergement web supportant php (Pas de base de données les infos persistantes sont mémorisées dans fichier .xml)
La fonction de scan des fichiers sphères, l'ecriture la lecture des fichiers .xml sont en php le reste en jacascript. 
