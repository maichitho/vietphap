var homeVM = {
    namespace: "homeVM",
    hotEntries: [],
    topEntry: {},
    entries: [],
    mainCatMap: [],
    copd: [],
    hen: [],
    vpq: [],
    phongbenh: [],
    tintuc: [],
    hoidap: [],
    subCateList: [],
    testimonials: [],
    qas: [],
    direction: "left",
    index: 3,
    widthSlide: 700,
    autoTiming: {},
    moveScale: 125,
    init: function() {

//        //load testimonial 
//        var album = homeVM.testimonials;
//        testimonilaSlideVM.loadSlides(album);
//        testimonilaSlideVM.auto();
//        alert(83);
        $("#headlight_list .thumbnail-link").height(83);
//        $("#headlight_list .thumbnail").height(121);


        homeVM.widthSlide = $(".headlight-box").width();
        homeVM.moveScale = Math.round(homeVM.widthSlide / 5.5);
        $(".headlight-item").width(homeVM.widthSlide - 2 * homeVM.moveScale);
        $(".headlight-item-2").css("left", homeVM.moveScale + "px");
        $(".headlight-item-3").css("left", 2 * homeVM.moveScale + "px");

        $(".category-more").click(function() {
            if ($(this).find("span").hasClass("icon-down")) {
                $(this).closest(".category-header").css("height", "auto");
                $(this).find("span").removeClass("icon-down");
                $(this).find("span").addClass("icon-up");
            } else {
                $(this).closest(".category-header").css("height", "36px");
                $(this).find("span").addClass("icon-down");
                $(this).find("span").removeClass("icon-up");
            }
        });

        $(".js-category-main").mouseenter(function() {
            if ($(this).closest(".category-header").find(".category-more span").hasClass("icon-down")) {
                $(this).closest(".category-header").css("height", "auto");
                $(this).closest(".category-header").find(".category-more span").removeClass("icon-down");
                $(this).closest(".category-header").find(".category-more span").addClass("icon-up");
            }
        });

        $(".category-header").mouseleave(function() {
            if ($(this).find(".category-more span").hasClass("icon-up")) {
                $(this).css("height", "36px");
                $(this).find(".category-more span").addClass("icon-down");
                $(this).find(".category-more span").removeClass("icon-up");
            }
        });

////        $(".headlight-item").width($(".headlight-box").width() - 210);
//
        $(".headlight-item-1").find(".headlight-media img").addClass("slideGray");
        $(".headlight-item-2").find(".headlight-media img").addClass("slideGray");

        $(".headlight-item-1").mouseover(function() {
            clearInterval(homeVM.autoTiming);
            if (homeVM.direction === "right") {
                homeVM.direction = "left";
            }
            homeVM.toggleSlide(3, homeVM.widthSlide - homeVM.moveScale);
            homeVM.hideTitle(3);
            homeVM.toggleSlide(2, homeVM.widthSlide - 2 * homeVM.moveScale);
            homeVM.hideTitle(2);
            homeVM.showTitle(1);
            homeVM.index = 1;
        }).click(function() {
            clearInterval(homeVM.autoTiming);
        });

        $(".headlight-item-3").mouseover(function() {
            clearInterval(homeVM.autoTiming);
            if (homeVM.direction === "left") {
                homeVM.direction = "right";
            }
            homeVM.toggleSlide(2, homeVM.moveScale);
            homeVM.hideTitle(1);
            homeVM.toggleSlide(3, 2 * homeVM.moveScale);
            homeVM.hideTitle(2);
            homeVM.showTitle(3);
            homeVM.index = 3;
        }).click(function() {
            clearInterval(homeVM.autoTiming);
        });

        $(".headlight-box").mouseout(function() {
            homeVM.autoTiming = setInterval(function() {
                homeVM.showslideHeadlight();
            }, 4000);
        });

        $(".headlight-item-2").mouseover(function() {
            clearInterval(homeVM.autoTiming);
            if (homeVM.index == 1) {
                homeVM.direction = "right";
                homeVM.toggleSlide(2, homeVM.moveScale);
                homeVM.hideTitle(1);
                homeVM.showTitle(2);
            } else if (homeVM.index == 3) {
                homeVM.direction = "left";
                homeVM.toggleSlide(3, homeVM.widthSlide - homeVM.moveScale);
                homeVM.hideTitle(3);
                homeVM.showTitle(2);
            }
            homeVM.index = 2;
        }).click(function() {
            clearInterval(homeVM.autoTiming);
        });
//
//        $(".b-overlay").click(function() {
//            clearInterval(homeVM.autoTiming);
//            flag = true;
//        });

        homeVM.autoTiming = setInterval(function() {
            homeVM.showslideHeadlight();
        }, 4000);
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
                    title: "Thông báo",
                    info: "Gửi câu hỏi thành công!",
                    isAlert: true
                });
                $('#form-add-qa').get(0).reset();
            }
            else {
                confirmDialog.show({
                    title: "Thông báo",
                    errorMessage: "Xin lỗi bạn. Bạn hãy gọi cho chúng tôi để được tư vấn trực tiếp.",
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
    showslideHeadlight: function() {
        var marginLeft = 0;
        if (homeVM.direction == "right") {
            if (homeVM.index == 1) {
                marginLeft = homeVM.moveScale;
            } else if (homeVM.index == 2) {
                marginLeft = 2 * homeVM.moveScale;
            }
            homeVM.toggleSlide(homeVM.index + 1, marginLeft)
            homeVM.hideTitle(homeVM.index);
            homeVM.showTitle(homeVM.index + 1);

            if (homeVM.index < 2) {
                homeVM.index++;
            } else {
                homeVM.index = 3;
                homeVM.direction = "left";
            }
        } else {
            if (homeVM.index == 3) {
                marginLeft = homeVM.widthSlide - homeVM.moveScale;
            } else if (homeVM.index == 2) {
                marginLeft = homeVM.widthSlide - 2 * homeVM.moveScale;
            }
            homeVM.toggleSlide(homeVM.index, marginLeft);
            homeVM.hideTitle(homeVM.index);
            homeVM.showTitle(homeVM.index - 1);

            if (homeVM.index > 2) {
                homeVM.index--;
            } else {
                homeVM.index = 1;
                homeVM.direction = "right";
            }

        }

    },
    toggleSlide: function(index, marginLeft) {
        $(".headlight-item-" + index).animate(
                {left: marginLeft}, // what we are animating
        'slow', // how fast we are animating
                'swing', // the type of easing
                function() { // the callback

                });
    },
    hideTitle: function(index) {
        var selObj = $(".headlight-item-" + index);
        selObj.find(".headlight-title").fadeOut(
                'normal', // the type of easing
                function() { // the callback
                    selObj.find(".headlight-short-title").animate(
                            {marginLeft: 0}, // what we are animating
                    'slow', // how fast we are animating
                            'swing');
                });
        selObj.find(".headlight-media img").addClass("slideGray");
    },
    showTitle: function(index) {
        var selObj = $(".headlight-item-" + index);
        selObj.find(".headlight-short-title").animate(
                {marginLeft: -homeVM.moveScale}, // what we are animating
        'slow', // how fast we are animating
                'swing', function() {
            selObj.find(".headlight-title").fadeIn("slow");
        });
        selObj.find(".headlight-media img").removeClass("slideGray");
    }
};

$(document).ready(function() {
    homeVM.init();
});

