@extends("layouts.app")
@section("content")
<script src="{{ asset('/js/before_juku_sales.js') }}"></script>
@push('css')
<link href="{{ asset('css/before_juku_sales.css') }}" rel="stylesheet">
@endpush

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">入塾前売上登録</div>
				<div class="panel-body">
				<a href="{{ url('/shinzemi/before_juku_sales') }}" title="Back">
				<button class="btn btn-warning btn-xs">戻る</button></a>
					<br />
					<br />
					<form method="POST" action="{{route('before_juku_sales.store',['before_student_id' => $before_student->id])}}" class="form-horizontal">
						{{ csrf_field() }}
						<div class="container">
							<div class="row">
								<div class="col-md-2">
									<label>生徒No：{{ $before_student->id}}</label>
								</div>
								<div class="col-md-2">
									<label>生徒氏名：{{ $before_student->surname}} {{ $before_student->name}}</label>
								</div>
									<div class="col-md-2">
									<label>学年：{{config('const.school_year')[$before_student->grade]}}</label>
								</div>
								<div class="col-md-2">
									<label>校舎名：{{ $before_student->schoolbuilding->name ?? ""}}</label>
								</div>
								<div class="col-md-2">
									<label>学校名：{{ $before_student->school->name ?? ""}}</label>
								</div>
							</div>
						</div>
						<table class="table table-striped table-hover table-bordered table-condensed table_sticky">
							<thead>
								<tr>
									<th>選択</th>
									<th>明細No</th>
									<th>売上年月</th>
									<th>入金日</th>
									<th>商品</th>
									<th>割引後金額(円)</th>
									<th>備考</th>
								</tr>
							</thead>
							<tbody id="sales_table_tbody">
								@if(count($beforejukusales)=== 0)
								<tr>
									<td class="col-md-1">
										{{Form::hidden('select_flg[]', '0') }}
										{{Form::checkbox('select_flg[]', '1',false, ['class'=>'custom-control-input select_flg','id'=>'select_flg'])}}
									</td>
									<td class="col-md-1 text-center No">
										{{Form::label('No','1')}}
									</td>
									<td class="col-md-1">
										{{Form::date('sales_date[]', false, ['class' => 'form-control sales_date','id' => 'sales_date'])}}
									</td>
									<td class="col-md-1">
										{{Form::date('payment_date[]', false, ['class' => 'form-control payment_date','id' => 'payment_date'])}}
									</td>
									<td class="col-md-3">
										{{ Form::select('product_id[]',$products_select_list,false,['placeholder' => '選択してください',  'class' => 'form-control product_id']) }}
									</td>
									<td class="col-md-3">
										{{Form::number('price_after_discount[]', false, ['class' => 'form-control price_after_discount','id' => 'price_after_discount','placeholder' => ''])}}
									</td>
									<td class="col-md-3">
										{{Form::textarea('note[]', false, ['class' => 'form-control note', 'id' => 'note1', 'placeholder' => '', 'rows' => '2'])}}
									</td>
								</tr>
								@endif
								@foreach($beforejukusales as $beforejukusale)
								<tr>
									<td class="col-md-1">
										{{Form::hidden('select_flg[]', '0') }}
										{{Form::checkbox('select_flg[]', '1',false, ['class'=>'custom-control-input select_flg','id'=>'select_flg'])}}
									</td>
									<td class="col-md-1 text-center No">
										{{Form::label('No','1')}}
									</td>
									<td class="col-md-1">
										{{Form::date('sales_date[]', $beforejukusale['sales_date'] ?? "", ['class' => 'form-control sales_date','id' => 'sales_date'])}}
									</td>
									<td class="col-md-1">
										{{Form::date('payment_date[]', $beforejukusale['payment_date'] ?? "", ['class' => 'form-control payment_date','id' => 'payment_date'])}}
									</td>
									<td class="col-md-3">
										{{ Form::select('product_id[]',$products_select_list,$beforejukusale['product_id'],['placeholder' => '選択してください',  'class' => 'form-control product_id']) }}
									</td>
									<td class="col-md-3">
										{{Form::number('price_after_discount[]',$beforejukusale['price_after_discount']  ?? "", ['class' => 'form-control price_after_discount','id' => 'price_after_discount[]','placeholder' => ''])}}
									</td>
									<td class="col-md-3">
										{{Form::textarea('note[]', $beforejukusale['note'] ?? "", ['class' => 'form-control note', 'id' => 'note', 'placeholder' => '', 'rows' => '2'])}}
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
						<div class="form-group">
							<div class="col-md-2">
								{{Form::submit('追加', ['name' => 'add','class'=>'btn btn-success add-input-sale'])}}
							</div>
							<div class="col-md-2">
								{{Form::button('削除', ['class'=>'btn btn-danger sale-delete'])}}
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4">
								{{ Form::submit('更新', array('name' => 'update','class' => 'btn btn-primary')) }}
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
