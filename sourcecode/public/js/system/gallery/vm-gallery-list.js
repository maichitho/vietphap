var galleryListVM = {
    namespace: "galleryListVM",
    delObj: {},
    delId: -1,
    galleries: [],
    init: function() {
       // resign images
        var widthParent = $(".gallery-thumbnail .thumbnail-link").width();
        $(".gallery-thumbnail .thumbnail-link").height(widthParent - 40);
        var heightParent = $(".gallery-thumbnail .thumbnail-link").height();
        $(".gallery-thumbnail .thumbnail-link").find(".thumbnail-img").each(function() {
            resizeImages($(this), widthParent, heightParent);
        });
    },
    registerChanel: function() {

    },
    showDeleteDialog: function(dom, id, url) {
        galleryListVM.delObj = $(dom).parent().parent();
        galleryListVM.delId = id;
        $(".dialog-content").height(50);
        confirmDialog.show({
            title: galleryListVM.messages['deleteTitle'],
            info: galleryListVM.messages['deleteGallery'],
            errorMessage: galleryListVM.messages['deleteGalleryError'],
            imageUrl: url,
            callbackFn: galleryListVM.deleteGallery
        });
    },
    deleteGallery: function() {
        $.ajax({
            url: "/system/gallery/async-delete",
            type: "post",
            dataType: "json",
            data: {
                id: galleryListVM.delId
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
                    for(var i=0; i< galleryListVM.galleries.length; i++){
                        if(galleryListVM.galleries[i].id == galleryListVM.delId){
                            galleryListVM.galleries.splice(i,1);
                        }
                    }
                    galleryListVM.delObj.remove();
                    galleryListVM.updateValue("galleries", galleryListVM.galleries);
                    confirmDialog.hide();
                } else {
                    confirmDialog.showError();
                }
            }
        });
    },
    showEditDialog: function() {
        $('#gallery_update_dialog').showDialog({
            width: '400px'
        });
    },
    updateGallery: function(obj) {
        var data = {
            id: screenManagerVM.project.id,
            name: $("#pro_name").val(),
            description: document.getElementById("pro_description").value,
            creatorId: screenManagerVM.project.creatorId,
            status: $(".project-status").attr("data-value"),
            createTime: screenManagerVM.project.createTime
        };
        $.ajax({
            url: "/system/gallery/async-update",
            type: "post",
            dataType: "json",
            data: data,
            error: function() {
                if (priority != 1) {
                    $(".loading-dialogform").hide();
                    $(".pro-error").show();
                } else {
                    $(".loading-pro").hide();
                    $(".dialog-content").height(50);
                    $(".menu-dropdown").attr("data-show", "false");
                    confirmDialog.show({
                        title: screenManagerVM.messages['updateProjectTitle'],
                        info: screenManagerVM.messages['updateProjectError'],
                        isAlert: true
                    });
                }
            },
            beforeSend: function() {
                // do smth before sending
            },
            complete: function() {
                // do smth when complete action
            },
            success: function(data) {
                if (data.status) {
                    if (priority != 1) {
                        $(".loading-dialogform").hide();
                        $(".pro-name").text($("#pro_name").val());
                        $(".pro-description").text(document.getElementById("pro_description").value);
                        screenManagerVM.project.name = $("#pro_name").val();
                        screenManagerVM.project.description = document.getElementById("pro_description").value;
                        $("#dialog_form").hideDialog();
                    } else {
                        $(".loading-pro").hide();
                        $(".menu-dropdown").attr("data-show", "false");
                    }
                } else {
                    if (priority != 1) {
                        $(".loading-dialogform").hide();
                        $(".pro-error").show();
                        $(".menu-dropdown").attr("data-show", "false");
                    } else {
                        $(".loading-pro").hide();
                        $(".dialog-content").height(50);
                        confirmDialog.show({
                            title: screenManagerVM.messages['updateProjectTitle'],
                            info: screenManagerVM.messages['updateProjectError'],
                            isAlert: true
                        });
                    }
                }
            }
        });

    }
};

$(document).ready(function() {
    galleryListVM.init();
});