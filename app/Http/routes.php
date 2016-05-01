<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function ()
{
	$items = \App\Inventory::selectRaw('id, name, expiry_date, amount as amount_single, COUNT(DISTINCT id) as count, SUM(amount) as amount, unit')
						   ->groupBy('name')
						   ->groupBy('amount')
						   ->orderBy('expiry_date', 'ASC')
						   ->orderBy('name', 'ASC')
						   ->orderBy('amount', 'ASC')
						   ->get();

	return view('welcome', compact('items'));
});

Route::get('show/{id}', function ($id)
{
	$item = \App\Inventory::find($id);

	$items = \App\Inventory::where('name', $item->name)
						   ->where('amount', $item->amount)
						   ->orderBy('expiry_date', 'ASC')
						   ->get();


	return Response::json([ 'name' => $item->name, 'items' => $items ]);
});

Route::post('new', function ()
{
	\App\Inventory::add(Request::get('name'), Request::get('amount'), Request::get('unit'), Request::get('expiry_input'));

	return Redirect::back();
});

Route::get('autocomplete', function ()
{
	$suggestions = \App\Inventory::where('name', 'LIKE', '%' . Request::get("query") . '%')->limit(5)->get();

	$suggestionsData = [ ];

	foreach ( $suggestions as $suggestion )
	{
		$suggestionsData[] = [
			'value' => $suggestion->name,
			'data'  => $suggestion->name
		];
	}

	return Response::json([ 'suggestions' => $suggestionsData ]);
});