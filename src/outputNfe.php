<?php

namespace smarter;

class outputNfe extends core
{
	const ENDPOINT = '/companies/$company$/output_nfes';

	const STATUS_TYPING = 1;
	const STATUS_RECEIVED = 2;
	const STATUS_REJECTED = 3;
	const STATUS_DENIED = 4;
	const STATUS_AUTHORIZED = 5;
	const STATUS_CANCELED = 8;
	const STATUS_DUPLICATED = 11;

	public static $statuses = [
		self::STATUS_TYPING => 'Em digitação',
		self::STATUS_RECEIVED => 'Lote recebido pela SEFAZ',
		self::STATUS_REJECTED => 'Rejeitada',
		self::STATUS_DENIED => 'Denegada',
		self::STATUS_AUTHORIZED => 'Autorizada',
		self::STATUS_CANCELED => 'Cancelada',
		self::STATUS_DUPLICATED => '"Duplicada',
	];
}
