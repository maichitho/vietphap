// manage album presentation
var testimonilaSlideVM = {
    selIndex: 0,
    slideNum: 0,
    slideWidth: 0,
    albums: [],
    isSlideDes: false,
    autoTiming: null,
    tmpl: {
        SLIDES: '<div class="slide">' +
                '<div class="slide-image"><div class="circle"><img /></div></div><div class="slide-panel">' +
                '<div class="slide-des"></div>' +
                '<div class="slide-author"></div>' +
                '</div></div>'
    },
    init: function() {
    },
    next: function() {
        if (this.selIndex < this.slideNum) {
            this.selIndex++;
            this.goto(this.selIndex);
        }
    },
    back: function() {
        if (this.selIndex > 0) {
            this.selIndex--;
            this.goto(this.selIndex);
        }
    },
    goto: function(index) {
        this.selIndex = index;
        $(".head-news .slides").stop(true, false).animate({'left': '-' + testimonilaSlideVM.slideWidth * index + 'px'}, 300);
    },
    auto: function() {
        testimonilaSlideVM.autoTiming = setInterval(function() {
            testimonilaSlideVM.autoShow();
        }, 3000);
    },
    stop: function() {
        clearInterval(testimonilaSlideVM.autoTiming);
    },
    autoShow: function() {
        if (this.selIndex < this.slideNum) {
            this.selIndex++;
            this.goto(this.selIndex);
        } else {
            this.selIndex = 0;
            this.goto(this.selIndex);
        }
    },
    loadSlides: function(album) {
        if (album !== null && album.length > 0) {
            $(".head-news .slides").empty();
            for (var i = 0; i < album.length; i++) {
                var jObj = $(testimonilaSlideVM.tmpl.SLIDES);
                if (album[i].company !== '') {
                    jObj.find(".slide-author").html(album[i].author + " - " + album[i].company);
                } else {
                    jObj.find(".slide-author").html(album[i].author);
                }
                jObj.find(".slide-des").html("&#171; " + album[i].des + " &#187;");
                jObj.find(".slide-image img").attr("src", album[i].src);
                jObj.find(".slide-image img").attr("alt", "");
                $(".head-news .slides").append(jObj);
            }

            $(".head-news .slides .slide").width($(".head-news").width() - 20);
            $(".head-news .slides .slide").height(80);
            testimonilaSlideVM.slideNum = album.length - 1;
            testimonilaSlideVM.slideWidth = $(".head-news").width();
            $(".head-news .slides").css('width', testimonilaSlideVM.slideWidth * (testimonilaSlideVM.slideNum + 1) + 'px');
            $(".head-news .slides .slide").mouseenter(function() {
                testimonilaSlideVM.stop();
            }).mouseleave(function() {
                testimonilaSlideVM.auto();
            });

        }
    }
};

$(document).ready(function() {
    testimonilaSlideVM.init();
});
