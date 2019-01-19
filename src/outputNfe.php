<?php

namespace smarter;

class outputNfe extends core
{
	const ENDPOINT = '/companies/$company$/output_nfes';

	const STATUS_TYPING = 1;
	const STATUS_RECEIVED = 2;
	const STATUS_REJECTED = 3;
	const STATUS_DENIED = 4;
	const STATUS_AUTHORIZED = 5;
	const STATUS_CANCELED = 8;
	const STATUS_DUPLICATED = 11;

	public static $statuses = [
		self::STATUS_TYPING => 'Em digitação',
		self::STATUS_RECEIVED => 'Lote recebido pela SEFAZ',
		self::STATUS_REJECTED => 'Rejeitada',
		self::STATUS_DENIED => 'Denegada',
		self::STATUS_AUTHORIZED => 'Autorizada',
		self::STATUS_CANCELED => 'Cancelada',
		self::STATUS_DUPLICATED => '"Duplicada',
	];

	/**
	 * Queries Smarter using given parameters
	 *
	 * @param array $params
	 * @return result
	 */
	public function get(array $params = [])
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
			$aux['filters'] = json_encode($params['filters']);
		}

		if (isset($params['is_grid'])) {
			$aux['is_grid'] = $params['is_grid'] ? 'true' : 'false';
		}

		$url = $this->url($aux);

		$curl = new curl($this, $url, curl::GET);
		$curl->execute();

		return $curl->result();
	}
}
