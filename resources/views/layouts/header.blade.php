<nav class="navbar navbar-default">
	<div class="container-fluid">

		<div class="navbar-header" style="margin-top:8px;margin-right: 8px;">
			<!-- Mobile hamburger button -->
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			
			<a class="" href="https://www.shinzemi.co.jp/" target="_blank">
				<img src="{{ asset('logo.gif') }}" alt="logo" style="max-height: 40px;">
			</a>
		</div>

		<!-- Collapsible content -->
		<div class="collapse navbar-collapse" id="navbar-collapse">
			<!-- if logouted  -->
			@if (Auth::check())
			@if (Auth::user()->roles != 4)
			<ul class="nav navbar-nav">

				<li class="nav-item" style="text-align: center;font-size: 16px;border-left: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;" onMouseOver="this.style.backgroundColor='#f5f5f5'" onMouseOut="this.style.backgroundColor='#fff'">
					<a href="/shinzemi">
						<span>Home</span>
					</a>
				</li>

				<li class="dropdown nav-item" style="text-align: center;border-right: 1px solid #dcdcdc;" onMouseOver="this.style.backgroundColor='#f5f5f5'" onMouseOut="this.style.backgroundColor='#fff'">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">生徒情報<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="{{route('student.index')}}">生徒情報登録</a></li>
						<li><a href="{{route('juko_info.index')}}">受講情報登録</a></li>
						<li><a href="{{route('average_point.index')}}">成績情報登録/平均点登録</a></li>
						<li><a href="{{route('student_karte.index')}}">過去成績情報登録/平均点確認</a></li>
						<li><a href="{{route('score.index')}}">試験別成績一覧</a></li>
						<li><a href="{{route('year_end.index')}}">年度末処理</a></li>
					</ul>
				</li>

				<li class="dropdown nav-item" style="text-align: center;border-right: 1px solid #dcdcdc;" onMouseOver="this.style.backgroundColor='#f5f5f5'" onMouseOut="this.style.backgroundColor='#fff'">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">入塾前生徒情報<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="{{route('before_student.index')}}">入塾前情報登録</a></li>
						<li><a href="{{route('before_juku_sales.index')}}">入塾前売上登録</a></li>
						<li><a href="{{route('before_juku_detail.index')}}">入塾前売上明細出力</a></li>
					</ul>
				</li>

				<li class="dropdown nav-item" style="text-align: center;border-right: 1px solid #dcdcdc;" onMouseOver="this.style.backgroundColor='#f5f5f5'" onMouseOut="this.style.backgroundColor='#fff'">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">アンケート<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="{{route('questionnaire_content.index')}}">アンケート内容登録</a></li>
						<li><a href="{{route('questionnaire_import.create')}}">アンケート結果自動取込</a></li>
						<li><a href="{{route('questionnaire_decision.create')}}">アンケート結果集計・確定</a></li>
						<li><a href="{{route('questionnaire_results_detail.index')}}">アンケート結果確認</a></li>
						<li><a href="{{route('questionnaire_output.index')}}">アンケート結果出力</a></li>
					</ul>
				</li>

				<li class="dropdown nav-item" style="text-align: center;border-right: 1px solid #dcdcdc;" onMouseOver="this.style.backgroundColor='#f5f5f5'" onMouseOut="this.style.backgroundColor='#fff'">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">マスタ<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li>
							<a href="{{ route('register.index')}}">ユーザー</a>
						</li>
						<li><a href="{{route('bank.index')}}">銀行</a></li>
						<li><a href="{{route('branch_bank.index')}}">銀行支店</a></li>
						<li><a href="{{route('product.index')}}">商品</a></li>
						<li><a href="{{route('discount.index')}}">割引</a></li>

						<li>
							<a href="{{ route('invoice_comment.index') }}">請求書説明文</a>
						</li>

						<li><a href="{{route('subject_teacher.index')}}">科目担当講師</a></li>
						<li><a href="{{route('questionnaire_score.index')}}">講師別アンケート数値</a></li>

						<li>
							<a href="{{ route('job_description.index') }}">業務内容</a>
						</li>

						<li>
							<a href="{{ route('other_job_description.index') }}">その他実績種別</a>
						</li>

						<li><a href="{{route('school_building.index')}}">校舎</a></li>
						<li><a href="{{route('school.index')}}">学校</a></li>
						<li><a href="{{route('highschool_course.index')}}">高校コース</a></li>
						<li><a href="{{route('result_category.index')}}">成績カテゴリー</a></li>

						<li>
							<a href="{{ route('division_code.index') }}">売上区分</a>
						</li>

						<li><a href="{{route('company.edit',1)}}">会社</a></li>
					</ul>
				</li>

				<!-- Add announcement menu item -->
				@if(in_array(auth()->user()->roles, [1, 2]))
				<li class="nav-item" style="text-align: center;border-right: 1px solid #dcdcdc;" onMouseOver="this.style.backgroundColor='#f5f5f5'" onMouseOut="this.style.backgroundColor='#fff'">
					<a href="{{ route('announcements.index') }}">
						<span>お知らせ管理</span>
					</a>
				</li>
				@endif

			</ul>
			@endif
			@endif

			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<p class="navbar-text" style="margin-right: 8px">
						{{ Auth::user()->last_name ?? ''}} {{ Auth::user()->first_name ?? ''}}
						<a class="navbar-link" href="{{ route('logout') }}" style="padding-left:16px">ログアウト</a>
					</p>
				</li>
			</ul>
		</div>
	</div>
</nav>

<style>
/* General navbar fixes */
.navbar-nav {
	list-style: none !important;
}

.navbar-nav > li {
	display: inline-block;
	vertical-align: top;
}

/* Force visibility of navbar items */
.nav-item {
	visibility: visible !important;
	opacity: 1 !important;
	display: inline-block !important;
}

/* Bootstrap navbar collapse fix */
.navbar-collapse {
	border-top: none !important;
}

/* Ensure navbar has enough width */
.container-fluid {
	width: 100% !important;
	max-width: none !important;
}

/* Fix navbar item display */
.navbar-nav > li {
	float: left !important;
}

.navbar-default .navbar-nav > li > a {
	color: #777 !important;
}

/* Prevent navbar from wrapping */
.navbar {
	min-height: 50px !important;
	white-space: nowrap;
}

/* Mobile responsive navbar styles */
@media (max-width: 767px) {
	.navbar-nav {
		margin: 0;
	}
	
	.navbar-nav > li {
		width: 100% !important;
		border: none !important;
		border-bottom: 1px solid #ddd !important;
		display: block !important;
	}
	
	.navbar-nav > li > a {
		padding: 15px !important;
		text-align: left !important;
	}
	
	.dropdown-menu {
		position: static !important;
		float: none !important;
		width: 100% !important;
		margin-top: 0 !important;
		background-color: #f8f8f8 !important;
		border: none !important;
		box-shadow: none !important;
	}
	
	.dropdown-menu > li > a {
		padding: 10px 20px !important;
		color: #777 !important;
	}
	
	.navbar-text {
		margin: 15px !important;
	}
	
	.navbar-header img {
		max-height: 30px !important;
	}
	
	/* Remove hover effects on mobile */
	.nav-item {
		background-color: #fff !important;
	}
	
	.nav-item:hover {
		background-color: #f5f5f5 !important;
	}
}

@media (min-width: 768px) {
	.nav-item {
		width: 180px;
		display: table-cell !important; /* Force display on desktop */
	}
	
	.navbar-nav {
		display: table !important; /* Force table display */
		width: auto !important;
		table-layout: auto !important;
	}
	
	.navbar-nav > li:first-child {
		width: 260px;
	}
	
	/* Ensure all nav items are visible */
	.navbar-collapse {
		display: block !important;
		height: auto !important;
		padding-bottom: 0;
		overflow: visible !important;
	}
	
	.navbar-collapse.collapse {
		display: block !important;
	}
	
	.navbar-collapse.in {
		overflow-y: visible !important;
	}
}
</style>