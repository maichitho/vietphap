/**
 * 
 * For MultiFile Upload Dialog
 * 
 * @html: components/dialog-multi-upload.phtml
 * @type type
 */
var uploadDialogVM = {
    namespace: "uploadDialogVM",
    screens: [],
    options: {}, // title, callbackFn
    ratio: 1.3,
    messages: {},
    index: -1,
    init: function() {

    },
    registerChanel: function() {

    },
    showDialog: function() {
        if (uploadDialogVM.options.title !== undefined) {
            $("#dialog_multi_upload .dialog-title").text(uploadDialogVM.options.title);
        }

        // binding events and run call back
        $("#dialog_insert").off().on("click", function() {
            if (uploadDialogVM.options.callbackFn !== undefined && typeof uploadDialogVM.options.callbackFn === "function") {
                uploadDialogVM.options.callbackFn.apply(this, [uploadDialogVM.screens]);
            }
            $('#dialog_multi_upload').hideDialog();
            uploadDialogVM.screens = [];
            uploadDialogVM.index = 0;
        });

        $("#dialog_cancel").off().on("click", function() {
            if (uploadDialogVM.screens.length > 0) {
                confirmDialog.show({
                    title: uploadDialogVM.messages.titleAlert,
                    info: uploadDialogVM.messages.cancelUpload,
                    callbackFn: uploadDialogVM.removeFileUpload
                });
            } else {
                uploadDialogVM.screens = [];
                uploadDialogVM.index = 0;
                $('#dialog_multi_upload').hideDialog();
                $("table tbody.files").empty();
                if (jqXHRUpload != null) {
                    jqXHRUpload.abort();
                }
            }
        });

        // show dialog
        uploadDialogVM.resize();
        $('#dialog_multi_upload').showDialog({width: '600px'});
        uploadDialogVM.index = 0;
    },
    hideDialog: function() {
        $("#dialog_multi_upload").hideDialog();
        uploadDialogVM.screens = [];
        uploadDialogVM.index = 0;
    },
    resize: function() {
        var height = $(window).height() - 100;
        $("#dialog_multi_upload").height(height);
        $("#dialog_multi_upload").width(height * uploadDialogVM.ratio);
        $("#dialog_multi_upload .dialog-header").width(height * uploadDialogVM.ratio + 2);
        $("#dialog_multi_upload .dialog-content").height(height - 100);
    },
    showUploadError: function() {
        confirmDialog.show({
            title: uploadDialogVM.messages.titleAlert,
            info: uploadDialogVM.messages.uploadError,
            isAlert: true
        });
    },
    showSelectError: function() {
        confirmDialog.show({
            title: uploadDialogVM.messages.titleAlert,
            info: uploadDialogVM.messages.selectError,
            isAlert: true
        });
    },
    removeFileUpload: function() {
        if (typeof uploadDialogVM.request !== undefined && uploadDialogVM.request) {
            uploadDialogVM.request.abort();
            delete uploadDialogVM.request;
        }
        uploadDialogVM.request = $.ajax({
            url: "/system/file/async-delete-files",
            type: "post",
            dataType: "json",
            data: {
                screens: uploadDialogVM.screens
            },
            error: function() {
//                confirmDialog.showError();
                uploadDialogVM.screens = [];
                uploadDialogVM.index = 0;
            },
            beforeSend: function() {
                // do smth before sending
            },
            complete: function() {
                // do smth when complete action
            },
            success: function(data) {
                uploadDialogVM.screens = [];
                uploadDialogVM.index = 0;
                if (data.status) {
                    $('#dialog_multi_upload').hideDialog();
                    $("table tbody.files").empty();
                    confirmDialog.hide();
                    if (jqXHRUpload != null) {
                        jqXHRUpload.abort();
                    }
                } else {

                }
            }
        });

    }
};

$(document).ready(function() {
    uploadDialogVM.init();
});