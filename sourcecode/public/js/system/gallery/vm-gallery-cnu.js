var galleryCnuVM = {
    namespace: "galleryCnuVM",
    delObj: {},
    delId: -1,
    images: [],
    gallery: {},
    selImage: {},
    selDom: {},
    init: function() {
        $("#input_value").keypress(function(e) {
            if ($(this).val()) {
                $("#input_update_dialog .input-valid-mes").hide();
                $("#input_update_dialog .input-update-mes").hide();
            }
        });

        $("#gallery_istop").on("change", function() {
            galleryCnuVM.updateGalleryStatus();
        });

        this.refreshGui();

        if (galleryCnuVM.gallery && galleryCnuVM.gallery.isTop != 0) {
            $("#gallery_istop").prop("checked", true);
        }
    },
    registerChanel: function() {

    },
    showDeleteImageDialog: function(dom, id, url) {
        galleryCnuVM.delObj = $(dom).parent().parent().parent();
        galleryCnuVM.delId = id;
        $(".dialog-content").height(70);
        confirmDialog.show({
            title: galleryCnuVM.messages['deleteTitle'],
            info: galleryCnuVM.messages['deleteImage'],
            errorMessage: galleryCnuVM.messages['deleteImageError'],
            imageUrl: url,
            callbackFn: galleryCnuVM.deleteImage
        });
    },
    deleteImage: function() {
        $.ajax({
            url: "/system/gallery/async-delete-image",
            type: "post",
            dataType: "json",
            data: {
                id: galleryCnuVM.delId
            },
            error: function() {
                confirmDialog.showError();
            },
            beforeSend: function() {
                // do smth before sending
            },
            complete: function() {
                // do smth when complete action
            },
            success: function(data) {
                if (data.status) {
                    galleryCnuVM.delObj.remove();
                    confirmDialog.hide();
                } else {
                    confirmDialog.showError();
                }
            }
        });
    },
    showDeleteGalleryDialog: function() {
        $(".dialog-content").height(70);
        confirmDialog.show({
            title: galleryCnuVM.messages['deleteTitle'],
            info: galleryCnuVM.messages['deleteGallery'],
            errorMessage: galleryCnuVM.messages['deleteGalleryError'],
            imageUrl: galleryCnuVM.gallery.thumbnailUrl,
            callbackFn: galleryCnuVM.deleteGallery
        });
    },
    deleteGallery: function() {
        $.ajax({
            url: "/system/gallery/async-delete-gallery",
            type: "post",
            dataType: "json",
            data: {
                id: galleryCnuVM.gallery.id
            },
            error: function() {
                confirmDialog.showError();
            },
            beforeSend: function() {
                // do smth before sending
            },
            complete: function() {
                // do smth when complete action
            },
            success: function(data) {
                if (data.status) {
                    confirmDialog.hide();
                    document.location.href = "/system/gallery/list";
                } else {
                    confirmDialog.showError();
                }
            }
        });
    },
    showEditImageDialog: function(dom, obj) {
        $("#input_update_dialog .input-valid-mes").hide();
        $("#input_update_dialog .input-update-mes").hide();
        if (obj) {
            $("#input_value").val(obj.title);
        }
        galleryCnuVM.selImage = obj;
        galleryCnuVM.selDom = dom;
        $('#input_update_dialog').showDialog({
            width: '400px'
        });
        $('#input_update_dialog').find(".dialog-content").height(80);
        $("#input_value").focus();
        $("#input_value").select();
        $("#input_ok").off().on("click", function() {
            galleryCnuVM.updateImage(obj);
        });
    },
    updateImage: function(obj) {
        if (typeof galleryCnuVM.request !== undefined && galleryCnuVM.request) {
            galleryCnuVM.request.abort();
            delete galleryCnuVM.request;
        }

        // accept empty title of an image
        galleryCnuVM.selImage.title = $("#input_value").val();

        $(".loading-dialogform").show();
        galleryCnuVM.request = $.ajax({
            url: '/system/gallery/async-update-image',
            type: "post",
            dataType: "json",
            data: {
                image: galleryCnuVM.selImage
            },
            error: function() {
                $(".input-update-mes").show();
                $(".loading-dialogform").hide();
            },
            beforeSend: function() {
                // do smth before sending
            },
            complete: function() {
                // do smth when complete action
            },
            success: function(data) {
                $(".loading-dialogform").hide();

                if (data.status) {
                    var title = galleryCnuVM.selImage.title;
                    $(galleryCnuVM.selDom).parent().parent().find(".gallery-title").text(title.length > 45 ? title.substring(0, 45) + " ..." : title);
                    $('#input_update_dialog').hideDialog();
                } else {
                    $(".input-update-mes").show();
                }
            }
        });
    },
    showEditGalleryDialog: function() {
        $("#input_update_dialog .input-valid-mes").hide();
        $("#input_update_dialog .input-update-mes").hide();
        $("#input_value").val(galleryCnuVM.gallery.title);
        $('#input_update_dialog').showDialog({
            width: '400px'
        });
        $('#input_update_dialog').find(".dialog-content").height(80);
        $("#input_value").focus();
        $("#input_value").select();
        $("#input_ok").off().on("click", function() {
            galleryCnuVM.updateGallery();
        });
    },
    updateGalleryStatus: function() {
        if (typeof galleryCnuVM.request !== undefined && galleryCnuVM.request) {
            galleryCnuVM.request.abort();
            delete galleryCnuVM.request;
        }

        galleryCnuVM.gallery.isTop = $("#gallery_istop").prop('checked') ? 1 : 0;

        galleryCnuVM.request = $.ajax({
            url: '/system/gallery/async-update-gallery',
            type: "post",
            dataType: "json",
            data: {
                gallery: galleryCnuVM.gallery
            },
            error: function() {
                confirmDialog.show({
                    title: galleryCnuVM.messages['title'],
                    successMessage: galleryCnuVM.messages['updateStatusSuccess'],
                    errorMessage: galleryCnuVM.messages['updateStatusError'],
                    isAlert: true
                });
                confirmDialog.showError();
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
                        title: galleryCnuVM.messages['title'],
                        successMessage: galleryCnuVM.messages['updateStatusSuccess'],
                        errorMessage: galleryCnuVM.messages['updateStatusError'],
                        isAlert: true
                    });
                    confirmDialog.showMessage();
                } else {
                    confirmDialog.show({
                        title: galleryCnuVM.messages['title'],
                        successMessage: galleryCnuVM.messages['updateStatusSuccess'],
                        errorMessage: galleryCnuVM.messages['updateStatusError'],
                        isAlert: true
                    });
                    confirmDialog.showError();
                }
            }
        });
    },
    updateGallery: function() {
        if (typeof galleryCnuVM.request !== undefined && galleryCnuVM.request) {
            galleryCnuVM.request.abort();
            delete galleryCnuVM.request;
        }

        if (!$("#input_value").val()) {
            $("#input_update_dialog .input-valid-mes").show();
            return;
        } else {
            galleryCnuVM.gallery.title = $("#input_value").val();
        }

        galleryCnuVM.gallery.isTop = $("#gallery_istop").prop('checked') ? 1 : 0;

        $(".loading-dialogform").show();
        galleryCnuVM.request = $.ajax({
            url: '/system/gallery/async-update-gallery',
            type: "post",
            dataType: "json",
            data: {
                gallery: galleryCnuVM.gallery
            },
            error: function() {
                $(".input-update-mes").show();
                $(".loading-dialogform").hide();
            },
            beforeSend: function() {
                // do smth before sending
            },
            complete: function() {
                // do smth when complete action
            },
            success: function(data) {
                $(".loading-dialogform").hide();

                if (data.status) {
                    var title = galleryCnuVM.gallery.title;
                    $(".box-description").text(title.length > 55 ? title.substring(0, 55) + " ..." : title);
                    $('#input_update_dialog').hideDialog();
                } else {
                    $(".input-update-mes").show();
                }
            }
        });
    },
    addImages: function() {
        if (typeof galleryCnuVM.request !== undefined && galleryCnuVM.request) {
            galleryCnuVM.request.abort();
            delete galleryCnuVM.request;
        }

        var images = [];

        if (uploadDialogVM.screens !== undefined) {
            for (var i = 0; i < uploadDialogVM.screens.length; i++) {
                var url = uploadDialogVM.screens[i].url;
                var thumbnailUrl = uploadDialogVM.screens[i].thumbnailUrl;
                var image = {
                    url: url,
                    thumbnailUrl: thumbnailUrl
                };
                images.push(image);
            }
        }

        $(".loading-dialogform").show();
        galleryCnuVM.request = $.ajax({
            url: '/system/gallery/async-create-images',
            type: "post",
            dataType: "json",
            data: {
                id: galleryCnuVM.gallery.id,
                images: images
            },
            error: function() {
                $(".loading-dialogform").hide();
            },
            beforeSend: function() {
                // do smth before sending
            },
            complete: function() {
                // do smth when complete action
            },
            success: function(data) {
                $(".loading-dialogform").hide();
                if (data.status) {
                    for (var i = 0; i < data.images.length; i++) {
                        galleryCnuVM.images.push(data.images[i]);
                    }
                    galleryCnuVM.updateValue("images", galleryCnuVM.images);
                    galleryCnuVM.refreshGui();
                } else {

                }
            }
        });
    },
    refreshGui: function() {
        // resign images
        var widthParent = $(".gallery-thumbnail .thumbnail-link").width();
        $(".gallery-thumbnail .thumbnail-link").height(widthParent - 40);
        var heightParent = $(".gallery-thumbnail .thumbnail-link").height();
        $(".gallery-thumbnail .thumbnail-link").find(".thumbnail-img").each(function() {
            resizeImages($(this), widthParent, heightParent);
        });
    }
};

$(document).ready(function() {
    galleryCnuVM.init();
});