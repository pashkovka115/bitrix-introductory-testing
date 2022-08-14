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
        resetStyles: function (old_slots) {
            old_slots.forEach(function (old_el) {
                if (old_el.classList.contains('busy')) {
                    old_el.classList.remove('busy');
                }
                old_el.classList.add('free');
                if (old_el.hasAttribute('data-element')) {
                    old_el.removeAttribute('data-element');
                }
                if (old_el.hasAttribute('title')) {
                    old_el.removeAttribute('title');
                }
            });
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
                post.selected_slot = el.dataset.slot;

                let options = '';
                for (let i = 0; i < this.params.users.length; i++) {
                    options += '<option value="' + this.params.users[i].ID + '">' + this.params.users[i].NAME + '</option>'
                }

                const messageBox = BX.UI.Dialogs.MessageBox.confirm(
                    '<div id="select_users_wrap" class="select_users_wrap">' +
                    'Пользователя<br> <select id="user_' + post.iblock_id + '" name="USER">' + options + '</select> <br><br>' +
                    'записать на <div><select id="selected_slot"><option value="">Сбросить время</option> '
                    + '<option value="' + post.selected_slot + '" selected>' + post.selected_slot + '</option> </select></div>' +
                    '</div>',
                    function (messageBox) {
                        let user = BX('user_' + post.iblock_id);
                        post.user_id = user.value;
                        post.user_name = user[user.selectedIndex].text;
                        let arrPostSlots = [];
                        let old_slots = document.querySelectorAll('span[data-element="' + post.user_id + '"]');
                        for (let i = 0; i < old_slots.length; i++) {
                            arrPostSlots.push(old_slots[i].dataset.slot);
                        }
                        let selectedSlot = BX('selected_slot').value;
                        if (selectedSlot === '') {
                            arrPostSlots = '';
                        } else {
                            arrPostSlots.push(post.selected_slot);
                        }
                        post.slot_datetime = arrPostSlots;
                        BX('select_users_wrap').innerText = 'Идёт запись...';

                        BX.ajax.runComponentAction('ylab:interview.write', 'ajaxHandler', {
                            mode: 'class',
                            data: post
                        }).then(function (response) {
                            console.log('response:', response)
                            // если сбрасываем время
                            if (selectedSlot === '') {
                                BX.YlabSlots.prototype.resetStyles(old_slots);
                            } else {
                                el.classList.add('busy');
                                el.setAttribute('data-element', post.user_id);
                                el.setAttribute('title', post.user_name);
                            }

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
