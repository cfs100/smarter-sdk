<?php

namespace smarter;

class city extends core
{
	const ENDPOINT = '/cities';

	/**
	 * Get Smarter states
	 *
	 * @param array $params
	 * @return result
	 */
	public function get(array $params = [])
	{
		$data = $this->getCurl($params)->response();

		return new result(json_encode([
			'count' => count($data),
			'data' => $data,
		]));
	}
}
