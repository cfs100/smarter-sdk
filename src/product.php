<?php

namespace smarter;

class product extends core
{
	const ENDPOINT = '/organizations/$organization$/products';

	public function edit($id, array $values)
	{
		$curl = new curl($this, "{$this->url()}/{$id}", curl::EDIT, $values);

		return [
			'code' => $curl->execute(),
			'response' => $curl->response(false),
		];
	}
}
