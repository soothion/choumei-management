define(function(require,exports,module){
	var path=location.origin+'/cms1/js/popup';
    var popup= {
        alert: function (options) {
            var self=this;
            options.type = 'alert';
            var popup = $(new EJS({url: path}).render({options: options}));
            popup.on('click', '.popup-alert-define', function () {
                options.define && options.define.call(this);
                self.close();
            });
            this.overlay();
            this.append(popup);
        },
        confirm: function (options) {
            var self=this;
            options.type = 'confirm';
            var popup = $(new EJS({url: path}).render({options: options}));
            popup.on('click', '.popup-alert-define', function () {
                options.define && options.define.call(this,popup.find('.popup-prompt-input').val());
                self.close();
            });
            popup.on('click', '.popup-alert-cancel', function () {
                self.close();
            });
            this.overlay();
            this.append(popup);
        },
        prompt: function (options) {
            options.inputType = options.inputType || 'text';
            this.confirm(options);
        },
        sheet: function (options) {
            var self=this;
            options.type = 'sheet';
            options.direction = 'bottom';
            var popup = $(new EJS({url: path}).render({options: options}));
            popup.on('click', '.popup-sheet-item', function () {
                var $this = $(this);
                options.define && options.define.call(this,{id: $this.data('id'), name: $this.text()});
                self.close();
            });
            popup.on('click', '.popup-sheet-cancel', function () {
                self.close();
            });
            this.overlay();
            this.append(popup);
        },
        menu: function (options) {
            options.type = 'menu';
            var popup = $(new EJS({url: path}).render({options: options}));
            var self=this;
            popup.on('click', '.popup-menu-item', function () {
                var $this = $(this);
                if(options.active){
                    $this.addClass('popup-menu-active').siblings().removeClass('popup-menu-active');
                }
                options.define && options.define.call(this,{id: $this.data('id'), name: $this.text()});
                self.close();
            });
            popup.find('.popup-menu-list').css('maxHeight', $(window).height() - 150);
            this.overlay().on('click',function(){
                self.close();
            });
            this.append(popup);
        },
        overlay: function () {
            var $dom=$('<div class="popup-overlay"></div>');
            $(document.body).append($dom);
            return $dom;
        },
        close: function () {
            var popup = $('.popup,.popup-overlay');
            popup.fadeOut(200, function () {
                popup.remove();
            });
        },
        append:function(popup){
			if(!lib.browser.mobile){
				popup.css({
					left:($(window).width()-300)/2,
					width:300
				});
			}
            $(document.body).append(popup);
            setTimeout(function(){
                popup.addClass('popup-trans');
            },100);
        }
    }
    module.exports=popup;
})