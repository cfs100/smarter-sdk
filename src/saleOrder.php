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
