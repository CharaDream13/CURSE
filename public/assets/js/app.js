document.addEventListener('submit', function (event) {
    var message = event.target.dataset.confirm;
    if (message && !window.confirm(message)) {
        event.preventDefault();
    }
});

document.querySelectorAll('form[data-dates]').forEach(function (form) {
    var checkIn = form.elements.check_in;
    var checkOut = form.elements.check_out;

    function syncMinimum() {
        if (!checkIn.value) {
            return;
        }
        var next = new Date(checkIn.value);
        next.setUTCDate(next.getUTCDate() + 1);
        checkOut.min = next.toISOString().slice(0, 10);
    }

    checkIn.addEventListener('change', syncMinimum);
    syncMinimum();

    form.addEventListener('submit', function (event) {
        if (checkIn.value && checkOut.value && checkOut.value <= checkIn.value) {
            event.preventDefault();
            window.alert('Дата выезда должна быть позже даты заезда');
        }
    });
});

document.querySelectorAll('input[data-filter]').forEach(function (input) {
    var table = document.getElementById(input.dataset.filter);

    input.addEventListener('input', function () {
        var text = input.value.toLowerCase();
        table.querySelectorAll('tr').forEach(function (row) {
            if (row.querySelector('th')) {
                return;
            }
            row.hidden = text !== '' && row.textContent.toLowerCase().indexOf(text) === -1;
        });
    });
});

document.querySelectorAll('select[data-category-select]').forEach(function (category) {
    var room = document.getElementById(category.dataset.categorySelect);

    function filterRooms() {
        Array.from(room.options).forEach(function (option) {
            var show = !option.dataset.category || option.dataset.category === category.value;
            option.hidden = !show;
            option.disabled = !show;
        });
        if (room.selectedOptions[0] && room.selectedOptions[0].disabled) {
            room.value = '';
        }
    }

    category.addEventListener('change', filterRooms);
    filterRooms();
});

var floorTabs = document.querySelectorAll('[data-floor-tab]');
var floors = document.querySelectorAll('[data-floor]');

floorTabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
        floors.forEach(function (floor) {
            floor.hidden = tab.dataset.floorTab !== 'all' && floor.dataset.floor !== tab.dataset.floorTab;
        });
        floorTabs.forEach(function (item) {
            item.classList.toggle('active', item === tab);
        });
    });
});

if (floorTabs.length) {
    floorTabs[0].click();
}
