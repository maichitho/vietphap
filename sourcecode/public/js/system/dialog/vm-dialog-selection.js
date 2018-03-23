/**
 * 
 * For Selection Dialog that you can bind any array of object and choose some
 * 
 * @html: components/dialog-selection.phtml
 * @type type
 */
var selectionDialogVM = {
    namespace: "selectionDialogVM",
    items: [],
    mapItems: [],
    options: {},
    isLoading: false,
    init: function() {
        // Product search dialog
        $("#dialog_list").off().on("click", "td.item-select", function(e) {
            var $checkItem = $($(this).parent().find(".check-item"));
            if ($checkItem.is(":checked")) {
                $checkItem.prop("checked", null);
                $(this).parent().removeClass("is-row--selected");
            } else {
                $checkItem.prop("checked", "true");
                $(this).parent().addClass("is-row--selected");
            }

            var n = $("#dialog_list .table--list").find("input:checked").length;
            var len = $("#dialog_list .check-item").length;
            if (n > 0 && n < len) {
                $("#check_all").show();
                $("#uncheck_all").hide();
            } else if (n > 0 && n === len) {
                $("#check_all").hide();
                $("#uncheck_all").show();
            } else if (n < 0) {
                $("#check_all").hide();
                $("#uncheck_all").show();
            }
            e.stopPropagation();
        });
        $("#dialog_query").keyup(function() {
            var id = 0;
            return function(e) {
                clearTimeout(id);
                id = setTimeout(selectionDialogVM.updateSearchItems(e.target.value), 500);
            };
        }());
        $("#dialog_query").keypress(function(event) {
            var value = $("#dialog_query").val();
            if (event.which === 13 && !selectionDialogVM.isLoading) {
                selectionDialogVM.loadData(value);
            }
        });
        $("#uncheck_all").click(function() {
            $("#dialog_list .check-item").prop("checked", false);
            $("#dialog_list .check-item").parent().parent().removeClass("is-row--selected");
            $("#check_all").show();
            $(this).hide();
        });
        $("#check_all").click(function() {
            $("#dialog_list .check-item").prop("checked", true);
            $("#dialog_list .check-item").parent().parent().addClass("is-row--selected");
            $("#uncheck_all").show();
            $(this).hide();
        });
    },
    registerChanel: function() {

    },
    updateSelectedItem: function(dom) {
        if (!$(dom).is(":checked")) {
            $(dom).parent().parent().removeClass("is-row--selected");
        } else {
            $(dom).parent().parent().addClass("is-row--selected");
        }

        var n = $("#dialog_list .table--list").find("input:checked").length;
        var len = $("#dialog_list .check-item").length;
        if (n > 0 && n < len) {
            $("#check_all").show();
            $("#uncheck_all").hide();
        } else if (n > 0 && n === len) {
            $("#check_all").hide();
            $("#uncheck_all").show();
        } else if (n < 0) {
            $("#check_all").hide();
            $("#uncheck_all").show();
        }
        return false;
    },
    /**
     * Show search dialog
     * 
     * @param {type} settings {data, attr, callbackFn, selectedLst, url, param}
     * @returns {undefined}
     */
    show: function(settings) {
        this.options = {};
        this.options = $.extend({}, settings);
        if (this.options.attr == null) {
            return;
        }

        if (this.options.data != undefined) {
            this.items = this.options.data;
        }

        // mapping data to mapItems
        // mapItems is array of { id, code, name }
        this.updateMapItems(this.options.data, this.options.attr);
        // updating view
        this.updateValue("mapItems", this.mapItems);
        // binding events and run call back
        $("#dialog_ok").click(function() {
            var list = selectionDialogVM.getSelectedList();
            if (selectionDialogVM.options.callbackFn !== undefined && typeof selectionDialogVM.options.callbackFn === "function") {
                selectionDialogVM.options.callbackFn(list);
            }
        });
        selectionDialogVM.showDialog();
    },
    showDialog: function() {
        var items = $("#dialog_list .table--list input");
        var selectedList = this.options.selectedLst;
        for (var i = 0; i < items.length; i++) {
            var $item = $(items[i]);
            var parent = $item.parent().parent();
            var id = parent.attr("data-row-id");
            var alreadyIn = false;
            for (var j = 0; j < selectedList.length; j++) {
                if (selectedList[j].id === id) {
                    alreadyIn = true;
                    break;
                }
            }
            if (alreadyIn) {
                $item.hide();
            } else {
                $item.show();
            }
        }

        $("#dialog_query").val("");
        $(".dialog-title").text(selectionDialogVM.options.title);
        $('#dialog_selection').showDialog({width: '600px'});
        $("#uncheck_all").trigger("click");
    },
    /**
     * Convert real objects to mapped objects
     * 
     * @param {type} objLst
     * @param {type} proArr
     * @returns {undefined}
     */
    updateMapItems: function(objLst, proArr) {
        this.mapItems.length = 0;
        if (objLst != null) {
            for (var i = 0; i < objLst.length; i++) {
                var item = {
                    id: "",
                    code: "",
                    name: ""
                };
                if (objLst[i][proArr.id] != undefined) {
                    item.id = objLst[i][proArr.id];
                }

                if (objLst[i][proArr.code] != undefined) {
                    item.code = objLst[i][proArr.code];
                }

                if (objLst[i][proArr.name] != undefined) {
                    item.name = objLst[i][proArr.name];
                }

                this.mapItems.push(item);
            }
        }
    },
    updateSearchItems: function(value) {
        this.mapItems.forEach(function(e) {
            var show = (e.code.toLowerCase().indexOf(value) > -1) ||
                    (e.name.toLowerCase().indexOf(value) > -1);
            $("#dialog_selection tr[data-index=" + e.$index + "]").toggle(show);
        });
        if (this.options.url != undefined && this.options.url != "") {
            if ($("#dialog_selection").find("tr:visible").length <= 0) {
                $(".dialog-message").show();
            } else {
                $(".dialog-message").hide();
            }
        }
        if (value === "") {
            this.refeshData();
        }
    },
    getSelectedList: function() {
        var selectedLst = this.options.selectedLst;
        if (selectedLst == null) {
            selectedLst = new Array();
            this.options.selectedLst = selectedLst;
        }
        var mapItems = selectionDialogVM.mapItems;
        var items = $("#dialog_list .table--list :checked");
        for (var i = 0; i < items.length; i++) {
            var $item = $(items[i]);
            var parent = $item.parent().parent();
            var index = parent.attr("data-index");
            var value = mapItems[index];
            var shouldAdd = true;
            for (var j in selectedLst) {
                if (selectedLst[j].id === value.id) {
                    shouldAdd = false;
                    break;
                }
            }
            if (shouldAdd) {
                var obj = this.getItemById(value.id);
                if (obj != null) {
                    selectedLst.push(obj);
                }
            }
            $item.hide();
        }
        $("#dialog_selection").hideDialog();
        $("#uncheck_all").trigger("click");
        $(".dialog-loading").hide();
        return selectedLst;
    },
    loadData: function(value) {
        var data = {};
        data[selectionDialogVM.options.param] = value;
        $(".dialog-loading").show();
        $(".dialog-message").hide();
        selectionDialogVM.isLoading = true;

        if (typeof selectionDialogVM.request !== undefined && selectionDialogVM.request) {
            selectionDialogVM.request.abort();
            delete selectionDialogVM.request;
        }

        selectionDialogVM.request = $.ajax({
            url: selectionDialogVM.options.url,
            type: "get",
            dataType: "json",
            data: data,
            error: function() {
                selectionDialogVM.isLoading = false;
                $(".dialog-loading").hide();
            },
            beforeSend: function() {
                // do smth before sending
            },
            complete: function() {
                // do smth when complete action
            },
            success: function(data) {
                $(".dialog-loading").hide();
                selectionDialogVM.updateItems(data.items);
                selectionDialogVM.updateMapItems(data.items, selectionDialogVM.options.attr);
                selectionDialogVM.updateValue("mapItems", selectionDialogVM.mapItems);
                selectionDialogVM.isLoading = false;
            }
        });
    },
    isLoaded: function(obj) {
        var retVal = false;
        if (selectionDialogVM.items.length == 0) {
            return retVal;
        }
        for (var i = 0; i < selectionDialogVM.items.length; i++) {
            var obj1 = selectionDialogVM.items[i];
            if (obj.id == obj1.id) {
                retVal = true;
                break;
            }
        }
        return retVal;
    },
    updateItems: function(newItems) {
        if (newItems && $.isArray(newItems)) {
            for (var i = 0; i < newItems.length; i++) {
                if (!selectionDialogVM.isLoaded(newItems[i])) {
                    this.items.push(newItems[i]);
                }
            }
        }
    },
    getItemById: function(id) {
        for (var i in selectionDialogVM.items) {
            if (id == selectionDialogVM.items[i].id) {
                return selectionDialogVM.items[i];
            }
        }
        return null;
    },
    refeshData: function() {
        selectionDialogVM.updateMapItems(this.items, selectionDialogVM.options.attr);
        selectionDialogVM.updateValue("mapItems", selectionDialogVM.mapItems);
        selectionDialogVM.isLoading = false;
    }
};
$(document).ready(function() {
    selectionDialogVM.init();
});

