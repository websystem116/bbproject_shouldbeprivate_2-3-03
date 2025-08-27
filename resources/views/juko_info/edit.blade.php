@extends("layouts.app")
@section("content")
@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
<script src="{{ asset('/js/juko_info.js') }}"></script>
<script>
	$(function() {
		$('.select_search1').select2({
			language: "ja",
			width: '100%'
		});
	});
</script>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">受講情報個別登録</div>
				<div class="panel-body">
					{{-- <a href="{{ url('juko_info') }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a> --}}
					<a href="{{ url()->previous() }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
					<br />
					<br />
					<form method="POST" action="{{route('juko_info.store',['student_id' => $student->id,'student_no' => $student->student_no])}}" class="form-horizontal">
						{{ csrf_field() }}
						<div class="container">
							<div class="row">
								<div class="col-md-2">
									<label>生徒No：{{ sprintf('%08d', $student->student_no)}}</label>
								</div>
								<div class="col-md-2">
									<label>生徒氏名：{{ $student->surname}} {{ $student->name}}</label>
								</div>
								<div class="col-md-2">
									<label>学年：{{config('const.school_year')[$student->grade]}}</label>
								</div>
								<div class="col-md-2">
									<label>校舎名：{{ $student->schoolbuilding->name ?? ""}}</label>
								</div>
								<div class="col-md-2">
									<label>学校名：{{ $student->school->name ?? ""}}</label>
								</div>
								{{-- <div class="col-md-2">
									<label>割引：{{ $student->discount->name ?? ""}}</label>
								</div> --}}
							</div>
						</div>
						<table id="juko_table" class="table table-striped table-hover table-bordered table-condensed">
							<thead>
								<tr>
									<th>明細No</th>
									<th>商品</th>
									<th>削除</th>
								</tr>
							</thead>
							<tbody id="juko_table_tbody">
								@if(count($jukoinfos)=== 0)
								<tr>
									<td class="col-md-1 text-center No">
										{{Form::label('No',1)}}
									</td>
									<td>
										{{ Form::select('product_id[]',$products_select_list,false,['placeholder' => '選択してください','class' => 'form-control product_id']) }}
									</td>
									<td class="col-md-1">
										{{Form::button('削除', ['class'=>'btn btn-danger product_delete[]'])}}
									</td>
								</tr>
								@endif
								@foreach ($jukoinfos as $jukoinfo_key => $jukoinfo)
								<tr>
									<td class="col-md-1 text-center No">
										{{Form::label('No',1)}}
									</td>
									<td>
										{{ Form::select('product_id[]',$products_select_list,$jukoinfo['product_id'],['placeholder' => '選択してください','class' => 'form-control product_id select_search1']) }}
									</td>
									<td class="col-md-1">
										{{Form::button('削除', ['class'=>'btn btn-danger btn-xs product_delete','data-id'=>$jukoinfo_key])}}
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
						<div class="form-group row">
							<div class="col-md-12">
								{{Form::textarea('note', $student->note,null,['class' => 'form-control', 'id' => 'textarea_note', 'placeholder' => '備考欄', 'rows' => '3'])}}
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-2">
								{{Form::button('追加', ['class'=>'btn btn-success add-input-sale'])}}
							</div>

						</div>
						<div class="form-group">
							<div class="col-md-4">
								{{ Form::submit('更新', array('class' => 'btn btn-primary')) }}
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
