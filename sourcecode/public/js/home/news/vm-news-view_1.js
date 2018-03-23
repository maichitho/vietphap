var newsVM = {
    namespace: "newsVM",
    entry: {},
    suggestionEntries: [],
    tags: [],
    init: function() {
        if (newsVM.entry && newsVM.entry.tags) {
            this.updateTag(newsVM.entry.tags);
        }
    },
    registerChanel: function() {

    },
    updateTag: function(str) {
        if (str != "") {
            var tagArr = str.split(",");
            for (var i = 0; i < tagArr.length; i++) {
                var tag = {
                    name: tagArr[i]
                };
                newsVM.tags.push(tag);
            }
        }

        if (newsVM.tags.length > 0) {
            newsVM.updateValue("tags", newsVM.tags);
        }
    }
};

$(document).ready(function() {
    newsVM.init();
});

// Thấy tinh nguyên, giữ mộc mạc, bớt suy nghĩ, giảm ham muốn - Lão tử
