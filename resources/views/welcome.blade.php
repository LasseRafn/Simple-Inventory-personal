<!DOCTYPE html>
<html>
<head>
	<title>Lager</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1, user-scalable=no, maximum-scale=1.0"/>
	<script src="https://code.jquery.com/jquery-1.12.3.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/fastclick/1.0.6/fastclick.min.js"></script>
	<script src="/picker.js"></script>
	<script src="/picker.date.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.devbridge-autocomplete/1.2.24/jquery.autocomplete.min.js"></script>

	<link rel="stylesheet" href="/style.css">

	<style>
		* {
			box-sizing:         border-box;
			-webkit-box-sizing: border-box;
			max-width: 100%;
		}

		html, body {
			height: 100%;
		}

		body {
			margin:      0;
			padding:     0;
			width:       100%;
			display:     table;
			font-size:   14px;
			line-height: 1.6;
			font-family: 'Helvetica Neue', 'Helvetica', sans-serif;
		}

		hr {
			border: none;
			height: 1px;
			background: #ddd;
		}

		table.mobile {
			width:      100%;
			text-align: left;
		}

		table.mobile td, table.mobile th {
			padding: 4px;
		}

		a {
			padding:         4px 8px;
			display:         inline-block;
			text-decoration: none;
			background:      #eee;
			border-radius:   3px;
			color:           #333;
		}

		a:hover {
			background: #ddd;
		}

		.popup_bg {
			background: rgba(0, 0, 0, .45);
			display:    none;
			position:   absolute;
			left:       0;
			top:        0;
			z-index:    9;
			height:     100%;
			width:      100%;
		}

		.autocomplete-suggestions { border: 1px solid #999; background: #FFF; overflow: auto; }
		.autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
		.autocomplete-selected { background: #F0F0F0; }
		.autocomplete-suggestions strong { font-weight: normal; color: #3399FF; }
		.autocomplete-group { padding: 2px 5px; }
		.autocomplete-group strong { display: block; border-bottom: 1px solid #000; }

		.popup_bg .popup {
			background:    #fff;
			width:         600px;
			border-radius: 2px;
			margin:        5% auto;
			padding:       14px;
		}

		@media all and (max-width: 991px) {
			table.mobile thead {
				display: none;
			}

			table.mobile tr {
				display:       block;
				padding:       20px 5px;
				border-bottom: 1px solid #ccc;
			}

			table.mobile tr td {
				display: block;
			}

			table.mobile tr td:before {
				display:   block;
				font-size: 12px;
				color:     #777;
				content:   attr(data-th)
			}
		}

		input, button, select {
			display: block; padding: 4px;
			width: 100%;
			margin: 4px 0;
			padding: 8px;
			border-radius: 0;
			border: 1px solid #aaa;
		}
	</style>
</head>
<body>
<form method="POST" action="/new">
	<input type="text" name="name" class="name_input" placeholder="Navn / Produkt" />
	<input type="text" name="amount" placeholder="Antal" />
	<select name="unit">
		@foreach([ 'ml', 'l', 'g', 'kg', 'cl', 'stk', 'pk', 'dl' ] as $unit)
			<option value="{{ $unit }}">{{ strtoupper($unit) }}</option>
		@endforeach
	</select>
	<input type="text" name="expiry_input" class="datepicker" placeholder="Udløb" />
	<button type="submit">Tilføj</button>
	{{ csrf_field() }}
</form>
<hr/>
<table class="mobile">
	<thead>
	<tr>
		<th>Produkt</th>
		<th>Antal</th>
		<th>Mængde total</th>
		<th>Tidligste udløb</th>
		<th></th>
	</tr>
	</thead>
	<tbody>
	@foreach($items as $item)
		<tr>
			<td data-th="Produkt">{{ $item->name }} {{ $item->amount_single }} {{ strtoupper($item->unit) }}</td>
			<td data-th="Antal">{{ $item->count }}</td>
			<td data-th="Mængde">{{ number_format($item->amount, 0, ',', '.') }} {{ strtoupper($item->unit) }}</td>
			<td data-th="Udløb tidligst">{{ \Jenssegers\Date\Date::createFromFormat('Y-m-d', $item->expiry_date)->format('j. M Y') }}
				<span style="font-size: 11px; color: #aaa;">({{ \Jenssegers\Date\Date::createFromFormat('Y-m-d', $item->expiry_date)->diffForHumans() }})</span>
			</td>
			<td data-th="Handling">
				<a href="#" data-id="{{ $item->id }}" class="show">Se alle</a>
			</td>
		</tr>
	@endforeach
	</tbody>
</table>

<div class="popup_bg" id="popup-show">
	<div class="popup">
		<a href="#" class="close" style="float: right;" data-popup="#popup-show">X</a>
		<h2>Alle <span data-bind="name">...</span></h2>
		<table class="mobile">
			<thead>
				<th>Mængde</th>
				<th>Udløb</th>
				<th>Handling</th>
			</thead>
			<tbody data-bind="items">

			</tbody>
		</table>
	</div>
</div>

<script>
	$(function ()
	{
		FastClick.attach(document.body);
	});

	$('.datepicker').pickadate({
		format: "yyyy-mm-dd"
	});

	$('.name_input').autocomplete({
		serviceUrl: "/autocomplete"
	});
</script>

<script>
	$(".show").on("click", function (e)
	{
		e.preventDefault();

		var popup = $("#popup-show");

		var id = $(this).data('id');

		$.ajax({
			url: '/show/' + id,
			method: 'GET',
			dataType: 'JSON',
			success: function (response)
			{
				var newHtml = '';
				$.each(response.items, function (i, item)
				{
					newHtml += "<tr>" +
						"<td data-th=\"Mængde\">" + item.amount + " " + item.unit + "</td>" +
						"<td data-th=\"Udløb\">" + item.expiry_date + "</td>" +
						"<td data-th=\"Handling\"><a href=\"/delete/" + item.id + "\">Slet</a> <a href=\"/edit/" + item.id + "\">Rediger</a></td>" +
						"</tr>";
				});

				popup.find("[data-bind='name']").text(response.name);
				popup.find("[data-bind='items']").html(newHtml);
				popup.fadeIn(200);
			},
		});
	});

	$(".edit").on("click", function (e)
	{
		e.preventDefault();

	});

	$(".new").on("click", function (e)
	{
		e.preventDefault();

		alert($(this).data('id'));
	});

	$(".close").click(function (e)
	{
		e.preventDefault();

		$($(this).data('popup')).fadeOut(200);
	});
</script>
</body>
</html>
