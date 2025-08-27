$(function () {

	$('.add-input-withdrawal').on('click', function () {
		var htmlStr = $('#withdrawal_accounts').html();
		$('#withdrawal_accounts_to').append(htmlStr);
	});

	$('.add-input-payroll').on('click', function () {
		var htmlStr = $('#payroll_accounts').html();
		$('#payroll_accounts_to').append(htmlStr);
	})

	$(document).on('click', '.withdrawal-delete', function () {
		var withdrawal_accounts = $('.withdrawal_accounts');
		if (withdrawal_accounts.length > 1) {
			$(withdrawal_accounts).last().remove();
		} else {
			alert("最初の一行は削除できません。");
		}

	}
	);

	$(document).on('click', '.payroll-delete', function () {
		var payroll_accounts = $('.payroll_accounts');
		if (payroll_accounts.length > 1) {
			$(payroll_accounts).last().remove();
		} else {
			alert("最初の一行は削除できません。");
		}

	}
	);

	$(document).on('change', 'select[name="bank_id[]"]', function () {

		var bank_id = $(this).val();

		var form_group = $(this).closest('.form-group');

		var branch_bank_id = form_group.find('select[name="branch_bank_id[]"]');

		form_group.find('select[name="branch_bank_id[]"]').val('');

		branch_bank_id.find('option').each(function () {
			if ($(this).data('bank') == bank_id) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
	});

	$(document).on('change', 'select[name="payroll_bank_id[]"]', function () {

		var payroll_bank_id = $(this).val();

		var form_group = $(this).closest('.form-group');

		var payroll_branch_bank_id = form_group.find('select[name="payroll_branch_bank_id[]"]');

		form_group.find('select[name="payroll_branch_bank_id[]"]').val("");

		payroll_branch_bank_id.find('option').each(function () {

			if ($(this).data('bank') == payroll_bank_id) {

				$(this).show();

			} else {

				$(this).hide();

			}
		});
	});

});


