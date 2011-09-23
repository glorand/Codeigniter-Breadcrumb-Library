<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Breadcrumb Class
 *
 * Help to create breadcrumbs
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Gombos Lorand
 * @version             1.0
 * 
 */
class Breadcrumb {

    /**
     * CI Instance
     * @var CI_Controller 
     */
    private $_ci;
    private $_breadcrumb_array = array();

    /**
     * Constructor.
     * @param array $config 
     */
    public function __construct($config = array()) {
        $this->_ci = & get_instance();
        $this->_ci->load->config('breadcrumb');
        $this->home_element = $this->_ci->config->item('breadcrumb_home_element');
        $this->use_home_element = $this->_ci->config->item('breadcrumb_use_home_element');
        $this->element_options = $this->_ci->config->item('breadcrumb_element_options');
        $this->home_element_options = $this->_ci->config->item('breadcrumb_home_element_options');
        $this->last_element_options = $this->_ci->config->item('breadcrumb_last_element_options');
        $this->separator = $this->_ci->config->item('breadcrumb_separator');
        if (count($config) > 0) {
            $this->initialize($config);
        }
        $this->_ci->load->helper('url');
        log_message('debug', 'Breadcrumb Class Initialized');
    }

    /**
     * Set the class atributes with the configuration values.
     * @param array $config 
     */
    public function initialize($config) {
        foreach ($config AS $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Add element to the breadcrumb
     * @param string $name
     * @param string $url
     * @param array $options
     * @return void
     */
    public function addElement($name = '', $url = '', $options = array()) {
        $key = $this->_nameToKey($name);
        if (!$key OR isset($this->_breadcrumb_array[$key])) {
            return false;
        }
        $this->_breadcrumb_array[$key] = array('name' => $name, 'url' => $url, 'options' => $options);
    }

    /**
     * Add more elements to the breadcrumb
     * @param array $elements
     * @return void
     */
    public function addElements($elements = array()) {
        if (!is_array($elements))
            return false;
        foreach ($elements AS $key => $row) {
            if (isset($row['name']) AND isset($row['url'])) {
                $this->addElement($row['name'], $row['url'], isset($row['options']) ? $row['options'] : array());
            }
        }
    }

    /**
     * Remove an element from the breadcrumb.
     * Return true or false, if completed sucessfly.
     * @param string $name
     * @return boolean
     */
    public function removeElementByKey($name = '') {
        $key = $this->_nameToKey($name);
        if (!$key) {
            return false;
        }
        elseif (isset($this->_breadcrumb_array[$key])) {
            unset($this->_breadcrumb_array[$key]);
            return true;
        }
        return false;
    }

    /**
     * Reset the breadcrumb array.
     */
    public function reset() {
        $this->_breadcrumb_array = array();
    }

    /**
     * Magic methode.
     * @return string
     */
    public function __toString() {
        return $this->get();
    }

    /**
     * Return the generated breadcrumb
     * @return string
     */
    public function get() {
        $result_array = $this->getGeneratedArray();
        $result_str = '';
        if (count($result_array)) {
            $result_str = implode($this->separator, $result_array);
        }
        return $result_str;
    }

    /**
     * Generate an array with final elements of the breadcrumb.
     * @return array
     */
    public function getGeneratedArray() {
        $breadcrumb_array = $this->_breadcrumb_array;
        if ($this->use_home_element) {
            array_unshift($breadcrumb_array, $this->home_element);
        }
        end($breadcrumb_array);
        $last_key = key($breadcrumb_array);
        $result_array = array();
        foreach ($breadcrumb_array AS $key => $row) {
            if (!isset($row['name']))
                continue;

            if (!$key) {
                $options = $this->home_element_options;
            }
            elseif ($key == $last_key) {
                $options = $this->last_element_options;
            }
            else {
                $options = $this->element_options;
            }

            if (isset($row['options']) AND is_array($row['options'])) {
                $options = array_merge($options, $row['options']);
            }
            $tmp = anchor($row['url'], $row['name'], $options);
            array_push($result_array, $tmp);
        }
        return $result_array;
    }

    /**
     * Return the breadcrumb in array format.
     * @return array
     */
    public function getBreadcrumbArray() {
        return $this->_breadcrumb_array;
    }

    /**
     * Return the md5 representatio of $name.
     * @param string $name
     * @return string
     */
    private function _nameToKey($name = '') {
        return!empty($name) ? md5($name) : false;
    }

}

/* End of file Breadcrumb.php */
/* Location: ./application/libraries/Breadcrumb.php */