<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after core.php
 *
 * This file should load/create any application wide configuration settings, such as
 * Caching, Logging, loading additional configuration files.
 *
 * You should also use this file to include any files that provide global functions/constants
 * that your application uses.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

// Setup a 'default' cache configuration for use in the application.
Cache::config('default', array('engine' => 'File'));

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 *
 * App::build(array(
 *     'Model'                     => array('/path/to/models/', '/next/path/to/models/'),
 *     'Model/Behavior'            => array('/path/to/behaviors/', '/next/path/to/behaviors/'),
 *     'Model/Datasource'          => array('/path/to/datasources/', '/next/path/to/datasources/'),
 *     'Model/Datasource/Database' => array('/path/to/databases/', '/next/path/to/database/'),
 *     'Model/Datasource/Session'  => array('/path/to/sessions/', '/next/path/to/sessions/'),
 *     'Controller'                => array('/path/to/controllers/', '/next/path/to/controllers/'),
 *     'Controller/Component'      => array('/path/to/components/', '/next/path/to/components/'),
 *     'Controller/Component/Auth' => array('/path/to/auths/', '/next/path/to/auths/'),
 *     'Controller/Component/Acl'  => array('/path/to/acls/', '/next/path/to/acls/'),
 *     'View'                      => array('/path/to/views/', '/next/path/to/views/'),
 *     'View/Helper'               => array('/path/to/helpers/', '/next/path/to/helpers/'),
 *     'Console'                   => array('/path/to/consoles/', '/next/path/to/consoles/'),
 *     'Console/Command'           => array('/path/to/commands/', '/next/path/to/commands/'),
 *     'Console/Command/Task'      => array('/path/to/tasks/', '/next/path/to/tasks/'),
 *     'Lib'                       => array('/path/to/libs/', '/next/path/to/libs/'),
 *     'Locale'                    => array('/path/to/locales/', '/next/path/to/locales/'),
 *     'Vendor'                    => array('/path/to/vendors/', '/next/path/to/vendors/'),
 *     'Plugin'                    => array('/path/to/plugins/', '/next/path/to/plugins/'),
 * ));
 *
 */

/**
 * Custom Inflector rules can be set to correctly pluralize or singularize table, model, controller names or whatever other
 * string is passed to the inflection functions
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */

/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. Make sure you read the documentation on CakePlugin to use more
 * advanced ways of loading plugins
 *
 * CakePlugin::loadAll(); // Loads all plugins at once
 * CakePlugin::load('DebugKit'); //Loads a single plugin named DebugKit
 *
 */
CakePlugin::loadAll(array(array('bootstrap' => true,'routes' => true, 'ignoreMissing' => true)));
/*
App::import('Component', 'EyPlugin');
$pluginsComponent = new EyPluginComponent();
$pluginsComponent->loadPlugins();

debug(CakePlugin::loaded());
*/
/**
 * You can attach event listeners to the request lifecycle as Dispatcher Filter. By default CakePHP bundles two filters:
 *
 * - AssetDispatcher filter will serve your asset files (css, images, js, etc) from your themes and plugins
 * - CacheDispatcher filter will read the Cache.check configure variable and try to serve cached content generated from controllers
 *
 * Feel free to remove or add filters as you see fit for your application. A few examples:
 *
 * Configure::write('Dispatcher.filters', array(
 *		'MyCacheFilter', //  will use MyCacheFilter class from the Routing/Filter package in your app.
 *		'MyCacheFilter' => array('prefix' => 'my_cache_'), //  will use MyCacheFilter class from the Routing/Filter package in your app with settings array.
 *		'MyPlugin.MyFilter', // will use MyFilter class from the Routing/Filter package in MyPlugin plugin.
 *		array('callable' => $aFunction, 'on' => 'before', 'priority' => 9), // A valid PHP callback type to be called on beforeDispatch
 *		array('callable' => $anotherMethod, 'on' => 'after'), // A valid PHP callback type to be called on afterDispatch
 *
 * ));
 */

//App::uses('AppExceptionHandler', 'Lib');

Configure::write('Dispatcher.filters', array(
	'AssetDispatcher',
	'CacheDispatcher'
));

/**
 * Configures default file logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
	'engine' => 'File',
	'types' => array('notice', 'info', 'debug'),
	'file' => 'debug',
));
CakeLog::config('error', array(
	'engine' => 'File',
	'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
	'file' => 'error',
));

@setlocale(LC_MESSAGES, 'fr_FR');
setlocale(LC_ALL, 'fr_FR');

/*
DATABASE
*/
if(!file_exists(ROOT.DS.'config'.DS.'install.txt')) {

	App::uses('CakeSchema', 'Model');
	$CakeSchema = new CakeSchema(array('name' => 'App', 'path' => ROOT.DS.'app'.DS.'Config'.DS.'Schema', 'file' => 'schema.php', 'connection' => 'default', 'plugin' => null));

	App::uses('SchemaShell', 'Console/Command');
	$SchemaShell = new SchemaShell();

	App::import('Model', 'ConnectionManager');
	$con = new ConnectionManager;
	$cn = $con->getDataSource($CakeSchema->connection);
	if(!$cn->isConnected()) {
			exit('Could not connect to database. Please check the settings in app/config/database.php and try again');
	}

	$db = ConnectionManager::getDataSource($CakeSchema->connection);

	$options = array(
			'name' => $CakeSchema->name,
			'path' => $CakeSchema->path,
			'file' => $CakeSchema->file,
			'plugin' => null,
			'connection' => $CakeSchema->connection,
	);
	$Schema = $CakeSchema->load($options);

	$Old = $CakeSchema->read(array('models' => false));
	$compare = $CakeSchema->compare($Old, $Schema);

	$contents = array();

	foreach ($compare as $table => $changes) {
			if (isset($compare[$table]['create'])) {
					$contents[$table] = $db->createSchema($Schema, $table);
			} else {

					// on vérifie que ce soit pas un plugin (pour ne pas supprimer ses modifications sur la tables lors d'une MISE A JOUR)
					if(isset($compare[$table]['drop'])) { // si ca concerne un drop de colonne

							foreach ($compare[$table]['drop'] as $column => $structure) {

									// vérifions que cela ne correspond pas à une colonne de plugin
									if(count(explode('__', $column)) > 1) {
											unset($compare[$table]['drop'][$column]);
									}
							}

					}

					if(isset($compare[$table]['drop']) && count($compare[$table]['drop']) <= 0) {
							unset($compare[$table]['drop']); // on supprime l'action si y'a plus rien à faire dessus
					}

					if(count($compare[$table]) > 0) {
							$contents[$table] = $db->alterSchema(array($table => $compare[$table]), $table);
					}
			}
	}

  if (!file_exists(ROOT.DS.'app'.DS.'tmp'.DS.'logs'.DS.'db.log'))
    @mkdir(ROOT.DS.'app'.DS.'tmp'.DS.'logs'.DS, 0755, true);
  file_put_contents(ROOT.DS.'app'.DS.'tmp'.DS.'logs'.DS.'db.log', '');
	$error = array();
	if(!empty($contents)) {
			foreach ($contents as $table => $query) {
					if(!empty($query)) {
							try {
									$db->execute($query);
							} catch (PDOException $e) {
									$error[] = $table . ': ' . $e->getMessage();
									file_put_contents(ROOT.DS.'app'.DS.'tmp'.DS.'logs'.DS.'db.log',
										file_get_contents(ROOT.DS.'app'.DS.'tmp'.DS.'logs'.DS.'db.log').
										"\n".$e->getMessage()
									);
							}
					}
			}
	}

	$Schema->after(array(), true);

	if(empty($error)) {
		$data = "CREATED AT ".date('H:i:s d/m/Y')."\n";
		$fp = fopen(ROOT.DS.'config'.DS.'install.txt', 'w+');
		fwrite($fp, $data);
		fclose($fp);
	} else {
		die('Unable to install MYSQL tables (try to create file /config/install.txt)');
	}
}
