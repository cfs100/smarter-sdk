<?php

namespace smarter;

class nfe extends core
{
	const ENDPOINT = '/companies/$company$/nfes';

	/**
	 * Queries Smarter using given parameters
	 *
	 * @param array $params
	 * @return result
	 */
	public function filter(array $params)
	{
		$url = "{$this->url()}/filter?" . http_build_query(array_merge(['_dc' => time()], $params));

		$curl = new curl($this, $url, curl::GET);
		$curl->execute();

		return $curl->result();
	}

	/**
	 * Ask Smarter to send nfes using given parameters
	 *
	 * @param array $params
	 * @return result
	 */
	public function mail(array $ids, array $emails)
	{
		$curl = new curl($this, "{$this->url()}/mail_files", curl::CREATE, [
			'nfe_ids' => implode(',', $ids),
			'has_cce' => 'false',
			'file_type' => 0,
			'emails' => implode(',', $emails),
		]);

		$response = [
			'code' => $curl->execute(),
			'response' => $curl->response(),
		];

		return $response;
	}
}
