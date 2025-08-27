<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Laravel</title>
	<script src="{{ asset('js/jquery.min.js') }}"></script>
	<!-- Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
	<!-- Styles -->
	<style>
		html,
		body {
			background-color: #fff;
			color: #64a19d;
			font-family: 'Raleway', sans-serif;
			font-weight: 100;
			height: 100vh;
			margin: 0;
		}

		a {
			color: #64a19d;
			font-weight: 600;
		}

		h4 {
			line-height: 2rem;
		}

		.full-height {
			height: 100vh;
		}

		.flex-center {
			align-items: center;
			display: flex;
			justify-content: center;
		}

		.position-ref {
			position: relative;
		}

		.top-right {
			position: absolute;
			right: 10px;
			top: 18px;
		}

		.content {
			text-align: center;
		}

		.title {
			font-size: 84px;
		}

		.links>a {
			clear: both;
			color: #636b6f;
			padding: 0 25px;
			font-size: 12px;
			font-weight: 600;
			letter-spacing: .1rem;
			text-decoration: none;
			text-transform: uppercase;
		}

		.m-b-md {
			margin-bottom: 100px;
		}

		ul {
			border: 1px solid #e0e0e0;
			padding: 15px;
			margin: 10px;
			box-shadow: 1px 1px 5px #ccc;
		}

		li {
			display: inline-block;
			width: 150px;
			border-bottom: 3px solid #ccc;
			margin: 5px;
			padding: 5px;
			list-style: none;
		}

		li:hover {
			box-shadow: 1px 2px 3px #CCC;
		}
	</style>
</head>

<body>
	<div class="flex-center position-ref full-height">
		@if (Route::has('login'))
		<div class="top-right links">
			@auth
			<a href="{{ url('/shinzemi/home') }}">Home</a>
			@else
			<a href="{{ route('login') }}">ログイン</a>
			@endauth
		</div>
		@endif

		<div class="content">

			<div class="title m-b-md">
				マスタ管理
			</div>

			<div class="links">
				<div class="menu">
					<div class="m-b-md">
						<ul>
							<li><a href="{{route('register.index')}}">ユーザー</a></li>
							<li><a href="{{route('school.index')}}">学校</a></li>
							<li><a href="{{route('bank.index')}}">銀行</a></li>
							<li><a href="{{route('discount.index')}}">割引</a></li>
							<li><a href="{{route('school_building.index')}}">校舎</a></li>
							<li><a href="{{route('branch_bank.index')}}">銀行支店</a></li>
							<li><a href="{{route('product.index')}}">講座</a></li>
							<li><a href="{{route('manage_target.index')}}">目標管理</a></li>
							<li><a href="{{route('authoritie.index')}}">権限</a></li>
							<li><a href="{{route('subject_teacher.index')}}">科目別担当講師</a></li>
							<li><a href="{{route('classification.index')}}">区分</a></li>
							<li><a href="{{route('questionnaire_rule.index')}}">アンケートルール</a></li>
							<li><a href="{{route('questionnaire_score.index')}}">アンケート点数</a></li>
							<li><a href="{{route('highschool_course.index')}}">高校コース</a></li>
						</ul>
					</div>
				</div>

			</div>
		</div>
	</div>
</body>

</html>
