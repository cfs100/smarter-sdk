<?php

namespace smarter;

/**
 * cURL wrapper with specific API definitions
 *
 * This class handles:
 *
 * * cURL Initiation
 * * cURL Execution
 * * Result Instantiation
 * * cURL Closure
 *
 * @author Caio Ferreira Silva <caio@ferreirasilva.com.br>
 */
class curl
{
	/**
	 * HTTP method to retrieve data
	 *
	 * @var string
	 */
	const GET = 'GET';

	/**
	 * HTTP method to create records
	 *
	 * @var string
	 */
	const CREATE = 'POST';

	/**
	 * HTTP method to edit records
	 *
	 * @var string
	 */
	const EDIT = 'PUT';

	/**
	 * HTTP method to delete records
	 *
	 * @var string
	 */
	const DELETE = 'DELETE';

	/**
	 * Object that instantiated this class
	 *
	 * @var \smarter\core
	 */
	protected $caller;

	/**
	 * cURL resource
	 *
	 * @var resource
	 */
	protected $handler;

	/**
	 * String with raw result from request
	 *
	 * @var string
	 */
	protected $result;

	/**
	 * Creates a new instance of this class
	 *
	 * During instantiation the constructor will set all common cURL opts
	 * and set post data if needed and provided.
	 *
	 * @param \smarter\core $caller The object that is instantiating this class
	 * @param type $url The full URL to be requested
	 * @param type $method The HTTP method to be used
	 * @param array $data The data to be posted, if applies
	 */
	public function __construct(core $caller, $url, $method, array $data = null)
	{
		$this->caller = $caller;
		$this->handler = curl_init($url);

		curl_setopt($this->handler, CURLOPT_URL, $url);
		curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->handler, CURLOPT_HTTPHEADER, [
			'Accept: application/json',
			'Content-type: application/json',
		]);

		$caller->authenticate($this->handler);

		$addPayload = false;
		switch ($method) {
			case static::CREATE:
				$addPayload = true;
				curl_setopt($this->handler, CURLOPT_POST, true);
				break;
			case static::EDIT:
			case static::DELETE:
				$addPayload = true;
				curl_setopt($this->handler, CURLOPT_CUSTOMREQUEST, $method);
				break;
		}

		if ($addPayload && is_array($data)) {
			curl_setopt($this->handler, CURLOPT_POSTFIELDS, json_encode($data));
		}
	}

	/**
	 * Executes the current cURL handler and returns the response HTTP code
	 *
	 * @return int
	 */
	public function execute()
	{
		$this->result = curl_exec($this->handler);
		return curl_getinfo($this->handler, CURLINFO_HTTP_CODE);
	}

	/**
	 * Decodes JSON result string from last request and returns it
	 *
	 * @return \smarter\result
	 */
	public function result()
	{
		return new result($this->result);
	}

	/**
	 * Decodes JSON response string from last request and returns it
	 *
	 * @param boolean $json_decode Determines if result should be decoded
	 * @return mixed Array, if $json_decode, or raw string
	 */
	public function response($json_decode = true)
	{
		return $json_decode ? json_decode($this->result, true) : $this->result;
	}

	/**
	 * The destructor handles the closure of the current cURL handler
	 */
	public function __destruct()
	{
		curl_close($this->handler);
	}
}
