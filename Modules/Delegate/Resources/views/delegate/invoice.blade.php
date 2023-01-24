<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>{{  translate('INVOICE') }}</title>
	<style media="all">
        @page {
			margin: 0;
			padding:0;
		}
		
		body{
			font-size: 0.875rem;
            font-family: '<?php echo  $font_family ?>';
            font-weight: normal;
            direction: '<?php echo  $direction ?>';
            text-align: '<?php echo  $text_align ?>';
			padding:0;
			margin:0; 
		}
		.gry-color *,
		.gry-color{
			color:#000;
		}
		table{
			width: 100%;
		}
		table th{
			font-weight: normal;
		}
		table.padding th{
			padding: .25rem .7rem;
		}
		table.padding td{
			padding: .25rem .7rem;
		}
		table.sm-padding td{
			padding: .1rem .7rem;
		}
		.border-bottom td,
		.border-bottom th{
			border-bottom:1px solid #eceff4;
		}
		.text-left{
			text-align:<?php echo  $text_align ?>;
		}
		.text-right{
			text-align:<?php echo  $not_text_align ?>;
		}
	</style>
</head>
<body>
	<div>
		<div style="background: #eceff4;padding: 1rem;">
			<table>
				<tr>
					<td style="font-size: 1.5rem; text-align: center;" class="strong">{{ $delegate_name }}</td>
				</tr>
			</table>
		</div>

        <div style="padding: 1rem;">
            <table>
				<tr>
					<td style="font-size: 1.5rem; text-align: center;" class="strong">{{  translate('INVOICE') }}  @lang('delegate::delivery.payment_request')</td>
				</tr>
			</table>
        </div>

	    <div style="padding: 1rem;">
			<table class="padding text-left small border-bottom">
				<thead>
	                <tr class="gry-color" style="background: #eceff4;">
	                    <th width="35%" class="text-left">{{ translate('Code') }}</th>
						<th width="15%" class="text-left">@lang('delegate::delivery.weekly_personal_earnings')</th>
	                    <th width="10%" class="text-left">@lang('delegate::delivery.weekly_system_earnings')</th>
	                    <th width="15%" class="text-left">@lang('delegate::delivery.commission_earnings')</th>
	                </tr>
				</thead>
				<tbody class="strong">
	                @foreach ($items as $item)
                        <tr>
                            <td>{{ \App\Models\Order::find($item->order_id)->code }}</td>
                            <td>{{ single_price($item->personal_earnings) }}</td>
                            <td>{{ single_price($item->system_earnings) }}</td>
                            <td>{{ single_price($item->commission) }}</td>
                        </tr>
					@endforeach
	            </tbody>
			</table>
		</div>
	</div>
</body>
</html>


