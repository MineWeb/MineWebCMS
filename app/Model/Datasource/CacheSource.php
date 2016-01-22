<?php
/**
 * Cache data source class.
 *
 * @copyright     Copyright 2010, Jeremy Harris
 * @link          http://42pixels.com
 * @package       cacher
 * @subpackage    cacher.models.behaviors
 */

/**
 * Includes
 */
App::uses('Folder', 'Utility');
App::uses('DataSource', 'Model/Datasource');

/**
 * CacheSource datasource
 *
 * Gets find results from cache instead of the original datasource. The cache
 * is stored under CACHE/cacher.
 *
 * @package       cacher
 * @subpackage    cacher.models.datasources
 */
class CacheSource extends DataSource {

/**
 * Stored original datasource for fallback methods
 *
 * @var DataSource
 */
	public $source = null;

/**
 * Constructor
 *
 * Sets default options if none are passed when the datasource is created and
 * creates the cache configuration. If a `config` is passed and is a valid
 * Cache configuration, CacheSource uses its settings
 *
 * ### Extra config settings
 * - `original` The name of the original datasource, i.e., 'default' (required)
 * - `config` The name of the Cache configuration to use. Uses 'default' by default
 * - other settings required by DataSource...
 *
 * @param array $config Configure options
 */
	public function __construct($config = array()) {
		$config = array_merge(array('config' => 'default'), $config);
		parent::__construct($config);

		if (Configure::read('Cache.disable') === true) {
			return;
		}
		if (!isset($this->config['original'])) {
			throw new CakeException('Missing name of original datasource.');
		}
		if (!Cache::isInitialized($this->config['config'])) {
			throw new CacheException(sprintf('Missing cache configuration for "%s".', $this->config['config']));
		}

		$this->source = ConnectionManager::getDataSource($this->config['original']);
	}

/**
 * Redirects calls to original datasource methods. Needed if the `Cacher.Cache`
 * behavior is attached before other behaviors that use the model's datasource methods.
 *
 * @param string $name Original db source function name
 * @param array $arguments Arguments
 * @return mixed
 */
	public function __call($name, $arguments) {
		return call_user_func_array(array($this->source, $name), $arguments);
	}

/**
 * Reads from cache if it exists. If not, it falls back to the original
 * datasource to retrieve the data and cache it for later
 *
 * @param Model $Model
 * @param array $queryData
 * @param integer $recursive
 * @return array Results
 * @see DataSource::read()
 */
	public function read(Model $Model, $queryData = array(), $recursive = null) {
		$this->_resetSource($Model);
		$key = $this->_key($Model, $queryData);
		$results = Cache::read($key, $this->config['config']);
		if ($results === false) {
			$results = $this->source->read($Model, $queryData, $recursive);
			// compress before storing
			if (isset($this->config['gzip'])) {
				Cache::write($key, gzcompress(serialize($results)), $this->config['config']);
			} else {
				Cache::write($key, $results, $this->config['config']);
			}
			$this->_map($Model, $key);
		} else {
			// uncompress data from cache
			if (isset($this->config['gzip'])) {
				$results = unserialize(gzuncompress($results));
			}
		}
		return $results;
	}

/*
 * Clears the cache for a specific model and rewrites the map. Pass query to
 * clear a specific query's cached results
 *
 * @param array $query If null, clears all for this model
 * @param Model $Model The model to clear the cache for
 */
	public function clearModelCache(Model $Model, $query = null) {
		$map = Cache::read('map', $this->config['config']);

		$keys = array();
		if ($query !== null) {
			$keys = array($this->_key($Model, $query));
		} else{
			if (!empty($map[$this->source->configKeyName]) && !empty($map[$this->source->configKeyName][$Model->alias])) {
				$keys = $map[$this->source->configKeyName][$Model->alias];
			}
		}
		if (empty($keys)) {
			return;
		}
		$map[$this->source->configKeyName][$Model->alias] = array_flip($map[$this->source->configKeyName][$Model->alias]);
		foreach ($keys as $cacheKey) {
			Cache::delete($cacheKey, $this->config['config']);
			unset($map[$this->source->configKeyName][$Model->alias][$cacheKey]);
		}
		$map[$this->source->configKeyName][$Model->alias] = array_values(array_flip($map[$this->source->configKeyName][$Model->alias]));
		Cache::write('map', $map, $this->config['config']);
	}

/**
 * Hashes a query into a unique string and creates a cache key
 *
 * @param Model $Model The model
 * @param array $query The query
 * @return string
 */
	protected function _key(Model $Model, $query) {
		$query = array_merge(
			array(
				'conditions' => null, 'fields' => null, 'joins' => array(), 'limit' => null,
				'offset' => null, 'order' => null, 'page' => null, 'group' => null, 'callbacks' => true
			),
			(array)$query
		);
		$gzip = (isset($this->config['gzip'])) ? '_gz' : '';
		$queryHash = md5(serialize($query));
		$sourceName = $this->source->configKeyName;
		return Inflector::underscore($sourceName).'_'.Inflector::underscore($Model->alias).'_'.$queryHash.$gzip;
	}

/**
 * Creates a cache map (used for deleting cache keys or groups)
 *
 * @param Model $Model
 * @param string $key
 */
	protected function _map(Model $Model, $key) {
		$map = Cache::read('map', $this->config['config']);
		if ($map === false) {
			$map = array();
		}
		$map = Set::merge($map, array(
			$this->source->configKeyName => array(
				$Model->alias => array(
					$key
				)
			)
		));
		Cache::write('map', $map, $this->config['config']);
	}

/**
 * Resets the model's datasource to the original
 *
 * @param Model $Model The model
 * @return boolean
 */
	protected function _resetSource($Model) {
		if (isset($Model->_useDbConfig)) {
			$this->source = ConnectionManager::getDataSource($Model->_useDbConfig);
		}
		return $Model->setDataSource(ConnectionManager::getSourceName($this->source));
	}

/**
 * Since Datasource has the method `describe()`, it won't be caught `__call()`.
 * This ensures it is called on the original datasource properly.
 *
 * @param mixed $model
 * @return mixed
 */
	public function describe($model) {
		if (method_exists($this->source, 'describe')) {
			return $this->source->describe($model);
		}
		return $this->describe($model);
	}

}
