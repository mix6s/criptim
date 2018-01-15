!(function () {
    var TabsSwitcher = function (params) {
        this.$container = params.container;
        this.ativeTabSelector = 'tabs-item--active';
        this.tabsSelector = '.js-tab-content';

        this._init();
    };

    TabsSwitcher.prototype = {
        _init: function () {
            var _this = this;

            this.$container.on('click', function (event) {
                var element = event.target;

                _this._hideAllTabs();
                _this._makeTabActive(element);
            });
        },

        _makeTabActive: function (element) {
            var tabName = $(element).data('tab');
            console.log(tabName);
            $(element).parent().addClass(this.ativeTabSelector);

            $('#' + tabName).show();
        },

        _hideAllTabs: function () {
            this.$container.children().removeClass(this.ativeTabSelector);
            $(this.tabsSelector).hide();
        }
    };

    window.TabsSwitcher = TabsSwitcher;
})();