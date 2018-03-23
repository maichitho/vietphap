var catalogSlideVM = {
    namespace: "catalogSlideVM",
    isShowing: 0,
    selIndex: 0,
    slideNum: 0,
    catalogImages: [],
    init: function() {

        //slide show catalog
        var widthSlide = 298;
        $(".slides-banner").width(widthSlide).css("overflow", "hidden");
        $(".slides-banner").height(197);
        $(".slides-banner .slide").width(widthSlide);
        $(".slides-banner .slide").height(197);
         $(".slides-banner .slide-image").width(widthSlide);
        $(".slides-banner .slide-image").height(197);
        $(".slides-banner .slide:first").show();

        $(".slides-banner").find(".slide").css("float", "none").css("position", "absolute");

        this.slideNum = $(".slides-banner").find(".slide").length - 1;

        $("#catalog_slide").find(".slide-img").each(function(e) {
            resizeImages($(this), 298, 197);
        });
        // slide control
        catalogSlideVM.slideWidth = widthSlide;
        $("#next_slide").click(function() {
            catalogSlideVM.next();
        });

        $("#back_slide").click(function() {
            catalogSlideVM.back();
        });

        $(".slides-banner").mouseover(function() {
            catalogSlideVM.stop();
        }).mouseout(function() {
            catalogSlideVM.auto();
        });

        $(".right-button").mouseover(function() {
            catalogSlideVM.stop();
        }).mouseout(function() {
            catalogSlideVM.auto();
        });

        $(".left-button").mouseover(function() {
            catalogSlideVM.stop();
        }).mouseout(function() {
            catalogSlideVM.auto();
        });

        catalogSlideVM.auto();
    },
    next: function() {
        if (this.selIndex < this.slideNum) {
            catalogSlideVM.isShowing = this.selIndex;
            this.selIndex++;
            this.goto(this.selIndex);
        }
    },
    back: function() {
        if (this.selIndex > 0) {
            catalogSlideVM.isShowing = this.selIndex;
            this.selIndex--;
            this.goto(this.selIndex);
        }
    },
    goto: function(index) {
        this.selIndex = index;
        $(".slides-banner").find(".slide").eq(catalogSlideVM.isShowing).fadeTo("fast", 0.3)
                .queue(function() {
                    $(".slides-banner").find(".slide").eq(index).fadeTo(2000, 1);
                    $(this).dequeue();
                     resizeImages($(".slides-banner").find(".slide").eq(index).find(".slide-img"), 300, 200);
                })
                .fadeTo("slow", 0);
    },
    auto: function() {
        catalogSlideVM.autoTiming = setInterval(function() {
            catalogSlideVM.autoShow();
        }, 3000);
    },
    stop: function() {
        clearInterval(catalogSlideVM.autoTiming);
    },
    autoShow: function() {
        if (this.selIndex < this.slideNum) {
            catalogSlideVM.isShowing = this.selIndex;
            this.selIndex++;
            this.goto(this.selIndex);
        } else {
            catalogSlideVM.isShowing = this.slideNum;
            this.selIndex = 0;
            this.goto(this.selIndex);
        }
    }
};

$(document).ready(function() {
    catalogSlideVM.init();
});

