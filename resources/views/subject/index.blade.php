@extends("layouts.app")
@section("content")
<!-- Sortable読み込み -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<!-- Sortableの実装 -->
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">成績教科マスタ</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th>成績カテゴリーNo</th>
									<th>成績カテゴリー名</th>
									<th></th>
								</tr>
							</thead>
							<tbody id="SortableArea" class="contents">
								@foreach($result_category as $key => $result_category_info)
								<div class="js_result_category_row">
									<tr>
										<td>{{ $result_category_info->id}} </td>
										<td>{{ $result_category_info->result_category_name}} </td>
										<td>
											<a href="{{ url('/shinzemi/subject/' . $result_category_info->id . '/edit') }}" title="Edit bank"><button class="btn btn-primary btn-xs">教科登録</button></a>
										</td>
									</tr>
								</div>
								@endforeach
							</tbody>
						</table>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection
