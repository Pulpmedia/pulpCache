<?php

class pulpCache {

	private $config = array('cache_dir' => "cache", "config_file" => "config.json", "expire_at" => 0, "cache_file" => 'empty.tmp', 'ttl' => 86400);

	private $file;

	public function __construct($config = null) {
		$this -> configure($config);
		$this -> init();
	}

	private function configure($config) {
		if (is_array($config)) {
			$this -> config = array_merge($this -> config, $config);
		}
		if (!$this -> getConfig()) {
			$this -> createConfigFile();
		} else {
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

	private function isFresh() {
		return ($this -> config['expire_at'] > time());
	}

	public function getCache() {
		if ($this -> hasCache()) {
			return $this -> getCacheFile();
		}
		return false;
	}

	public function hasCache() {
		return $this -> isFresh() && is_file($this -> getCachePath());
	}

	public function saveCache($data) {
		if ($this -> getCacheFile()) {
			unlink($this -> getCachePath());
		}
		$this -> config['cache_file'] = sha1(md5(time()));
		$this -> config['expire_at'] = time() + $this -> config['ttl'];
		$this -> save($this -> getCachePath(), $data);
		$this -> saveConfig();
	}

	private function getCachePath() {
		return $this -> getDir() . $this -> config['cache_file'];
	}

	private function getCacheFile() {
		$file = $this -> getCachePath();
		if (!is_file($file)) {
			return false;
		}
		return $this -> load($file);
	}

	private function load($file) {
		return file_get_contents($file);
	}

	private function save($filename, $data) {
		return file_put_contents($filename, $data);
	}

	public function createDir() {
		mkdir($this -> getDir());
	}

	/*** CONFIG ***/
	private function createConfigFile() {
		$this -> save($this -> getConfigFile(), "");
	}

	private function getConfig() {
		if (!is_file($this -> getConfigFile()))
			return false;
		$config = $this -> getConfigRaw();
		if (!isset($config[$this -> config['cache_name']]))
			return false;
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

}
