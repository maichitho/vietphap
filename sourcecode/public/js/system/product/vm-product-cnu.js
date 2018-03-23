var productCnuVM = {
    namespace: "productCnuVM",
    product: {},
    products: [],
    categories: [],
    images: [],
    selObj: {},
    selDom: {},
    selCategories: [],
    type: {},
    init: function() {
        if (!$.isArray(productCnuVM.images)) {
            productCnuVM.images = [];
        }

        if (productCnuVM.type == "update") {
            CKEDITOR.instances['product_description'].setData(productCnuVM.product["description"]);
            if (productCnuVM.product.isNew == 1) {
                $("#is_new").prop("checked", true);
            } else {
                $("#is_new").prop("checked", false);
            }

            if (productCnuVM.product.isShow == 1) {
                $("#is_show").prop("checked", true);
            } else {
                $("#is_show").prop("checked", false);
            }

            var widthP = $("#image-panel .thumbnail-link").width();
            var heigtP = widthP;
            $("#image-panel .thumbnail-link").height(heigtP);
            $("#image-panel .thumbnail-link").find("img").each(function() {
                resizeImages($(this), widthP, heigtP);
            });
        }

        $("#is_new").change(function() {
            if ($("#is_new").prop("checked")) {
                $("#isNew").val(1);
            } else {
                $("#isNew").val(0);
            }
        });

        $("#is_show").change(function() {
            if ($("#is_show").prop("checked")) {
                $("#isShow").val(1);
            } else {
                $("#isShow").val(0);
            }
        });

    },
    registerChanel: function() {

    },
    addUploadedImages: function(data) {
        if (data !== undefined) {
            //update form data for image urls
            for (var i = 0; i < data.length; i++) {
                var imgArr = $("#imageLinks").val();
                if (imgArr === "") {
                    imgArr += data[i].url + "," + data[i].thumbnailUrl;
                } else {
                    imgArr += ";" + data[i].url + "," + data[i].thumbnailUrl;
                }

                $("#imageLinks").val(imgArr);
                if (!productCnuVM.checkExistImage(data[i])) {
                    var image = {
                        id: -1,
                        productId: (productCnuVM.product.id > 0 && typeof productCnuVM.product.id != "object") ? productCnuVM.product.id : -1,
                        name: "",
                        url: data[i].url,
                        thumbnailUrl: data[i].thumbnailUrl,
                        isRepresentation: 0
                    };
                    productCnuVM.images.push(image);
                }
            }

            // update view
            if (data.length > 0) {
                productCnuVM.updateValue("images", productCnuVM.images);
                var widthP = $("#image-panel .thumbnail-link").width();
                var heigtP = widthP;
                $("#image-panel .thumbnail-link").height(heigtP);
                $("#image-panel .thumbnail-link").find("img").each(function() {
                    resizeImages($(this), widthP, heigtP);
                });
            }
        }
    },
    updateProductImages: function(images) {
        if (images && $.isArray(images)) {
            for (var i = 0; i < images.length; i++) {
                var paths = images[i]["url"].split("\/");
                var data = {
                    fileName: paths[paths.length - 1],
                    isRepresentation: images[i]["isRepresentation"],
                    id: images[i].id
                };
                productCnuVM.addUploadedImage(data);
            }
        }
    },
    updateHomeThumbnail: function(dom, num) {
        var checkObj = $(dom).closest(".product-check").find("input");
        $(dom).closest(".product-thumbnail").siblings().find("input").prop("checked", false);
        if (num == 0) {
            if (!checkObj.prop("checked")) {
                checkObj.prop("checked", false);
                $("#thumbnailUrl").val("");
            } else {
                checkObj.prop("checked", true);
                $("#thumbnailUrl").val($(dom).closest(".thumbnail").find("img").attr("src"));
            }
        } else {
            if (checkObj.prop("checked")) {
                checkObj.prop("checked", false);
                $("#thumbnailUrl").val("");
            } else {
                checkObj.prop("checked", true);
                $("#thumbnailUrl").val($(dom).closest(".thumbnail").find("img").attr("src"));
            }
        }
    },
    showDeleteImageDialog: function(dom, obj) {
        productCnuVM.selObj = obj;
        productCnuVM.selDom = dom;

        confirmDialog.show({
            title: productCnuVM.messages.deleteTitle,
            info: productCnuVM.messages.deleteImage,
            errorMessage: productCnuVM.messages.deleteImageError,
            callbackFn: productCnuVM.deleteImage
        });
    },
    deleteImage: function() {
        var screens = [];
        screens.push(productCnuVM.selObj);

        var url = "/system/file/async-delete-files";
        if (productCnuVM.selObj && productCnuVM.selObj.productId != -1) {
            url = "/system/product/async-delete-product-images";
        }

        if (typeof productCnuVM.request !== undefined && productCnuVM.request) {
            productCnuVM.request.abort();
            delete productCnuVM.request;
        }

        productCnuVM.request = $.ajax({
            url: url,
            type: "post",
            dataType: "json",
            data: {
                screens: screens
            },
            error: function() {
//                confirmDialog.showError();
            },
            beforeSend: function() {
                // do smth before sending
            },
            complete: function() {
                // do smth when complete action
            },
            success: function(data) {
                if (data.status) {
                    productCnuVM.removeImage(productCnuVM.selObj);
                    $(productCnuVM.selDom).closest(".product-thumbnail").remove();
                    confirmDialog.hide();
                } else {
                    confirmDialog.showError();
                }
            }
        });
    },
    /**
     * Validate data before submitting
     * @returns {Boolean}
     */
    updatePostedValues: function() {
        // check before posting
        $("#categoryIds").val($("#category").val());

        return true;
    },
    checkExistImage: function(image) {
        for (var i = 0; i < productCnuVM.images.length; i++) {
            if (image.url == productCnuVM.images[i].url) {
                return true;
            }
        }
        return false;
    },
    removeImage: function(image) {
        for (var i = 0; i < productCnuVM.images.length; i++) {
            if (image.url == productCnuVM.images[i].url) {
                productCnuVM.images.splice(i, 1);
            }
        }
    }
};
$(document).ready(function() {
    productCnuVM.init();
});