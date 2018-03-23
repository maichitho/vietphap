var menuCnuVM = {
    namespace: "menuCnuVM",
    menu: {},
    manualType: {},
    qaType: {},
    qaCategoryType: {},
    entryType: {},
    newsType: {},
    oneCategoryType: {},
    categoriesType: {},
    imageType: {},
    htmlType: {},
    selectedLinks: [],
    parentMenus: [],
    treeMenus: [],
    treeName: {},
    types: [],
    linkId: -1,
    selObj: {},
    init: function() {
        $(".tree-menu").height($(window).height() - 200);
        $("#linkType").change(function() {
            menuCnuVM.renderUI(false);
        });
        $("#type").change(function() {
            menuCnuVM.loadParentMenu();
            menuCnuVM.reloadTreeMenu();
        });
        $("#menu-list .tree-item").click(function() {
            $("#menu-list .tree-item").removeClass("tree-item-active");
            $(this).addClass("tree-item-active");
            $("#parentId").val($(this).attr("menu-id"));
        });
        menuCnuVM.renderUI(true);

        $('.tr-tree-menu').hover(function() {
            $(this).find('.img-tree-menu').show();
        }, function() {
            $(this).find('.img-tree-menu').hide();
        })
                .click(function() {
                    window.location.href = "/system/setting/update-menu?id=" + $(this).attr('data-id');
                });

        $('#htmlCode').focusout(function() {
            $('#demo-html-view').html($(this).val());
        });

        if (menuCnuVM.menu != null) {
            if (menuCnuVM.menu.status == "1") {
                $("#chk-display").prop("checked", true);
            } else {
                $("#chk-display").prop("checked", false);
            }
        }
    },
    renderUI: function(updateSelected) {
        if ($("#linkType").val() == menuCnuVM.manualType) {
            $("#linkCategory").hide();
            $("#linkUrl").show();
            $("#linkUrl").removeAttr("disabled");
            $("#linkId").val('0');
            $('#imageUpload').hide();
            $('#htmlInput').hide();
        } else if ($("#linkType").val() == menuCnuVM.imageType) {
            $("#linkCategory").hide();
            $("#linkUrl").show();
            $("#linkId").val('0');
            $('#imageUpload').show();
            $('#htmlInput').hide();
        } else if ($("#linkType").val() == menuCnuVM.htmlType) {
            $("#linkCategory").hide();
            $("#linkUrl").hide();
            $("#linkId").val('0');
            $('#imageUpload').hide();
            $('#htmlInput').show();
        } else if ($("#linkType").val() == menuCnuVM.fixMomType) {
            $("#linkCategory").hide();
            $("#linkUrl").show();
            $("#linkUrl").attr("disabled", "disabled");
            $("#linkUrl").val(menuCnuVM.messages["fix_link_mom_child"]);
            $("#linkId").val('0');
            $('#imageUpload').hide();
            $('#htmlInput').hide();
        } else if ($("#linkType").val() == menuCnuVM.fixDistributionType) {
            $("#linkCategory").hide();
            $("#linkUrl").show();
            $("#linkUrl").attr("disabled", "disabled");
            $("#linkUrl").val(menuCnuVM.messages["fix_link_distribution"]);
            $("#linkId").val('0');
            $('#imageUpload').hide();
            $('#htmlInput').hide();
        } else {
            $("#linkCategory").show();
            $("#linkUrl").hide();
            $('#imageUpload').hide();
            $('#htmlInput').hide();

            if ($("#linkType").val() === menuCnuVM.entryType) {
//                alert(menuCnuVM.entryType);
                menuCnuVM.updateSelectedLinks("/system/setting/async-list-entries", updateSelected, $("#linkType").val());
            } else if ($("#linkType").val() === menuCnuVM.categoriesType || $("#linkType").val() === menuCnuVM.oneCategoryType) {
//                 alert(menuCnuVM.categoriesType);
                menuCnuVM.updateSelectedLinks("/system/category/async-list-categories-with-first-element", updateSelected, $("#linkType").val());
            } else if ($("#linkType").val() === menuCnuVM.qaType) {
                menuCnuVM.updateSelectedLinks("/system/setting/async-list-qas", updateSelected, $("#linkType").val());
            } else if ($("#linkType").val() === menuCnuVM.qaCategoryType) {
//                 alert(menuCnuVM.qaCategoryType);
                menuCnuVM.updateSelectedLinks("/system/category/async-list-categories-with-first-element", updateSelected, $("#linkType").val());
            } else if ($("#linkType").val() === menuCnuVM.albumType) {
                menuCnuVM.updateSelectedLinks("/system/setting/async-list-albums", updateSelected, $("#linkType").val());
            } else if ($("#linkType").val() === menuCnuVM.eventType) {
                menuCnuVM.updateSelectedLinks("/system/setting/async-list-events", updateSelected, $("#linkType").val());
            }
        }
        $('#span-type-suggestion').text(menuCnuVM.messages['suggestion_' + $('#linkType').val()]);
    },
    onSelectedLinks: function(obj) {
        $("#linkCategory .tree-item").removeClass("tree-item-active");
        $(obj).addClass("tree-item-active");
        $("#linkId").val($(obj).attr("linkId"));
    },
    updateSelectedLinks: function(url, updateSelected, type) {
        $("#processing-img").show();
        var param = {
            type: type
        };
        $.post(url, param, function(data) {
            if (data.status) {
                menuCnuVM.selectedLinks = data.data;
                //                menuCnuVM.updateValue("selectedLinks", data.data);
                $("#linkCategory").html("");
                for (var i = 0; i < data.data.length; i++) {
                    var selectedAttr = "";
                    if (updateSelected && $("#linkId").val() == data.data[i].id) {
                        selectedAttr = "tree-item-active";
                    }
                    var imgCode = "";
                    if (data.data[i].parentId != '0')
                        imgCode = "<img src=\"/img/icon/right.png\" style=\"height: 12px; margin-right: 5px;\" /> ";
                    $("#linkCategory").append("<div onclick=\"menuCnuVM.onSelectedLinks(this)\" class=\"tree-item " + selectedAttr + "\" linkid=\"" + data.data[i].id + "\">" + imgCode + data.data[i].name + "</div>");
                }
                $("#processing-img").hide();
            }
        }, "json");
    },
    registerChanel: function() {

    },
    loadParentMenu: function() {
        var params = {
            type: $('#type').val()
        };
        $('#loading-parent-menu').show();
        $.post('/system/setting/async-load-parent-menu', params, function(data) {
            if (data.success) {
                menuCnuVM.parentMenus = data.items;
                menuCnuVM.updateValue('parentMenus', menuCnuVM.parentMenus);
            }
        }, 'json')
                .success(function() {
                })
                .error(function(xhr, ajaxOptions, thrownError) {
                    alert(ajaxOptions);
                })
                .complete(function() {
                    $('#loading-parent-menu').hide();
                });
    },
    showDeleteImageDialog: function(dom, url) {
        menuCnuVM.selObj.dom = $(dom);
        menuCnuVM.selObj.url = url;
        menuCnuVM.selObj.filePath = $(dom).parent().find(".thumbnail").attr("data-path");
        menuCnuVM.selObj.fieldName = $(dom).parent().find(".thumbnail input");
        confirmDialog.show({
            title: menuCnuVM.messages['deleteTitle'],
            info: menuCnuVM.messages['deleteImage'],
            errorMessage: menuCnuVM.messages['deleteImageError'],
            callbackFn: menuCnuVM.deleteImage
        });
    },
    deleteImage: function() {
        confirmDialog.showLoading();
        var url = "/system/file/delete-file";
        $.post(url, {
            filePath: menuCnuVM.selObj.filePath
        },
        function(data)
        {
            confirmDialog.hideLoading();
            if (data.status) {
                confirmDialog.hide();
                menuCnuVM.selObj.dom.parent().find("img").attr("src", menuCnuVM.selObj.url);
                menuCnuVM.selObj.dom.hide();
                menuCnuVM.selObj.fieldName.val("");
                menuCnuVM.selObj.dom.parent().find(".thumbnail").attr("data-path", "");
                if (menuCnuVM.selObj.dom.hasClass("contract-file")) {
                    $(".contract-file").hide();
                }
            } else {
                confirmDialog.showError();
            }
        }, "json");
    },
    reloadTreeMenu: function() {
        var params = {
            type: $('#type').val()
        };
        $.post('/system/setting/async-get-menu-by-type', params, function(data) {
            if (data.success) {
                menuCnuVM.treeMenus = data.treeMenus;
                menuCnuVM.treeName = data.treeName;
                menuCnuVM.updateValue('treeMenus', menuCnuVM.treeMenus);
                menuCnuVM.updateValue('treeName', menuCnuVM.treeName);

                $('.tr-tree-menu').hover(function() {
                    $(this).find('.img-tree-menu').show();
                }, function() {
                    $(this).find('.img-tree-menu').hide();
                })
                        .click(function() {
                            window.location.href = "/system/setting/update-menu?id=" + $(this).attr('data-id');
                        });
            }
        }, 'json')
                .success(function() {
                })
                .error(function(xhr, ajaxOptions, thrownError) {
                    alert(ajaxOptions);
                })
                .complete(function() {
                });
    },
    validateInput: function() {
        //        if($("#name").val().length <= 0){
        //            return false;
        //        }
        $('#statusValue').val($('#chk-display').prop('checked') ? '1' : '');
        return true;
    }
};

$(document).ready(function() {
    menuCnuVM.init();
});