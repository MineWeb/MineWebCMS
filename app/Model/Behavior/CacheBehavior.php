<?php
/**
 * Cache behavior class.
 *
 * @copyright     Copyright 2010, Jeremy Harris
 * @link          http://42pixels.com
 * @package       cacher
 * @subpackage    cacher.models.behaviors
 */

/**
 * Cache Behavior
 *
 * Auto-caches find results into the cache. Running an exact find again will
 * pull from the cache. Requires the CacherSource datasource.
 *
 * @package       cacher
 * @subpackage    cacher.models.behaviors
 */
class CacheBehavior extends ModelBehavior {

/**
 * Whether or not to cache this call's results
 *
 * @var boolean
 */
	public $cacheResults = false;

/**
 * Settings
 *
 * @var array
 */
	public $settings;

/**
 * Sets up a connection using passed settings
 *
 * ### Config
 * - `config` The name of an existing Cache configuration to use. Default is 'default'
 * - `clearOnSave` Whether or not to delete the cache on saves
 * - `clearOnDelete` Whether or not to delete the cache on deletes
 * - `auto` Automatically cache or look for `'cache'` in the find conditions
 *		where the key is `true` or a duration
 *
 * @param Model $Model The calling model
 * @param array $config Configuration settings
 * @see Cache::config()
 */
	public function setup(Model $Model, $config = array()) {
		$_defaults = array(
			'config' => 'default',
			'clearOnDelete' => true,
			'clearOnSave' => true,
			'auto' => false,
			'gzip' => false
		);
		$settings = array_merge($_defaults, $config);

		$Model->_useDbConfig = $Model->useDbConfig;
		$ds = ConnectionManager::getDataSource($Model->useDbConfig);
		if (!in_array('cacher', ConnectionManager::sourceList())) {
			$settings += array(
				'original' => $Model->useDbConfig,
				'datasource' => 'CacheSource'
			);
			$settings = array_merge($ds->config, $settings);
			ConnectionManager::create('cacher', $settings);
		} else {
			$ds = ConnectionManager::getDataSource('cacher');
			$ds->config = array_merge($ds->config, $settings);
		}

		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = $settings;
		}
		$this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], $settings);
	}

/**
 * Intercepts find to use the caching datasource instead
 *
 * If `$queryData['cacher']` is true, it will cache based on the setup settings
 * If `$queryData['cacher']` is a duration, it will cache using the setup settings
 * and the new duration.
 *
 * @param Model $Model The calling model
 * @param array $queryData The query
 */
	public function beforeFind(Model $Model, $queryData) {
		if (Configure::read('Cache.disable') === true || $Model->alias == "History" || $Model->alias == "User" || $Model->alias == "Visit") {
			return $queryData;
		}
		$this->cacheResults = false || $this->settings[$Model->alias]['auto'];

		$queryData['cacher'] = false;

		if (isset($queryData['cacher'])) {
			if (is_string($queryData['cacher'])) {
				Cache::config($this->settings[$Model->alias]['config'], array('duration' => $queryData['cacher']));
				$this->cacheResults = true;
			} else {
				$this->cacheResults = (boolean)$queryData['cacher'];
			}
			unset($queryData['cacher']);
		}

		if ($this->cacheResults) {
			$Model->setDataSource('cacher');
		}
		return $queryData;
	}

/**
 * Intercepts delete to use the caching datasource instead
 *
 * @param Model $Model The calling model
 */
	public function beforeDelete(Model $Model, $cascade = true) {
		if ($this->settings[$Model->alias]['clearOnDelete']) {
			$this->clearCache($Model);
		}
		return true;
	}

/**
 * Intercepts save to use the caching datasource instead
 *
 * @param Model $Model The calling model
 */
	public function beforeSave(Model $Model, $options = array()) {
		if ($this->settings[$Model->alias]['clearOnSave']) {
			$this->clearCache($Model);
		}
		return true;
	}

/**
 * Clears all of the cache for this model's find queries. Optionally, pass
 * `$queryData` to just clear a specific query
 *
 * @param Model $Model The calling model
 * @return boolean
 */
	public function clearCache(Model $Model, $queryData = null) {
		if (Configure::read('Cache.disable') === true) {
			return true;
		}
		if ($queryData !== null) {
			$queryData = $this->_prepareFind($Model, $queryData);
		}
		$ds = ConnectionManager::getDataSource('cacher');
		$success = $ds->clearModelCache($Model, $queryData);
		return $success;
	}

/*
 * Prepares a query by adding missing data. This function is needed because
 * reads on the database typically bypass Model::find() which is where the query
 * is changed.
 *
 * @param array $query The query
 * @return array The modified query
 * @see Model::find()
 */
	protected function _prepareFind(Model $Model, $query = array()) {
		$query = array_merge(
			array(
				'conditions' => null, 'fields' => null, 'joins' => array(), 'limit' => null,
				'offset' => null, 'order' => null, 'page' => null, 'group' => null, 'callbacks' => true
			),
			(array)$query
		);
		if (!is_numeric($query['page']) || intval($query['page']) < 1) {
			$query['page'] = 1;
		}
		if ($query['page'] > 1 && !empty($query['limit'])) {
			$query['offset'] = ($query['page'] - 1) * $query['limit'];
		}
		if ($query['order'] === null && $Model->order !== null) {
			$query['order'] = $Model->order;
		}
		$query['order'] = array($query['order']);

		return $query;
	}
}
