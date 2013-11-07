<?php

class pulpCache {

	private $config = array('cache_dir' => "cache", "config_file" => "config.json", "expire_at" => 0, "cache_file" => 'empty.tmp', "cache_name" => "cache",'ttl' => 86400);


	public function __construct($config = null) {
		$this -> configure($config);
		$this -> init();
	}

	private function configure($config) {
		if (is_array($config)) {
			$this -> config = array_merge($this -> config, $config);
		}
		if ($this -> getConfig()) {
			$this -> config = array_merge($this -> config, $this -> getConfig());
		}
	}

	private function init() {
		if (!is_dir($this -> getDir())) {
			$this -> createDir();
		}
	}

	private function getDir() {
		return $this -> config['cache_dir'] . "/";
	}

	/**
	 * Checks if the Cache is Fresh
	 * @return boolean
	 */
	private function isFresh() {
		return ($this -> config['expire_at'] > time());
	}
	/**
	 * Returns the cached Content if available
	 * @return string
	 */
	public function getCache() {
		if ($this -> hasCache()) {
			return $this -> getCacheFile();
		}
		return false;
	}

	/**
	 * Returns true if there is fresh cached Content
	 * @return boolean
	 */
	public function hasCache() {
		return $this -> isFresh() && is_file($this -> getCachePath());
	}
	/**
	 * Deletes old cached Content and saves new Cache
	 * @return boolean
	 */
	public function saveCache($data) {
		if ($this -> getCacheFile()) {
			unlink($this -> getCachePath());
		}
		$this -> config['cache_file'] = sha1(md5(time()));
		$this -> config['expire_at'] = time() + $this -> config['ttl'];
		$this -> save($this -> getCachePath(), $data);
		$this -> saveConfig();
		return true;
	}

	/**
	 * Returns the Directory of the cached File
	 * @return string
	 */
	private function getCachePath() {
		return $this -> getDir() . $this -> config['cache_file'];
	}
	
	/**
	 * Returns the content of the Cached File
	 * @return string
	 */
	private function getCacheFile() {
		$file = $this -> getCachePath();
		if (!is_file($file)) {
			return false;
		}
		return $this -> load($file);
	}

	/**
	 * Creates the cache Directory
	 */
	public function createDir() {
		mkdir($this -> getDir());
	}

	/*** CONFIG ***/
	

	/**
	 * Returns the Config for the current Cache Name
	 * @return array;
	 */
	private function getConfig() {
		if (!is_file($this -> getConfigFile()))
			return false;
		$config = $this -> getConfigRaw();
		if (!isset($config[$this -> config['cache_name']]))
			return array();
		return $config[$this -> config['cache_name']];
	}

	private function getConfigRaw() {
		$config = json_decode($this -> load($this -> getConfigFile()), true);
		if (is_array($config))
			return $config;
		return array();
	}

	private function saveConfig() {
		$config = array_merge($this -> getConfigRaw(), array($this -> config['cache_name'] => $this -> config));
		$this -> save($this -> getConfigFile(), json_encode($config));
		return true;
	}

	private function getConfigFile() {
		return $this -> getDir() . $this -> config["config_file"];
	}
	
	/** UTILITIES **/
	
	 
	/**
	 * Loads a File
	 * @return string
	 */
	private function load($file) {
		return file_get_contents($file);
	}

	/**
	 * Saves a File
	 * @return boolean
	 */
	private function save($filename, $data) {
		return file_put_contents($filename, $data);
	}
}
