<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006 - 2012 EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 2.0
 * @filesource	
 */
// ------------------------------------------------------------------------

/**
 * CodeIgniter Memcached Caching Class 
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		ExpressionEngine Dev Team
 * @link		
 */
class CI_Cache_memcached extends CI_Driver {

    private $_memcached; // Holds the memcached object
    protected $_memcache_conf = array(
        'default' => array(
            'default_host' => '127.0.0.1',
            'default_port' => 11211,
            'default_weight' => 1
        )
    );
    public $status = TRUE;
    private $config_mem;

    // ------------------------------------------------------------------------	

    /**
     * Fetch from cache
     *
     * @param 	mixed		unique key id
     * @return 	mixed		data on success/false on failure
     */
    public function get($id) {
        if ($this->status === FALSE)
            return false;

        if(empty($this->config_mem['ns']) == FALSE){
            $id = $this->config_mem['ns'] . $id;
        }

        $data = $this->_memcached->get($id);
        return (is_array($data)) ? $data[0] : FALSE;
    }

    // ------------------------------------------------------------------------

    /**
     * Save
     *
     * @param 	string		unique identifier
     * @param 	mixed		data being cached
     * @param 	int			time to live
     * @return 	boolean 	true on success, false on failure
     */
    public function save($id, $data, $ttl = 60) {

        if(empty($this->config_mem['ns']) == FALSE){
            $id = $this->config_mem['ns'] . $id;
        }

        if ($this->status === FALSE)
            return false;
        if (get_class($this->_memcached) == 'Memcached') {
            return $this->_memcached->set($id, array($data, time(), $ttl), $ttl);
        } else if (get_class($this->_memcached) == 'Memcache') {
            return $this->_memcached->set($id, array($data, time(), $ttl), 0, $ttl);
        }

        return FALSE;
    }

    // ------------------------------------------------------------------------

    /**
     * Delete from Cache
     *
     * @param 	mixed		key to be deleted.
     * @return 	boolean 	true on success, false on failure
     */
    public function delete($id) {
        if ($this->status === FALSE)
            return false;

        if(empty($this->config_mem['ns']) == FALSE){
            $id = $this->config_mem['ns'] . $id;
        }

        return $this->_memcached->delete($id, 0);
    }

    // ------------------------------------------------------------------------

    /**
     * Clean the Cache
     *
     * @return 	boolean		false on failure/true on success
     */
    public function clean() {
        if ($this->status === FALSE)
            return false;
        return $this->_memcached->flush();
    }

    // ------------------------------------------------------------------------

    /**
     * Cache Info
     *
     * @param 	null		type not supported in memcached
     * @return 	mixed 		array on success, false on failure
     */
    public function cache_info($type = NULL) {
        if ($this->status === FALSE)
            return false;
        return $this->_memcached->getStats();
    }

    // ------------------------------------------------------------------------

    /**
     * Get Cache Metadata
     *
     * @param 	mixed		key to get cache metadata on
     * @return 	mixed		FALSE on failure, array on success.
     */
    public function get_metadata($id) {
        if ($this->status === FALSE)
            return false;
        $stored = $this->_memcached->get($id);

        if (count($stored) !== 3) {
            return FALSE;
        }

        list($data, $time, $ttl) = $stored;

        return array(
            'expire' => $time + $ttl,
            'mtime' => $time,
            'data' => $data
        );
    }

    // ------------------------------------------------------------------------

    /**
     * Setup memcached.
     */
    private function _setup_memcached($config) {
        if (extension_loaded('memcached'))
            $this->_memcached = new Memcached();
        if (extension_loaded('memcache'))
            $this->_memcached = new Memcache();
        if ($config['status'] == FALSE) {
            $this->status = FALSE;
            return FALSE;
        }
        $this->config_mem = $config;
        $cache_server = array();
        $cache_server['hostname'] = $config['hostname'];
        $cache_server['port'] = $config['port'];
        $cache_server['weight'] = 1;
        if (!@$this->_memcached->pconnect($cache_server['hostname'], $cache_server['port'], $cache_server['weight'])) {
            $this->status = FALSE;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Is supported
     *
     * Returns FALSE if memcached is not supported on the system.
     * If it is, we setup the memcached object & return TRUE
     */
    public function is_supported($params = array()) {
        if (!extension_loaded('memcached') && !extension_loaded('memcache')) {
            log_message('error', 'The Memcached Extension must be loaded to use Memcached Cache.');
            return FALSE;
        }
        $this->_setup_memcached($params['params']);
        return TRUE;
    }

    // ------------------------------------------------------------------------
}

// End Class

/* End of file Cache_memcached.php */
/* Location: ./system/libraries/Cache/drivers/Cache_memcached.php */