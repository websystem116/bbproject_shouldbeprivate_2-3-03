@extends("layouts.app")
@section("content")
@push('css')
@endpush
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">受講情報個別登録</div>
				<div class="panel-body">
				<a href="{{ url('/shinzemi/juko_info') }}" title="Back">
				<button class="btn btn-warning btn-xs">戻る</button></a>
					<br />
					<br />
					<form method="POST" action="{{route('juko_info.store',['student_id' => $student->id])}}" class="form-horizontal">
						{{ csrf_field() }}
						<div class="container">
							<div class="row">
								<div class="col-md-2">
									<label>生徒No：{{ $student->id}}</label>
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
								<div class="col-md-2">
									<label>割引：{{ $student->discount->name ?? ""}}</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-2">
								{{Form::button('追加', ['class'=>'btn btn-success add-input-sale'])}}
							</div>
							<div class="col-md-2">
								{{Form::button('削除', ['class'=>'btn btn-danger sale-delete'])}}
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
