<?php

namespace smarter;

class inputNfe extends core
{
	const ENDPOINT = '/companies/$company$/input_nfes';

	const STATUS_TYPING = 1;
	const STATUS_RECEIVED = 2;
	const STATUS_REJECTED = 3;
	const STATUS_DENIED = 4;
	const STATUS_AUTHORIZED = 5;
	const STATUS_AUTHORIZED_SCAN = 6;
	const STATUS_AUTHORIZED_DPEC = 7;
	const STATUS_CANCELED = 8;
	const STATUS_SUPPLIER = 10;
	const STATUS_DUPLICATED = 11;
	const STATUS_WAITING_STOCK = 12;
	const STATUS_WAITING_SHIPPING = 13;

	const PURPOSE_REGULAR = 1;
	const PURPOSE_ADDITIONAL = 2;
	const PURPOSE_ADJUST = 3;
	const PURPOSE_RETURN = 4;

	public static $statuses = [
		self::STATUS_TYPING => 'Em digitação',
		self::STATUS_RECEIVED => 'Lote recebido pela SEFAZ',
		self::STATUS_REJECTED => 'Rejeitada',
		self::STATUS_DENIED => 'Denegada',
		self::STATUS_AUTHORIZED => 'Autorizada',
		self::STATUS_AUTHORIZED_SCAN => 'Autorizada SCAN',
		self::STATUS_AUTHORIZED_DPEC => 'Autorizada DPEC',
		self::STATUS_CANCELED => 'Cancelada',
		self::STATUS_SUPPLIER => 'Fornecedor',
		self::STATUS_DUPLICATED => 'Duplicada',
		self::STATUS_WAITING_STOCK => 'Aguardando distribuição estoque',
		self::STATUS_WAITING_SHIPPING => 'Aguardando distribuição embarque',
	];

	public static $purposes = [
		self::PURPOSE_REGULAR => 'Normal',
		self::PURPOSE_ADDITIONAL => 'Complementar',
		self::PURPOSE_ADJUST => 'Ajuste',
		self::PURPOSE_RETURN => 'Devolução',
	];
}
