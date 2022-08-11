
function showSlotConfirm(event) {
    let el = event.target;

    let dataset = el.dataset;
    let slot = dataset.slot;
    let iblock_id = dataset.iblock;
    let element_id = dataset.element
    let user = document.querySelector('select#user');
    let user_id = user.value;
    let user_name = user.options[user.selectedIndex].text;

    let old_slot = document.querySelector('span[data-element="' + user_id + '"]');


    const messageBox = BX.UI.Dialogs.MessageBox.confirm(
        '<div id="confirm-message" class="confirm-message">Записать пользователя "' + user_name + '" на "' + slot + '"?</div>',
        function (messageBox) {
            let message = BX('confirm-message');
            message.innerText = 'Идёт запись...'

            BX.ajax.runComponentAction('ylab:interview.write', 'ajaxHandler', {
                mode: 'class', //это означает, что мы хотим вызывать действие из class.php
                data: {
                    //данные будут автоматически замаплены на параметры метода
                    slot_datetime: slot, // новое значение слота для юзера
                    user_id: user_id, // для этого юзера
                    iblock_id: iblock_id // с каким инфоблоком работаем
                    // slot_datetime_name - символьный код свойства (SLOT_DATETIME)
                    // time_slot - продолжительность слота в минутах (30)
                    // end_day - на сколько дней генерировать слоты (2)
                }
            }).then(function (response) {

                if (el.classList.contains('free')){
                    el.classList.remove('free');
                }

                el.classList.add('busy');
                el.setAttribute('title', user_name);
                el.setAttribute('data-element', user_id);
                el.removeEventListener('click', showSlotConfirm);

                if (old_slot){
                    old_slot.classList.remove('busy');
                    old_slot.classList.add('free');
                    old_slot.removeAttribute('title');
                    old_slot.setAttribute('data-element', element_id);
                    old_slot.addEventListener('click', showSlotConfirm);
                }

                messageBox.close();
            }, function (response) {
                //сюда будут приходить все ответы, у которых status !== 'success'
                console.log(response);
            });
        },
        'Записать',
        function (messageBox) {
            messageBox.close();
        }
    );
}

BX.ready(function (){
    let wrap_slots = BX('slots');

    BX.findChildren(wrap_slots, 'span').forEach(element => {
        if (element.classList.contains('free')){
            element.addEventListener('click', showSlotConfirm);
        }
    });
});

