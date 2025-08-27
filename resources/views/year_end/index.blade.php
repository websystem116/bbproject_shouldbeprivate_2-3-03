@extends("layouts.app")
@section("content")
@push('css')
@endpush
@push('scripts')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">年度末処理</div>
				<div class="panel-body">
					<div class="my-5">
						<h3 class="title">【年度末処理に当たって】</h3>
						<ul>
							<li class="mb-3">
								年度末処理開始の取り消し、巻き戻しはできません。
							</li>
							<li>
								年度末処理開始後、生徒情報の学年、それに紐づく兄弟情報の学年,<br>
								入塾前生徒情報の学年、それに紐づく兄弟情報の学年が上の学年に進みます。
							</li>
						</ul>
					</div>
					<div>
						<a href="{{ route('year_end.fiscal_year_end_process') }}" title="fiscal_year_end_process"><button class="btn btn-primary btn-xs">年度末処理開始</button></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
@endsection
