var headerVM = {
    namespace: "headerVM",
    headerMenus: [],
    headerRootMenus: [],
    leftMenus: [],
    rightMenus: [],
    navigations: [],
    hotline: {},
    email: {},
    companyLogo: {},
    companySlogan: {},
    companySloganMobile: {},
    bannerImage: {},
    companyLogoMobile: {},
    isHome: true,
    init: function() {
        var isShow = false;
        var selectedMenu = $(".f-menu-hr .menu-item-selected");

        if (!headerVM.isHome) {
            $(".f-toolbar-layer").show();
        } else {
            $(".f-toolbar-layer").hide();
        }

        $(".f-menu-hr .menu-item").mouseover(function() {
            $(".f-menu-hr").find(".f-toolbar-menu").hide();
            $(this).siblings().removeClass("menu-item--active");
            $(this).addClass("menu-item--active");
            $(this).find(".f-toolbar-menu").show();
            $(".f-bar-layer").show();
            $(".f-toolbar-layer").show();
        }).mouseout(function() {
            if (!isShow) {
                $(this).find(".f-toolbar-menu").hide();
                selectedMenu.find(".f-toolbar-menu").show();
            }
            $(this).removeClass("menu-item--active");
            isShow = false;
            if ($(".f-menu-hr > ul:first > li:first").hasClass("menu-item-selected")) {
                $(".f-bar-layer").hide();
                $(".f-toolbar-layer").hide();
            }
        }).mouseleave(function() {

        });

        $(".f-toolbar-menu .menu-hr").mouseover(function() {
            $(this).parent().show();
            isShow = true;
        }).mouseout(function() {
            if (!$(this).parent().hasClass("is-show")) {
                $(this).parent().hide();
                isShow = false;
                selectedMenu.find(".f-toolbar-menu").show();
            }
        });

        var isSearh = false;
        $(".f-search-box").click(function(e) {
            $(".f-header").find(".f-toolbar-menu").hide();
            e.stopPropagation();
        });

        $(".f-search").click(function(e) {
            $(".f-search-box").toggle("slow");
            $("#search_input").focus();
            isSearh = !isSearh;
            e.stopPropagation();
        });

        $(document).click(function() {
            $(".f-search-box").hide();
            $(".f-header").find(".f-toolbar-menu").hide();
        });


        $(".f-popup-mobile .f-qa-question").click(function() {
            $(this).closest(".f-qa-item").find(".f-qa-answer").slideToggle();
        });

        $(".f-menu-mobile").click(function() {
            $(".f-popup-mobile").toggle();
        });
    },
    getChidNodes: function(parentId) {
        var result = [];
        for (var i = 0; i < headerVM.headerMenus.length; i++) {
            if (headerVM.headerMenus[i].parentId == parentId)
                result.push(headerVM.headerMenus[i]);
        }
        return result;
    }
};

$(document).ready(function() {
    headerVM.init();
});

