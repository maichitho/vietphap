var mediaListVM = {
    namespace: "mediaListVM",
    entries: [],
    init: function() {
        $(".f-news-item").mouseover(function() {
            $(this).addClass("f-news-item--active");
        }).mouseout(function() {
            $(this).removeClass("f-news-item--active");
        });

    }
};

$(document).ready(function() {
    mediaListVM.init();
});

// Thấy tinh nguyên, giữ mộc mạc, bớt suy nghĩ, giảm ham muốn - Lão tử
