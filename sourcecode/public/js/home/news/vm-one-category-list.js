var oneCategoryListVM = {
    namespace: "oneCategoryListVM",
    entries: [],
    categoryName: {},
    categoryId: 0,
    category: {},
    init: function() {
        $(".f-experience-item").mouseover(function() {
            $(this).addClass("f-experience-item--active");
        }).mouseout(function() {
            $(this).removeClass("f-experience-item--active");
        });

    }
};

$(document).ready(function() {
    oneCategoryListVM.init();
});

// Thấy tinh nguyên, giữ mộc mạc, bớt suy nghĩ, giảm ham muốn - Lão tử
