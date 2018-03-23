var qaCnuVM = {
    namespace: "qaCnuVM",
    categories: [],
    entry: [],
    entryId: {},
    type: {},
    isCheckedUrl: false,
    oldRewriteUrl: "",
    init: function() {
        $("#news_content").height(600);
        qaCnuVM.updateData();

        if (qaCnuVM.type != "create") {
            qaCnuVM.isCheckedUrl = true;
            qaCnuVM.oldRewriteUrl = qaCnuVM.entry.rewriteUrl;
        }

        // check rewrite url
        $("#rewriteUrl").blur(function() {
            var url = $(this).val();
            if (!url) {

            } else {
                if (qaCnuVM.type != "create") {
                    if (url != newsCnuVM.oldRewriteUrl) {
                        qaCnuVM.checkRewriteUrl(url);
                    } else {
                        qaCnuVM.isCheckedUrl = true;
                    }
                } else {
                    qaCnuVM.checkRewriteUrl(url);
                }
            }
        });


        // render rewrite url automatically
        $("#title").keyup(function() {
            var title = $(this).val();
            if (!title) {

            } else {
                $("#rewriteUrl").val(change_alias(title));
            }
        }).blur(function() {
            $("#rewriteUrl").trigger("blur");
        });
    },
    updateData: function() {
        if (qaCnuVM.entry.categoryId > 0) {
            $("#categoryId").val(qaCnuVM.entry.categoryId);
        }
        CKEDITOR.instances["news_content"].setData(qaCnuVM.entry.content);
    },
    registerChanel: function() {

    },
    validateInput: function() {
        if ($("#title").val().length <= 0 || $("#asker").val().length <= 0) {
            return false;
        }
        $('#idTopValue').val($('#chk-isTop').prop('checked') ? '1' : '');
        $('#displayValue').val($('#chk-display').prop('checked') ? '1' : '');
        return true && qaCnuVM.isCheckedUrl;
    },
    checkRewriteUrl: function(rewriteUrl) {
        qaCnuVM.request = $.ajax({
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
                        title: qaCnuVM.messages['titleAlert'],
                        info: qaCnuVM.messages['rewriteUrlError'],
                        isAlert: true
                    });
                    qaCnuVM.isCheckedUrl = false;
                } else {
                    qaCnuVM.isCheckedUrl = true;
                }
            }
        });
    }
};

$(document).ready(function() {
    qaCnuVM.init();
});