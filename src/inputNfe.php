<?php

namespace smarter;

class inputNfe extends core
{
	const ENDPOINT = '/companies/$company$/input_nfes';

	const STATUS_TYPING = 1;
	const STATUS_RECEIVED = 2;
	const STATUS_REJECTED = 3;
	const STATUS_DENIED = 4;
	const STATUS_AUTHORIZED = 5;
	const STATUS_AUTHORIZED_SCAN = 6;
	const STATUS_AUTHORIZED_DPEC = 7;
	const STATUS_CANCELED = 8;
	const STATUS_SUPPLIER = 10;
	const STATUS_DUPLICATED = 11;
	const STATUS_WAITING_STOCK = 12;
	const STATUS_WAITING_SHIPPING = 13;

	public static $statuses = [
		self::STATUS_TYPING => 'Em digitação',
		self::STATUS_RECEIVED => 'Lote recebido pela SEFAZ',
		self::STATUS_REJECTED => 'Rejeitada',
		self::STATUS_DENIED => 'Denegada',
		self::STATUS_AUTHORIZED => 'Autorizada',
		self::STATUS_AUTHORIZED_SCAN => 'Autorizada SCAN',
		self::STATUS_AUTHORIZED_DPEC => 'Autorizada DPEC',
		self::STATUS_CANCELED => 'Cancelada',
		self::STATUS_SUPPLIER => 'Fornecedor',
		self::STATUS_DUPLICATED => 'Duplicada',
		self::STATUS_WAITING_STOCK => 'Aguardando distribuição estoque',
		self::STATUS_WAITING_SHIPPING => 'Aguardando distribuição embarque',
	];

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
			$aux['filters'] = $params['filters'];
		}

		$url = $this->url($aux);

		$curl = new curl($this, $url, curl::GET);
		$curl->execute();

		return $curl->result();
	}

	public function fullDetails($id)
	{
		$url = $this->url() . "/{$id}";

		$curl = new curl($this, $url, curl::GET);
		$curl->execute();

		return $curl->response();
	}
}
