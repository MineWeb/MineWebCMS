## Liste des events disponibles


### Global

- requestPage : Appelé lors de chaque requête sans données particulières.
- onPostRequest : Appelé lors d'une requête POST sans données particulières.
- onLoadPage : Appelé lors de chaque chargement de page dans le beforeRender sans données particulières.
- onLoadAdminPanel : Appelé lors de chaque chargement de page admin (prefix) dans le beforeRender sans données particulières.


### Fonction particulière
- beforeEncodePassword : Appelé avant chaque encodage de mot de passe avec le pseudo et le mot de passe en données.
- beforeSendMail : Appelé avant chaque envoie d'email avec le message et la configuration en données.
- beforeUploadImage : Appelé avant chaque upload d'image avec la requête et le nom de l'image voulu en données.


### News
- beforeAddComment : Appelé avant que le commentaire ne soit enregistré avec le contenu, l'ID de la news et les infos de l'utilisateur en données.
- beforeLike : Appelé avant que le like ne soit enregistré avec l'ID de la news et les infos de l'utilisateur en données.
- beforeDislike : Appelé avant que le like ne soit supprimé avec l'ID de la news et les infos de l'utilisateur en données.
- beforeDeleteComment : Appelé avant que le commentaire ne soit supprimé avec l'ID du commentaire, l'ID de la news et les infos de l'utilisateur en données.
- beforeDeleteNews : Appelé avant que la news ne soit supprimé avec l'ID de la news et les infos de l'utilisateur en données.
- beforeAddNews : Appelé avant que la news ne soit enregistré avec le contenu de la requête et les infos de l'utilisateur en données.
- beforeEditNews : Appelé avant que la news ne soit enregistré avec le contenu de la requête, l'ID de la news et les infos de l'utilisateur en données.


### User
- onLogin : Appelé à chaque login avec l'utilisateur et register (boolean) comme données.
- beforeRegister : Appelé avant l'enregistrement d'un utilisateur (après la validation) avec les données de la requête comme données.
- beforeConfirmAccount : Appelé avant la confirmation en base de donnée de l'utilisateur avec l'ID de l'utilisateur et manual si confirmé par un administrateur comme données.
- beforeSendResetPassMail : Appelé avant l'envoie de l'email permettant la rénitialisation du mot de passe avec l'ID de l'utilisateur et clé de reset comme données
- beforeResetPassword : Appelé avant l'enregistrement du nouveau mot de passe avec l'ID de l'utilisateur et le nouveau mot de passe comme données.
- onLogout : Appelé pendant la déconnexion avec la session "user" comme données.
- beforeUpdatePassword : Appelé avant l'enregistrement du nouveau mot de passe avec l'utilisateur et le nouveau mot de passe encodé comme données.
- beforeUpdateEmail : Appelé avant l'enregistrement du nouvel email avec l'utilisateur et le nouveau email comme données.
- beforeSendPoints : Appelé avant l'enregistrement de la transaction avec l'utilisateur, le nouveau solde de l'utilisateur, à qui sont transféré les points et combien comme données.
- beforeEditUser : Appelé avant que les données ne soit enregistrées avec l'ID de l'utilisateur, les données et password_updated comme données.
- beforeDeleteUser : Avant que l'utilisateur ne soit supprimé avec ses informations comme données.

### Shop
- onBuy : Appelé à chaque achat avant tout enregistrement avec les articles, le prix total et les infos de l'user comme données.
- beforeEditItem : Appelé avant que les modifications de l'article ne soit enregistrés avec les infos et l'utilisateur en données.
- beforeAddItem : Appelé avant que les modifications de l'article ne soit enregistrés avec les infos et l'utilisateur en données.
- beforeAddCategory : Appelé avant l'enregistrement de la catégorie avec le nom de la catégorie et les infos de l'utilisateur en données.
- beforeDeleteItem : Appelé avant la suppression de l'article avec l'ID de l'article et les infos de l'utilisateur en données.
- beforeDeleteCategory : Appelé avant la suppression de la catégorie avec l'ID de la catégorie et les infos de l'utilisateur en données.
- beforeDeletePaypalOffer : Appelé avant la suppression de l'offre PayPal avec l'ID de l'offre et les infos de l'utilisateur en données.
- beforeDeleteStarpassOffer : Appelé avant la suppression de l'offre StarPass avec l'ID de l'offre et les infos de l'utilisateur en données.
- beforeAddVoucher : Appelé avant l'enregistrement du code promo avec les données et les infos de l'utilisateur en données.
- beforeDeleteVoucher : Appelé avant la suppression du code promo avec l'ID du code et les infos de l'utilisateur en données.


### Vote
- onVote : Appelé pendant le vote (après l'enregistrement du vote, avant les récompenses) avec le choix du moyen de récompense (now/later), le site, la config et les infos de l'utilisateur en données.
- beforeReceiveRewards : Appelé avant le gain des récompenses avec les récompenses, le type et les infos de l'utilisateur en données.
- beforeGetWaitingReward : Appelé avant que la récompense en attente ne soit donné avec l'utilisateur en données.
- beforeResetVotes : Appelé avant que tous les votes ne soit rénitialisés avec les infos de l'utilisateur en données.
