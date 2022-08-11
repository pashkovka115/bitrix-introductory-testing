BX.ready(function () {
    BX.bindDelegate(
        document.body, 'click', {className: 'btn__check_password'},
        function (e) {
            if (!e) {
                e = window.event;
            }
            BX.ajax.runComponentAction('ylab:interview.user.add',
                'checkPassport', {
                    mode: 'class',
                    data: {post: {passport: BX('passport').value, iblock_id: BX('iblock_id').value}},
                })
                .then(function (response) {
                    if (response.status === 'success') {
                        if (response.data.correct == 1) {
                            BX.removeClass(BX("btn__check_password_message"), 'user_add_form_errors');
                            BX.addClass(BX("btn__check_password_message"), 'user_add_form_success');
                        } else {
                            BX.removeClass(BX("btn__check_password_message"), 'user_add_form_success');
                            BX.addClass(BX("btn__check_password_message"), 'user_add_form_errors');
                        }
                        BX('btn__check_password_message').innerHTML = response.data.message;
                    }
                });
            return BX.PreventDefault(e);
        }
    );
});