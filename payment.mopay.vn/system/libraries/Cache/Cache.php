<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package                CodeIgniter
 * @author                ExpressionEngine Dev Team
 * @copyright        Copyright (c) 2006 - 2012 EllisLab, Inc.
 * @license                http://codeigniter.com/user_guide/license.html
 * @link                http://codeigniter.com
 * @since                Version 2.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * CodeIgniter Caching Class
 *
 * @package                CodeIgniter
 * @subpackage        Libraries
 * @category        Core
 * @author                ExpressionEngine Dev Team
 * @link
 */
class CI_Cache extends CI_Driver_Library {

    protected $valid_drivers = array(
        'cache_apc', 'cache_file', 'cache_memcached', 'cache_dummy'
    );
    protected $_cache_path = NULL;  // Path of cache files (if file-based cache)
    protected $_adapter = 'dummy';
    protected $_backup_driver;
    public $params;
    private $cached_group;

    // ------------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param array
     */
    public function __construct($config = array()) {
        if ($config['adapter'] == 'memcached' || $config['adapter'] == 'file') {
            $config['adapter'] .= '-' . $config['params']['obj'];
        }
        if (!empty($config)) {
            $this->_initialize($config);
        }
    }

    public function store($key, $class, $func, $params = array(), $ttl = 0, $callback = NULL, $obj = NULL) {
        $value = $this->get($key);
        if ($value || $value === 0) {
            $this->cached_group[$key] = TRUE;
            return $value;
        }
        $value = call_user_func_array(array($class, $func), $params);
        if (empty($callback) === FALSE)
            if ($obj) {
                if (is_callable($callback)) {
                    $value = $callback($value, $obj);
                }
            } else {
                if (is_callable($callback)) {
                    $value = $callback($value);
                }
            }
        if ($value || $value === 0) {
            $this->cached_group[$key] = FALSE;
            $this->save($key, $value, $ttl);
            return $value;
        }
    }

    public function append_key($key, $group, $ttl = 0) {
        if (empty($this->cached_group) == FALSE) {
            if (in_array($key, array_keys($this->cached_group)) == TRUE) {
                if ($this->cached_group[$key] == TRUE) {
                    return TRUE;
                }
            }
        }

        $stack = $this->get($group);

        if (empty($stack) == FALSE) {
            if (in_array($key, $stack) == TRUE) {
                return TRUE;
            }
        }

        $stack[] = $key;
        $this->save($group, $stack, $ttl);
    }

    // ------------------------------------------------------------------------

    /**
     * Get
     *
     * Look for a value in the cache.  If it exists, return the data
     * if not, return FALSE
     *
     * @param        string
     * @return        mixed                value that is stored/FALSE on failure
     */
    public function get($id) {
        if (is_array($id) === TRUE)
            $id = $id['key'];
        $id = md5($id);
        return $this->{$this->_adapter}->get($id);
    }

    // ------------------------------------------------------------------------

    /**
     * Cache Save
     *
     * @param        string                Unique Key
     * @param        mixed                Data to store
     * @param        int                        Length of time (in seconds) to cache the data
     *
     * @return        boolean                true on success/false on failure
     */
    public function save($id, $data, $ttl = 0) {
        if (is_array($id) === TRUE) {
            if ($id['group'] && $id['key']) {
                $group_name = $id['group'];
                $id_md5 = md5($id['key']);
                $list = $this->get($group_name);
                if (is_array($list) === FALSE || in_array($id_md5, $list) === FALSE) {
                    $list[] = $id_md5;
                    $this->save($group_name, $list);
                }
                $id = $id['key'];
            } else {
                die('[MEMCACHE] Invalid group or key');
            }
        }
        $id = md5($id);
        return $this->{$this->_adapter}->save($id, $data, $ttl);
    }

    public function delete_group($group_name, $except_key = NULL) {
        $data = $this->get($group_name);
        if (empty($data) == TRUE) {
            return FALSE;
        }

        if ($except_key == NULL) {
            foreach ($data as $value) {
                $this->delete($value);
            }
            $this->delete($group_name);
        } else {
            if (is_array($except_key) == TRUE) {
                foreach ($data as $value) {
                    if (in_array($value, $except_key) == TRUE)
                        continue;
                    $this->delete($value);
                }
            } else {
                foreach ($data as $value) {
                    if ($except_key == $value)
                        continue;
                    $this->delete($value);
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Delete from Cache
     *
     * @param        mixed                unique identifier of the item in the cache
     * @return        boolean                true on success/false on failure
     */
    public function delete($id) {
        if (is_array($id) === TRUE)
            $id = $id['key'];
        $id = md5($id);
        return $this->{$this->_adapter}->delete($id);
    }

    // ------------------------------------------------------------------------

    /**
     * Clean the cache
     *
     * @return        boolean                false on failure/true on success
     */
    public function clean() {
        return $this->{$this->_adapter}->clean();
    }

    // ------------------------------------------------------------------------

    /**
     * Cache Info
     *
     * @param        string                user/filehits
     * @return        mixed                array on success, false on failure
     */
    public function cache_info($type = 'user') {
        return $this->{$this->_adapter}->cache_info($type);
    }

    // ------------------------------------------------------------------------

    /**
     * Get Cache Metadata
     *
     * @param        mixed                key to get cache metadata on
     * @return        mixed                return value from child method
     */
    public function get_metadata($id) {
        $id = md5($id);
        return $this->{$this->_adapter}->get_metadata($id);
    }

    // ------------------------------------------------------------------------

    /**
     * Initialize
     *
     * Initialize class properties based on the configuration array.
     *
     * @param        array
     * @return        void
     */
    private function _initialize($config) {
        $default_config = array(
            'adapter',
            'memcached',
            'params'
        );
        foreach ($default_config as $key) {
            if (isset($config[$key])) {
                $param = '_' . $key;
                $this->{$param} = $config[$key];
                $params[$key] = $config[$key];
            }
        }
        $this->params = $params;
        if (isset($config['backup'])) {
            if (in_array('cache_' . $config['backup'], $this->valid_drivers)) {
                $this->_backup_driver = $config['backup'];
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Is the requested driver supported in this environment?
     *
     * @param        string        The driver to test.
     * @return        array
     */
    public function is_supported($driver) {
        static $support = array();
        if (!isset($support[$driver])) {
            $support[$driver] = $this->{$driver}->is_supported($this->params);
        }
        return $support[$driver];
    }

    // ------------------------------------------------------------------------

    /**
     * __get()
     *
     * @param        child
     * @return        object
     */
    public function __get($child) {
        $obj = parent::__get($child);

        if (!$this->is_supported($child)) {
            $this->_adapter = $this->_backup_driver;
        }
        return $obj;
    }

    public function check() {
        return $this->{$this->_adapter}->status;
    }

    // ------------------------------------------------------------------------
}

// End Class

/* End of file Cache.php */
/* Location: ./system/libraries/Cache/Cache.php */