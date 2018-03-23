var categoryListVM = {
    namespace: "categoryListVM",
    categories: [], // contain origin data
    category: {},
    isUpdate: false,
    type: {},
    catType: {},
    selObj: {},
    mainCate: [],
    init: function() {
        // check create/update or list status
        if (categoryListVM.type == "list") {
            $(".category-cnu-div").hide();
            $(".category-list-div").show();
            categoryListVM.isUpdate = false;
        } else {
            //            if(categoryListVM.catType != categoryListVM.messages['categoryCardType']){
            //                $('#tr-code-input').remove();
            //            }
            $(".category-cnu-div").show();
            $(".category-list-div").hide();

            if ($('#id-sl-parent-category').length && categoryListVM.type == 'update')
                $('#id-sl-parent-category').val(categoryListVM.category.parentId);
        }

        $("#category_close").click(function() {
            $(".category-cnu-div").hide();
            $(".category-list-div").removeClass("med-8 pad-8 wid-8");
            categoryListVM.isUpdate = false;
            categoryListVM.resetFields();
        });

        $('.add-category-button').click(function() {
            categoryListVM.resetFields();
            $(".category-cnu-div").show();
            $(".category-list-div").addClass("med-8 pad-8 wid-8");
        });

        $('.chk-using-in-menu').click(function() {
            var id = $(this).attr('data-id');
            var menuType = $(this).attr('menu-type');
            $('#img-loading-' + id).show();

            var param = {
                id: id,
                type: menuType,
                isAdd: $(this).prop('checked') ? '1' : '0',
                linkType: (categoryListVM.catType == categoryListVM.messages['qaLinkType']) ? categoryListVM.catType : categoryListVM.messages['serviceLinkType']
            };

            $.post('/system/setting/async-add-remove-menu-by-category', param, function(data) {
                if (data.success) {
                }
                else {
                    alert(categoryListVM.messages['errorUpdateUsingMenu']);
                }
            }, "json")
                    .complete(function() {
                        $('#img-loading-' + id).hide();
                    });
        });
    },
    registerChanel: function() {

    },
    updateCategory: function(dom) {
//        alert("dsa");
//        $('html, body').animate({scrollTop: 0}, 800);
//        $(window).scrollTop(0);
        if (categoryListVM.catType != categoryListVM.messages['categoryCardType']) {
            $('#tr-code-input').remove();
        }

        var id = $(dom).attr("id");
        categoryListVM.isUpdate = true;
        categoryListVM.category = categoryListVM.getCategoryById(id);
        categoryListVM.updateValue("category", categoryListVM.category);
        if ($('#id-sl-parent-category').length)
            $('#id-sl-parent-category').val(categoryListVM.category.parentId);
//        if ($('#category_des').length > 0)
//            CKEDITOR.instances['category_des'].setData(categoryListVM.category.description);

        $(".category-list-div").addClass("med-8 pad-8 wid-8");
        $(".category-cnu-div").fadeIn("fast");

        $(window).scrollTop(0);
//        alert($("html, body").scrollTop());
//        var top = $(dom).offset().top;
//        var winHeight = $(window).height();
//        var panelHeight = $(".f-category-panel").height();
//
//        if (top + panelHeight > $(".category-list-div").height()) {
//            top = $(".category-list-div").height() - panelHeight - 10;
//        } else if (top > winHeight / 3) {
//            top = 80;
//        }
//        

//        $(".f-category-panel").offset({top: top});
    },
    getCategoryById: function(id) {
        var reVal;
        for (var i = 0; i < categoryListVM.categories.length; i++) {
            if (categoryListVM.categories[i]["id"] == id) {
                reVal = categoryListVM.categories[i];
            }
        }
        return reVal;
    },
    showDialogConfirm: function(dom, id) {
        var delObj = $(dom).parent().parent().parent().parent();
        categoryListVM.selObj["id"] = id;
        categoryListVM.selObj["obj"] = delObj;
        $("#delete_dialog").showDialog({
            width: "400px"
        });
    },
    deleteCategory: function() {
        var id = categoryListVM.selObj.id;
        var delObj = categoryListVM.selObj.obj;
        $(".loading").show();
        $.ajax({
            url: "/system/category/delete-category",
            type: "post",
            dataType: "json",
            data: {
                id: id,
                type: categoryListVM.catType
            },
            error: function() {
                $("#delete_dialog").hideDialog();
                $(".loading").hide();
                $("#delete_dialog").hideDialog();
                $(".loading").hide();
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
                    delObj.remove();
                    $("#delete_dialog").hideDialog();
                    $(".loading").hide();
                    $("#alert_success").show();
                } else {
                    $("#delete_dialog").hideDialog();
                    $(".loading").hide();
                    $("#alert_error").show();
                }
            }
        });
    },
    updateValueForm: function() {
        return true;
    },
    checkExistMainCategoryInList: function(mainCateId) {
        for (var i = 0; i < categoryListVM.categories.length; i++) {
            if (categoryListVM.categories[i].parentId == mainCateId)
                return true;
        }
        return false;
    },
    filterByMainCategory: function(mainCateId) {
        var result = [];
        for (var i = 0; i < categoryListVM.categories.length; i++) {
            if (categoryListVM.categories[i].parentId == mainCateId)
                result.push(categoryListVM.categories[i]);
        }
        return result;
    },
    resetFields: function() {
        categoryListVM.category = {};
        categoryListVM.updateValue("category", categoryListVM.category);
        if ($('#category_des').length > 0) {
//            CKEDITOR.instances['category_des'].setData('');
            $('#form-general').get(0).reset();
        }
    },
    showDeleteImageDialog: function(dom, url) {
        categoryListVM.selObj.dom = $(dom);
        categoryListVM.selObj.url = url;
        categoryListVM.selObj.filePath = $(dom).parent().find(".thumbnail").attr("data-path");
        categoryListVM.selObj.fieldName = $(dom).parent().find(".thumbnail input");
        confirmDialog.show({
            title: categoryListVM.messages['deleteTitle'],
            info: categoryListVM.messages['deleteImage'],
            errorMessage: categoryListVM.messages['deleteImageError'],
            callbackFn: categoryListVM.deleteImage
        });
    },
    deleteImage: function() {
        confirmDialog.showLoading();
        var url = "/system/file/delete-file";
        $.post(url, {
            filePath: categoryListVM.selObj.filePath
        },
        function(data)
        {
            confirmDialog.hideLoading();
            if (data.status) {
                confirmDialog.hide();
                categoryListVM.selObj.dom.parent().find("img").attr("src", categoryListVM.selObj.url);
                categoryListVM.selObj.dom.hide();
                categoryListVM.selObj.fieldName.val("");
                categoryListVM.selObj.dom.parent().find(".thumbnail").attr("data-path", "");
                if (categoryListVM.selObj.dom.hasClass("contract-file")) {
                    $(".contract-file").hide();
                }
            } else {
                confirmDialog.showError();
            }
        }, "json");
    },
};

$(document).ready(function() {
    categoryListVM.init();
});