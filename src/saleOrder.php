<?php

namespace smarter;

class saleOrder extends core
{
	const ENDPOINT = '/companies/$company$/sale_orders';

	const STATUS_OPENED = 1;
	const STATUS_APPROVED = 2;
	const STATUS_INTERNAL_REVIEW = 3;
	const STATUS_REJECTED = 4;
	const STATUS_WAITING_CUSTOMER = 5;
	const STATUS_NO_STOCK = 6;
	const STATUS_CANCELLED = 7;
	const STATUS_WAITING_PICKUP = 8;
	const STATUS_SHIPPED = 9;
	const STATUS_WAITING_REPRESENTATIVE = 11;
	const STATUS_BILLING_PENDENCY = 12;
	const STATUS_QUOTE = 13;
	const STATUS_FUTURE_STOCK_REVIEW = 14;
	const STATUS_APPROVED_FUTURE_STOCK = 15;
	const STATUS_DELIVERED = 16;
	const STATUS_SELECTION_PICKING = 17;
	const STATUS_CREDIT_ANALYSIS = 18;
	const STATUS_REGISTER_ANALYSIS = 19;

	public static $statuses = [
		self::STATUS_OPENED => 'Em aberto',
		self::STATUS_APPROVED => 'Aprovado',
		self::STATUS_INTERNAL_REVIEW => 'Avaliação interna',
		self::STATUS_REJECTED => 'Rejeitado',
		self::STATUS_WAITING_CUSTOMER => 'Aguardando cliente',
		self::STATUS_NO_STOCK => 'Falta de estoque',
		self::STATUS_CANCELLED => 'Cancelado',
		self::STATUS_WAITING_PICKUP => 'Aguardando coleta',
		self::STATUS_SHIPPED => 'Despachado',
		self::STATUS_WAITING_REPRESENTATIVE => 'Aguardando representante',
		self::STATUS_BILLING_PENDENCY => 'Pendência financeira',
		self::STATUS_QUOTE => 'Orçamento',
		self::STATUS_FUTURE_STOCK_REVIEW => 'Avaliação de estoque futuro',
		self::STATUS_APPROVED_FUTURE_STOCK => 'Aprovado estoque futuro',
		self::STATUS_DELIVERED => 'Entregue',
		self::STATUS_SELECTION_PICKING => 'Seleção/Picking',
		self::STATUS_CREDIT_ANALYSIS => 'Análise de crédito',
		self::STATUS_REGISTER_ANALYSIS => 'Análise de cadastro',
	];

	public function calculate(array &$data, $id = null)
	{
		$baseURL = $this->expand($this->domain() . '/sale_orders/' . ((string) $id ?: 'new'));

		$preCalcCurl = new curl($this, "{$baseURL}/change_nfe_setting", curl::CREATE, [
			'company_id' => $this->expand('$company$'),
			'nfe_setting_id' => $data['nfe_setting_id'],
			'is_end_customer' => (boolean) $data['is_end_customer'],
			'entity_customer_id' => $data['entity_customer_id'],
			'sale_order' => [
				'sale_order_products_attributes' => $data['sale_order_products_attributes'],
			],
		]);

		$preCalcResult = [
			'code' => $code = $preCalcCurl->execute(),
			'response' => $preCalcCurl->response($code == 200),
		];

		if ($preCalcResult['code'] != 200) {
			return false;
		}

		foreach ($preCalcResult['response']['sale_order_products'] as $index => $preCalc) {
			foreach ($preCalc as $info => $value) {
				$data['sale_order_products_attributes'][$index][$info] = $value;
			}
		}

		$curl = new curl($this, "{$baseURL}/calculate", $id ? curl::EDIT : curl::CREATE, [
			'company_id' => $this->expand('$company$'),
			'sale_order' => [
				'sale_order_products_attributes' => $data['sale_order_products_attributes'],
				'nfe_setting_id' => $data['nfe_setting_id'],
				'sale_commission_table_id' => $data['sale_commission_table_id'],
				'entity_customer_id' => $data['entity_customer_id'],
				'discount_amount' => $data['discount_amount'],
				'discount_at' => $data['discount_at'],
				'discount_calc_rule' => $data['discount_calc_rule'],
				'discount_application' => $data['discount_application'],
				'discount_percent' => $data['discount_percent'],
				'discount_type' => $data['discount_type'],
				'freight_amount' => $data['freight_amount'],
				'insurance_amount' => $data['insurance_amount'],
				'is_end_customer' => (boolean) $data['is_end_customer'],

				'notes' => '',
			],
		]);

		$response = [
			'code' => $code = $curl->execute(),
			'response' => $curl->response($code == 200),
		];

		if ($response['code'] != 200) {
			return false;
		}

		foreach ($response['response'] as $field => $value) {
			if (is_array($value)) {
				continue;
			}

			$data[$field] = $value;
		}

		return true;
	}

	public function create(array $values)
	{
		$curl = new curl($this, $this->url(), curl::CREATE, ['sale_order' => $values]);

		$response = [
			'code' => $curl->execute(),
			'response' => $curl->response(),
		];

		if ($response['code'] == 200) {
			$response = $this->job($response['response']['token']);
		}

		return $response;
	}

	public function edit($id, array $values)
	{
		$curl = new curl($this, "{$this->url()}/{$id}", curl::EDIT, ['sale_order' => $values]);

		return [
			'code' => $curl->execute(),
			'response' => $curl->response(),
		];
	}
}
