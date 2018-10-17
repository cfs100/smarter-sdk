<?php

namespace smarter;

class stock extends core
{
	const ENDPOINT = '/companies/$company$/stocks';

	public function adjustInitial($stockID, $quantity, $cost)
	{
		$stockID = (integer) $stockID;
		$url = $this->url() . "/{$stockID}/adjust_stock_balance";

		$curl = new curl($this, $url, curl::EDIT, [
			'adjustment' => [
				'adjustment_type' => 2,
				'new_initial_quantity' => (float) $quantity,
				'new_initial_unit_cost' => (float) $cost,
			],
			'company_id' => $this->expand('$company$'),
		]);

		return [
			'code' => $curl->execute(),
			'response' => $curl->response(false),
		];
	}

	public function adjustQuantity($stockID, $variation, $description)
	{
		$stockID = (integer) $stockID;
		$url = $this->url() . "/{$stockID}/adjust_stock_balance";

		$variation = (float) $variation;
		$type = 10;

		if ($variation < 0) {
			$type = 11;
			$variation *= -1;
		}

		$curl = new curl($this, $url, curl::EDIT, [
			'adjustment' => [
				'adjustment_type' => 1,
				'movement_type' => $type,
				'adjustment_quantity' => $variation,
				'justification_manual_adjustment' => trim($description),
			],
			'company_id' => $this->expand('$company$'),
		]);

		return [
			'code' => $curl->execute(),
			'response' => $curl->response(false),
		];
	}
}
