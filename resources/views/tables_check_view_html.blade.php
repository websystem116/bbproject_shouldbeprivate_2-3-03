
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Tables</title>
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="https://getbootstrap.com/docs/4.0/examples/dashboard/dashboard.css" rel="stylesheet">
</head>

<body>
<h1>Tables</h1>
    <div class="container-fluid">
        <div class="row">
            
                


                <!-- table[Start] --><div class="col-md-3"><h2 class="info">schools</h2><h5>[学校マスタ ]</h5><div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                            <tr class="bg-primary text-white">
                                <th>-</th>
                                <th>Name(Type Size)</th>
                                <th>Comment</th>
                            </tr>
                            </thead>

                            <tbody>
                            <!-- TR --><tr><td>1</td><td>increments('id');
</td><td></td></tr><tr><td>2</td><td>string('name',20)->nullable();</td><td>学校名</td></tr><tr><td>3</td><td>string('name_short',10)->nullable();</td><td>学校（略称）</td></tr><tr><td>4</td><td>string('school_classification',2)->nullable();</td><td>学校区分</td></tr><tr><td>5</td><td>string('university_classification',2)->nullable();</td><td>国立・公立・私立区分</td></tr><!-- TR -->
                            </tbody>

                        </table>
                    </div>
                     </div>
                    <!-- table[end] --><div class="col-md-3"><h2 class="info">banks</h2><h5>[銀行マスタ ]</h5><div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                            <tr class="bg-primary text-white">
                                <th>-</th>
                                <th>Name(Type Size)</th>
                                <th>Comment</th>
                            </tr>
                            </thead>

                            <tbody>
                            <!-- TR --><tr><td>1</td><td>increments('id');
</td><td></td></tr><tr><td>2</td><td>string('code',4)->nullable();</td><td>銀行コード</td></tr><tr><td>3</td><td>string('name',15)->nullable();</td><td>銀行名</td></tr><tr><td>4</td><td>string('name_kana',40)->nullable();
</td><td></td></tr><!-- TR -->
                            </tbody>

                        </table>
                    </div>
                     </div>
                    <!-- table[end] --><div class="col-md-3"><h2 class="info">discounts</h2><h5>[割引マスタ ]</h5><div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                            <tr class="bg-primary text-white">
                                <th>-</th>
                                <th>Name(Type Size)</th>
                                <th>Comment</th>
                            </tr>
                            </thead>

                            <tbody>
                            <!-- TR --><tr><td>1</td><td>increments('id');
</td><td></td></tr><tr><td>2</td><td>string('name',40)->nullable();</td><td>割引名</td></tr><tr><td>3</td><td>string('name_short',10)->nullable();</td><td>割引名（略名）</td></tr><tr><td>4</td><td>integer('discount_rate_class')->nullable();</td><td>割引率（授業料クラス）</td></tr><tr><td>5</td><td>integer('discount_rate_personal')->nullable();</td><td>割引率（授業料個別）</td></tr><tr><td>6</td><td>integer('discount_rate_course')->nullable();</td><td>割引率（講習料）</td></tr><tr><td>7</td><td>integer('discount_rate_join')->nullable();</td><td>割引率（入塾金）</td></tr><tr><td>8</td><td>integer('discount_rate_monthly')->nullable();</td><td>割引率（月間諸費用）</td></tr><tr><td>9</td><td>integer('discount_rate_teachingmaterial')->nullable();</td><td>割引率（教材代）</td></tr><tr><td>10</td><td>integer('discount_rate_test')->nullable();</td><td>割引率（テスト代）</td></tr><tr><td>11</td><td>integer('discount_rate_certification')->nullable();</td><td>割引率（検定代）</td></tr><tr><td>12</td><td>integer('discount_rate_other')->nullable();</td><td>割引率（その他費用）</td></tr><!-- TR -->
                            </tbody>

                        </table>
                    </div>
                     </div>
                    <!-- table[end] --><div class="col-md-3"><h2 class="info">schoolbuildings</h2><h5>[ ]</h5><div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                            <tr class="bg-primary text-white">
                                <th>-</th>
                                <th>Name(Type Size)</th>
                                <th>Comment</th>
                            </tr>
                            </thead>

                            <tbody>
                            <!-- TR --><tr><td>1</td><td>increments('id');
</td><td></td></tr><tr><td>2</td><td>string('name',20)->nullable();</td><td>校舎名</td></tr><tr><td>3</td><td>string('name_short',10)->nullable();</td><td>校舎名（略称）</td></tr><tr><td>4</td><td>string(' zipcode',8)->nullable();
</td><td></td></tr><tr><td>5</td><td>string('address1',30)->nullable();</td><td>住所1</td></tr><tr><td>6</td><td>string('address2',30)->nullable();</td><td>住所2</td></tr><tr><td>7</td><td>string('address3',30)->nullable();</td><td>住所3</td></tr><tr><td>8</td><td>string('tel',15)->nullable();</td><td>TEL</td></tr><tr><td>9</td><td>string('fax',15)->nullable();</td><td>FAX番号</td></tr><tr><td>10</td><td>string('email',50)->nullable();</td><td>Eメールアドレス</td></tr><!-- TR -->
                            </tbody>

                        </table>
                    </div>
                     </div>
                    <!-- table[end] --><div class="col-md-3"><h2 class="info">branch_banks</h2><h5>[銀行支店 ]</h5><div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                            <tr class="bg-primary text-white">
                                <th>-</th>
                                <th>Name(Type Size)</th>
                                <th>Comment</th>
                            </tr>
                            </thead>

                            <tbody>
                            <!-- TR --><tr><td>1</td><td>increments('id');
</td><td></td></tr><tr><td>2</td><td>string('code',3)->nullable();</td><td>銀行支店コード</td></tr><tr><td>3</td><td>string('name',15)->nullable();</td><td>銀行支店名</td></tr><tr><td>4</td><td>string('name_kana',40)->nullable();</td><td>銀行支店カナ名</td></tr><tr><td>5</td><td>string('zipcode',8)->nullable();</td><td>銀行支店郵便番号</td></tr><tr><td>6</td><td>string('address',60)->nullable();
</td><td></td></tr><tr><td>7</td><td>string('tel',15)->nullable();</td><td>銀行支店電話番号</td></tr><!-- TR -->
                            </tbody>

                        </table>
                    </div>
                     </div>
                    <!-- table[end] --><div class="col-md-3"><h2 class="info">products</h2><h5>[商品 ]</h5><div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                            <tr class="bg-primary text-white">
                                <th>-</th>
                                <th>Name(Type Size)</th>
                                <th>Comment</th>
                            </tr>
                            </thead>

                            <tbody>
                            <!-- TR --><tr><td>1</td><td>increments('id');
</td><td></td></tr><tr><td>2</td><td>string('name',40)->nullable();</td><td>商品名</td></tr><tr><td>3</td><td>string('name_short',10)->nullable();</td><td>商品名（略称）</td></tr><tr><td>4</td><td>string('description',80)->nullable();</td><td>内容</td></tr><tr><td>5</td><td>integer('price')->nullable();</td><td>価格</td></tr><tr><td>6</td><td>string('tax_category')->nullable();</td><td>価格表示1:内税2:外税</td></tr><tr><td>7</td><td>string('division_code',2)->nullable();</td><td>分類コード売上区分</td></tr><tr><td>8</td><td>string('item_no',2)->nullable();</td><td>項目No売上区分</td></tr><tr><td>9</td><td>string('tabulation',2)->nullable();</td><td>集計区分</td></tr><!-- TR -->
                            </tbody>

                        </table>
                    </div>
                     </div>
                    <!-- table[end] --><div class="col-md-3"><h2 class="info">manage_targets</h2><h5>[目標管理マスタ ]</h5><div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                            <tr class="bg-primary text-white">
                                <th>-</th>
                                <th>Name(Type Size)</th>
                                <th>Comment</th>
                            </tr>
                            </thead>

                            <tbody>
                            <!-- TR --><tr><td>1</td><td>increments('id');
</td><td></td></tr><tr><td>2</td><td>string('year',4)->nullable();</td><td>年度</td></tr><tr><td>3</td><td>integer('taget_classification')->nullable();</td><td>目標区分1：1月、2：2月、3：3月、4：4月、5：5月、6：6月、7：7月、8：8月、9：9月、10：10月、11：1</td></tr><tr><td>4</td><td>integer('target_value')->nullable();</td><td>目標値</td></tr><!-- TR -->
                            </tbody>

                        </table>
                    </div>
                     </div>
                    <!-- table[end] --><div class="col-md-3"><h2 class="info">authorities</h2><h5>[権限設定マスタ ]</h5><div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                            <tr class="bg-primary text-white">
                                <th>-</th>
                                <th>Name(Type Size)</th>
                                <th>Comment</th>
                            </tr>
                            </thead>

                            <tbody>
                            <!-- TR --><tr><td>1</td><td>increments('id');
</td><td></td></tr><tr><td>2</td><td>integer('user_id')->nullable();</td><td>ユーザID</td></tr><tr><td>3</td><td>string('password',20)->nullable();</td><td>パスワード</td></tr><tr><td>4</td><td>integer('classification_code')->nullable();</td><td>分類コード　権限区分</td></tr><tr><td>5</td><td>string('item_no',2)->nullable();</td><td>項目No　権限区分</td></tr><tr><td>6</td><td>integer('Is_need_password')->nullable();</td><td>パスワード変更要求区分</td></tr><tr><td>7</td><td>datetime('last_login_date')->nullable();</td><td>最終ログイン日時</td></tr><tr><td>8</td><td>datetime('changed_password_date')->nullable();</td><td>前回パスワード変更日時</td></tr><tr><td>9</td><td>integer('fail_times_login')->nullable();</td><td>ログイン失敗回数</td></tr><!-- TR -->
                            </tbody>

                        </table>
                    </div>
                     </div>
                    <!-- table[end] --><div class="col-md-3"><h2 class="info">subject_teachers</h2><h5>[科目担当講師マスタ ]</h5><div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                            <tr class="bg-primary text-white">
                                <th>-</th>
                                <th>Name(Type Size)</th>
                                <th>Comment</th>
                            </tr>
                            </thead>

                            <tbody>
                            <!-- TR --><tr><td>1</td><td>increments('id');
</td><td></td></tr><tr><td>2</td><td>string('school_year',2)->nullable();</td><td>学年　1：小1、2：小2、3：小3、4：小4、5：小5、6：小6</td></tr><tr><td>3</td><td>string('classification_code_class',4)->nullable();</td><td></td></tr><tr><td>4</td><td>string('item_no_class',2)->nullable();</td><td>分類コード　科目</td></tr><tr><td>5</td><td>integer('user_id')->nullable();</td><td>分類コード　クラス</td></tr><!-- TR -->
                            </tbody>

                        </table>
                    </div>
                     </div>
                    <!-- table[end] --><div class="col-md-3"><h2 class="info">classifications</h2><h5>[区分マスタ ]</h5><div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                            <tr class="bg-primary text-white">
                                <th>-</th>
                                <th>Name(Type Size)</th>
                                <th>Comment</th>
                            </tr>
                            </thead>

                            <tbody>
                            <!-- TR --><tr><td>1</td><td>increments('id');
</td><td></td></tr><tr><td>2</td><td>string('no',2)->nullable();</td><td>項目No</td></tr><tr><td>3</td><td>string('name',20)->nullable();</td><td>項目名</td></tr><!-- TR -->
                            </tbody>

                        </table>
                    </div>
                     </div>
                    <!-- table[end] --><div class="col-md-3"><h2 class="info">questionnaire_rules</h2><h5>[アンケート結果点数化ルールマスタ ]</h5><div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                            <tr class="bg-primary text-white">
                                <th>-</th>
                                <th>Name(Type Size)</th>
                                <th>Comment</th>
                            </tr>
                            </thead>

                            <tbody>
                            <!-- TR --><tr><td>1</td><td>increments('id');
</td><td></td></tr><tr><td>2</td><td>integer('rankstart')->nullable();</td><td>開始点</td></tr><tr><td>3</td><td>integer('rankend')->nullable();</td><td>終了点</td></tr><tr><td>4</td><td>integer('rankscore')->nullable();</td><td>点数</td></tr><!-- TR -->
                            </tbody>

                        </table>
                    </div>
                     </div>
                    <!-- table[end] --><div class="col-md-3"><h2 class="info">questionnaire_scores</h2><h5>[講師別アンケート数値マスタ ]</h5><div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                            <tr class="bg-primary text-white">
                                <th>-</th>
                                <th>Name(Type Size)</th>
                                <th>Comment</th>
                            </tr>
                            </thead>

                            <tbody>
                            <!-- TR --><tr><td>1</td><td>increments('id');
</td><td></td></tr><tr><td>2</td><td>integer('user_id')->nullable();</td><td>ユーザーID</td></tr><tr><td>3</td><td>integer('classroom_score')->nullable();</td><td>アンケート数値　教室数</td></tr><tr><td>4</td><td>integer('subject_score')->nullable();</td><td>アンケート数値　教科数</td></tr><!-- TR -->
                            </tbody>

                        </table>
                    </div>
                     </div>
                    <!-- table[end] --><div class="col-md-3"><h2 class="info">highschool_courses</h2><h5>[高校コースマスタ ]</h5><div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                            <tr class="bg-primary text-white">
                                <th>-</th>
                                <th>Name(Type Size)</th>
                                <th>Comment</th>
                            </tr>
                            </thead>

                            <tbody>
                            <!-- TR --><tr><td>1</td><td>increments('id');
</td><td></td></tr><tr><td>2</td><td>string('school_id',4)->nullable();</td><td>学校No</td></tr><tr><td>3</td><td>string('name',20)->nullable();</td><td>名称</td></tr><tr><td>4</td><td>string('name_short',10)->nullable();</td><td>略称</td></tr><!-- TR -->
                            </tbody>

                        </table>
                    </div>
                     </div>
                    <!-- table[end] --> </main>
        </div>
    </div>
</body>

</html>
