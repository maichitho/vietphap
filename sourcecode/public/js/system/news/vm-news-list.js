var newsVM = {
    namespace: "newsVM",
    entries: [],
    categories: [],
    //companyCategoryId: {},
    createTimeFrom: " ",
    createTimeTo: " ",
    delObj: {},
    delId: "",
    selectedCateId: {},
    keyword: {},
    entryCates: [],
    serviceCates: [],
    init: function() {
        // init datepicker
        $(".input-date").each(function() {
            $(this).datepicker({
                dateFormat: newsVM.messages["dateFormat"],
                altField: "#" + $(this).attr("altField"),
                altFormat: "@"
            });
        });

        $(".tree-collapse").click(function(e) {
            var id = $(this).attr("data-id");
//            alert(id);
//            $(this).closest(".menu-vr").find(".tree-link-child").is(":visible").slideUp();
            $(this).closest(".menu-vr").find("[data-parent='" + id + "']").slideToggle();

            if ($(this).hasClass("plus-icon")) {
                $(this).addClass("minus-icon");
                $(this).removeClass("plus-icon");
                $(this).removeClass("plus-icon-sel");
            } else {
                $(this).removeClass("minus-icon");
                $(this).addClass("plus-icon");
                $(this).removeClass("minus-icon-sel");
            }
            e.stopPropagation();
        }).mouseover(function() {
            if ($(this).hasClass("plus-icon")) {
//                $(this).removeClass("plus-icon");
                $(this).addClass("plus-icon-sel");
            } else {
//                $(this).removeClass("minus-icon");
                $(this).addClass("minus-icon-sel");
            }
        }).mouseout(function() {
            if ($(this).hasClass("plus-icon")) {
//                $(this).removeClass("plus-icon");
                $(this).removeClass("plus-icon-sel");
            } else {
//                $(this).removeClass("minus-icon");
                $(this).removeClass("minus-icon-sel");
            }
        });

        $(".f-menu-left").find(".tree-expand-item").each(function(){
             var id = $(this).attr("data-id");
//            alert(id);
//            $(this).closest(".menu-vr").find(".tree-link-child").is(":visible").slideUp();
            $(this).closest(".menu-vr").find("[data-parent='" + id + "']").slideToggle();

            if ($(this).hasClass("plus-icon")) {
                $(this).addClass("minus-icon");
                $(this).removeClass("plus-icon");
                $(this).removeClass("plus-icon-sel");
            } else {
                $(this).removeClass("minus-icon");
                $(this).addClass("plus-icon");
                $(this).removeClass("minus-icon-sel");
            }
        });

    },
    registerChanel: function() {

    },
    showDeleteDialog: function(dom, id) {
        newsVM.delObj = $(dom).parent().parent().parent().parent();
        newsVM.delId = id;
        confirmDialog.show({
            title: newsVM.messages['deleteTitle'],
            info: newsVM.messages['deleteNews'],
            callbackFn: newsVM.deleteEntry
        });
    },
    deleteEntry: function() {
        $.ajax({
            url: "/system/news/delete",
            type: "post",
            dataType: "json",
            data: {
                id: newsVM.delId
            },
            error: function() {
                confirmDialog.hide();
                $("#alert_error").show();
            },
            beforeSend: function() {
                // do smth before sending
            },
            complete: function() {
                // do smth when complete action
            },
            success: function(data) {
                if (data.status) {
                    newsVM.delObj.remove();
                    confirmDialog.hide();
                    $("#alert_success").show();
                } else {
                    confirmDialog.hide();
                    $("#alert_error").show();
                }
            }
        });
    },
    reloadCategories: function(type) {

        if (type == newsVM.messages["typeService"]) {
            newsVM.categories = newsVM.serviceCates;
        }
        else {
            newsVM.categories = newsVM.entryCates;
        }
        newsVM.updateValue("categories", newsVM.categories);
    }
};

$(document).ready(function() {
    newsVM.init();
});