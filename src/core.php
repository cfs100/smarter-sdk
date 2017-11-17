<?php

namespace smarter;

abstract class core
{
	const ENVIRONMENT_SANDBOX = 'sandbox';
	const ENVIRONMENT_PRODUCTION = 'app';
	const DOMAIN = '$environment$.worksmarter.com.br';

	protected static $defaultConfig = [];
	protected $config = [];
	protected static $required = [
		'environment',
		'token',
		'organization',
	];

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

	public static function config(array $data, array &$config = null)
	{
		if (is_null($config)) {
			$config = &static::$defaultConfig;
		}

		foreach ($data as $item => $value) {
			$config[$item] = trim($value);
		}
	}

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

	public function authenticate($curl)
	{
		curl_setopt($curl, CURLOPT_USERPWD, $this->config['token'] . ':X');
	}


	protected function url(array $parameters = [])
	{
		$protocol = $this->config['environment'] == static::ENVIRONMENT_PRODUCTION ? 'https' : 'http';
		$url = $this->expand("{$protocol}://" . static::DOMAIN . static::ENDPOINT);
		return $url . (!empty($parameters) ? '?' . http_build_query($parameters) : null);
	}

	public abstract function get();
}
