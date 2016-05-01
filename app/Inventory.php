<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
	/**
	 * @var string
	 */
	protected $table = 'inventory';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'amount',
		'unit',
		'expiry_date'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
	];

	public static function add($name, $amount, $unit, $expiryDate)
	{
		return Inventory::create([
			'name'        => ucfirst($name),
			'amount'      => strtolower($amount),
			'unit'        => strtolower($unit),
			'expiry_date' => $expiryDate
		]);
	}
}
