<?php

namespace smarter;

class attachment extends core
{
	const ENDPOINT = '/companies/$company$/attachments';

	const TYPE_OUTPUT_NFE = 'OutputNfe';

	public static $types = [
		self::TYPE_OUTPUT_NFE => 'Nota Fiscal Eletrônica',
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

		foreach (['type', 'id'] as $field) {
			if (!isset($params["object_{$field}"])) {
				throw new \BadMethodCallException("Attachment object_{$field} not provided");
			}

			$aux["object_{$field}"] = $params["object_{$field}"];
		}

		$pagination = ['start', 'limit', 'page'];
		foreach ($pagination as $field) {
			$$field = isset($params[$field]) ? (integer) $params[$field] : null;
		}

		if ($limit) {
			$aux['start'] = (integer) $start;
			$aux['limit'] = $limit;
			$aux['page'] = $page ?: 1;
		}

		$url = $this->url($aux);

		$curl = new curl($this, $url, curl::GET);
		$curl->execute();

		return $curl->result();
	}

	public function download($id)
	{
		$url = $this->expand(sprintf(
			'%s/organizations/%s/attachments/%d/download?%s',
			$this->domain(),
			'$organization$',
			(integer) $id,
			 http_build_query([
				 'is_general_files' => false,
			 ])
		));

		$curl = new curl($this, $url, curl::GET);
		$curl->execute();

		return $curl->response(false);
	}
}
