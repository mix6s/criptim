function initEvents() {
    $('.js-toggle-sidebar').on('click', function () {
        $('.sidebar-mobile').addClass('sidebar-mobile--active');
    });

    $('.js-close-sidebar').on('click', function () {
        $('.sidebar-mobile').removeClass('sidebar-mobile--active');
    });

    $('.js-show-user-profile').on('click', function () {
        $('.user-menu').addClass('user-menu--active');
    });

    $('.js-close-user-profile').on('click', function () {
        $('.user-menu').removeClass('user-menu--active');
    });
}

$(function () {
    initEvents();
});

function showSettings() {
    var toggle = document.getElementById('js-toggle');
    var container = document.getElementById('js-container');

    if (container.classList.contains('hidden-statistic--hide')) {
        container.classList.remove('hidden-statistic--hide');
        container.classList.add('hidden-statistic--show');
        toggle.childNodes[0].nodeValue = 'Hide settings';
    } else {
        container.classList.add('hidden-statistic--hide');
        container.classList.remove('hidden-statistic--show');
        toggle.childNodes[0].nodeValue = 'Show settings';
    }
}