var qaListVM = {
    namespace: "qaListVM",
    qas: [],
    pageId: 0,
    showMoreLink: {},
    categoryName: {},
    categoryId: 0,
    category: {},
    resultCount: {},
    keyword: {},
    init: function() {
        $(".f-qa-item").mouseover(function() {
            $(this).addClass("f-qa-item--active");
        }).mouseout(function() {
            $(this).removeClass("f-qa-item--active");
        });

        $(".f-qa-question").click(function() {
            if ($(this).find(".f-icon").hasClass("f-collapse-down")) {
                $(this).find(".f-icon").removeClass("f-collapse-down");
                $(this).find(".f-icon").addClass("f-collapse-up");
            } else {
                $(this).find(".f-icon").removeClass("f-collapse-up");
                $(this).find(".f-icon").addClass("f-collapse-down");
            }
            $(this).closest(".f-qa-item").find(".f-qa-answer").slideToggle();
        });

        $("#qa_post").autogrow();
    },
    loadMore: function() {
        qaListVM.pageId = qaListVM.pageId + 1;
        $('#img-loading-more').show();

        var param = {
            pageId: qaListVM.pageId
        };

        $.post('/home/qa/async-load-more-result', param, function(data) {
            if (data.success) {
                qaListVM.showMoreLink = data.showMoreLink;
                for (var i = 0; i < data.items.length; i++) {
                    qaListVM.qas.push(data.items[i]);
                }

                qaListVM.updateValue("qas", qaListVM.qas);
                if (qaListVM.showMoreLink == '0')
                    $('#div-load-more').hide();

                $(".f-qa-item").mouseover(function() {
                    $(this).addClass("f-qa-item--active");
                }).mouseout(function() {
                    $(this).removeClass("f-qa-item--active");
                });

                $(".f-qa-question").click(function() {
                    if ($(this).find(".f-icon").hasClass("f-collapse-down")) {
                        $(this).find(".f-icon").removeClass("f-collapse-down");
                        $(this).find(".f-icon").addClass("f-collapse-up");
                    } else {
                        $(this).find(".f-icon").removeClass("f-collapse-up");
                        $(this).find(".f-icon").addClass("f-collapse-down");
                    }
                    $(this).closest(".f-qa-item").find(".f-qa-answer").slideToggle();
                });
            }
            else {
                alert(qaListVM.messages['errorUpdateDisplayStatus']);
            }
        }, "json")
                .complete(function() {
                    $('#img-loading-more').hide();
                });
    },
    addQa: function() {
        if ($('#qa_post').val().length <= 0 || $('#title').val().length <= 0
                || $('#asker').val().length <= 0) {
            return false;
        }

        $('#img-loading-insert').show();

        var param = {
            title: $('#title').val(),
            description: $('#qa_post').val(),
            asker: $('#asker').val(),
            askerEmail: $('#askerEmail').val()
        };

        $.post('/tao-qa', param, function(data) {
            if (data.success) {
                confirmDialog.show({
                    title: qaListVM.messages['titleAlert'],
                    info: qaListVM.messages['successInsertQa'],
                    isAlert: true
                });
                $('#form-add-qa').get(0).reset();
            }
            else {
                confirmDialog.show({
                    title: qaListVM.messages['titleAlert'],
                    errorMessage: qaListVM.messages['errorInsertQa'],
                    isAlert: true
                });
                confirmDialog.showError();
            }
        }, "json")
                .complete(function() {
                    $('#img-loading-insert').hide();
                });

        return false;
    },
    shareFacebook: function(title, url) {
        window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url) + '&t=' + encodeURIComponent(title));
        return false;
    },
    shareGoogle: function(title, url) {
        window.open('https://plus.google.com/share?url=' + encodeURIComponent(url));
        return false;
    },
    shareTwitter: function(title, url) {
        window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(title) + ':%20' + encodeURIComponent(url));
        return false;
    }
};

$(document).ready(function() {
    qaListVM.init();
});

// Thấy tinh nguyên, giữ mộc mạc, bớt suy nghĩ, giảm ham muốn - Lão tử
