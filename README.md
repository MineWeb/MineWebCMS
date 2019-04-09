# MineWebCMS


## C'est quoi MineWeb ?

Le CMS est en bêta depuis 15 août 2015 et en version finale depuis le 3 mai 2016.

MineWeb est un CMS (c'est-à-dire un système de gestion de contenu), en plus simple, un site complètement personnalisable et intuitif, qui s'adaptera parfaitement à vos serveurs Minecraft !

Vous pourrez tenir vos joueurs au courant des actualités, leur faire acheter des articles sur la boutique... Vous pourrez personnaliser le CMS avec toutes sortes de thèmes ou plugins ! 

### Le CMS est gratuit et opensource ?

Jusqu'en mars 2018, il vous fallait avoir une licence payante pour utiliser le CMS. Mais désormais celui-ci est entièrement disponible sur Github et gratuitement. Il vous suffit de télécharger le repo pour utiliser le CMS sans aucun problème, celui-ci n'est plus dépendant de notre API. 

#### Pourquoi l'avoir rendu gratuit et opensource ?

Premièrement je maintenais ce projet depuis plusieurs années presque complètement seul (hormis les 2/3 supports et 2 amis pour m'aider sur l'infrastructure) et je commençais à m'en lasser, de plus, ne jouant plus à Minecraft je ne m'intéressais plus à ce domaine. Dès mars 2018 j'ai eu ma réelle première expérience professionnelle me poussant également a arrêter ce projet. 

Deuxièmement, le CMS est en développement depuis plusieurs années sur un vieux framework, le code est vieux et j'ai évolué depuis le début, une refonte sera nécessaire pour rajouter des fonctionnalités intéressantes et maintenir correctement le projet mais le manque de temps et de motivation ne m'ont pas permis de le faire.

Troisièmement je ne tenais pas a faire disparaitre le CMS et empêcher mes anciens utilisateurs de l'utiliser. De plus, cela ajoute du contenu sur mon Github. 

### Statistiques

MineWeb, avant ce passage à l'OpenSource c'est plus de 5 000 utilisateurs, plus de 1 500 licences, plus de 100 licences hébergées tous les mois, un Discord avec plus de 800 membres, plusieurs milliers de tickets sur le support.

## Comment l'utiliser ?

### Installation

Pour installer le CMS, rien de plus simple, il vous suffit de télécharger les fichiers sur ce repo, et de les placer sur votre serveur web, pour les pré-requis et autres informations, vous avez la documentation disponible [ici](https://docs.mineweb.org) ou [ici](https://github.com/MineWeb/docs.mineweb.org)

### Support

Le site officiel étant partiellement coupé, plus aucun support officiel n'est assuré, mais vous pouvez toujours faire part de vos problèmes sur le [Discord](https://discordapp.com/invite/3QYdt8r) ou avec les [Issues](https://github.com/MineWeb/MineWebCMS/issues) sur Github. 

### Plugins et thèmes

Le site officiel étant partiellement coupé, le market n'est plus disponible et maintenu sur celui-ci. Le CMS se base donc maintenant sur les repos faisant partie de l'[organisation MineWeb](https://github.com/MineWeb). Tous les plugins affichés sur le CMS sont les repos commençant par _Plugin-_ (et pour les thèmes _Theme-_). 
Si vous souhaitez voir les sources d'un plugin ou alors y contribuer, il vous suffit de vous rendre sur le repo correspondant (ex: [Boutique](https://github.com/MineWeb/Plugin-Shop)). 

Si vous souhaitez ajouter un plugin ou un thème sur le CMS, il vous suffit simplement de nous le demander sur notre Discord pour être ajouté dans l'organisation, ce qui vous permettra de créer un plugin ou un thème. 
Pour cela, il vous suffira de créer votre propre repo (sur l'organisation) avec le préfix adéquate (_Plugin-_ ou _Theme-_), et d'y développer votre plugin/thème en suivant la [documentation](https://docs.mineweb.org). 
Il vaut mieux créer plusieurs branches (_dev_ pour le développement par exemple) car le CMS utilise la branche _master_ pour récupérer le contenu du plugin ou du thème. 

## Contribuer

Si vous souhaitez contribuer au CMS vous êtes libres de soumettre des pull-requests que je me ferait une joie de regarder et merge.
De plus vous pouvez également encore développer des thèmes ou des plugins pour la communauté. 
