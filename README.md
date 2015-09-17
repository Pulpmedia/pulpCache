#pulpCache

A simple Cache class

___


##Usage

###Initialization

To initialize a new Cache, simply create a new Instance of ``PulpCache``

	$cache = new pulpCache();
	
There are a few options you can add as an array:
	
	cache_dir: Target directory for the cache (default ./cache)
	config_file: Name of the Cache Configfile (defailt config.json)
	expire_at: TimeStamp at which the Cache will be marked as old next (will be overwritten with each "save")
	cache_name: Name of the Current cached file
	ttl: Time to live (in Seconds). This will be used to set "expire_at" (current time + ttl). Default: 86500 (One Day)
	
#####Example
	<?php
	
	require_once(dirname(__FILE)) . 'pulpcache/pulpCache.php');
	
	$config = new array('cache_dir' => '/path/to/cache/dir',
						 'cache_name' => 'file_to_cache',
						 'ttl' => 3600
						);
	$cache = new PulpCache($config);
	

###Caching

To actually cache Content, there are 3 methods you should use:
	
	hasCache() : Returns true if there is cached content available
	getCache(): Returns the cached Content
	saveCache($content): Saves $content
	
#####Example

	<?php
	
	require_once(dirname(__FILE)) . 'pulpcache/pulpCache.php');
	
	$config = new array('cache_dir' => '/path/to/cache/dir',
						 'cache_name' => 'file_to_cache',
						 'ttl' => 3600
						);
	$cache = new PulpCache($config);
	
	$content = "";
	
	if(!$cache -> hasCache() ){
		
		...
		... Fill $content wicth Content
		...
		
		$cache->saveCache($content);
		
	} else {
			$content = $cache -> getCache();
	}
	
	echo $content
