@extends("layouts.app")
@section("content")
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">成績情報登録</div>
				<div class="panel-body">

					<div class="table-responsive">
						<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th>生徒No</th>
									<th>生徒氏名</th>
									<th>学年</th>
									<th>校舎名</th>
								</tr>
							</thead>
							<tbody>
								@foreach($student as $item)
								<tr>
									<td>{{ $item->id}} </td>
									<td>{{ $item->surname}} {{ $item->name}}</td>
									<td>{{ config('const.school_year')[$item->grade] ?? ""}}</td>
									<td>{{ $item->schoolbuilding->name ?? ""}} </td>
									<td>
										<a href="{{ url('/shinzemi/score_info/' . $item->id . '/edit') }}" title="Edit bank"><button class="btn btn-primary btn-xs">成績情報登録</button></a>
									</td>
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
