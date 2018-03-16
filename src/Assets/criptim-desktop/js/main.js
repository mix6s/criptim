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