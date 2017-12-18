<?php

namespace smarter;

/**
 * Handles data parsing from endpoints results
 *
 * @author Caio Ferreira Silva <caio@ferreirasilva.com.br>
 */
class result
{
	/**
	 * The key to the currently fetched item
	 *
	 * @var mixed
	 */
	protected $key;

	/**
	 * The JSON decoded array of the result string
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Decodes and stores a raw JSON result string
	 *
	 * @param string $string The raw result string
	 */
	public function __construct($string)
	{
		$this->data = json_decode($string, true);
	}

	/**
	 * Returns the grand total from the API request
	 *
	 * @return integer
	 */
	public function total()
	{
		return isset($this->data['count']) ? $this->data['count'] : null;
	}

	/**
	 * Returns the available total from the API request
	 *
	 * @return integer
	 */
	public function available()
	{
		return isset($this->data['data']) ? count($this->data['data']) : null;
	}

	/**
	 * Tries to fetch a new item from result array
	 *
	 * @return boolean Did the result have one more item?
	 */
	public function fetch()
	{
		if (!isset($this->data['data'])) {
			return false;
		}

		$this->key = key($this->data['data']);
		if (is_null($this->key)) {
			reset($this->data['data']);
		} else {
			next($this->data['data']);
		}

		return !is_null($this->key);
	}

	/**
	 * Returns the current result item's whole array of data
	 *
	 * @return array
	 */
	public function data()
	{
		return !is_null($this->key) ? $this->data['data'][$this->key] : false;
	}

	/**
	 * Returns a given field value from current result item
	 *
	 * @param string $field The field name to be returned
	 * @return mixed
	 */
	public function get($field)
	{
		return isset($this->data['data'][$this->key][$field]) ? $this->data['data'][$this->key][$field] : null;
	}
}
