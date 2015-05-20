<?php
class Ticket extends SupportAppModel {
	/**
	* Structure de la table
	**/
	/* 
	- ID 
	- Title -> Titre du ticket
	- Content -> Contenue du ticket
	- Author -> Auteur du ticket
	- Private -> Si le ticket et seulement visible par les administrateurs ou non (0 -> public, 1 -> privé)
	- State -> Si le ticket est résolu ou non (0 -> non résolu, 1 -> résolu)
	*/
}