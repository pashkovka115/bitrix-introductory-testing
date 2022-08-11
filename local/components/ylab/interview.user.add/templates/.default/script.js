if (typeof (BX.UserAddForm) === 'undefined') {
    BX.UserAddForm = function (id) {
        this._id = id;
        this._settings = {};
        this._submitHandler = BX.delegate(this._clickHandler, this);
        BX.bind(BX('btn__check_password'), 'click', this._submitHandler);
    };
    BX.UserAddForm.prototype =
        {
            initialize: function (id, settings) {
                this._id = id;
                this._settings = settings;
            },
            getId: function () {
                return this._id;
            },
            _clickHandler: function (e) {
                if (!e) {
                    e = window.event;
                }
                this._settings.post.passport = BX('passport').value;
                BX.ajax.runComponentAction('ylab:interview.user.add',
                    'checkPassport', {
                        mode: 'class',
                        data: this._settings,
                    })
                    .then(function (response) {
                        if (response.status === 'success') {
                            if (response.data.isCorrect == "Y") {
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
        };
    BX.UserAddForm.create = function (id, settings) {
        var _self = new BX.UserAddForm(id);
        _self.initialize(id, settings);
        return _self;
    };
}
