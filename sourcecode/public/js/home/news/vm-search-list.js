var searchListVM = {
    namespace: "searchListVM",
    entries: [],
    keyword: {},
    resultCount: {},
    init: function() {
        $(".f-qa-item").mouseover(function() {
            $(this).addClass("f-qa-item--active");
        }).mouseout(function() {
            $(this).removeClass("f-qa-item--active");
        });
    }
};

$(document).ready(function() {
    searchListVM.init();
});

// Thấy tinh nguyên, giữ mộc mạc, bớt suy nghĩ, giảm ham muốn - Lão tử
