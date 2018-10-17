<?php

namespace smarter;

class state extends core
{
	const ENDPOINT = '/states';

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
