/**
 * 
 * For Suggestion Dialog that you can bind to an input element
 * 
 * @html: components/dialog-popup.phtml
 * @type type
 */
var popupVM = {
    namespace: "popupVM",
    items: [],
    mapItems: [],
    options: [],
    isLoading: false,
    selIndex: -1,
    init: function() {

    },
    registerChanel: function() {

    },
    /**
     * Bind element to suggestion popup
     * 
     * @param {type} settings {data, attr, callbackFn, selObj, url, param}
     * @returns {undefined}
     */
    bind: function(id, settings) {
        this.options[id] = {};
        this.options[id] = $.extend({}, settings);
        if (this.options[id].attr === null) {
            return;
        }

        // Total left or right padding of input 
        if (popupVM.options[id].paddingLeft !== undefined) {
            var paddingLeft = popupVM.options[id].paddingLeft;
        } else {
            paddingLeft = 0;
        }

        if (popupVM.options[id].paddingTop !== undefined) {
            var paddingTop = popupVM.options[id].paddingTop;
        } else {
            paddingTop = 0;
        }



        $("#" + id).focus(function() {
            // Items is array of { id, key, value }
            popupVM.updateItems(id, popupVM.options[id].data, popupVM.options[id].attr);

            // updating view
            popupVM.updateValue("mapItems", popupVM.mapItems);

            $("#dialog_popup .popup-item").off().on("click", function() {
                popupVM.selIndex = $(this).attr("data-index");
                var value = popupVM.mapItems[popupVM.selIndex].value;
                popupVM.selectItem(id, value);
            });

            var top = $("#" + id).offset().top + $("#" + id).height() + 2 + paddingTop;
            var left = $("#" + id).offset().left;
            $("#dialog_popup").offset({
                top: top,
                left: left
            });
        }).blur(function() {

        }).keyup(function() {
            var ids = 0;
            return function(e) {
                e = e || window.event;
                var value = e.target.value;
                if (value !== "" && value !== undefined) {
                    $("#dialog_popup").show();

                    var top = $("#" + id).offset().top + $("#" + id).height() + 2 + paddingTop;
                    var left = $("#" + id).offset().left;
                    $("#dialog_popup").offset({
                        top: top,
                        left: left
                    });
                    if ($("#" + id).width() > 200) {
                        $("#dialog_popup").width($("#" + id).width() + paddingLeft);
                    }
                } else {
                    $("#dialog_popup").hide();
                }

                if (e.keyCode == 13 && !popupVM.isLoading) { // ENTER
                    popupVM.selectItem(id, value);
                } else if (e.keyCode == 38) { // UP
                    if (popupVM.selIndex > 0) {
                        popupVM.selIndex--;
                        popupVM.scrollToView(id);
                    } else {
                        popupVM.selIndex = $(".popup-item[data-selected]").length - 1;
                        popupVM.scrollToView(id);
                    }
                } else if (e.keyCode == 40) { // DOWN
                    if (popupVM.selIndex < $(".popup-item[data-selected]").length - 1) {
                        popupVM.selIndex++;
                        popupVM.scrollToView(id);
                    } else {
                        popupVM.selIndex = 0;
                        popupVM.scrollToView(id);
                    }
                } else {
                    clearTimeout(ids);
                    ids = setTimeout(popupVM.updateSearchItems(id, e.target.value), (popupVM.options[id].timeOut || 500));
                }
            };
        }());
    },
    selectItem: function(id, value) {
        var obj = {
            id: "",
            key: "",
            value: ""
        };

        if (popupVM.selIndex >= 0) {
            obj = popupVM.mapItems[popupVM.selIndex];
        } else {
            if (popupVM.options[id].selectedKey) {
                obj.key = value;
            } else {
                obj.value = value;
            }
        }

        if (popupVM.options[id].callbackFn !== undefined && typeof popupVM.options[id].callbackFn === "function") {
            popupVM.options[id].callbackFn(obj);
            $("#dialog_popup").hide();
        }

        $("#dialog_popup").hide();
    },
    /**
     * Convert real objects to mapped objects
     * 
     * @param {type} objLst
     * @param {type} proArr
     * @returns {undefined}
     */
    updateItems: function(id, objLst, proArr) {
//        this.items[id].length = 0; // clear suggestion list
        if (popupVM.items[id] === undefined) {
            popupVM.items[id] = [];
        }
        if (objLst !== null) {
            for (var i = 0; i < objLst.length; i++) {
                var item = {
                    id: "",
                    key: "",
                    value: ""
                };
                if (objLst[i][proArr.id] != undefined) {
                    item.id = objLst[i][proArr.id];
                }

                if (objLst[i][proArr.key] != undefined) {
                    item.key = objLst[i][proArr.key];
                }

                if (objLst[i][proArr.value] != undefined) {
                    item.value = objLst[i][proArr.value];
                }

                if (!popupVM.isLoaded(id, item)) {
                    popupVM.items[id].push(item); // append
                }
            }
            popupVM.mapItems = popupVM.items[id];
        }
    },
    updateSearchItems: function(id, value) {
        var count = 0;
        $(".popup-not-found").hide();
        for (var i = 0; i < popupVM.mapItems.length; i++) {
            var item = popupVM.mapItems[i];
            var show = (item.key.toLowerCase().search(value.toLowerCase()) > -1) ||
                    (item.value.toLowerCase().search(value.toLowerCase()) > -1);
//            console.log("search:===" + value + " count: " + count + " show+"+ show);
            if (count < 4 && show) {
                $("#dialog_popup .popup-items").find("[data-index='" + item.$index + "']").toggle(show);
                $("#dialog_popup .popup-items").find("[data-index='" + item.$index + "']").attr("data-selected", "1");
                count++;
            } else {
                $("#dialog_popup .popup-items").find("[data-index='" + item.$index + "']").hide();
                $("#dialog_popup .popup-items").find("[data-index='" + item.$index + "']").removeAttr("data-selected");
            }
        }
        if ($("#dialog_popup").find(".popup-item:visible").length <= 0) {
//            console.log("load data:===" + value + " count: " + count);
            popupVM.loadData(id, value);
        }
    },
    loadData: function(id, value) {
        if (value == "") {
            return;
        }
        var data = {};
        data[popupVM.options[id].param] = value;
        $(".popup-loading").show();
        $(".popup-not-found").hide();

        popupVM.isLoading = true;
        if (typeof popupVM.request !== undefined && popupVM.request) {
            popupVM.request.abort();
            delete popupVM.request;
        }

        popupVM.request = $.ajax({
            url: popupVM.options[id].url,
            type: "get",
            dataType: "json",
            data: data,
            error: function() {
                popupVM.isLoading = false;
                $(".popup-loading").hide();
            },
            beforeSend: function() {
                // do smth before sending
            },
            complete: function() {
                // do smth when complete action
            },
            success: function(data) {
                $(".popup-loading").hide();
                if (data.items.length > 0) {
                    popupVM.updateItems(id, data.items, popupVM.options[id].attr);
                    popupVM.updateValue("mapItems", popupVM.mapItems);
//                    // binding events and run call back
                    $("#dialog_popup .popup-item").on("click", function() {
                        popupVM.selIndex = $(this).attr("data-index");
                        var value = popupVM.mapItems[popupVM.selIndex].value;
                        popupVM.selectItem(id, value);
                    });
                    popupVM.updateSearchItems(id, value);
                    popupVM.isLoading = false;
                    popupVM.selIndex = -1;
                    $(".popup-not-found").hide();
                } else {
                    $(".popup-not-found").show();
                }
            }
        });
    },
    scrollToView: function(id) {
        $(".popup-item").removeClass("popup-item--active");
        $(".popup-item[data-selected]").each(function(index) {
            if (index == popupVM.selIndex) {
                if (popupVM.options[id].selectedKey) {
                    $("#" + id).val($(this).find(".item-key").text());
                } else {
                    $("#" + id).val($(this).find(".item-value").text());
                }

                $(this).addClass("popup-item--active");
            }
        });
    },
    isLoaded: function(id, obj) {
        var retVal = false;
        if (popupVM.items[id].length == 0) {
            return retVal;
        }
        for (var i = 0; i < popupVM.items[id].length; i++) {
            var obj1 = popupVM.items[id][i];
            if (obj.id == obj1.id || (obj.key == obj1.key && obj.value == obj1.value)) {
                retVal = true;
                break;
            }
        }
        return retVal;
    }
};
$(document).ready(function() {
    popupVM.init();
});

