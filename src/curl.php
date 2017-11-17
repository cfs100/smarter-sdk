<?php

namespace smarter;

class curl
{
	const GET = 'GET';
	const CREATE = 'POST';
	const EDIT = 'PUT';
	const DELETE = 'DELETE';

	protected $caller;
	protected $handler;
	protected $result;

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
			curl_setopt($this->handler, CURLOPT_POSTFIELDS, $data);
		}
	}

	public function execute()
	{
		$this->result = curl_exec($this->handler);
		return curl_getinfo($this->handler, CURLINFO_HTTP_CODE);
	}

	public function result()
	{
		return json_decode($this->result);
	}

	public function __destruct()
	{
		curl_close($this->handler);
	}
}
