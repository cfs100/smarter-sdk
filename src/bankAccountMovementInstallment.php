<?php

namespace smarter;

class bankAccountMovementInstallment extends core
{
    const ENDPOINT = '/bank_account_movement_installments';

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

		if (isset($params['bank_account_id']) && ($sort = (int) $params['bank_account_id'])) {
			$aux['bank_account_id'] = $params['bank_account_id'];
		}

		if (isset($params['sort']) && ($sort = trim($params['sort']))) {
			$aux['sort'] = $params['sort'];
			$aux['dir'] = isset($params['dir']) ? $params['dir'] : 'ASC';
		}

		if (isset($params['filters']) && is_array($params['filters'])) {
			$aux['filters'] = json_encode($params['filters']);
		}

		$url = $this->url($aux);

		$curl = new curl($this, $url, curl::GET);
		$curl->execute();

		return $curl;
	}
}
