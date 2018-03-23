var newsCnuVM = {
    namespace: "newsCnuVM",
    categories: [],
    relatedEntries: [],
    selectiveEntries: [],
    entry: [],
    entryId: {},
    type: {},
    tagArr: [],
    typeUpdate: {},
    isCheckedUrl: false,
    oldRewriteUrl: "",
    init: function() {
        $("#news_content").height(600);
        newsCnuVM.updateData();

        $(".menu-general-info").click(function() {
            $(this).siblings().removeClass("tab-active");
            $(this).addClass("tab-active");
            $(".div-seo-content").hide();
            $(".div-general-content").show();
        });

        $(".menu-seo-info").click(function() {
            $(this).siblings().removeClass("tab-active");
            $(this).addClass("tab-active");
            $(".div-general-content").hide();
            $(".div-seo-content").show();
        });

        if (newsCnuVM.entry.tags.trim().length > 0) {
            var temp = newsCnuVM.entry.tags.trim().split(",");
            newsCnuVM.tagArr = [];

            for (var i = 0; i < temp.length; i++) {
                if (temp[i].trim().length > 0)
                    newsCnuVM.tagArr.push({
                        "id": i,
                        "name": temp[i].trim()
                    });
            }
            newsCnuVM.updateValue("tagArr", newsCnuVM.tagArr);
        }

        if (newsCnuVM.type != "create") {
            newsCnuVM.isCheckedUrl = true;
            newsCnuVM.oldRewriteUrl = newsCnuVM.entry.rewriteUrl;
        }

        $('#tags').keypress(function(e) {
            if (e.keyCode == 13) {
                e.preventDefault(); // Makes no difference                

                if ($(this).val().trim().length > 0) {
                    var newId = Math.max.apply(this, $.map(newsCnuVM.tagArr, function(o) {
                        return o.id;
                    }));
                    newId = (isFinite(newId) ? newId : 0);

                    var temp = $(this).val().trim().split(",");
                    for (var i = 0; i < temp.length; i++) {
                        if (temp[i].trim().length > 0) {
                            newId = newId + 1;
                            newsCnuVM.tagArr.push({
                                "id": newId,
                                "name": temp[i].trim()
                            });
                        }
                    }
                    newsCnuVM.updateValue("tagArr", newsCnuVM.tagArr);
                    $(this).val("");
                }
                return false;
            }
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

        $(".f-menu-left").find(".tree-expand-item").each(function() {
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

        if (newsCnuVM.entry != null) {
            newsCnuVM.entry.relatedEntries = newsCnuVM.relatedEntries;
            newsCnuVM.updateValue("entry.relatedEntries", newsCnuVM.entry.relatedEntries);
        }

        // check rewrite url
        $("#rewriteUrl").blur(function() {
            var url = $(this).val();
            if (!url) {

            } else {
                if (newsCnuVM.type != "create") {
                    if (url != newsCnuVM.oldRewriteUrl) {
                        newsCnuVM.checkRewriteUrl(url);
                    }else{
                        newsCnuVM.isCheckedUrl = true;
                    }
                } else {
                    newsCnuVM.checkRewriteUrl(url);
                }
            }
        });

        // render rewrite url automatically
        $("#title").keyup(function() {
            var title = $(this).val();
            if (!title) {

            } else {
                $("#seoTitle").val(title);
                $("#seoKeyword").val(title);
                $("#seoDescription").val(title);
                $("#rewriteUrl").val(change_alias(title));
            }
        }).blur(function() {
            $("#rewriteUrl").trigger("blur");
        });

    },
    updateData: function() {
        if (newsCnuVM.entry.categoryId > 0) {
            $("#categoryId").val(newsCnuVM.entry.categoryId);
        }



        //newsCnuVM.updateValue("entry", newsCnuVM.entry);
        CKEDITOR.instances["news_content"].setData(newsCnuVM.entry.content);
    },
    registerChanel: function() {

    },
    submit: function() {
        return true;
    },
    insertImages: function() {
        if (uploadDialogVM.screens !== undefined) {
            for (var i = 0; i < uploadDialogVM.screens.length; i++) {
                var url = uploadDialogVM.screens[i].url;
                var imageHtml = "<br/><img src=\'" + url + "\' style=\'width:60%; position:relative; margin: 5px auto;\'/>";
                CKEDITOR.instances["news_content"].insertHtml(imageHtml);
            }
        }
    },
    refreshForm: function() {
        CKEDITOR.instances["news_content"].setData("");
    },
    validateInput: function() {
        if ($("#title").val().length <= 0 ||
                $("#seoTitle").val().length <= 0 ||
                $("#seoKeyword").val().length <= 0 ||
                $("#seoDescription").val().length <= 0) {
            confirmDialog.show({
                title: newsCnuVM.messages['titleAlert'],
                info: newsCnuVM.messages['notifyInputError'],
                isAlert: true
            });
//            alert(newsCnuVM.messages["notifyInputError"]);
            return false;
        }

        $('#isTopValue').val($('#chk-isTop').prop('checked') ? '1' : '');
        $('#displayValue').val($('#chk-display').prop('checked') ? '1' : '');

        var tmpStr = "";
        if (newsCnuVM.tagArr.length > 0) {
            for (var i = 0; i < newsCnuVM.tagArr.length; i++) {
                tmpStr += newsCnuVM.tagArr[i].name + ',';
            }
            tmpStr = tmpStr.substring(0, tmpStr.length - 1);
        }
        $('#tagStr').val(tmpStr);

        var relatedIds = "";
        if ($.isArray(newsCnuVM.entry.relatedEntries)) {
            for (var i = 0; i < newsCnuVM.entry.relatedEntries.length; i++) {
                if (relatedIds == "") {
                    relatedIds += newsCnuVM.entry.relatedEntries[i].id;
                } else {
                    relatedIds += ";" + newsCnuVM.entry.relatedEntries[i].id;
                }
            }
        }
        $("input[name='entryIds']").val(relatedIds);

        $("#news_content").val(CKEDITOR.instances["news_content"].getData());

        return true && newsCnuVM.isCheckedUrl;
    },
    deleteTags: function(id) {
        for (var i = 0; i < newsCnuVM.tagArr.length; i++) {
            if (newsCnuVM.tagArr[i].id == id) {
                newsCnuVM.tagArr.splice(i, 1);
                newsCnuVM.updateValue("tagArr", newsCnuVM.tagArr);
                break;
            }
        }
    },
    showRelatedEntryDialog: function(title) {
        selectionDialogVM.show({
            title: title,
            data: newsCnuVM.selectiveEntries,
            attr: {
                id: "id",
                code: "",
                name: "title"
            },
            callbackFn: newsCnuVM.addRelatedEntry,
            selectedLst: newsCnuVM.entry.relatedEntries,
            url: "/system/news/async-list",
            param: "keyword"
        });
    },
    addRelatedEntry: function(selectedList) {
        newsCnuVM.entry.relatedEntries = selectedList;
        newsCnuVM.updateValue("entry.relatedEntries", newsCnuVM.entry.relatedEntries);
    },
    deleteRelatedEntry: function(id) {
        var relatedEntries = newsCnuVM.entry.relatedEntries;
        for (var i = 0; i < relatedEntries.length; i++) {
            if (relatedEntries[i].id == id) {
                $("#related_entry").find("[data-id=" + id + "]").remove();
                relatedEntries.splice(i, 1);
            }
        }
    },
    checkRewriteUrl: function(rewriteUrl) {
        newsCnuVM.request = $.ajax({
            url: "/system/news/async-check-rewrite-url",
            type: "get",
            dataType: "json",
            data: {
                rewriteUrl: rewriteUrl
            },
            error: function() {

            },
            beforeSend: function() {
                // do smth before sending
            },
            complete: function() {
                // do smth when complete action
            },
            success: function(data) {
                if (data.status) {
                    confirmDialog.show({
                        title: newsCnuVM.messages['titleAlert'],
                        info: newsCnuVM.messages['rewriteUrlError'],
                        isAlert: true
                    });
                    newsCnuVM.isCheckedUrl = false;
                } else {
                    newsCnuVM.isCheckedUrl = true;
                }
            }
        });

    }
};

$(document).ready(function() {
    newsCnuVM.init();
});