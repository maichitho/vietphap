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
        $("#dialog_list").on("click", "tr", function(e) {
            var $checkItem = $($(this).find(".check-item"));
            if ($checkItem.is(":checked")) {
                $checkItem.prop("checked", null);
                $(this).removeClass("is-row--selected");
            } else {
                $checkItem.prop("checked", "true");
                $(this).addClass("is-row--selected");
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

        // stop checkbox listener
        $("#dialog_selection .check-item").click(function(e) {
            if (!$(this).is(":checked")) {
                $(this).parent().parent().removeClass("is-row--selected");
            } else {
                $(this).parent().parent().addClass("is-row--selected");
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
    /**
     * Show search dialog
     * 
     * @param {type} settings {data, attr, callbackFn, selectedLst, url, param}
     * @returns {undefined}
     */
    show: function(settings) {
        this.options = $.extend({}, settings);
        if (this.options.attr == null) {
            return;
        }
        var callbackFn = this.options.callbackFn;

        // mapping data to mapItems
        // mapItems is array of { id, code, name }
        this.updateMapItems(this.options.data, this.options.attr);

        // updating view
        this.updateValue("mapItems", this.mapItems);

        // binding events and run call back
        $("#dialog_ok").click(function() {
            var list = selectionDialogVM.getSelectedList();
            if (callbackFn !== undefined && typeof callbackFn === "function") {
                callbackFn(list);
            }
        });

        selectionDialogVM.showDialog();
    },
    showDialog: function() {
        var items = $("#dialog_list .table--list input");
        var selectedList = this.options.selectedLst;
        var mapItems = selectionDialogVM.mapItems;

        if(selectedList != null){
            for (var i = 0; i < items.length; i++) {
                var $item = $(items[i]);
                var parent = $item.parent().parent();
                var index = parent.attr("data-index");
                var value = mapItems[index];
                var alreadyIn = false;
                for (var j = 0; j < selectedList.length; j++) {
                    if (selectedList[j].id === value.id) {
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
        }

        $(".dialog-title").text(selectionDialogVM.options.title);
        $('#dialog_selection').showDialog({
            width: '600px'
        });
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
                item.id = objLst[i][proArr.id],
                item.code = objLst[i][proArr.code],
                item.name = objLst[i][proArr.name];
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
        if ($("#dialog_selection").find("tr:visible").length <= 0) {
            // show suggestion of hitting enter key
            $(".dialog-message").show();
        } else {
            $(".dialog-message").hide();
        }
    },
    getSelectedList: function() {
        var selectedLst = this.options.selectedLst;
        if (selectedLst == null) {
            selectedLst = new Array();
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
                selectedLst.push(value);
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
        $.ajax({
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
                selectionDialogVM.updateMapItems(data.items, selectionDialogVM.options.attr);
                selectionDialogVM.updateValue("mapItems", selectionDialogVM.mapItems);
                selectionDialogVM.isLoading = false;
            }
        });
    }
};
$(document).ready(function() {
    selectionDialogVM.init();
});

