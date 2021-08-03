# PanoDrone
Visualisation sphères et placement de points d'intérets

__But__: Permettre de visualiser et d'enrichir les photos sphères prisent avec son drone.

Pour ce faire, deux logiciels sont utilisés :

- [Photo Sphères Viewer](https://photo-sphere-viewer.js.org/) de Damien Sorel pour l'affichage et le marquage de point d'intérêt
- [Cute File Browser](https://tutorialzine.com/2014/09/cute-file-browser-jquery-ajax-php) de Nick Anastasov pour parcourir les photos

## Principe de fonctionnement : 

Cute File Browser permet de se déplacer dans l'arborescence des photos sphères, puis lors du clique sur la tuile info de la photosphère on passe la main à Photo Sphères View qui permet alors de naviguer visuellement dans la sphère et afficher les marqueurs.  

Cute File Browser est légérement modifié, il scan les fichiers .jpg, puis insert le nom du fichier dans une base de donnée sqlite qui sera alors enrichie pour donner un titre et des infos de marqueurs.  
Photo Sphère Viewer est utilisé soit pour visualiser les sphères ainsi que les points d'intérêts soit pour créer ou mettre à jour ces derniers. Pour ce faire il recupére ou écrit les infos marqueurs dans la base de données.

Dans la première version, il n'y avait pas de base de données, les infos étaient mémorisées dans un fichier .xml du même nom, à l'usage il s'est avéré difficile d'ajouter des marqueurs multiples dans une sphère d'où l'abandon de ce choix. 

## Pré-requis :
Un hergement web supportant php  

Base de données sqlite3 pour mémoriser les infos persistantes.

La fonction de scan des fichiers sphères est en php le reste en javascript.

__Reste à faire__:
Améliorer la création d'un marqueur  

## Démo ##
[Démonstration](http://www.wse.fr/PanoDrone/)

__Change log__:
02/08/2021 abandon .xml pour memo info marqueurs
03/08/2021 ajout hash fichier qui ont des marqueurs cela permet ainsi de reconnaitre un fichier peu importe son nom et son emplacement ils pourront avoir un titre et une légende respectifs mais auront les mêmes marqueurs.