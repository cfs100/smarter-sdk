<?php

namespace smarter;

class entity extends core
{
	const ENDPOINT = '/organizations/$organization$/entities';

	public function create(array $values)
	{
		$curl = new curl($this, $this->url(), curl::CREATE, ['entity' => $values]);

		return [
			'code' => $curl->execute(),
			'response' => $curl->response(false),
		];
	}

	public function edit($id, array $values)
	{
		$curl = new curl($this, "{$this->url()}/{$id}", curl::EDIT, ['entity' => $values]);

		return [
			'code' => $curl->execute(),
			'response' => $curl->response(false),
		];
	}
}
