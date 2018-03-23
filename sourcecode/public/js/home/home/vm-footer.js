var footerVM = {
    namespace: "footerVM",
    footerMenus: [],
    footerRootMenus: {},
    footerList1: [],
    footerList2: [],
    footerList3: [],
    linkFb: {},
    linkGg: {},
    linkTw: {},
    fSlogan: {},
    footerAvatar: {},
    footerAvatarText: {},
    footerAvatarLink: {},
    init: function() {
        $("#footer-ul-menu > .menu-item").mouseover(function() {
            //        $(this).addClass("menu-item--active");
            var top = $(this).offset().top + 34;
            var left = $(this).offset().left;

            if ($(this).find(".popup-menu").length > 0) {
                $(this).find(".popup-menu").show().offset({
                    top: top,
                    left: left
                });
                //$(this).find(".popup-menu").show();
            }
        }).mouseout(function() {
            //        $(this).removeClass("menu-item--active");
            if ($(this).find(".popup-menu").length > 0) {
                $(this).find(".popup-menu").hide();
            }
        });

        $(".popup-menu").mouseout(function() {
            $(this).hide();
        });

        footerVM.menus = prepareArrayByColumn(footerVM.footerRootMenus, 3);
        footerVM.footerList1 = footerVM.menus[0];
        footerVM.footerList2 = footerVM.menus[1];
        footerVM.footerList3 = footerVM.menus[2];
        footerVM.updateValue("footerList1", footerVM.footerList1);
        footerVM.updateValue("footerList2", footerVM.footerList2);
        footerVM.updateValue("footerList3", footerVM.footerList3);
    },
    getChidNodes: function(parentId) {
        var result = [];

        for (var i = 0; i < footerVM.footerMenus.length; i++) {
            if (footerVM.footerMenus[i].parentId === parentId)
                result.push(footerVM.footerMenus[i]);
        }
        return result;
    }
};

$(document).ready(function() {
    footerVM.init();
});

