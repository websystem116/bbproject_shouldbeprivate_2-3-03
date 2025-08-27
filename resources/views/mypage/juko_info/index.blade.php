@extends("layouts.app")
@section("content")
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">生徒リスト</div>
				<div class="panel-body">
					<a href="{{ url('/shinzemi/student/create') }}" class="btn btn-success btn-sm" title="Add New student">
						新規追加
					</a>


					<div class="table-responsive">
						<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th>生徒No</th>
									<th>生徒名</th>
									<th>生徒名（カナ）</th>
								</tr>
							</thead>
							<tbody>
								@foreach($student as $item)
								<tr>
									<td>{{ $item->id}} </td>
									<td>{{ $item->surname}} {{ $item->name}}</td>
									<td>{{ $item->surname_kana}} {{$item->name_kana}}</td>
									<td>
										<a href="{{ url('/shinzemi/student/' . $item->id . '/edit') }}" title="Edit bank"><button class="btn btn-primary btn-xs">編集</button></a>
									</td>
									<td>
										<form method="POST" action="{{route('student.destroy',$item->id)}}" class="form-horizontal" style="display:inline;">
											{{ csrf_field() }}

											{{ method_field("DELETE") }}
											<button type="submit" class="btn btn-danger btn-xs" title="Delete student" onclick="return confirm('本当に{{$item->surname}} {{ $item->name}}を削除しますか？元に戻すことはできません。')">
												削除
											</button>
										</form>
								</tr>
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
