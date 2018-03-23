var categoryListVM = {
    namespace: "categoryListVM",
    hotEntries: [],
    topEntry: {},
    entries: [],
    categories: [],
    subCateList: [],
    testimonials: [],
    indexTop: 0,
    autoTiming: {},
    init: function() {

//        //load testimonial 
//        var album = categoryListVM.testimonials;
//        testimonilaSlideVM.loadSlides(album);
//        testimonilaSlideVM.auto();
//        alert(83);
        $("#headlight_list .thumbnail-link").height(83);
//        $("#headlight_list .thumbnail").height(121);

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

        categoryListVM.autoTiming = setInterval(function() {
            categoryListVM.showslideHeadlight();
        }, 5000);
    },
    showslideHeadlight: function() {
        if (categoryListVM.indexTop < categoryListVM.hotEntries.length - 1) {
            categoryListVM.indexTop++;
        } else {
            categoryListVM.indexTop = 0;
        }

        categoryListVM.topEntry = categoryListVM.hotEntries[categoryListVM.indexTop];

        $(".headlight-box").find("img").fadeTo(500, 0)
                .queue(function() {
                    categoryListVM.updateValue("topEntry", categoryListVM.topEntry);
                    $(".headlight-box").find("img").fadeTo(2000, 1);
                    $(this).dequeue();
                });

        $(".headlight-box img").fadeIn("slow");
        $(".hot-entry").removeClass("hot-entry--active");
        $(".hot-entry").eq(categoryListVM.indexTop).addClass("hot-entry--active");
    }
};

$(document).ready(function() {
    categoryListVM.init();
});

