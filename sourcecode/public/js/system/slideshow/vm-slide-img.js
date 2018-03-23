// manage album presentation
var presentAlbum = {
    selIndex: 0,
    slideNum: 0,
    slideWidth: 0,
    albums: [],
    isSlideDes: false,
    autoTiming: null,
    tmpl: {
        SLIDES: '<div class="slide">' +
                '<div class="slide-des"></div>' +
                '<div class="slide-img">' +
                '<img src="" />' +
                '</div>' +
                '</div>'
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
        $(".slides").stop(true, false).animate({'left': '-' + presentAlbum.slideWidth * index + 'px'}, 300);
    },
    auto: function() {
        presentAlbum.autoTiming = setInterval(function() {
            presentAlbum.autoShow();
        }, 3000);
    },
    stop: function() {
        clearInterval(presentAlbum.autoTiming);
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
        if (album != null && album.length > 0) {
            $(".slides").empty();
            for (var i = 0; i < album.length; i++) {
                var jObj = $(presentAlbum.tmpl.SLIDES);
                jObj.find("img").attr("src", album[i].src);
                jObj.find(".slide-des").text(album[i].des);
                $(".slides").append(jObj);
            }
            presentAlbum.resizeImages();
        }
    },
    resizeImages: function() {
        // define params of slideshow
        var width = $(".slide-panel").width();
        var height = $(".slide-panel").height();
        if (presentAlbum.isSlideDes) {
            $(".slide").width(width);
            $(".slide").height(height - 20);
            $(".slide-img").width(width - 200);
            $(".slide-img").height(height - 36);
            $(".slide-des").height(height - 36);
        } else {
            $(".slide").width(width);
            $(".slide").height(height);
            $(".slide-img").width(width);
            $(".slide-img").height(height);
        }

        this.slideNum = $(".slides .slide").length - 1 || 0;
        this.slideWidth = $(".slides .slide").width() || 0;
        $(".slides").css('width', presentAlbum.slideWidth * (presentAlbum.slideNum + 1) + 'px');
        // resize image

        var imgWith = $(".slides .slide-img").width(),
                imgHeight = $(".slides .slide-img").height();

        $(".slide img").each(function() {
            var width = $(this).width(),
                    height = $(this).height();
            if (width > imgWith) {
                if (height > imgHeight) {
                    if (width / imgWith > height / imgHeight) {
                        $(this).width(imgWith);
                        $(this).css("margin-top", (imgHeight - height) / 2 + "px");
                        $(this).css("height", "auto");
                    } else {
                        $(this).height(imgHeight);
                        $(this).css("width", "auto");
                    }
                } else {
                    $(this).width(imgWith);
                    $(this).css("height", "auto");
                    $(this).css("margin-top", (imgHeight - height) / 2 + "px");
                }
            } else {
                if (height > imgHeight) {
                    $(this).height(imgHeight);
                    $(this).css("width", "auto");
                } else {
                    $(this).css("margin-top", (imgHeight - height) / 2 + "px");
                }
            }
        });
    }
};

$(document).ready(function() {
    presentAlbum.init();
});
