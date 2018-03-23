var entryVM = {
    namespace: "entryVM",
    entry: {},
    categoryName: {},
    suggestionEntries: [],
    tags: [],
    innerRightMenus: [],
    init: function() {
        $(".f-news-relative-item").mouseover(function() {
            $(this).addClass("f-news-relative-item--active");
        }).mouseout(function() {
            $(this).removeClass("f-news-relative-item--active");
        });
         $(".f-experience-item").mouseover(function() {
            $(this).addClass("f-experience-item--active");
        }).mouseout(function() {
            $(this).removeClass("f-experience-item--active");
        });
        
         if (entryVM.entry && entryVM.entry.tags) {
            this.updateTag(entryVM.entry.tags);
        }
    },
    updateTag: function(str) {
        if (str != "") {
            var tagArr = str.split(",");
            for (var i = 0; i < tagArr.length; i++) {
                var tag = {
                    name: tagArr[i]
                };
                entryVM.tags.push(tag);
            }
        }

        if (entryVM.tags.length > 0) {
            entryVM.updateValue("tags", entryVM.tags);
        }
    }
};

$(document).ready(function() {
    entryVM.init();
});

// Thấy tinh nguyên, giữ mộc mạc, bớt suy nghĩ, giảm ham muốn - Lão tử
