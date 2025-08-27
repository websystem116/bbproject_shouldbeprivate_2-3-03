<!-- <nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">進学ゼミナール</a>
    </div>
    <ul class="nav navbar-nav">
      <li class="active"><a href="/shinzemi">Home</a></li>
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">マスタ<span class="caret"></span></a>
        <ul class="dropdown-menu">
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
          <li><a href="{{route('questionnaire_content.index')}}">アンケート内容</a></li>
        </ul>
      </li>
      {{-- <li><a href="#">Page 2</a></li>
      <li><a href="#">Page 3</a></li> --}}
    </ul>
  </div>
</nav> -->

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Link</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Dropdown
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
        </li>
      </ul>
      <form class="d-flex">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>