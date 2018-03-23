/**
 * timjs-1.3.4.1
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
 * ==============================================================================
 *  
 *  @Date  : 2 August 2014
 *  @Contact to: timjss@sililab.com
 */

-(function(window, document, $, undefined) {
    'use strict';
    /*
     * Các định nghĩa hằng cho các sự kiện thay đổi
     * Define event names for notifing
     **/
    var channels = {
        CHANGE_VIEW_DATA: "CHANGE_VIEW_DATA",
        CHANGE_WATCH_DATA: "CHANGE_WATCH_DATA",
        CHANGE_MODEL_DATA: "CHANGE_MODEL_DATA",
        UPDATE_MODEL_ROW_DATA: "UPDATE_MODEL_ROW_DATA",
        ADD_MODEL_ROW_DATA: "ADD_MODEL_ROW_DATA",
        REMOVE_MODEL_ROW_DATA: "REMOVE_MODEL_ROW_DATA",
        CHANGE_CONTROL: "CHANGE_CONTROL",
        SUBMIT_VALIDATION: "SUBMIT_VALIDATION"
    };

    /**
     * Lưu trữ các hằng sử dụng trong chương trình
     * 
     * @type type
     */
    var constants = {
        CHECK_ORDER: "order",
        CHECK_ALL: "all"
    };

    /**
     * Lưu trữ thông số cấu hình cho một trang đang thực hiện
     * Store configs of a page
     * @type type
     */
    var pageParams = {};

    /**
     * Lưu trữ các viewmodeler
     * Store viewmodelers
     * @type type
     */
    var maps = {};

    /**
     * Lưu trữ các phần tử thông báo data-message-for
     * Store data-message-for elements
     * @type type
     */
    var messagesFors = {};

    /**
     * Lưu trữ các hàm validation 
     * Store validatation functions
     * @type type
     */
    var validationFns = {};

    /**
     * Lấy các liên kết đăng ký sự kiện của một đối tượng qua namespace
     * 
     * @param {type} namespace
     * @returns {@exp;@call;$@call;data}
     */
    function getBindings(namespace) {
        if ($(document).data(namespace) === undefined) {
            $(document).data(namespace, []);
        }
        return  $(document).data(namespace);
    }

    /**
     * Biến đổi biểu thức thành mảng các chuỗi có thể là tên hàm
     * Get possible string name of function
     * 
     * @param {type} expression
     * @returns {undefined}
     */
    function expToNameArray(expression) {
        var reVal = expression.replace(/(\'.*\')|(\".*\")/g, " ");
        return reVal.match(/[$A-Za-z_]+[\w.]*([\w]*(\[|\'|\"|\])*)*[\.\(]?/g);
    }

    /**
     * Trả về đối tượng map lưu trữ giá trị thuộc tính
     * và đối tượng cấp cha
     * Get map of child and parent object that are properties of viewmodel
     * 
     * @param {type} exp
     * @param {type} obj
     * @returns {unresolved}
     */
    function getFunction(exp, obj) {
        if (exp) {
            var func = {},
                    mObj,
                    len = exp.length;
            if (len === 1 && obj[exp[0]] !== undefined) {
                func["parent"] = {
                    "obj": obj,
                    "attr": exp[0]
                };
                func["child"] = obj[exp[0]];
                return func;
            }
            for (var i = 0; i < exp.length; i++) {
                if (obj[exp[i]] === undefined)
                {
                    if (i === 0) {
                        return undefined;
                    } else {
                        obj[exp[i]] = new Object( );
                    }
                }
                if (i < len - 1) {
                    mObj = obj[exp[i]];
                    obj = mObj;
                } else if (i === len - 1) {
                    func["child"] = obj[exp[i]];
                    func["parent"] = {
                        "obj": obj,
                        "attr": exp[i]
                    };
                }
            }
            if (func["child"] !== undefined)
                return func;
            else
                return undefined;
        } else {
            return undefined;
        }
    }

    /**
     * Trả về đối tượng map chứa giá trị của tham số truyền vào
     * từ các data-*, và các namespace của nó
     *  
     * @param {type} element
     * @param {type} expression
     * @returns {
     *   namespace : namespaces map
     *   fun : functional value of expression
     *  }
     */
    function stringToFun(element, expression) {
        var viewmodeler = maps[element.attr("data-scope")],
                retFun = {},
                keyStack = [],
                runCode = "",
                namespaces = {};
        var matchedExp = expToNameArray(expression);
        if (matchedExp !== null) {
            var namespaces = [];
            for (var i = 0; i < matchedExp.length; i++) {
                var functionStr = matchedExp[i].replace(/(\.|\()$/g, ""),
                        fn = functionStr.match(/[^\.\[\]\'\"]+/g);
                // output : name']['name
                // check current context is viewmodeler
                var func = getFunction(fn, viewmodeler);
                if (func === undefined)
                    continue;
                if (func["child"] !== undefined) {
                    var ns = functionStr.replace(/(\'\]\[\')|(\'\]\[\')/g, "\.").replace(/\'\]|\"\]|\[(.*)|\](.*)/g, "");
                    if (matchedExp.length === 1 && expression.match(/\"|\'|\+|\-|\*|\//g) === null) {
                        namespaces[ns] = func["parent"];
                        retFun["namespace"] = namespaces;
                        retFun["fun"] = func["child"];
                        return retFun;
                    }
                    namespaces[ns] = func["parent"];
                    // key for parse expression in context of viewmodeler
                    var key = splitByPattern(ns, "\.")[0];
                    keyStack.push(key);
                }
                //TODO: check other contexts
            }
        }

        //parse expression in context of controls like for/if/switch or js script code
        var map = viewmodeler.scopes,
                key,
                tem = "",
                runCode = " " + expression + " ";
        if (map !== undefined) {
            for (key in map) {
                var regEpx = new RegExp("(" + key + "[\\.|\\(|\\[|\\s|\\)])", "g");
                tem = runCode.replace(regEpx, "$viewmodeler.scopes.$1");
                runCode = tem;
            }
            // expression does not have refered context
            if (runCode === "") {
                runCode = expression;
            }
        }

        // parse expression in viewmodeler context
        if (keyStack) {
            for (var i = 0; i < keyStack.length; i++) {
                var regExp = new RegExp("((\\s)(" + keyStack[i] + "[\\.|\\[|\\(]))", "g");
                tem = runCode.replace(regExp, "$viewmodeler.$3");
                var regExp2 = new RegExp("((\\[|\\()(" + keyStack[i] + "[\\.|\\[|\\(]))", "g");
                runCode = tem.replace(regExp2, "$2$viewmodeler.$3");
            }
        }
        retFun["namespace"] = namespaces;
        retFun["fun"] = Function("$viewmodeler", "tim", "$dom", "return " + runCode + ";")(viewmodeler, window.tim, element.get(0));
        return retFun;
    }

    /**
     * Cập nhật giá trị một thuộc tính của model và kích hoạt sự kiện thayư
     * đổi cho các đối tượng quan sát nó
     * Update value to viewmodel attributes when some events are fired
     * 
     * @param {type} attr : namespace of attribute
     * @param {type} value 
     * @returns {undefined}
     */
    function updateValue(attr, value) {
        var self = this,
                namespace = self["namespace"];
        var matchedExp = expToNameArray(attr);
        if (matchedExp !== null && matchedExp.length === 1) {
            for (var i = 0; i < matchedExp.length; i++) {
                var functionStr = matchedExp[i].replace(/(\.|\()$/g, ""),
                        fn = functionStr.match(/[^\.\[\]\'\"]+/g);
                // output : name']['name
                var func = getFunction(fn, self);
                if (func && func["child"] !== undefined) {
                    var ns = functionStr.replace(/(\'\]\[\')|(\'\]\[\')/g, "\.").replace(/\'\]|\"\]/g, ""),
                            mObj = func["parent"];
                    mObj["obj"][mObj["attr"]] = value;
                    $(self).triggerHandler(channels.CHANGE_WATCH_DATA + "." + namespace + "." + ns, [value]);
                    $(self).triggerHandler(channels.CHANGE_MODEL_DATA + "." + namespace + "." + ns, [value]);
                }
            }
        }
    }

    /**
     * Cập nhật giá trị một phần tử view tương ứng ở một vị trí cụ thể trong mảng
     * liên kết bởi vòng lặp for
     * Update a row of array attribute of viewmodel
     * 
     * @param {type} attr : name of array attribute
     * @param {type} index : row will be updated
     * @returns {undefined}
     */
    function updateRowValue(attr, index) {
        var self = this,
                namespace = self["namespace"];
        $(self).triggerHandler(channels.UPDATE_MODEL_ROW_DATA + "." + namespace + "." + $.trim(attr), [index]);
    }

    /**
     * Thêm một phần tử vào cuối vòng lặp for
     * Add a row to array of viewmodel and refresh the view
     * 
     * @param {type} attr
     * @param {type} value
     * * @param {boolean} flag: check whether obj is added to array
     * @returns {undefined}
     */
    function addRowValue(attr, obj, flag) {
        var self = this,
                namespace = self["namespace"];
        $(self).triggerHandler(channels.ADD_MODEL_ROW_DATA + "." + namespace + "." + $.trim(attr), [obj, flag]);
    }

    /**
     * Cập nhật giá trị một phần tử view tương ứng ở một vị trí cụ thể trong mảng
     * liên kết bởi vòng lặp for
     * Remove a row from array of viewmodel and refresh the view
     * 
     * @param {type} attr
     * @param {type} value
     * @returns {undefined}
     */
    function removeRowValue(attr, index) {
        var self = this,
                namespace = self["namespace"];
        $(self).triggerHandler(channels.REMOVE_MODEL_ROW_DATA + "." + namespace + "." + $.trim(attr), [index]);
    }

    /**
     * Cập nhật lại các expression theo các tên biến mới
     * 
     * @param {type} element
     * @param {type} attrName
     * @param {type} alias
     * @param {type} curAlias
     * @param {type} parentScope
     * @returns {undefined}
     */
    function parseContext(element, attrName, alias, curAlias, parentScope) {
        element.find("[" + attrName + "]").each(function( ) {
            var itemName = $(this).attr(attrName),
                    scope = $(this).attr("data-scope");
            if (itemName !== undefined && scope !== undefined && scope === parentScope) {
                replaceVarContext($(this), attrName, "\\$current", curAlias);
                replaceVarContext($(this), attrName, alias, curAlias);
            }
        });
    }

    /**
     * Cập nhật lại các expression với các tên biến đã được thay thế, 
     * cập nhật giá trị mỗi hàng của vòng for
     * 
     * @param {type} element
     * @param {type} parentScope
     * @param {type} proName
     * @param {type} alias
     * @param {type} curAlias
     * @param {type} isAlias
     * @param {type} curObj
     * @returns {undefined}
     */
    function updateAttrContext(element, parentScope, proName, alias, curAlias, isAlias, curObj, parentName, flag) {

        updateParent(element, parentScope, alias, curAlias);

        // parse for/if/if not
        element.find("[data-control^='if']").each(function( ) {
            var jObj = $(this),
                    childName = jObj.attr("data-parent");
            if (jObj !== undefined) {
                var scope = jObj.attr("data-scope");
                if (scope !== undefined && scope === parentScope && parentName === childName) {
                    replaceVarContext(jObj, "data-control", "\\$current", curAlias);
                    replaceVarContext(jObj, "data-control", alias, curAlias);
                    parseIf(jObj, proName);
                }
            }
        });

        element.find("[data-control^='for']").each(function( ) {
            var scope = $(this).attr("data-scope");
            // loop parse
            if (scope !== undefined && scope === parentScope) {
                parseFor($(this), flag);
            }
        });

        element.find("[data-item]").each(function( ) {
            var itemName = $(this).attr("data-item"),
                    scope = $(this).attr("data-scope");
            if (itemName !== undefined && scope !== undefined && scope === parentScope) {
                if (!isAlias) {
                    var attrNames = expToNameArray(itemName);
                    if (attrNames !== null) {
                        for (var j = 0; j < attrNames.length; j++) {
                            if (curObj[attrNames[j]] !== undefined) {
                                var regExp = new RegExp("(" + attrNames[j] + ")", "g");
                                itemName = itemName.replace(regExp, alias + ".$1");
                            }
                        }
                        $(this).attr("data-item", itemName);
                    }
                }
                replaceVarContext($(this), "data-item", "\\$current", curAlias);
                replaceVarContext($(this), "data-item", alias, curAlias);
                updateItem($(this), true);
            }
        });

        parseContext(element, "data-call", alias, curAlias, parentScope);
        parseContext(element, "data-attr", alias, curAlias, parentScope);
        parseContext(element, "data-var", alias, curAlias, parentScope);
        parseContext(element, "data-style", alias, curAlias, parentScope);
        parseContext(element, "data-watch", alias, curAlias, parentScope);
        parseContext(element, "data-out", alias, curAlias, parentScope);
        parseContext(element, "data-in", alias, curAlias, parentScope);
        parseContext(element, "data-select-value", alias, curAlias, parentScope);
    }

    function updateParent(element, parentScope, alias, curAlias) {
        element.find("[data-control^='if']").each(function( ) {
            var self = $(this),
                    scope = $(this).attr("data-scope");
            // loop parse
            if (scope !== undefined && scope === parentScope) {
                var closest = self.parents("[data-control]"),
                        ctrlName = closest.attr("data-control");
                replaceVarContext(self, "data-control", "\\$current", curAlias);
                replaceVarContext(self, "data-control", alias, curAlias);
                self.attr("data-parent", ctrlName);
            }
        });
    }

    /**
     * Cập nhật lại các liên kết sự kiện giữa view và viewmodel
     * 
     * @param {type} element
     * @returns {undefined}
     */
    function reBinding(element) {
        // rebind event
        element.find("[data-attr]").each(function( ) {
            bindDataAttr($(this), true);
        });
        element.find("[data-var]").each(function( ) {
            bindDataVar($(this), true);
        });
        element.find("[data-style]").each(function( ) {
            bindDataStyle($(this), true);
        });
        element.find("[data-watch]").each(function( ) {
            bindDataWatch($(this), true);
        });
        element.find("[data-out]").each(function( ) {
            bindDataOut($(this), true);
        });
        registerEvent(element, true);
    }

    /**
     * Lưu vết khi debug
     * 
     * @param {type} message
     * @returns {undefined}
     */
    function debug(message) {
        if (!window.console) {
            window.console = {log: function( ) {
                }};
        }
        if (pageParams.debug) {
            console.log(message);
        }
    }

    /**
     * Tách chuỗi ký tự theo pattern và trả về mảng string
     * Functional split
     * 
     * @param {type} param
     * @param {type} pattern
     * @returns {undefined}
     */
    function splitByPattern(param, pattern) {
        if (param.indexOf(pattern) !== -1) {
            if (pattern === ":") {
                var index = param.indexOf(pattern),
                        arr = [];
                arr.push(param.slice(0, index));
                arr.push(param.slice(index + 1));
                return arr;
            } else if (pattern === "in") {
                var index = param.indexOf(" in "),
                        arr = [];
                arr.push(param.slice(0, index));
                arr.push(param.slice(index + 4));
                return arr;
            } else
                return param.split(pattern);
        } else {
            var arr = [];
            arr.push(param);
            return arr;
        }
    }

    /*
     * Thay tên biến trong một ngữ cảnh không biết trước ( for )
     * 
     * 
     * @param {type} element
     * @param {type} tagName
     * @returns {Boolean}
     * 
     */
    function replaceVarContext(element, attr, oldStr, newStr) {
        var command = element.attr(attr);
        var temCmd = "";
        if (oldStr === "\\$current") {
            var reg = new RegExp("((\[\\(|\\[\]*)(" + oldStr + "(\?\=(\\.|\\[|\\(|\\s|\\)|\\]|\$))))", "g");
            temCmd = command.replace(reg, "$2" + newStr);
        } else {
            reg = new RegExp("(\\b" + oldStr + "(\?\=(\\.|\\[|\\(|\\s|\$)))", "g");
            temCmd = command.replace(reg, newStr);
        }
        element.attr(attr, temCmd);
    }

    /**
     * Kiểm tra tên của phần tử
     * 
     * @param {type} element
     * @param {type} tagName : DIV, FORM .v.v
     * @returns {Boolean}
     */
    function isTag(element, tagName) {
        var tag = element.get(0).tagName;
        if (tag === tagName)
            return true;
        else
            return false;
    }

    /**
     * Kiểm tra ngày tháng nhập vào
     * Kiểu format được lấy từ data-format-date
     * 
     * @param {type} element
     * @returns {Boolean}
     */
    function isdate(element) {
        var format = element.attr("data-format-date") || "dd/mm/yyyy";
        var dateStr = element.val( ) || "";
        if (dateStr === "") {
            return false;
        } else {
            var regExp = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
            var dateInfor = dateStr.match(regExp);
            if (dateInfor === null) {
                return false;
            }

            var day, month, year;
            format = $.trim(format).toLowerCase( );
            switch (format) {
                case "mm/dd/yyyy" :
                    day = dateInfor[3];
                    month = dateInfor[1];
                    year = dateInfor[5];
                    break;
                case "dd/mm/yyyy":
                    day = dateInfor[1];
                    month = dateInfor[3];
                    year = dateInfor[5];
                    break;
                default:
                    day = dateInfor[1];
                    month = dateInfor[3];
                    year = dateInfor[5];
                    break;
            }

            var isleap = (year % 4 === 0 && (year % 100 !== 0 || year % 400 === 0));
            if (month < 1 || month > 12) {
                return false;
            } else if (day < 1 || day > 31) {
                return false;
            } else if ((month === 4 || month === 6 || month === 9 || month === 11) && day === 31) {
                return false;
            } else if (month === 2) {
                if (day > 29 || (day === 29 && !isleap))
                {
                    return false;
                }
            }
            return true;
        }
    }

    /**
     * Tạo tên ngẫu nhiên 4 ký tự
     * 
     * @returns {String}
     */
    function randomName() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        for (var i = 0; i < 5; i++)
            text += possible.charAt(Math.floor(Math.random( ) * possible.length));
        return text;
    }

    /**
     * Thực hiện chạy một hàm validate dữ liệu nhập vào
     * Thực hiện kiểm tra hàm trong trường hợp overided thì gọi
     * không thì sẽ gọi hàm buit-in của mình
     * 
     */
    function runValidateFn(viewmodeler, fnName, element) {
        // check for overied validated functions
        if (viewmodeler[fnName] && typeof viewmodeler[fnName] === "function")
        {
            return viewmodeler[fnName](element.val());
        } else if (window[fnName] && typeof window[fnName] === "function") {
            return window[fnName](element.val());
        } else {
            // use buit-in functions
            switch (fnName) {
                case "required":
                    if (!element.val( )) {
                        return false;
                    } else
                        return true;
                case "isdate":
                    if (!element.val( ))
                        return true;
                    return isdate(element);
                case "isnumeric":
                    if (!element.val( ))
                        return true;
                    return $.isNumeric(element.val( ));
                case "isemail":
                    if (!element.val( ))
                        return true;
                    var regExp = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;
                    if (!regExp.test(element.val( ))) {
                        return false;
                    } else {
                        return true;
                    }
                default:
                    return true;
            }
        }
    }

    /**
     * Khoanh vùng phạm vi biến cho các viewmodeler
     * các viewmodeler có thể lồng nhau, 
     * các điều khiển for/if có thể lồng nhau
     * 
     * @param {type} element
     * @returns {undefined}
     */
    function scanViewModelerScope(element) {
        if (element.attr("data-parse") === undefined)
            element.attr("data-parse", "parent");
        if (element.attr("data-viewmodeler") !== undefined) {
            // track nested viewmodeler
            element.find("[data-viewmodeler]").each(function( ) {
                scanViewModelerScope($(this));
                $(this).attr("data-parse", "child");
            });
            var vmName = element.attr("data-viewmodeler");
            // track controls
            element.find("[data-control]").each(function( ) {
                scanControlScope($(this), vmName);
            });
            element.find("[data-in],[data-out],[data-watch],[data-call],[data-check],[data-var],[data-style],[data-attr]").each(function( ) {
                if ($(this).attr("data-scope") === undefined)
                    $(this).attr("data-scope", vmName);
            });
            // update name for radio button group
            element.find("input[type='radio']").each(function( ) {
                var attr = $(this).attr("data-call"), name;
                if (attr !== undefined && attr.indexOf(":")) {
                    name = $.trim(splitByPattern(attr, ":")[1]);
                    if ($(this).attr("name") === undefined)
                        $(this).attr("name", name);
                }
            });
        }
    }

    /**
     * Khoanh vùng phạm vi cho các khai báo điều khiển for/if/switch
     * 
     * @param {type} element
     * @param {type} namespace
     * @returns {unresolved}
     */
    function scanControlScope(element, namespace) {
        if (element.attr("data-scope") !== undefined) {
            return;
        }

        var ctrlName = element.attr("data-control"); // loop parse
        if (ctrlName !== undefined && element.attr("data-parse") === undefined) {
            element.attr("data-parse", "parent");
            element.attr("data-scope", namespace);
            if (ctrlName.match(/^for/g) !== null) {
                element.find("[data-control]").each(function( ) {
                    var ctrlName = $(this).attr("data-control");
                    if (ctrlName !== undefined && $(this).attr("data-parse") === undefined) {
                        if (ctrlName.match(/^for/g) !== null || ctrlName.match(/^if/g) !== null) {
                            $(this).attr("data-parse", "child");
                            $(this).attr("data-scope", namespace);
                        }
                    }
                });
            } else if (ctrlName.match(/^if/g) !== null || ctrlName.match(/^switch/g) !== null) {
                $(this).attr("data-parse", "parent");
                $(this).attr("data-scope", namespace);
            }
            element.find("[data-item],[data-case]").each(function( ) {
                $(this).attr("data-scope", namespace);
            });
        }
    }

    /**
     * Thực hiện việc liên kết giữa trang html và viewmodeler
     * Các thành phần khai báo data-* được biên dịch
     * và khởi tạo giá trị hoặc đăng ký các sự kiện thay đổi
     * 
     * @param {type} vm
     * @param {type} ele
     * @param {type} isBack
     * @returns {undefined}
     */
    var bindData = function(element, isBack) {

        // Parse for conditions         
        element.find("[data-control^='for']").each(function( ) {
            if ($(this).attr("data-parse") !== undefined && $(this).attr("data-parse") !== "child") {
                parseFor($(this));
            }
        });

        // Parse  if/ifnot conditions
        element.find("[data-control^='if']").each(function( ) {
            parseIf($(this), "viewmodeler");
        });

        // parse switch condition
        element.find("[data-control^='switch:']").each(function( ) {
            parseSwitch($(this));
        });
        // bind attribute to view   
        element.find("[data-attr]").each(function( ) {
            bindDataAttr($(this));
        });
        // bind declarative variable to view   
        element.find("[data-var]").each(function( ) {
            bindDataVar($(this));
        });
        // bind style   
        element.find("[data-style]").each(function( ) {
            bindDataStyle($(this));
        });
        // bind view to view
        element.find("[data-watch]").each(function( ) {
            bindDataWatch($(this));
        });
        //bind view to viewmodel
        element.find("[data-in]").each(function( ) {
            var vm = maps[$(this).attr('data-scope')];
            if (vm.tim_isback !== undefined) {
                isBack = vm.tim_isback;
            }
            bindDataIn($(this), isBack, true);
        });
        // bind viewmodel to view
        element.find("[data-out]").each(function( ) {
            bindDataOut($(this), true);
        });
        // bind check
        element.find("[data-check]").each(function( ) {
            bindCheck($(this), true);
        });
    };

    /**
     * Đăng ký xử lý các sự kiện khai báo ở data-call
     * 
     * data-call="event: action"
     *  
     * @param {type} admission : true: register without checking tracked
     * @param {type} element
     * @returns {undefined}
     */
    function registerEvent(element, admission) {
        var viewmodeler;
        // event for data-call
        element.find("[data-call]").each(function( ) {
            var self = $(this);
            if (!admission) {
                if (self.attr("data-track") !== undefined) {
                    return;
                }
            }
            self.attr("data-track", "tracked");
            var callName = self.attr("data-call") || "";
            viewmodeler = maps[self.attr("data-scope")];
            if (callName.indexOf(":") !== -1) {
                var data = splitByPattern(callName, ":"),
                        namespace = viewmodeler.namespace || "tim";
                if (data) {
                    var eventType = $.trim(data[0]),
                            expresion = $.trim(data[1]);
                    // submit process
                    if (eventType === "submit") {
                        if (isTag(self, "FORM")) {
                            self.submit(function(event) {
                                var isSubmit = true;
                                viewmodeler = maps[self.attr("data-scope")];
                                //validate data-check
                                self.find("[data-check]").each(function( ) {
                                    var chkName = $(this).attr("data-check");
                                    var tem = splitByPattern(chkName, ":"),
                                            onName = $.trim(tem[0]),
                                            fns = splitByPattern($.trim(tem[1]), ","),
                                            elementFor = messagesFors[onName],
                                            isShow = false,
                                            showType = $(this).attr("data-show-message"),
                                            isShowAll = true;
                                    // check show type all/order
                                    if (showType !== undefined) {
                                        if (showType === constants.CHECK_ORDER) {
                                            isShowAll = false;
                                        }
                                    }

                                    var count = 0;
                                    for (var i = 0; i < fns.length; i++) {
                                        var isValidated = runValidateFn(viewmodeler, $.trim(fns[i]), $(this));
                                        if (!isValidated) {
                                            isShow = true;
                                            isSubmit = false;
                                            if (elementFor !== undefined) {
                                                if (count < 1) {
                                                    elementFor.find("[data-message-item='" + $.trim(fns[i]) + "']").show( );
                                                    if (!isShowAll)
                                                        count++;
                                                } else {
                                                    elementFor.find("[data-message-item='" + $.trim(fns[i]) + "']").hide( );
                                                }
                                            }
                                        } else
                                        {
                                            if (elementFor !== undefined) {
                                                elementFor.find("[data-message-item='" + $.trim(fns[i]) + "']").hide( );
                                            }
                                        }
                                    }
                                    if (isShow && elementFor !== undefined) {
                                        elementFor.show( );
                                    }
                                });
                                // validate submit function
                                var isValidatedForm = stringToFun(self, expresion) || false;
                                if (typeof isValidatedForm['fun'] === "function") {
                                    var isValidation = isValidatedForm['fun']( );
                                }
                                return isSubmit && isValidation;
                            });
                        }
                    } else {
                        if (eventType === "check")
                            eventType = "change";
                        var handlerFn = function( ) {
                            var retFun = stringToFun(self, expresion);
                            if (typeof retFun['fun'] === "function")
                                retFun['fun']( );
                        };
                        self.off(eventType + "." + namespace).on(eventType + "." + namespace, handlerFn);
                    }
                }
            }
        });
    }

    /***
     * Liên kết một biến khai báo trên view với viewmodeler, nếu viewmodeler không có biến này
     * thì tự động sinh. Biến khai báo giới hạn cho các thẻ SELECT, INPUT kiểu checkbox, radio
     * 
     * SELECT -> giá trị biến là giá trị chọn
     * INPUT[type=checkbox] -> giá trị biến là true/false
     * INPUT[type=radio] -> giá trị biến là giá trị của radio
     * OTHER -> lưu giá trị của thẻ theo data-type
     * 
     * @param {type} element
     * @returns {_L11}
     */
    function bindDataVar(element, admission) {
        if (!admission) {
            if (element.attr("data-track") !== undefined) {
                return;
            }
        }
        element.attr("data-track", "tracked");
        var param = element.attr("data-var");
        if (param === undefined && $.trim(param) === "")
            return this;
        var viewmodeler = maps[element.attr("data-scope")],
                varName = $.trim(param);
        // bind variable to viewmodeler
        if (viewmodeler[varName] === undefined)
            viewmodeler[varName] = "";
        if (isTag(element, "SELECT")) {
            var handlerFn = function( ) {
                var selType = element.attr("data-select-type") || "value", value;
                if (selType === "text") {
                    value = element.find('option:selected').text( );
                } else {
                    value = element.find('option:selected').val( );
                }
                if (value !== undefined)
                    viewmodeler.updateValue(varName, value);
            };
            element.off("change", handlerFn).on("change", handlerFn);
            handlerFn( );
        } else if (isTag(element, "INPUT")) {
            var type = element.attr("type"), value;
            var handlerFn = function( ) {
                if (type && type === "checkbox") {
                    value = element.is(':checked');
                } else if (type && type === "radio") {
                    if (element.is(':checked')) {
                        value = element.val( );
                    }
                }
                if (value !== undefined)
                    viewmodeler.updateValue(varName, value);
            };
            if (type && type === "checkbox" || type === "radio") {
                element.off("change", handlerFn).on("change", handlerFn);
                handlerFn( );
            }

            if (type && type === "text") {
                value = element.val( );
                if (value !== undefined)
                    viewmodeler.updateValue(varName, value);
            }
        } else {
            var dataType = element.attr("data-type") || "text";
            if (dataType === "html" || dataType === "text" || dataType === "val") {
                value = element[dataType]( );
                if (value !== undefined)
                    viewmodeler.updateValue(varName, value);
            }
        }
    }

    /***
     * Lưu trữ dữ liệu vào thuộc tính của thẻ html
     * 
     * @param {type} element
     * @returns {_L11}
     */
    function bindDataAttr(element, admission) {
        if (!admission) {
            if (element.attr("data-track") !== undefined) {
                return;
            }
        }
        element.attr("data-track", "tracked");
        var param = element.attr("data-attr");
        if (param === undefined && $.trim(param) === "")
            return this;
        var viewmodeler = maps[element.attr('data-scope')],
                namespace = viewmodeler.namespace || "tim",
                onAttr,
                onValue,
                arrAttr;
        arrAttr = param.split(",");
        for (var i = 0; i < arrAttr.length; i++) {
            var arrExp = splitByPattern(arrAttr[i], ":");
            if (arrExp.length > 1) {
                onAttr = $.trim(arrExp[0]);
                onValue = $.trim(arrExp[1]);
            } else {
                onAttr = $.trim(arrExp[0]);
                onValue = "";
            }

            if (onAttr === "") {
                return;
            }

            if (onValue === "") {
                var dataType = element.attr("data-type") || "text";
                if (dataType === "html" || dataType === "text" || dataType === "val") {
                    var value = element[dataType]( );
                    if (value !== undefined)
                        element.attr(onAttr, value);
                }
            } else {
                var valueOnValue = stringToFun(element, onValue),
                        namespaces = valueOnValue["namespace"],
                        key;
                var handlerFn = function(event) {
                    valueOnValue = stringToFun(element, onValue);
                    var value = valueOnValue["fun"];
                    var dataType = element.attr("data-type") || "text";
                    if (dataType === "html" || dataType === "text" || dataType === "val") {
                        if (value !== undefined)
                            element.attr(onAttr, value);
                    }
                };
                for (key in namespaces) {
                    var namespace = namespace + "." + key;
                    var link = {
                        event: channels.CHANGE_WATCH_DATA + "." + namespace,
                        obj: $(viewmodeler),
                        handlerFn: handlerFn,
                        objBack: null,
                        eventBack: "",
                        handlerFnBack: null
                    };
                    $(viewmodeler).off(channels.CHANGE_MODEL_DATA + "." + namespace, handlerFn).on(channels.CHANGE_MODEL_DATA + "." + namespace, handlerFn);
                    getBindings(namespace).push(link);
                }
                // loading Action
                handlerFn( );
            }
        }
    }

    /**  
     * Liên kết dữ liệu giữa các khai báo ở view với viewmodeler
     * Khi nhập dữ liệu vào view ( input/ textarea )
     * Giá trị được tự động cập nhật vào view và ngược lại nếu isBack = true
     * Mặc định isBack = true
     * 
     * 
     * @param {type} element
     * @param {type} isBack      
     * @returns {undefined}
     */
    function bindDataIn(element, isBack, admission) {
        if (!admission) {
            if (element.attr("data-track") !== undefined) {
                return;
            }
        }
        element.attr("data-track", "tracked");
        var param = element.attr("data-in");
        if (param === undefined && $.trim(param) === "")
            return this;
        var viewmodeler = maps[element.attr('data-scope')],
                namespace = viewmodeler.namespace || "tim",
                convertIn = element.attr("data-convert-in") || "",
                convertOut = element.attr("data-convert-out") || "",
                dataType = element.attr("data-type") || "val", // val is default
                fun = stringToFun(element, param),
                namespaces = fun["namespace"],
                value = "";
        var handlerFn = function( ) {
            if (dataType === "text" || dataType === "val" || dataType === "html") {
                value = element[dataType]( );
            }

            var convertFn = stringToFun(element, convertIn)["fun"];
            // not update if the same data
            if (fun["fun"] === value) {
                return;
            } else {
                // format firstly at view, then at model
                for (var key in namespaces) {
                    var ns = key;
                    break;
                }
                if (fun) {
                    if (typeof convertFn === "function") {
                        viewmodeler.updateValue(ns, convertFn(value));
                    } else {
                        viewmodeler.updateValue(ns, value);
                    }
                }
            }
        };
        var handlerBackFn = function(event) {
            var convertOutFn = stringToFun(element, convertOut)["fun"];
            updateValue = stringToFun(element, param)["fun"];
            if (dataType === "text" || dataType === "val" || dataType === "html") {
                if (convertOutFn && typeof convertOutFn === "function") {
                    if (typeof convertOutFn(updateValue) !== "object" && typeof convertOutFn(updateValue) !== "function")
                        element[dataType](convertOutFn(updateValue));
                } else {
                    if (typeof updateValue !== "object" && typeof updateValue !== "function")
                        element[dataType](updateValue);
                }
            }
        };
        for (var key in namespaces) {
            namespace = namespace + "." + key;
            var link = {
                event: "change" + "." + namespace,
                obj: element,
                handlerFn: handlerFn,
                objBack: isBack ? $(viewmodeler) : null,
                eventBack: channels.CHANGE_MODEL_DATA + "." + namespace,
                handlerFnBack: handlerBackFn
            };
            element.off("change" + "." + namespace, handlerFn).on("change" + "." + namespace, handlerFn);
            if (isBack) {
                $(viewmodeler).off(channels.CHANGE_MODEL_DATA + "." + namespace, handlerBackFn).on(channels.CHANGE_MODEL_DATA + "." + namespace, handlerBackFn);
            }
            getBindings(namespace).push(link);
        }

        if (isBack) {
            handlerBackFn(null, fun["fun"]);
        }
    }

    /**
     * Theo dõi sự thay đổi của một biến và thực hiện hiển thị hoặc hiện giá trị
     * data-watch="variable: acction"
     * 
     */
    function bindDataWatch(element, admission) {

        if (!admission) {
            if (element.attr("data-track") !== undefined) {
                return;
            }
        }
        element.attr("data-track", "tracked");
        var param = element.attr("data-watch");
        if (param === undefined && $.trim(param) === "") {
            return this;
        }

        var viewmodeler = maps[element.attr('data-scope')],
                namespace = viewmodeler.namespace || "tim",
                onVar,
                onAction;
        var arrExp = splitByPattern(param, ":");
        if (arrExp.length > 1) {
            onVar = $.trim(arrExp[0]);
            onAction = $.trim(arrExp[1]);
        } else {
            onVar = $.trim(arrExp[0]);
            onAction = "show";
        }

        var valueOnVar = stringToFun(element, onVar),
                namespaces = valueOnVar["namespace"],
                key;
        var handlerFn = function(event) {
            valueOnVar = stringToFun(element, onVar);
            if (!valueOnVar && typeof valueOnVar["fun"] !== "boolean") {
                return;
            }
            if (onAction === "show") {
                if (valueOnVar["fun"]) {
                    element.show( );
                } else {
                    element.hide( );
                }
            } else {
                if (valueOnVar["fun"]) {
                    if (onAction !== "show")
                        var fun = stringToFun(element, onAction);
                    if (fun && typeof fun["fun"] === "function") {
                        fun["fun"]( );
                    } else if (fun && typeof fun["fun"] === "object") {
                        fun["fun"];
                    }
                }
            }
        };
        for (key in namespaces) {
            var namespace = namespace + "." + key;
            var link = {
                event: channels.CHANGE_WATCH_DATA + "." + namespace,
                obj: $(viewmodeler),
                handlerFn: handlerFn,
                objBack: null,
                eventBack: "",
                handlerFnBack: null
            };
            $(viewmodeler).off(channels.CHANGE_WATCH_DATA + "." + namespace, handlerFn).on(channels.CHANGE_WATCH_DATA + "." + namespace, handlerFn);
            getBindings(namespace).push(link);
        }

        // loading Action
        handlerFn( );
    }

    /**
     * Theo dõi sự thay đổi của một biến và cập nhật style
     * data-style="boolean var: style"
     * Nếu var = true thì style được cập nhật / false thì style bị loại bỏ
     * 
     * style is class or script { backgroundColor: "blue", color: "#fff"} | {'background-color':'blue'}
     * 
     */
    function bindDataStyle(element, admission) {

        if (!admission) {
            if (element.attr("data-track") !== undefined) {
                return;
            }
        }
        element.attr("data-track", "tracked");
        var param = element.attr("data-style");
        if (param === undefined && $.trim(param) === "") {
            return this;
        }

        var viewmodeler = maps[element.attr('data-scope')],
                namespace = viewmodeler.namespace || "tim",
                onVar,
                onStyle;
        var arrExp = splitByPattern(param, ":");
        if (arrExp.length > 1) {
            onVar = $.trim(arrExp[0]);
            onStyle = $.trim(arrExp[1]);
        } else {
            onStyle = $.trim(arrExp[0]);
        }

        var inlineStyle = onStyle.match(/(\{.*\})/g);
        var handlerFn = function(event) {
            valueOnVar = stringToFun(element, onVar);
            var styleObj = {}, arr = [];
            if (inlineStyle) {
                var strStyle = inlineStyle[0].slice(1, -1),
                        cssArr = splitByPattern(strStyle, ",");
                for (var i = 0; i < cssArr.length; i++) {
                    if (cssArr[i].indexOf(":")) {
                        var pro = cssArr[i].replace(/\'|\"/g, "").split(":");
                        styleObj[$.trim(pro[0])] = pro[1];
                        arr.push($.trim(pro[0]));
                    }
                }
            }
            if (valueOnVar["fun"] === true) {
                if (inlineStyle !== null) {
                    element.css(styleObj);
                } else {
                    element.addClass(onStyle);
                }
            } else if (valueOnVar["fun"] === false) {
                if (inlineStyle !== null) {
                    var styleStr = element.attr("style"), stillStyle = "";
                    if (styleStr !== undefined) {
                        var styleArr = styleStr.split(";");
                        for (var j = 0; j < arr.length; j++) {
                            for (var k = 0; k < styleArr.length; k++) {
                                if (styleArr[k] !== "" && styleArr[k].replace("-", "").search(arr[j].toLowerCase( )) !== -1) {
                                    styleArr.splice(k, 1);
                                    k--;
                                }
                            }
                        }
                        for (j = 0; j < styleArr.length; j++) {
                            stillStyle += styleArr[j];
                        }
                    } else {
                        stillStyle = "";
                    }

                    element.attr("style", stillStyle);
                } else {
                    element.removeClass(onStyle);
                }
            }
        };
        if (onVar !== undefined) {
            var valueOnVar = stringToFun(element, onVar),
                    namespaces = valueOnVar["namespace"],
                    key;
            for (key in namespaces) {
                var namespace = namespace + "." + key;
                var link = {
                    event: channels.CHANGE_MODEL_DATA + "." + namespace,
                    obj: $(viewmodeler),
                    handlerFn: handlerFn,
                    objBack: null,
                    eventBack: "",
                    handlerFnBack: null
                };
                $(viewmodeler).off(channels.CHANGE_MODEL_DATA + "." + namespace, handlerFn).on(channels.CHANGE_MODEL_DATA + "." + namespace, handlerFn);
                getBindings(namespace).push(link);
            }
        } else {

        }
        handlerFn( );
    }

    /**      
     * 
     * Liên kết dữ liệu từ model ra view, 
     * dữ liệu được cập nhật vào view khi model có thay đổi
     * 
     * Expression là thuộc tính của đối tượng trong viewmodeler được liên kết
     * 
     * 
     * @param {type} element
     * @returns {undefined}      
     * */
    function bindDataOut(element, admission) {

        if (!admission) {
            if (element.attr("data-track") !== undefined) {
                return;
            }
        }
        element.attr("data-track", "tracked");
        var param = element.attr("data-out");
        if (param === undefined && $.trim(param) === "")
            return this;
        var fun = stringToFun(element, param);
        var viewmodeler = maps[element.attr('data-scope')],
                namespace = viewmodeler.namespace || "tim",
                convertOut = element.attr("data-convert-out") || "",
                defaultValue = element.attr("data-select-value") || "",
                selType = element.attr("data-select-type") || "value", // value is default
                dataType = element.attr("data-type") || "text", // text is default
                varName = element.attr("data-var");
        // auto update data to view when model changed
        var handlerFn = function(event) {

            var value = stringToFun(element, param)["fun"];
            if (isTag(element, "SELECT")) {
                var defaultVal = stringToFun(element, defaultValue)["fun"];

                if (varName !== undefined && viewmodeler[varName] === undefined) {
                    viewmodeler[varName] = defaultVal;
                }
                if ($.isArray(value)) {
                    element.empty( );
                    for (var i = 0; i < value.length; i++) {
                        var cloneEle = $("<option>" + value[i] + "</option>");
                        if (selType === "text" && defaultVal === value[i]) {
                            cloneEle.attr("selected", "selected");
                        } else if (selType === "value" && defaultVal === "" + i) {
                            cloneEle.attr("selected", "selected");
                        }
                        element.append(cloneEle);
                    }
                } else if (typeof value === "object") {
                    element.empty( );
                    for (var key in value) {
                        if (typeof value[key] !== "object") {
                            var cloneEle = $("<option value='" + key + "'>" + value[key] + "</option>");
                            if (selType === "text" && defaultVal === value[key]) {
                                cloneEle.attr("selected", "selected");
                            } else if (selType === "value" && defaultVal === key) {
                                cloneEle.attr("selected", "selected");
                            }
                            element.append(cloneEle);
                        }
                    }
                }
            } else {
                var convertOutFn = stringToFun(element, convertOut)["fun"];
                if (typeof convertOutFn === "function") {
                    if (value)
                        value = convertOutFn(value);
                }
                if (dataType === "text" || dataType === "val" || dataType === "html") {
                    if (typeof value !== "object" && typeof value !== "function")
                        element[dataType](value);
                } else if (dataType === "object" && typeof value === "object") {
                    element.find("[data-item]").each(function( ) {
                        var itemName = $(this).attr("data-item"),
                                alias = $.trim(param);
                        var attrNames = expToNameArray(itemName);
                        $(this).attr("data-scope", element.attr('data-scope'));

                        if (attrNames !== null) {
                            for (var j = 0; j < attrNames.length; j++) {
                                if (value[attrNames[j]] !== undefined) {
                                    var regExp = new RegExp("(" + attrNames[j] + ")", "g");
                                    itemName = itemName.replace(regExp, alias + ".$1");
                                }
                            }
                            $(this).attr("data-item", itemName);
                        }

                        replaceVarContext($(this), "data-item", "\\$current", alias);
                        updateItem($(this), true);
                    });
                }
            }
        };
        if (fun) {
            var namespaces = fun['namespace'],
                    key;
            for (key in namespaces) {
                var namespace = namespace + "." + key;
                var link = {
                    event: channels.CHANGE_MODEL_DATA + "." + namespace,
                    obj: element,
                    handlerFn: handlerFn,
                    objBack: null,
                    eventBack: "",
                    handlerFnBack: null
                };
                $(viewmodeler).off(channels.CHANGE_MODEL_DATA + "." + namespace, handlerFn).on(channels.CHANGE_MODEL_DATA + "." + namespace, handlerFn);
                getBindings(namespace).push(link);
            }

        }

        // firstly binding data
        handlerFn(null, fun['fun']);
    }

    /**
     * Bỏ liên kết giữa view và viewmodeler
     * 
     * @param {type} ele
     * @param {type} isChild
     * @returns {undefined}
     */
    var unBindData = function(element, isChild) {
        function doUnbind(ele) {
            var ns = element.data("ns");
            if (ns && ns !== "") {
                var links = window.tim.getBindings(ns);
                for (var i = links.length - 1; i > 0; i--) {
                    var link = links[i],
                            event = link.event,
                            obj = link.obj,
                            handlerFn = link.handlerFn,
                            objBack = link.objBack,
                            eventBack = link.eventBack,
                            handlerFnBack = link.handlerFnBack;
                    obj.off(event, handlerFn);
                    if (objBack) {
                        objBack.off(eventBack, handlerFnBack);
                    }
                }
            }
        }
        doUnbind(element); // its children
        if (isChild) {
            // unbind view to viewmodel
            element.find("[data-in]").each(function( ) {
                doUnbind($(this));
            }); // unbind viewmodel to view
            element.find("[data-out]").each(function( ) {
                doUnbind($(this));
            }); // unbind view to view
            element.find("[data-watch]").each(function( ) {
                doUnbind($(this));
            });
        }
    };

    /**   
     * Lưu trữ phần tử chứa thông báo 
     * 
     * 
     * @returns {undefined}
     */
    function parseCheckMessage( ) {
        $(document).find("[data-message-for]").each(function( ) {
            var attr = $(this).attr("data-message-for");
            if (attr !== undefined) {
                var arrName = splitByPattern(attr, ",");
                for (var i = 0; i < arrName.length; i++) {
                    messagesFors[$.trim(arrName[i])] = $(this);
                }
            }
            $(this).hide( );
        });
    }

    /**
     * Liên kết các khai báo kiểm tra dữ liệu nhập
     * 
     * Các hàm validate được liệt kê cách nhau dấu phảy trong data-check
     * 
     * Các tin thông báo được viết theo cú pháp:
     * data-[validateFunction]-message="Your message"
     * 
     * Tin được thông báo theo thứ tự check hay hiển thị tất một lượt
     * data-show-message="order/all"
     * 
     * Dữ liệu được format theo:
     * data-format-date="dd/mm/yyyy"
     *  
     * <input data-in="birthday"
     *        data-check="birthday: required, isdate, validateDate"
     *        data-validateDate-message = "Birthday must be smaller than now"
     *        data-required-message = "It must be not empty!"
     *        data-isdate-message = "It must be date"
     *        data-show-message="order"
     *        data-format-date="dd/mm/yyyy"/>
     *
     * @param {type} element
     * @returns {undefined}
     */
    function bindCheck(element, admission) {

        if (!admission) {
            if (element.attr("data-track") !== undefined) {
                return;
            }
        }
        element.attr("data-track", "tracked");
        var param = element.attr("data-check"), viewmodeler,
                onName,
                onValues = "",
                preFix = "data-",
                postFix = "-message",
                map = {},
                elementFor;
        // map validation function to validation message
        var mapping = function(arrVal) {
            var message, name;
            if (arrVal) {
                for (var i = 0; i < arrVal.length; i++) {
                    name = $.trim(arrVal[i]);
                    message = $.trim(element.attr(preFix + name + postFix));
                    if (message !== undefined) {
                        map[name] = message;
                    }
                }
            }
        };
        if (param !== undefined) {
            viewmodeler = maps[element.attr('data-scope')];
            var tem = splitByPattern(param, ":"),
                    onName = $.trim(tem[0]),
                    onValues = $.trim(tem[1]),
                    elementFor = messagesFors[onName];
            if (onValues !== undefined && elementFor !== undefined) {
                var arrVal = splitByPattern(onValues, ",");
                validationFns[onName] = arrVal;
                //update map
                mapping(arrVal);
                var template1 = elementFor.find("ul > li:first"),
                        template2 = elementFor.find("[data-message-item]");
                if (map) {
                    if (template1.length !== 0) {
                        for (var key in map) {
                            var cloneElement = template1.clone(true);
                            cloneElement.text(map[key]);
                            cloneElement.attr("data-message-item", key);
                            elementFor.find("ul").append(cloneElement);
                        }
                        template1.hide( );
                    } else if (template2.length !== 0) {
                        elementFor.find("[data-message-item]").each(function( ) {
                            var validateFn = $(this).attr("data-message-item");
                            if (validateFn !== undefined) {
                                var mess = map[$.trim(validateFn)];
                                if (mess !== undefined) {
                                    $(this).text(mess);
                                }
                            }
                        });
                        template2.hide( );
                    } else {
                        elementFor.append("<ul></ul>");
                        for (var key in map) {
                            var cloneElement = $("<li></li>");
                            cloneElement.text(map[key]);
                            cloneElement.attr("data-message-item", key);
                            elementFor.find("ul").append(cloneElement);
                        }
                    }
                }
            }
            // hide anounment when user retype
            element.on("keydown", function( ) {
                if (element.val( )) {
                    if (elementFor !== undefined)
                        elementFor.hide( );
                }
            });
        }
    }

    /**      
     * Parse các khai báo điều khiển for: data-control="for:"
     * Các điều khiển if cũng được xử lý nếu trong scope của for
     * các for trong viewmodeler khác cũng được xử lý
     * 
     * @param {type} element
     * @returns {_L9}
     */
    function parseFor(element, admission) {
        if (!admission) {
            if (element.attr("data-track") !== undefined) {
                return;
            }
        }
        var ctrlName = element.attr("data-control");
        var ctrlArr = splitByPattern(ctrlName, ":"),
                parentScope = $.trim(element.attr("data-scope")),
                viewmodeler = maps[parentScope],
                namespace = viewmodeler.namespace || "tim",
                isAlias = true;
        // with alias
        if (ctrlArr.length > 1) {
            var forNames = splitByPattern(ctrlArr[1], "in");
            if (forNames.length > 1) { //  for with alias
                var alias = $.trim(forNames[0]),
                        proName = $.trim(forNames[1]);
            } else {
                var alias = randomName( ),
                        proName = $.trim(forNames[0]);
                isAlias = false;
            }
            element.attr("data-track", proName);

            var fun = stringToFun(element, proName);

            element.find("[data-control^='if']").each(function() {
                $(this).attr("data-parent", ctrlName);
            });

            var handlerUpdateRowFn = function(event, index) {
                var arrObj = stringToFun(element, proName)['fun'];
                if (index !== null && index !== undefined && index >= 0 && index < arrObj.length) {

                    if (element.attr("data-info") !== undefined) {
                        var infoName = element.attr("data-info"),
                                infoObject = viewmodeler["scopes"][infoName];
                        var wrap = infoObject["htmlTpl"];
                    }

                    var copy = wrap.clone(true),
                            curObj = arrObj[index],
                            curAlias = randomName( );

                    if (typeof curObj !== "object") {
                        viewmodeler["scopes"][alias] = {};
                        window.$value = viewmodeler["scopes"][alias].$value = curObj;
                    } else {
                        viewmodeler["scopes"][alias] = curObj;
                    }

                    window.$index = viewmodeler["scopes"][alias].$index = index;

                    viewmodeler["scopes"][curAlias] = viewmodeler["scopes"][alias];

                    updateAttrContext(copy, parentScope, proName, alias, curAlias, isAlias, curObj, ctrlName);

                    copy.find("[data-row]").each(function( ) {
                        if ($(this).attr("data-track") === undefined || $(this).attr("data-track") === proName) {
                            $(this).attr("data-track", proName);
                            $(this).attr("data-row", proName + index);
                        }
                    });

                    // update clone after fill data
                    element.find("[data-row='" + proName + index + "']").html(copy.children("[data-row]").html());

                    wrap.remove( );

                    reBinding(element);
                }
            };

            var handlerAddRowFn = function(event, obj, flag) {
                var arrObj = stringToFun(element, proName)['fun'];

                if (element.attr("data-info") !== undefined) {
                    var infoName = element.attr("data-info"),
                            infoObject = viewmodeler["scopes"][infoName];
                    var wrap = infoObject["htmlTpl"];
                }

                var index = arrObj.length,
                        copy = wrap.clone(true),
                        curObj = obj,
                        curAlias = randomName( );
                if (flag === undefined || flag === null || !flag) {
                    arrObj[index] = obj;
                }

                if (typeof curObj !== "object") {
                    viewmodeler["scopes"][alias] = {};
                    window.$value = viewmodeler["scopes"][alias].$value = curObj;
                } else {
                    viewmodeler["scopes"][alias] = curObj;
                }

                window.$index = viewmodeler["scopes"][alias].$index = index;

                viewmodeler["scopes"][curAlias] = viewmodeler["scopes"][alias];

                updateAttrContext(copy, parentScope, proName, alias, curAlias, isAlias, curObj, ctrlName);

                copy.find("[data-row]").each(function( ) {
                    if ($(this).attr("data-track") === undefined || $(this).attr("data-track") === proName) {
                        $(this).attr("data-track", proName);
                        $(this).attr("data-row", proName + index);
                    }
                });

                // update clone after fill data
                element.append(copy.html( ));

                wrap.remove( );

                reBinding(element);
            };

            var handlerRemoveRowFn = function(event, index) {
                if (index !== null && index !== undefined) {
                    var arrObj = stringToFun(element, proName)['fun'];
                    if ($.isArray(arrObj) && arrObj.length > 0) {
                        arrObj.splice(index, 1);
                    }
                    element.find("[data-row='" + proName + index + "']").each(function( ) {
                        $(this).remove();
                    });
                    var index = 0;
                    element.find("[data-row^='" + proName + "']").each(function( ) {
                        $(this).attr("data-row", proName + index);
                        index++;
                    });
                }
            };

            var handlerFn = function(event, value) {

                //store information of element
                if (element.attr("data-info") !== undefined) {
                    var infoName = element.attr("data-info"),
                            infoObject = viewmodeler["scopes"][infoName],
                            arrObj;
                    var wrap = infoObject["htmlTpl"];
                    if (value !== undefined && value !== null)
                    {
                        arrObj = value;
                    } else {
                        arrObj = infoObject["objectVm"];
                    }
                } else {
                    var arrObj = stringToFun(element, proName)['fun'];
                    wrap = $("<div></div>");
                    wrap.html(element.html( ));
                    var infoName = randomName();
                    element.attr("data-info", infoName);
                    viewmodeler["scopes"][infoName] = {
                        htmlTpl: wrap,
                        objectVm: arrObj
                    };
                }
                element.html(wrap);

                if ($.isArray(arrObj) && arrObj.length > 0) {
                    for (var i = 0; i < arrObj.length; i++) {
                        // make a copy from origin pattern
                        var copy = wrap.clone(true),
                                curObj = arrObj[i],
                                curAlias = randomName( );
                        // store variable scope, it will be overided if has the same alias                                
                        if (typeof curObj !== "object") {
                            viewmodeler["scopes"][alias] = {};
                            window.$value = viewmodeler["scopes"][alias].$value = curObj;
                        } else {
                            viewmodeler["scopes"][alias] = curObj;
                        }

                        window.$index = viewmodeler["scopes"][alias].$index = i;

                        viewmodeler["scopes"][curAlias] = viewmodeler["scopes"][alias];

                        updateAttrContext(copy, parentScope, proName, alias, curAlias, isAlias, curObj, ctrlName);

                        copy.find("[data-row]").each(function( ) {
                            if ($(this).attr("data-track") === undefined || $(this).attr("data-track") === proName) {
                                $(this).attr("data-track", proName);
                                $(this).attr("data-row", proName + i);
                            }
                        });
                        // update clone after fill data
                        element.append(copy.html( ));
                    }

                    wrap.remove();

                    //rebinding event
                    reBinding(element);
                } else {
                    if (element.attr("data-info") !== undefined) {
                        var infoName = element.attr("data-info"),
                                infoObject = viewmodeler["scopes"][infoName],
                                arrObj;
                        var wrap = infoObject["htmlTpl"];
                        if (value !== undefined && value !== null)
                        {
                            arrObj = value;
                        } else {
                            arrObj = infoObject["objectVm"];
                        }
                    } else {
                        var arrObj = stringToFun(element, proName)['fun'];
                        wrap = $("<div></div>");
                        wrap.html(element.html( ));
                        var infoName = randomName();
                        element.attr("data-info", infoName);
                        viewmodeler["scopes"][infoName] = {
                            htmlTpl: wrap,
                            objectVm: arrObj
                        };
                    }

                    wrap.remove();
                }
            };

            var namespaces = fun['namespace'],
                    key;
            for (key in namespaces) {
                var namespace = namespace + "." + key;
                var link = {
                    event: channels.CHANGE_MODEL_DATA + "." + namespace,
                    obj: $(viewmodeler),
                    handlerFn: handlerFn,
                    objBack: null,
                    eventBack: "",
                    handlerFnBack: null
                };

                $(viewmodeler).off(channels.CHANGE_MODEL_DATA + "." + namespace, handlerFn).on(channels.CHANGE_MODEL_DATA + "." + namespace, handlerFn);
                $(viewmodeler).off(channels.UPDATE_MODEL_ROW_DATA + "." + namespace, handlerUpdateRowFn).on(channels.UPDATE_MODEL_ROW_DATA + "." + namespace, handlerUpdateRowFn);
                $(viewmodeler).off(channels.ADD_MODEL_ROW_DATA + "." + namespace, handlerAddRowFn).on(channels.ADD_MODEL_ROW_DATA + "." + namespace, handlerAddRowFn);
                $(viewmodeler).off(channels.REMOVE_MODEL_ROW_DATA + "." + namespace, handlerRemoveRowFn).on(channels.REMOVE_MODEL_ROW_DATA + "." + namespace, handlerRemoveRowFn);

                getBindings(namespace).push(link);
            }

            handlerFn();
        } else {
            // error syntax "for:"
        }
    }

    /**
     * Cập nhật giá trị cho một phần tử  
     * 
     * @param {type} itemName
     * @param {type} element
     * @param {type} alias
     * @returns {unresolved}
     */
    function updateItem(element, admission) {
        if (!admission) {
            if (element.attr("data-track") !== undefined) {
                return;
            }
        }
        element.attr("data-track", "tracked");
        // is parse
        var dataType = element.attr("data-type") || "text",
                expression = element.attr("data-item"),
                convertOut = element.attr("data-convert-out") || "";
        var value = stringToFun(element, expression)['fun'];
        var convertOutFn = stringToFun(element, convertOut)["fun"];
        if (typeof convertOutFn === "function") {
            if (value)
                value = convertOutFn(value);
        }
        if (dataType === "text" || dataType === "val" || dataType === "html") {
            if (typeof value !== "object" && typeof value !== "function")
                element[dataType](value);
        }
    }

    /**
     * Parse cho các khai báo data-control="switch:"
     * 
     * @param {type} element
     * @returns {undefined}
     */
    function parseSwitch(element, admission) {
        if (!admission) {
            if (element.attr("data-track") !== undefined) {
                return;
            }
        }
        element.attr("data-track", "tracked");
        var param = element.attr("data-control"),
                viewmodeler,
                onName;
        if (param === undefined && $.trim(param) === "")
            return this;
        var attrArr = splitByPattern(param, ":");
        if (attrArr.length > 1) {
            onName = $.trim(attrArr[1]);
            viewmodeler = maps[element.attr("data-scope")];
            element.find("[data-case]").hide( );
            var handlerFn = function(event) {
                var value = stringToFun(element, onName)["fun"];
                element.find("[data-case]").each(function( ) {
                    var caseVal = $(this).attr("data-case");
                    if (caseVal !== undefined && caseVal === value) {
                        $(this).show( );
                        element.find("[data-default]").hide( );
                    } else {
                        $(this).hide( );
                    }
                });
            };
            var namespace = viewmodeler.namespace + "." + onName;
            $(viewmodeler).off(channels.CHANGE_MODEL_DATA + "." + namespace, handlerFn).on(channels.CHANGE_MODEL_DATA + "." + namespace, handlerFn);
        }
    }

    /**
     * Parse cho các khai báo điều khiển data-control="if/ifnot:" 
     * 
     * @param {type} element
     * @param {type} namespace
     * @returns {unresolved}
     */
    function parseIf(element, namespace, admission) {
        if (!admission) {
            if (element.attr("data-track") !== undefined) {
                return;
            }
        }
        element.attr("data-track", "tracked");
        var ctrlName = element.attr("data-control") || "";
        var ctrl = splitByPattern(ctrlName, ":");
        if ($.isArray(ctrl) && ctrl.length > 1) {
            var ctrlType = $.trim(ctrl[0]),
                    ctrlExp = $.trim(ctrl[1]);
            var fun = stringToFun(element, ctrlExp);
            if (ctrlType === "if") {
                if (fun["fun"] === undefined || fun["fun"] === null || !fun["fun"]) {
                    element.remove( );
                } else if (namespace !== null && namespace !== undefined) {
                    element.attr("data-track", namespace);
                }
            } else if (ctrlType === "if!") {
                if (fun["fun"] === undefined || fun["fun"] === null || fun["fun"]) {
                    element.remove( );
                } else if (namespace !== null && namespace !== undefined) {
                    element.attr("data-track", namespace);
                }
            }
        }
    }

    /**
     * Khởi tạo dữ liệu cho trang
     * 
     * @param {type} jsSrc
     * @returns {undefined}
     */
    function load(jsSrc) {
        $("head").append("<script type='text/javascript' src='" + jsSrc + "'></script>");
        //TODO: do smth here to init document
    }

    /**
     * Cập nhật dữ liệu vào viewmodeler
     * 
     * @param {type} mObj
     * @param {type} data      
     * @returns {undefined}
     */
    function updateData(mObj, data) {
        if (data) {
            var key;
            for (key in data) {
                if (mObj[key]) {
                    mObj[key] = data[key];
                }
            }
        }
    }

    /**
     * Khởi tạo trang
     * 
     * @param {type} params
     * @returns {_L9}
     */
    var initPage = function(params) {
        var now = new Date( );
        var settings = $.extend({
            bindings: [],
            pipes: {
                // defined default channels
            },
            messages: {
                // defined default messages
            },
            labels: {
                // defined default labels
            }, jsfiles: [
                // defined default jsfiles
            ],
            cssfiles: [
                // defined default cssfiles
            ],
            debug: false
        }, params);
        pageParams = settings;
        // load source
        for (var s = 0; s < settings.jsfiles.length; s++) {
            var src = settings.jsfiles[s].src + settings.jsfiles[s].name;
            if (src && src !== "") {
                load(src);
            }
        }
        var map = [];
        // binding data for each viewmodeler
        var len = settings.bindings.length;
        if (len > 0) {
            // for complete updating data to viewmodeler
            for (var i = 0; i < len; i++) {
                var link = {};
                link["viewer"] = settings.bindings[i].viewer;
                link["viewmodeler"] = window[settings.bindings[i].viewmodeler];
                link["linkBack"] = settings.bindings[i].linkback;
                link["data"] = settings.bindings[i].data;
                link["viewmodeler"]["namespace"] = link["viewer"];
                // update data from server
                updateData(link["viewmodeler"], link["data"]);
                map.push(link);
                maps[link["viewer"]] = link["viewmodeler"];
                link["viewmodeler"].scopes = {};
                link["viewmodeler"].isParsed = false;
                link["viewmodeler"].updateValue = updateValue;
                link["viewmodeler"].updateRowValue = updateRowValue;
                link["viewmodeler"].addRowValue = addRowValue;
                link["viewmodeler"].removeRowValue = removeRowValue;
                link["viewmodeler"].messages = pageParams.messages;
            }

            // Scan elements those show announment message
            parseCheckMessage( );
            // binding viewer
            for (var j = 0; j < len; j++) {
                var link = map[j],
                        viewmodeler = link["viewmodeler"],
                        viewer = $("[data-viewmodeler='" + link["viewer"] + "']"),
                        linkBack = link["linkBack"];
                scanViewModelerScope(viewer);
                if (viewer.attr("data-parse") === "parent") {
                    viewmodeler.tim_isback = linkBack;
                    bindData(viewer, linkBack);
                    registerEvent(viewer);
                }
            }
            // init context variables
            window.$index = 0;
            window.$value = null;
            window.$current = null;
        }
        return this;
    };

    function refreshView(element) {
        if (typeof element === "object") {
            bindData(element, true);
            registerEvent(element);
        }
    }

    /**
     * Một module có thể đăng ký một kênh trao đổi 
     * thông tin với một module khác
     * 
     * @param {type} namespace
     * @param {type} callbackFn
     * @returns {undefined}
     */
    function registerChannel(namespace, callbackFn) {
        var self = this,
                event = $.trim(namespace), handlerFn = function(event, value) {
            callbackFn(value);
        };
        $(self).off(event, handlerFn).on(event, handlerFn);
    }

    /**
     * Gửi một tin vào một kênh mà các module khác đăng ký nhận
     * 
     * @param {type} namespace
     * @param {type} value
     * @returns {undefined}      */
    function sendToChannel(namespace, value) {
        var self = this, event = $.trim(namespace);
        // broadcast message to all object that registered event chanel
        $(self).triggerHandler(event, value);
    }

    /**
     * Công khai đối tượng và các hàm
     */
    window.tim = new function( ) {
        // public methods
        return {
            initPage: initPage,
            refreshView: refreshView,
            registerEvent: registerEvent,
            registerChannel: registerChannel,
            sendToChannel: sendToChannel
        };
    }( );
}(window, document, window.jQuery));
