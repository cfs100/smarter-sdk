<?php

namespace smarter;

class productCategory extends core
{
	const ENDPOINT = '/organizations/$organization$/product_categories';

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

		$url = $this->url($aux);

		$curl = new curl($this, $url, curl::GET);
		$curl->execute();

		return $curl->result();
	}
}
