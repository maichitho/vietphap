/**
 * timstyle-1.3
 * Copyright (c) 2014 Sililab Jsc. Vietnam
 * 
 * License under MIT
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * @Datetime : Wed, 26 Mar 2014
 * @Contact to: thuannguyenchi@gmail.com
 */

+(function(window, document, $, undefined) {

    if (!Array.prototype.indexOf)
    {
        Array.prototype.indexOf = function(elt /*, from*/)
        {
            var len = this.length >>> 0;

            var from = Number(arguments[1]) || 0;
            from = (from < 0)
                    ? Math.ceil(from)
                    : Math.floor(from);
            if (from < 0)
                from += len;

            for (; from < len; from++)
            {
                if (from in this &&
                        this[from] === elt)
                    return from;
            }
            return -1;
        };
    }
    var instances = [];

    function splitByPattern(param, pattern) {
        if (param.indexOf(pattern) !== -1) {
            if (pattern === ":") {
                var index = param.indexOf(pattern),
                        arr = [];
                arr.push(param.slice(0, index));
                arr.push(param.slice(index + 1));
                return arr;
            } else
                return param.split(pattern);
        } else {
            var arr = [];
            arr.push(param);
            return arr;
        }
    }

    function store(obj) {
        if (instances.indexOf(obj) === -1) {
            instances.push(obj);
        }
    }

    function getObjectById(id) {
        for (var i = 0; i < instances.length; i++) {
            if (instances[i].id === id) {
                return instances[i];
            }
        }
        return null;
    }

    window.timstyle = new function( ) {
        // public methods
        return {
            store: store,
            splitByPattern: splitByPattern,
            getObjectById: getObjectById
        };
    }( );

    $(document).ready(function() {
        for (var i = 0; i < instances.length; i++) {
            if (instances[i]) {
                instances[i].init();
            }
        }
    });
}(window, document, window.jQuery));

+(function(window, document, $, timstyle) {
    var SELECTOR = ".box-group",
            TITLE = "data-title",
            TEMPLATE = "<ul class='tim-box-title'>"
            + "<li class='box-left'></li>"
            + "<li class='box-title'><span style='display:inline-block;margin:2px;'></span></li>"
            + "<li class='box-right'></li>"
            + "</ul>";
    var boxgroup = {
        id: "boxgroup",
        options: {},
        init: function( ) {
            $(SELECTOR).each(function() {
                var self = $(this);
                self.prepend(TEMPLATE);
                var title = self.attr(TITLE);
                if (title !== undefined) {
                    self.find(".tim-box-title > .box-title > span").text(title);
                }

                var middleW = self.find(".tim-box-title > .box-title > span").width(),
                        parentW = self.width(),
                        rightW = parentW - 21 - middleW;
                self.find(".tim-box-title > .box-right").width(rightW);

                if (self.hasClass("rounded--sm")) {
                    self.find(".tim-box-title > .box-right").addClass("rounded--sm");
                    self.find(".tim-box-title > .box-left").addClass("rounded--sm");
                } else if (self.hasClass("rounded--md")) {
                    self.find(".tim-box-title > .box-right").addClass("rounded--md");
                    self.find(".tim-box-title > .box-left").addClass("rounded--md");
                } else if (self.hasClass("rounded--lg")) {
                    self.find(".tim-box-title > .box-right").addClass("rounded--lg");
                    self.find(".tim-box-title > .box-left").addClass("rounded--lg");
                }


            });
        }
    };
    timstyle.store(boxgroup);
}(window, document, window.jQuery, timstyle));

+(function(window, document, $, timstyle) {
    var SELECTOR = ".dialog",
            TITLE = "data-title",
            BUTTON = "data-button",
            TEMPLATE_HEADER = '<div class="dialog-header">'
            + '<div class="dialog-title"></div>'
            + '<div class="dialog-close">&times;</div>'
            + '</div>',
            TEMPLATE_FOOTER = '<div class="dialog-footer">'
            + '</div>',
            TEMPLATE_BUTTON = '<button class="button--sm dialog-button"></button>',
            TEMPLATE_MODAL = '<div class="dialog-modal"><div class=dialog-modal--wrap></div></div>',
            TEMPLATE_MODAL_OVERLAY = '<div class="dialog-modal--overlay"></div>';
    var dialog = {
        id: "dialog",
        options: {},
        init: function( ) {
            var overlay = $(TEMPLATE_MODAL_OVERLAY);
            overlay.click(function(e) {
                e.stopPropagation();
                return;
            });
            $("body").append(overlay);
            overlay.hide();

            $(SELECTOR).each(function() {
                var self = $(this);
                self.prepend(TEMPLATE_HEADER);
                self.append(TEMPLATE_FOOTER);

                var title = self.attr(TITLE),
                        button = self.attr(BUTTON);

                if (title !== undefined) {
                    self.find(".dialog-title").html(title);
                }

                self.find(".dialog-close").click(function() {
                    self.hide();
                    self.parent().parent().hide();
                    if ($("body").find(".dialog").filter(":visible").length < 1)
                        $("body").find(".dialog-modal--overlay").hide();
                });

                if (button !== undefined) {
                    var arrButton = timstyle.splitByPattern(button, ",");
                    for (var i = 0; i < arrButton.length; i++) {
                        var btn = timstyle.splitByPattern(arrButton[i], ":");
                        var jqButton = $(TEMPLATE_BUTTON);
                        jqButton.text(btn[0]);
                        if (btn.length > 1) {
                            jqButton.attr("onclick", btn[1]);
                        }
                        self.find(".dialog-footer").append(jqButton);
                    }
                }

                if (!self.hasClass("dialog--close")) {
                    self.find(".dialog-close").hide();
                }
            });
        },
        show: function(obj, options) {
            if (options !== undefined && options.modal) {
                if (!obj.parent().hasClass("dialog-modal--wrap")) {
                    var modal = $(TEMPLATE_MODAL);
                    modal.click(function(e) {
                        e.stopPropagation();
                        return;
                    });
                    modal.find(".dialog-modal--wrap").width($(window).width());
                    $("body").append(modal);
                    if (obj.height() > $(window).height()) {
                        modal.find(".dialog-modal--wrap").height(obj.height());
                    } else {
                        modal.find(".dialog-modal--wrap").height($(window).height());
                    }
                    modal.find(".dialog-modal--wrap").append(obj);
                    obj.css("width", options.width);
                    obj.show().css("z-index", 1001);
//                    obj.find(".dialog-content").width(obj.width() - 20);
                    modal.show();
                    $("body").find(".dialog-modal--overlay").show();
                } else {
                    obj.parent().parent().show();
                    $("body").find(".dialog-modal--overlay").show();
                    obj.show();
                }
            } else {
                var top = $(window).height() / 2 - obj.height() / 2;
                if (options.top !== undefined)
                    top = options.top;
                obj.show().css("margin-top", top + "px");
                if (options.left !== undefined) {
                    obj.css("margin-left", options.left + "px");
                }
            }
        },
        hide: function(obj) {
            obj.hide();
            obj.parent().parent().hide();
//            alert($("body").find(".dialog").filter(":visible").length);
            if ($("body").find(".dialog").filter(":visible").length < 1)
                $("body").find(".dialog-modal--overlay").hide();
        },
        fadeOut: function(obj) {
            obj.fadeOut();
            obj.parent().parent().fadeOut();
            $("body").find(".dialog-modal--overlay").fadeOut();
        },
        fadeIn: function(obj, options) {
            if (options !== undefined && options.modal) {
                if (!obj.parent().hasClass("dialog-modal--wrap")) {
                    var modal = $(TEMPLATE_MODAL);
                    modal.click(function(e) {
                        e.stopPropagation();
                        return;
                    });
                    $("body").append(modal);
                    modal.find(".dialog-modal--wrap").append(obj);
                    obj.css("width", options.width);
                    obj.fadeIn().css("z-index", 1001);
//                    obj.find(".dialog-content").width(obj.width() - 20);
                    modal.fadeIn();
                } else {
                    obj.parent().parent().fadeIn();
                    $("body").find(".dialog-modal--overlay").fadeIn();
                    obj.fadeIn();
                }
            } else {
                var top = $(window).height() / 2 - obj.height() / 2;
                if (options.top !== undefined)
                    top = options.top;
                obj.show().css("margin-top", top + "px");
                if (options.left !== undefined) {
                    obj.css("margin-left", options.left + "px");
                }
            }
        }
    };

    $.fn.extend({
        showDialog: function(options) {
            var defaults = {
                modal: true,
                width: "100%"
            };
            var opt = $.extend(defaults, options);
            return this.each(function() {
                dialog.show($(this), opt);
            });
        }
    });
    $.fn.extend({
        hideDialog: function( ) {
            return this.each(function() {
                dialog.hide($(this));
            });
        }
    });

    $.fn.extend({
        fadeInDialog: function(options) {
            var defaults = {
                modal: true,
                width: "100%"
            };
            var opt = $.extend(defaults, options);
            return this.each(function() {
                dialog.fadeIn($(this), opt);
            });
        }
    });
    $.fn.extend({
        fadeOutDialog: function( ) {
            return this.each(function() {
                dialog.fadeOut($(this));
            });
        }
    });

    timstyle.store(dialog);
}(window, document, window.jQuery, timstyle));

+(function(window, document, $, timstyle) {

    var ACTION = "click",
            SELECTOR = ".dropdown",
            POPUP = "[class='menu-dropdown']";
    var dropdown = {
        id: "dropdown",
        options: {},
        handlerFn: function(e) {
            var $this = $(this).find(POPUP);
            if ($this.attr("data-show") !== undefined && $this.attr("data-show") === "false") {
                $(document).triggerHandler(ACTION);
                $this.toggle();
                $this.attr("data-show", "true");
                dropdown.setPosition($this);
                e.stopPropagation();
                return;
            }

            if ($this.attr("data-show") !== undefined && $this.attr("data-show") === "true") {
                console.log("enter:true");
                e.stopPropagation();
                return;
            }
            return;
        },
        showPopup: function(e, obj) {
            e = e || window.event;
            var $this = obj.find(POPUP);
            if ($this.attr("data-show") !== undefined && $this.attr("data-show") === "false") {
                $(document).triggerHandler(ACTION);
                $this.toggle( );
                $this.attr("data-show", "true");
                dropdown.setPosition($this);
                e.stopPropagation();
                return;
            }

            if ($this.attr("data-show") !== undefined && $this.attr("data-show") === "true") {
                e.stopPropagation();
                return;
            }
            return;
        },
        refresh: function() {
            $(document).find(SELECTOR + " > " + POPUP).attr("data-show", "false").hide( );
        },
        setPosition: function(jObj) {
            var parentHeight = jObj.parent().outerHeight(),
                    height = jObj.height(),
                    width = jObj.width(),
                    parentOffset = jObj.parent().offset();

            var left = parentOffset.left,
                    top = parentOffset.top + parentHeight;
            if (left + width > $(document).width()) {
                var x = left + width - $(document).width();
                left = left - x;
            }

            if (parentOffset.top + height > $(document).height()) {
                top = parentOffset.top - height;
            }

            jObj.offset({"top": top, "left": left});
        },
        init: function( ) {
            $(SELECTOR).each(function( ) {
                var $this = $(this);
                var popup = $this.find(POPUP);
                popup.hide( );
                if (popup.attr("data-show") === undefined) {
                    popup.attr("data-show", "false");
                }
            });
            $(document).off(ACTION, SELECTOR).on(ACTION, SELECTOR, dropdown.handlerFn);
            $(document).off(ACTION, dropdown.refresh).on(ACTION, dropdown.refresh);
        }
    };

    $.fn.extend({
        showPopup: function() {
            return this.each(function(e) {
                dropdown.showPopup(e, $(this));
            });
        }
    });

    $.fn.extend({
        refresh: function() {
            return this.each(function() {
                dropdown.refresh();
            });
        }
    });

    timstyle.store(dropdown);
}(window, document, window.jQuery, timstyle));

-(function(window, document, $, timstyle) {
    var SELECTOR = ".thumbnail",
            LINK = "data-link",
            SRC = "data-src",
            EDIT_TITLE = "data-edit-title",
            EDIT_COMMAND = "data-edit-command",
            TEMPLATE = "<a class='thumbnail-link'>"
            + "<img class='thumbnail-img' src=''/><a class='thumbnail-edit'></a>"
            + "</a>";

    var thumbnail = {
        id: "thumbnail",
        options: {},
        init: function( ) {
            $(SELECTOR).each(function() {
                var self = $(this);
                var skip = self.attr("data-skip");
                if (skip !== undefined) {
                    return;
                }
                self.prepend(TEMPLATE);
                var src = self.attr(SRC),
                        link = self.attr(LINK),
                        editTitle = self.attr(EDIT_TITLE),
                        editCommand = self.attr(EDIT_COMMAND);

                if (src !== undefined) {
                    self.find(".thumbnail-img").attr("src", src);
                }

                if (editTitle !== undefined) {
                    self.find(".thumbnail-edit").text(editTitle);
                    self.find(".thumbnail-edit").attr("onclick", editCommand);
                }

                if (link !== undefined) {
                    var arr = timstyle.splitByPattern(link, ":");
                    if (arr.length > 1) {
                        self.find(".thumbnail-link").attr("href", arr[1]);
                        self.find(".thumbnail-link").attr("target", arr[0]);
                    } else {
                        self.find(".thumbnail-link").attr("href", arr[0]);
                    }
                }

                if (self.find(".thumbnail-content--right").length > 0) {
                    self.find(".thumbnail-link").css("display", "inline-block");
                }
            });
        }
    };
    timstyle.store(thumbnail);
}(window, document, window.jQuery, timstyle));

+(function(window, document, $, timstyle) {
    var SELECTOR = ".progressbar",
            VALUE = "data-value",
            TEMPLATE = '<span class="progressbar-value"></span>'
            + '<div class="progressbar-scale"></div>';

    var progressbar = {
        id: "progressbar",
        options: {},
        init: function( ) {
            $(SELECTOR).each(function() {
                var self = $(this);
                self.prepend(TEMPLATE);
                var value = self.attr(VALUE),
                        valuePx = 0;

                if (value !== undefined && !isNaN(parseInt(value))) {
                    value = parseInt(value);
                } else {
                    value = 0;
                }
                var width = self.width();
                valuePx = Math.ceil(value * (width / 100));

                self.find(".progressbar-scale").width(valuePx);
                self.find(".progressbar-value").text(value + '%');

            });
        },
        updateProgress: function(obj, value) {
            if (value !== undefined && !isNaN(parseInt(value))) {
                value = parseInt(value);
            } else {
                value = 0;
            }
            var width = obj.width();
            var valuePx = Math.ceil(value * (width / 100));

            obj.find(".progressbar-scale").width(valuePx);
            obj.find(".progressbar-value").text(value + '%');
        }
    };

    $.fn.extend({
        updateProgress: function(value) {
            return this.each(function() {
                progressbar.updateProgress($(this), value);
            });
        }
    });

    timstyle.store(progressbar);
}(window, document, window.jQuery, timstyle));

+(function(window, document, $, timstyle) {
    var SELECTOR = ".color-picker",
            LABEL = "data-label",
            TEMPLATE_COLOR = '<div class="color-picker-item"></div>',
            TEMPLATE = '<div class="color-picker-box"></div>'
            + '<input type="hidden" name="color_value"/>'
            + '<a class="color-picker-link"></a>',
            TEMPLATE_PANEL = '<div class="color-picker-panel"></div>';
    var selectedObj = null;
    var colorpicker = {
        id: "colorpicker",
        options: {
            colors: ["000000", "010066", "346633", "663200", "990100", "cc6601",
                "9acc99", "fe9900", "ffff66", "bcdd5a", "e6e6e6", "FFFFFF"]
        },
        init: function() {
            // ensure that only one color panel is created
            var isExisted = false;
            $(SELECTOR).each(function() {
                var self = $(this);
                self.append(TEMPLATE);

                var label = self.attr(LABEL);

                if (label !== undefined) {
                    self.find(".color-picker-link").text(label);
                }

                var jObj;
                if (!isExisted) {
                    jObj = $(TEMPLATE_PANEL);
                    for (var i = 0; i < colorpicker.options.colors.length; i++) {
                        var jColor = $(TEMPLATE_COLOR);
                        jColor.css("background-color", "#" + colorpicker.options.colors[i]);
                        jObj.append(jColor);
                    }

                    $("body").append(jObj);
                    isExisted = true;
                }

                $(this).find(".color-picker-box").css("background-color", "#" + colorpicker.options.colors[0]);
            });

            $(".color-picker-item").click(function(e) {
                var color = $(this).css('backgroundColor');
                selectedObj.find("input").val(color);
                selectedObj.find(".color-picker-box").css('backgroundColor', color);
                colorpicker.hideColorPanel();
                $(colorpicker).triggerHandler("changeColor");
                e.stopPropagation();
            });

            $(".color-picker-link").click(function(e) {
                colorpicker.showColorPanel($(this).parent());
                e.stopPropagation();
            });

            $(".color-picker-box").click(function(e) {
                colorpicker.showColorPanel($(this).parent());
                e.stopPropagation();
            });

            $(document).click(function() {
                colorpicker.hideColorPanel();
            });
        },
        addColor: function(obj, color) {
            var jColor = $(TEMPLATE_COLOR);
            jColor.css("background-color", "#" + color);
            obj.find(".color-picker-panel").append(jColor);
        },
        showColorPanel: function(obj) {
            selectedObj = obj;
            var topPicker = obj.offset().top,
                    leftPicker = obj.offset().left;
            var widhtPanel = $("body").find(".color-picker-panel").outerWidth(),
                    heightPanel = $("body").find(".color-picker-panel").outerHeight();
            var top = topPicker + 20,
                    left = leftPicker + 20;

            if ((topPicker + heightPanel) > $(window).height()) {
                top = topPicker - heightPanel;
            }

            if ((leftPicker + widhtPanel) > $(window).width()) {
                left = leftPicker - widhtPanel;
            }

            $("body").find(".color-picker-panel").show().offset({
                top: top,
                left: left
            });
        },
        hideColorPanel: function() {
            $("body").find(".color-picker-panel").hide();
        },
        setting: function(obj, options) {
            if (options["width"] !== undefined) {
                obj.find(".color-picker-panel").width(parseInt(options["width"]));
            }
        },
        getSelectedColor: function(obj) {
            return obj.find("input").val();
        },
        setSelectedColor: function(obj, color) {
            obj.find(".color-picker-box").css('backgroundColor', color);
            obj.find("input").val(obj.find(".color-picker-box").css('backgroundColor'));
        }
    };

    $.fn.extend({
        addColor: function(color) {
            return this.each(function() {
                colorpicker.addColor($(this), color);
            });
        },
        colorpicker: function(options) {
            var defaults = {
                width: 220
            };
            var options = $.extend(defaults, options);
            return this.each(function() {
                colorpicker.setting($(this), options);
            });
        },
        getSelectedColor: function() {
            return colorpicker.getSelectedColor($(this));
        },
        setSelectedColor: function(color) {
            colorpicker.setSelectedColor($(this), color);
        },
        onChange: function(callback) {
            $(colorpicker).on("changeColor", callback);
        }
    });

    timstyle.store(colorpicker);
}(window, document, window.jQuery, timstyle));
