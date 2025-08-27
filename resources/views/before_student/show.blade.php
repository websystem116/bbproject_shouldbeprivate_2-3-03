@extends("layouts.app")
@section("content")
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">bank {{ $bank->id }}</div>
				<div class="panel-body">

					<a href="{{ url("bank") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
					<a href="{{ url("bank") ."/". $bank->id . "/edit" }}" title="Edit bank"><button class="btn btn-primary btn-xs">Edit</button></a>
					<form method="POST" action="/bank/{{ $bank->id }}" class="form-horizontal" style="display:inline;">
						{{ csrf_field() }}
						{{ method_field("delete") }}
						<button type="submit" class="btn btn-danger btn-xs" title="Delete User" onclick="return confirm('Confirm delete')">
							Delete
						</button>
					</form>
					<br />
					<br />
					<div class="table-responsive">
						<table class="table table-borderless">
							<tbody>
								<tr>
									<th>id</th>
									<td>{{$bank->id}} </td>
								</tr>
								<tr>
									<th>code</th>
									<td>{{$bank->code}} </td>
								</tr>
								<tr>
									<th>name</th>
									<td>{{$bank->name}} </td>
								</tr>
								<tr>
									<th>name_kana</th>
									<td>{{$bank->name_kana}} </td>
								</tr>

							</tbody>
						</table>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection
