<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInventory extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('inventory', function (Blueprint $table)
		{
			$table->enum('unit', [ 'ml', 'l', 'g', 'kg', 'cl', 'stk', 'pk', 'dl' ])->after('amount');
			$table->integer('amount')->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('inventory', function (Blueprint $table)
		{
			$table->dropColumn('unit');
			$table->string('amount', 10)->change();
		});
	}
}
