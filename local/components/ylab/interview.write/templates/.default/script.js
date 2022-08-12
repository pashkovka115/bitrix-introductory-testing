if (typeof (BX.YlabSlots) === 'undefined') {
    BX.YlabSlots = function (id) {
        this._id = id;
        this.params = {};
        this._submitHandler = BX.delegate(this._clickHandler, this);
        BX.bind(BX(id), 'click', this._submitHandler);
    };
    BX.YlabSlots.prototype = {
        initialize: function (id, params) {
            this._id = id;
            this.params = params;
        },
        getId: function () {
            return this._id;
        },
        _getElement: function (e) {
            if (e.target.tagName === 'SPAN' &&
                e.target.classList.contains('slot') &&
                e.target.classList.contains('free')
            ) {
                return e.target;
            }
        },
        _clickHandler: function (e) {
            if (!e) {
                e = window.event;
            }
            let el = this._getElement(e);
            if (el !== undefined) {
                let post = {};
                post.slots_id = this._id;
                post.iblock_id = el.dataset.iblock;
                post.slot_datetime = el.dataset.slot;
                // slot_datetime_name - символьный код свойства (SLOT_DATETIME)
                // time_slot - продолжительность слота в минутах (30)
                // end_day - на сколько дней генерировать слоты (2)

                let options = '';
                for (let i = 0; i < this.params.users.length; i++) {
                    options += '<option value="' + this.params.users[i].ID + '">' + this.params.users[i].NAME + '</option>'
                }

                const messageBox = BX.UI.Dialogs.MessageBox.confirm(
                    '<div class="select_users_wrap">' +
                    'Пользователя <select id="user_' + post.iblock_id + '" name="USER">' + options + '</select> ' +
                    'записать на <div id="selected_slot">' + post.slot_datetime + '</div>' +
                    '</div>',
                    function (messageBox, $this, $my) {
                        let user = BX('user_' + post.iblock_id);
                        post.user_id = user.value;
                        post.user_name = user[user.selectedIndex].text;

                        BX.ajax.runComponentAction('ylab:interview.write', 'ajaxHandler', {
                            mode: 'class',
                            data: post
                        }).then(function (response) {
                            let old_slot = document.querySelector('span[data-element="' + post.user_id + '"]');

                            if (old_slot.classList.contains('busy')) {
                                old_slot.classList.remove('busy');
                            }
                            old_slot.classList.add('free');
                            if (old_slot.hasAttribute('data-element')) {
                                old_slot.removeAttribute('data-element');
                            }
                            if (old_slot.hasAttribute('title')) {
                                old_slot.removeAttribute('title');
                            }


                            if (el.classList.contains('free')) {
                                el.classList.remove('free');
                            }
                            el.classList.add('busy');
                            el.setAttribute('data-element', post.user_id);
                            el.setAttribute('title', post.user_name);


                            messageBox.close();
                        }, function (response) {
                            //сюда будут приходить все ответы, у которых status !== 'success'
                            console.log('ERROR', response);
                        });
                    },
                    'Записать',
                    function (messageBox) {
                        messageBox.close();
                    }
                );
            }
        }
    };
    BX.YlabSlots.create = function (id, params) {
        var _self = new BX.YlabSlots(id);
        _self.initialize(id, params);
        return _self;
    };
}
