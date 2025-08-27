<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'shinzemi'], function () {
Auth::routes();

Route::get('/logout', 'Auth\LoginController@logout');
Route::post("/student_access/store", 'StudentAccessController@store');

Route::group(
        ['middleware' => 'auth'],
        function () {
                //default
                Route::group(
                        ['middleware' => 'part_time_auth'],
                        function () {
                                Route::get('/home', 'HomeController@index')->name('home');
                                Route::get("/", 'HomeController@index');
                                //Demo (Delete after site publish.)
                                Route::get("/tables_check_view_html", function () {
                                        return view("tables_check_view_html");
                                });

                                Route::get("/student_access", 'StudentAccessController@index')->name('student_access.index');
                                Route::get("/student_access/history_index", 'StudentAccessController@history_index')->name('student_access.history_index');
                                Route::get("/student_access/getHistoryData", 'StudentAccessController@getHistoryData')->name('student_access.getHistoryData');
                                //=======================================================================
                                //生徒情報
                                //=======================================================================
                                //生徒情報管理画面
                                Route::view("/student_info_management", "student_info_management.index");
                                //生徒情報登録
                                //生徒情報CVS出力
                                Route::post('student/student_info_output', 'StudentController@student_info_output')->name('student.student_info_output');

                                Route::resource('student', 'StudentController')->except(['show']);
                                Route::get('/student/{id}/get_shools', 'StudentController@get_shools')->name('student.get_shools');
                                Route::get('/student/{id}/get_branch_banks', 'StudentController@get_branch_banks')->name('student.get_branch_banks');

                                // 生徒パスワードリセット
                                Route::post('/student/{id}/reset-password', 'StudentController@resetPassword')->name('student.reset_password');

                                // マイページ再案内メール送信
                                Route::post('/student/{id}/resend-mypage-guide', 'StudentController@resendMyPageGuide')->name('student.resend_mypage_guide');

					//マイページ再案内（不具合報告付き）メール送信
					Route::post('/student/{id}/resend-mypage-guide-re', 'StudentController@resendMyPageGuideRe')->name('student.resend_mypage_guide_re');

                                //受講情報登録
                                Route::resource('juko_info', 'JukoInfoController');
                                Route::get('/juko_info/{id}/product_delete', 'JukoInfoController@product_delete')->name('juko_info.product_delete');

                                // 受講情報一括登録処理
                                Route::post('juko_info/bulk_store', 'JukoInfoController@bulk_store')->name('juko_info.bulk_store');
                                // 受講情報一括削除
                                Route::post('juko_info/bulk_delete', 'JukoInfoController@bulk_delete')->name('juko_info.bulk_delete');

                                // 入退室生徒情報
				Route::resource('access_user', 'AccessUserController')->except(['show', 'destroy']);
                                Route::match(['post', 'delete'], '/access_user/{access_user}', 'AccessUserController@destroy')
                                ->name('access_user.destroy');

                                //生徒数情報出力
                                Route::view("/number_of_student_info_management", "number_of_student_info_management.index");
                                //試験別成績一覧表
                                Route::resource('score', 'ScoreController');
                                Route::get('/score/{id}/get_implementations', 'ScoreController@get_implementations')->name('score.get_implementations');
                                //成績情報登録
                                Route::resource('score_info', 'ScoreInfoController');
                                //成績情報/平均点登録
                                Route::resource('average_point', 'AveragePointsController');
                                //小学生成績出力
                                Route::get('/average_point/{id}/output_elementary_school_student_result', 'AveragePointsController@output_elementary_school_student_result')->name('average_point.output_elementary_school_student_result');
                                //中学生出力
                                Route::get('/average_point/{id}/output_junior_high_school_student_result', 'AveragePointsController@output_junior_high_school_student_result')->name('average_point.output_junior_high_school_student_result');
                                //生徒カルテ出力指示
                                Route::resource('student_karte', 'StudentKarteController');
                                //年度末処理
                                // Route::resource('year_end', 'YearEndController');
                                Route::get('/year_end', 'YearEndController@index')->name('year_end.index');
                                Route::get('/year_end/fiscal_year_end_process', 'YearEndController@fiscal_year_end_process')->name('year_end.fiscal_year_end_process');

                                //=======================================================================
                                //請求管理
                                //=======================================================================
                                //売上作成
                                Route::get('sales/data_migration', 'SalesController@data_migration')->name('sales.data_migration');

                                // 売上一括登録処理
                                Route::post('sales/bulk_store', 'SalesController@bulk_store')->name('sales.bulk_store');
                                // 売上一括削除
                                Route::post('sales/bulk_delete', 'SalesController@bulk_delete')->name('sales.bulk_delete');

                                Route::resource('sales', 'SalesController');
                                Route::get('/sales/{id}/get_product_price', 'SalesController@get_product_price')->name('sales.get_product_price');
                                Route::get('charge/data_migration', 'ChargesController@data_migration')->name('charge.data_migration');
                                Route::get('charge/charge_confirm', 'ChargesController@charge_confirm')->name('charge.charge_confirm');
                                Route::get('charge/charge_confirm_lift', 'ChargesController@charge_confirm_lift')->name('charge.charge_confirm_lift');
                                Route::get('charge/charge_closing', 'ChargesController@charge_closing')->name('charge.charge_closing');
                                Route::resource('charge', 'ChargesController');
                                Route::get('/charge_output/export_nanto', 'ChargeOutputController@export_nanto')->name('charge_output.export_nanto');
                                Route::get('/charge_output/export_risona', 'ChargeOutputController@export_risona')->name('charge_output.export_risona');
                                Route::post('charge_output/import_nanto', 'ChargeOutputController@import_nanto')->name('charge_output.import_nanto');
                                Route::post('charge_output/import_risona', 'ChargeOutputController@import_risona')->name('charge_output.import_risona');
                                Route::get('charge_output/nanto_index', 'ChargeOutputController@nanto_index')->name('charge_output.nanto_index');
                                Route::get('charge_output/risona_index', 'ChargeOutputController@risona_index')->name('charge_output.risona_index');

                                // 南都銀行出力前blade表示
                                Route::get('charge_output/index_nanto', 'ChargeOutputController@index_nanto')->name('charge_output.index_nanto');
                                // りそな銀行出力前blade表示
                                Route::get('charge_output/index_risona', 'ChargeOutputController@index_risona')->name('charge_output.index_risona');
                                // 南都銀行インポート前blade表示
                                Route::get('charge_output/nanto_import_index', 'ChargeOutputController@nanto_import_index')->name('charge_output.nanto_import_index');
                                // りそな銀行インポート前blade表示
                                Route::get('charge_output/risona_import_index', 'ChargeOutputController@risona_import_index')->name('charge_output.risona_import_index');

                                Route::resource('charge_output', 'ChargeOutputController');
                                Route::resource('payment', 'PaymentController');
                                Route::post('charge_excel/export_charge', 'ChargeExcelController@export_charge')->name('charge_excel.export_charge');
                                Route::post('charge_excel/export_school_building_sales', 'ChargeExcelController@export_school_building_sales')->name('charge_excel.export_school_building_sales');
                                Route::post('charge_excel/export_school_building_charge', 'ChargeExcelController@export_school_building_charge')->name('charge_excel.export_school_building_charge');
                                Route::post('charge_excel/export_month_sales', 'ChargeExcelController@export_month_sales')->name('charge_excel.export_month_sales');
                                Route::post('charge_excel/export_school_building_payment', 'ChargeExcelController@export_school_building_payment')->name('charge_excel.export_school_building_payment');
                                Route::post('charge_excel/export_year_sales', 'ChargeExcelController@export_year_sales')->name('charge_excel.export_year_sales');
                                Route::post('charge_excel/export_withdrawal', 'ChargeExcelController@export_withdrawal')->name('charge_excel.export_withdrawal');
                                Route::post('charge_excel/export_first_sales', 'ChargeExcelController@export_first_sales')->name('charge_excel.export_first_sales');
                                Route::resource('charge_excel', 'ChargeExcelController');

                                Route::get('invoice', 'InvoiceController@index')->name('invoice.index');
                                Route::get('invoice/{invoice}', 'InvoiceController@show')->name('invoice.show');
                                Route::get('invoice/{invoice}/download', 'InvoiceController@downloadPdf')->name('invoice.download');
                                Route::post('invoice/transfer', 'InvoiceTransferController@transfer')->name('invoice.transfer');
                                Route::post('/invoice/transfer/confirm', 'InvoiceTransferController@confirmTransfer')->name('invoice.confirm_transfer');

                                Route::post('/invoice/notify/confirm', 'InvoiceNotificationController@confirmNotification')
                                        ->name('invoice.confirm_notification'); // 確認用
                                Route::post('/invoice/notify', 'InvoiceNotificationController@sendNotification')
                                        ->name('invoice.send_notification'); // 送信用

                                //=======================================================================
                                //非常勤管理
                                //=======================================================================

                                Route::get('salary/monthly_tightening', 'SalariesController@monthly_tightening')->name('salary.monthly_tightening');

                                Route::get('salary/{id}/approval_edit/{date}', 'SalariesController@approval_edit')->name('salary.approval_edit');
                                Route::put('approval_update/{salary}', 'SalariesController@approval_update')->name('salary.approval_update');

                                Route::post('salary/salary_approval', 'SalariesController@salary_approval')->name('salary.salary_approval');
                                Route::post('salary/month_approval', 'SalariesController@month_approval')->name('salary.month_approval');
                                Route::get('salary/monthly_salary_index', 'SalariesController@monthly_salary_index')->name('salary.monthly_salary_index');
                                Route::post('salary/monthly_salary', 'SalariesController@monthly_salary')->name('salary.monthly_salary');
                                Route::post('salary/month_approval_cancel', 'SalariesController@month_approval_cancel')->name('salary.month_approval_cancel');
                                Route::post('salary/salary_approval_cancel', 'SalariesController@salary_approval_cancel')->name('salary.salary_approval_cancel');
                                
                                Route::get('salary/invoice', 'SalaryInvoiceController@index')->name('salary.invoice.index');
                                Route::get('salary/invoice/{invoice}', 'SalaryInvoiceController@show')->name('salary.invoice.show');
                                Route::get('salary/invoice/{invoice}/download', 'SalaryInvoiceController@downloadPdf')->name('salary.invoice.download');
                                Route::post('salary/part_time/transfer', 'SalaryInvoiceTransferController@transfer')->name('part_time.transfer');
                                Route::post('salary/part_time/transfer/confirm', 'SalaryInvoiceTransferController@confirmTransfer')->name('part_time.confirm_transfer');

                                Route::post('/salary/invoice/notify/confirm', 'SalaryInvoiceNotificationController@confirmNotification')->name('salary_invoice.confirm_notification'); // 確認用
                                Route::post('/invoice/notify', 'SalaryInvoiceNotificationController@sendNotification')->name('invoice.send_notification'); // 送信用

                                // 非常勤給与振込データ作成出力前blade表示
                                Route::get('salary_output/export_salary_index', 'SalaryOutputController@export_salary_index')->name('salary_output.export_salary_index');

                                Route::get('salary_output/school_building_index', 'SalaryOutputController@school_building_index')->name('salary_output.school_building_index');
                                Route::get('salary_output/working_school_building_index', 'SalaryOutputController@working_school_building_index')->name('salary_output.working_school_building_index');
                                Route::get('salary_output/export_part_timer_list', 'SalaryOutputController@export_part_timer_list')->name('salary_output.export_part_timer_list');
                                Route::get('salary_output/export_salary', 'SalaryOutputController@export_salary')->name('salary_output.export_salary');
                                Route::post('salary_output/export_salary_list', 'SalaryOutputController@export_salary_list')->name('salary_output.export_salary_list');
                                Route::post('salary_output/export_school_building_salary_list', 'SalaryOutputController@export_school_building_salary_list')->name('salary_output.export_school_building_salary_list');
                                Route::post('salary_output/export_worked_school_building_salary_list', 'SalaryOutputController@export_worked_school_building_salary_list')->name('salary_output.export_worked_school_building_salary_list');
                                Route::post('salary_output/export_payslip', 'SalaryOutputController@export_payslip')->name('salary_output.export_payslip');
                                Route::get('salary_output/export_wage_ledger_index', 'SalaryOutputController@export_wage_ledger_index')->name('salary_output.export_wage_ledger_index');
                                Route::post('salary_output/export_wage_ledger', 'SalaryOutputController@export_wage_ledger')->name('salary_output.export_wage_ledger');
                                Route::resource('salary_output', 'SalaryOutputController');

                                //=======================================================================
                                // アンケート結果出力
                                //=======================================================================
                                Route::get('/questionnaire_output/index', 'QuestionnaireResultsDetailsController@index_export')->name('questionnaire_output.index');

                                Route::get('/questionnaire_results_detail/{id}/export', 'QuestionnaireResultsDetailsController@export')->name('questionnaire.export');

                                // 講師別評価点一覧
                                Route::get('/questionnaire_results_detail/{id}/export_teacher_evaluation', 'QuestionnaireResultsDetailsController@export_teacher_evaluation')->name('questionnaire.export_teacher_evaluation');

                                //教室別
                                Route::get('/questionnaire_results_detail/{id}/export_every_classroom', 'QuestionnaireResultsDetailsController@export_every_classroom')->name('questionnaire.export_every_classroom');

                                // 講師別一覧
                                Route::get('/questionnaire_results_detail/{id}/export_every_teachers', 'QuestionnaireResultsDetailsController@export_every_teachers')->name('questionnaire.export_every_teachers');

                                Route::resource('questionnaire_results_detail', 'QuestionnaireResultsDetailsController');

                                Route::get('/questionnaire_content/form_questionnaire_papers', 'QuestionnaireContentsController@form_questionnaire_papers')->name('questionnaire_content.form_questionnaire_papers');
                                Route::get('/questionnaire_content/export_questionnaire_papers', 'QuestionnaireContentsController@export_questionnaire_papers')->name('questionnaire_content.export_questionnaire_papers');
                                Route::resource("questionnaire_content", "QuestionnaireContentsController");


                                //=======================================================================
                                // アンケートインポート
                                //=======================================================================
                                Route::get('/questionnaire_import/create', 'QuestionnaireResultsDetailsController@import')->name('questionnaire_import.create');
                                Route::post('/questionnaire_import/store_import', 'QuestionnaireResultsDetailsController@store_import')->name('questionnaire_import.store_import');
                                Route::get('/questionnaire_import/confirm_import', 'QuestionnaireResultsDetailsController@confirm_import')->name('questionnaire_import.confirm_import');

                                Route::post('/questionnaire_import/stores', 'QuestionnaireResultsDetailsController@stores')->name('questionnaire_import.stores');

                                //=======================================================================
                                //入塾前情報管理
                                //=======================================================================
                                //入塾前管理画面
                                Route::view("/before_student_info_management", "before_student_info_management.index");
                                //入塾前生徒情報登録
                                Route::resource('before_student', 'BeforeStudentController');
                                //入塾前情報登録
                                Route::resource('before_juku_info', 'BeforeJukuInfoController');
                                //入塾前生徒情報CVS出力
                                Route::post('/student/before_student_info_output', 'BeforeStudentController@before_student_info_output')->name('before_student.before_student_info_output');
                                //特別体験授業入塾率出力
                                Route::resource('class_juku_rate', 'ClassJukuRateController');
                                //各講習生徒数目標出力
                                Route::resource('student_target', 'StudentTargetController');
                                //入塾前売上登録
                                Route::resource('before_juku_sales', 'BeforeJukuSalesController');
                                Route::get('/before_juku_sales/{id}/get_product_price', 'BeforeJukuSalesController@get_product_price')->name('before_juku_sales.get_product_price');
                                //入塾前明細出力
                                // Route::resource('before_juku_detail', 'BeforeJukuDetailController');
                                Route::get('/before_juku_detail', 'BeforeJukuDetailController@index')->name('before_juku_detail.index');
                                //入塾前明細出力
                                Route::get('/before_juku_detail/sales_item_output', 'BeforeJukuDetailController@sales_item_output')->name('before_juku_detail.sales_item_output');
                                //問い合わせ管理表出力
                                Route::resource('contact_us_management', 'ContactUsManagementController');


                                //=======================================================================
                                // マスタメンテ
                                //=======================================================================
                                // 会社マスタ
                                Route::resource('company', 'CompanysController');
                                //学校マスタ
                                Route::resource('school', 'SchoolsController');
                                // 銀行マスタ
                                Route::resource('bank', 'BanksController');
                                // 割引マスタ
                                Route::resource('discount', 'DiscountsController');
                                // 校舎マスタ
                                Route::resource("school_building", 'SchoolBuildingsController');
                                // 銀行支店マスタ
                                Route::resource('branch_bank', 'BranchBanksController');
                                // 商品マスタ
                                Route::resource('product', 'ProductsController');
                                // 業務内容マスタ
                                Route::resource('job_description', 'JobDescriptionsController');
                                // その他実績種別マスタ
                                Route::resource('other_job_description', 'OtherJobDescriptionsController');
                                //科目担当マスタ
                                Route::resource('subject_teacher', 'SubjectTeachersController');
                                //区分マスタ
                                // Route::resource('classification', 'ClassificationsController');
                                //講師別アンケート数値マスタ
                                Route::resource('questionnaire_score', 'QuestionnaireScoresController');
                                //高校コースマスタ
                                Route::resource('highschool_course', 'HighschoolCoursesController');
                                //成績カテゴリーマスタ
                                Route::resource('result_category', 'ResultCategoryController');
                                Route::match(['get', 'post'], '/result_category/category_add', 'ResultCategoryController@category_add')->name('result_category.category_add');
                                Route::match(['get', 'post'], '/result_category/order_save', 'ResultCategoryController@order_save')->name('result_category.order_save');
                                //成績教科マスタ
                                Route::resource('subject', 'SubjectController');
                                // コースマスタ
                                Route::resource('course', 'CoursesController');
                                Route::post('/course/get_course_curriculum', 'CoursesController@getCourseCurriculum')->name('course.get_course_curriculums');
                                // 教科マスタ
                                Route::resource('curriculum', 'CurriculumsController');
                                //ユーザーマスタ
                                Route::get('/auth/index', 'Auth\RegisterController@index')->name('register.index');
                                Route::get('/auth/{id}/edit', 'Auth\RegisterController@edit')->name('register.edit');
                                Route::get('/auth/{id}/read', 'Auth\RegisterController@read')->name('register.read');
                                Route::put('/auth/{id}/destroy', 'Auth\RegisterController@destroy')->name('register.destroy'); // Route::resource('register', 'Auth\RegisterController');
                                Route::put('/auth/{id}/update', 'Auth\RegisterController@update')->name('register.update'); // Route::resource('register', 'Auth\RegisterController');
					//ユーザー情報CVS出力
					Route::post('user/user_info_output', 'Auth\RegisterController@user_info_output')->name('user.user_info_output');
					Route::resource('invoice_comment', 'InvoiceCommentController');


					// 売上区分
					//index
					Route::resource('division_code', 'DivisionCodesController');

					//=======================================================================
					//　マスタ管理のメニュー画面
					//=======================================================================
					Route::view("/master_data_management", "master_data_management.index");
					//=======================================================================
					// アンケート集計・確定
					Route::resource("questionnaire_decision", "QuestionnaireDecisionsController");

					//=======================================================================
					// お知らせ管理
					//=======================================================================
					Route::resource('announcements', 'AnnouncementController');
					Route::post('announcements/upload-image', 'AnnouncementController@uploadImage')->name('announcements.upload_image');

					// 承認関連のルート
                    Route::post('announcements/{announcement}/request-approval', 'AnnouncementController@requestApproval')->name('announcements.request_approval');
                    Route::post('announcements/{announcement}/approve', 'AnnouncementController@approve')->name('announcements.approve');
                    Route::post('announcements/{announcement}/reject', 'AnnouncementController@reject')->name('announcements.reject');
                    Route::post('announcements/{announcement}/publish', 'AnnouncementController@publish')->name('announcements.publish');
                    Route::post('announcements/{announcement}/unpublish', 'AnnouncementController@unpublish')->name('announcements.unpublish');

					//=======================================================================
					// スケジュール管理
					//=======================================================================
					// スケジュール管理メイン
					Route::resource('schedules', 'ScheduleController');
					// スケジュール承認
					Route::get('/schedules-approval', 'ScheduleController@approval')->name('schedules.approval');
					Route::put('/schedules/{schedule}/approval', 'ScheduleController@updateApproval')->name('schedules.updateApproval');
					// スケジュール一括承認
					Route::post('schedules-bulk-approval', 'ScheduleController@bulkApproval')->name('schedules.bulkApproval');
					// スケジュール承認者管理
					Route::resource('schedule_approvers', 'ScheduleApproverController');
					// スケジュール履歴
					Route::get('/schedules-history', 'ScheduleController@history')->name('schedules.history');
					Route::get('/schedules-history/data', 'ScheduleController@getHistoryData')->name('schedules.history.data');
					Route::get('/schedules-history/export', 'ScheduleController@exportHistory')->name('schedules.history.export');

					//=======================================================================
					//　QRカード表示
					//=======================================================================
					Route::get('/card/print_preview', 'CardController@printPreview')->name('card.print_preview');
					Route::post('/card/print_preview_all', 'CardController@printPreviewAll')->name('student_access.print_preview_all');

					//入退塾等の手続き
					Route::get('/application', 'ApplicationController@index')->name('application.index');
					Route::any('/application/accept_index', 'ApplicationController@acceptIndex')->name('application.accept_index');
					Route::get('/application/accept_detail/{id}', 'ApplicationController@acceptDetail')->name('application.accept_detail');
					Route::post('/application/accept_process', 'ApplicationController@acceptProcess')->name('application.accept_process');
					Route::get('/application/admission_index', 'ApplicationController@admissionIndex')->name('application.admission_index');
					Route::any('/application/admission_student_create', 'ApplicationController@admissionStudentCreate')->name('application.admission_student_create');
					Route::post('/application/admission_course_create', 'ApplicationController@admissionCourseCreate')->name('application.admission_course_create');
					Route::post('/application/admission_others_create', 'ApplicationController@admissionOthersCreate')->name('application.admission_others_create');
					Route::post('/application/admission_confirm', 'ApplicationController@admissionConfirm')->name('application.admission_confirm');
					Route::any('/application/admission_sign', 'ApplicationController@admissionSign')->name('application.admission_sign');
					Route::any('/application/admission_store', 'ApplicationController@admissionStore')->name('application.admission_store');
				}
			);
			Route::resource('salary', 'SalariesController');
			Route::group(
				['middleware' => 'part_time_auth'],
				function () {
					Route::get('salary/{id}/deduction/{month}', 'SalariesController@deduction')->name('salary.deduction');
					Route::put('/salary/{id}/deduction_update', 'SalariesController@deduction_update')->name('salary.deduction_update'); // Route::resource('register', 'Auth\RegisterController');
				}
			);
			Route::get('salary/{id}/edit/{date}', 'SalariesController@edit')->name('salary.edit');
			Route::get('/auth/myedit', 'Auth\RegisterController@myedit')->name('register.myedit');
			Route::put('/auth/myupdate', 'Auth\RegisterController@myupdate')->name('register.myupdate');
        }

);
});
