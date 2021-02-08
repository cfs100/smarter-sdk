<?php

namespace smarter;

class salePriceTable extends core
{
	const ENDPOINT = '/companies/$company$/sale_price_tables';

	public function create(array $values)
	{
		$curl = new curl($this, $this->url(), curl::CREATE, ['sale_price_table' => $values]);

		$response = [
			'code' => $curl->execute(),
			'response' => $curl->response(),
		];

		if ($response['code'] == 200) {
			$response['response'] = $this->job($response['response']['token'])['response']['id'];
		}

		return $response;
	}

	public function edit($id, array $values)
	{
		$curl = new curl($this, "{$this->url()}/{$id}", curl::EDIT, ['sale_price_table' => $values]);

		return [
			'code' => $curl->execute(),
			'response' => $curl->response(),
		];
	}
}
