<?php

namespace smarter;

/**
 * Core for all classes that access API endpoints
 *
 * This class handles:
 *
 * * Configuration
 * * cURL Authentication
 * * URL Generation
 *
 * All classes that access API endpoints extends this one.
 *
 * @author Caio Ferreira Silva <caio@ferreirasilva.com.br>
 */
abstract class core
{
	/**
	 * Sandbox environment name to be used in configuration
	 *
	 * @var string
	 */
	const ENVIRONMENT_SANDBOX = 'sandbox';

	/**
	 * Production environment name to be used in configuration
	 *
	 * @var string
	 */
	const ENVIRONMENT_PRODUCTION = 'app-legacy';

	/**
	 * Domain string to be expanded when needed
	 *
	 * @var string
	 * @see \smarter\core::expand()
	 */
	const DOMAIN = '$environment$.worksmarter.com.br';

	/**
	 * Holds default configuration set before instantiation
	 *
	 * @var array
	 */
	protected static $defaultConfig = [];

	/**
	 * All required items to be set before or during instantiation
	 *
	 * @var array
	 */
	protected static $required = [
		'environment',
		'user',
		'password',
		'organization',
		'company',
	];

	/**
	 * Holds all configuration of current instance
	 *
	 * Used configuration parameters are:
	 *
	 * * environment
	 * * token
	 * * organization
	 *
	 * Settings may vary according to each endpoint.
	 *
	 * @var array
	 */
	protected $config = [];

	/**
	 * Creates a new instance of this class
	 *
	 * During instantiation the constructor will validate all required
	 * configuration settings and will throw exceptions if missing any.
	 *
	 * @uses \smarter\core::config()
	 * @param array $config Key-value array, overrides prior settings
	 * @throws \Exception If class does not have ENDPOINT constant
	 * @throws \InvalidArgumentException If missing required setting
	 */
	public function __construct(array $config = [])
	{
		if (!defined('static::ENDPOINT')) {
			throw new \Exception('Class ' . static::class . ' does not have ENDPOINT constant');
		}

		$this->config = static::$defaultConfig;
		static::config($config, $this->config);

		foreach (static::$required as $item) {
			if (!(isset($this->config[$item]) && $this->config[$item])) {
				throw new \InvalidArgumentException("Missing configuration: {$item}");
			}
		}
	}

	/**
	 * Sets configuration settings according to given array
	 *
	 * @param array $data Key-value array with settings
	 * @param array $config Existing array of settings to use. Default: core::$defaultConfig
	 */
	public static function config(array $data, array &$config = null)
	{
		if (is_null($config)) {
			$config = &static::$defaultConfig;
		}

		foreach ($data as $item => $value) {
			$config[$item] = trim($value);
		}
	}

	/**
	 * Expands string with configuration settings
	 *
	 * Expansion works by replacing keys with configuration settings. The
	 * format is: $name$
	 *
	 * For instance:
	 *
	 * * /endpoint.json?token=$token$
	 *
	 * will become
	 *
	 * * /endpoint.json?token=123456 *
	 *
	 * @param string $string String to be expanded
	 * @return string
	 */
	protected function expand($string)
	{
		$parts = explode('$', $string);

		$count = 0;
		foreach ($parts as $index => $part) {
			if ($count++ % 2 == 0) {
				continue;
			}

			$parts[$index] = $this->config[$part];
		}

		return implode(null, $parts);
	}

	/**
	 * Sets proper authentication for the given cURL resource
	 *
	 * @param resource $curl Resource of type curl
	 */
	public function authenticate($curl)
	{
		curl_setopt($curl, CURLOPT_USERPWD, "{$this->config['user']}:{$this->config['password']}");
	}

	/**
	 * Generates a full URL according to object's settings
	 *
	 * @param array $parameters Parameters to be added to final URL
	 * @return string
	 */
	protected function url(array $parameters = [])
	{
		$url = $this->expand($this->domain() . static::ENDPOINT);
		return $url . (!empty($parameters) ? '?' . http_build_query($parameters) : null);
	}

	/**
	 * Generates a URL with protocol and domain according to object's settings
	 *
	 * @return string
	 */
	protected function domain()
	{
		$protocol = $this->config['environment'] == static::ENVIRONMENT_PRODUCTION ? 'https' : 'http';
		return $this->expand("{$protocol}://" . static::DOMAIN);
	}

	/**
	 * Queries Smarter using given parameters
	 *
	 * @param array $params
	 * @return result
	 */
	public function get(array $params = [])
	{
		return $this->getCurl($params)->result();
	}

	/**
	 * Queries Smarter using given parameters
	 *
	 * @param array $params
	 * @return curl
	 */
	protected function getCurl(array $params = [])
	{
		$aux = ['_dc' => time()];

		$pagination = ['start', 'limit', 'page'];
		foreach ($pagination as $field) {
			$$field = isset($params[$field]) ? (integer) $params[$field] : null;
		}

		if ($limit) {
			$aux['start'] = (integer) $start;
			$aux['limit'] = $limit;
			$aux['page'] = $page ?: 1;
		}

		if (isset($params['sort']) && ($sort = trim($params['sort']))) {
			$aux['sort'] = $params['sort'];
			$aux['dir'] = isset($params['dir']) ? $params['dir'] : 'ASC';
		}

		if (isset($params['filters']) && is_array($params['filters'])) {
			$aux['filters'] = $params['filters'];
		}

		$url = $this->url($aux);

		$curl = new curl($this, $url, curl::GET);
		$curl->execute();

		return $curl;
	}

	/**
	 * Retrieves full details for the given ID using current endpoint
	 *
	 * @return array
	 */
	public function fullDetails($id)
	{
		$url = $this->url() . "/{$id}";

		$curl = new curl($this, $url, curl::GET);
		$curl->execute();

		return $curl->response();
	}

	/**
	 * Checks for job response
	 *
	 * @param string $id
	 * @return array
	 */
	public function job($id)
	{
		$response = [
			'code' => null,
			'response' => null,
		];

		for ($i = 0; $i < 5; $i++) {
			sleep(2);

			$curl = new curl($this, "{$this->domain()}/jobs/{$id}?_dc=" . time(), curl::GET);

			$response = [
				'code' => $curl->execute(),
				'response' => $curl->response(),
			];

			if (isset($response['response']['id'])) {
				$response['response'] = json_decode($response['response']['id'], true);
				break;
			}
		}

		return $response;
	}
}
