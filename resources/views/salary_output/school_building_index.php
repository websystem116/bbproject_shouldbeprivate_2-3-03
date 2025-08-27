@extends('layouts.app')
@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">校舎別非常勤給与一覧出力</div>
				<div class="panel-body">


					<div class="form-group row">
						{{ Form::open(['route' => ['salary_output.export_school_building_salary_list'], 'method' => 'get', 'class' => 'form-horizontal']) }}
						<div class="col-md-1 col-form-label m-0">
							{{ Form::label('year', '年度') }}
						</div>
						<div class="col-md-2 m-0">
							{{ Form::text('year', null, ['class' => 'form-control', 'id' => 'year', 'placeholder' => '年度']) }}
						</div>
						<div class="col-md-1 col-form-label">
							{{ Form::label('month', '月') }}
						</div>
						<div class="col-md-2">
							{{ Form::select('month', config('const.month'), null, ['placeholder' => '選択してください', 'class' => 'form-control']) }}
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-2 ">
							{{ Form::submit('校舎別非常勤給与一覧出力', ['class' => 'btn btn-primary', 'onfocus' => 'this.blur();']) }}
						</div>
					</div> <br />
					<br />
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
