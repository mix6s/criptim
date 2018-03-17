!(function () {
    var PasswordTypeSwitcher = function (params) {
        this.$container = params.container;
        this.$input = this.$container.find('input');
        this.switchButton = this.$container.find('.js-change-password-visibility');

        this._init();
    };

    PasswordTypeSwitcher.prototype = {
        _init: function () {
            var _this = this;
            this.switchButton.on('click', function () {
                _this._changeInputType();
            });
        },
        _changeInputType: function () {
            var inputType = this.$input.prop('type');

            if (inputType === 'password') {
                this.$input.prop('type', 'text');
            } else {
                this.$input.prop('type', 'password');
            }
        }
    };

    window.PasswordTypeSwitcher = PasswordTypeSwitcher;
})();