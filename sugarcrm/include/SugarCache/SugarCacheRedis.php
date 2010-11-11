<?php

/**
 * Redis SugarCache backend, using the PHP Redis C library at http://github.com/owlient/phpredis
 */
class SugarCacheRedis extends SugarCacheAbstract
{
    /**
     * @var Redis server name string
     */
    protected $_host = 'localhost';
    
    /**
     * @var Redis server port int
     */
    protected $_port = 6379;
    
    /**
     * @var Redis object
     */
    protected $_redis = null;
    
    /**
     * @see SugarCacheAbstract::$_priority
     */
    protected $_priority = 920;
    
    /**
     * @see SugarCacheAbstract::useBackend()
     */
    public function useBackend()
    {
        if ( !parent::useBackend() )
            return false;
        
        if ( extension_loaded("redis")
                && empty($GLOBALS['sugar_config']['external_cache_disabled_redis'])
                && $this->_getRedisObject() )
            return true;
            
        return false;
    }
    
    /**
     * @see SugarCacheAbstract::__construct()
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->_host = SugarConfig::getInstance()->get('external_cache.redis.host', $this->_host);
        $this->_port = SugarConfig::getInstance()->get('external_cache.redis.port', $this->_port);
    }
    
    /**
     * Get the memcache object; initialize if needed
     */
    protected function _getRedisObject()
    {
        try {
            if ( !($this->_redis instanceOf Redis) ) {
                $this->_redis = new Redis();
                if ( !$this->_redis->connect($this->_host,$this->_port) ) {
                    return false;
                }
            }
        }
        catch (RedisException $e)
        {
            return false;
        }
        
        return $this->_redis;
    }
    
    /**
     * @see SugarCacheAbstract::_setExternal()
     */
    protected function _setExternal(
        $key,
        $value
        )
    {
        $value = serialize($value);
        $key = $this->_fixKeyName($key);
        
        $this->_getRedisObject()->set($key,$value);
        $this->_getRedisObject()->expire($key, $this->expireTimeout);
    }
    
    /**
     * @see SugarCacheAbstract::_getExternal()
     */
    protected function _getExternal(
        $key
        )
    {
        $key = $this->_fixKeyName($key);
        $returnValue = $this->_getRedisObject()->get($key);
        
        return is_string($returnValue) ?
            unserialize($returnValue) :
            $returnValue;
    }
    
    /**
     * @see SugarCacheAbstract::_clearExternal()
     */
    protected function _clearExternal(
        $key
        )
    {
        $key = $this->_fixKeyName($key);
        $this->_getRedisObject()->delete($key);
    }
    
    /**
     * @see SugarCacheAbstract::_resetExternal()
     */
    protected function _resetExternal()
    {
        $this->_getRedisObject()->flushAll();
    }
    
    /**
     * Fixed the key naming used so we don't have any spaces
     *
     * @param  string $key
     * @return string fixed key name
     */
    protected function _fixKeyName($key)
    {
        return str_replace(' ','_',$key);
    }
}
