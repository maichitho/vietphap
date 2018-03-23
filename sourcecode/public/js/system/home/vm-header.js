var headerVM = {
    namespace: "headerVM",
    init: function() {
        var isShow = false;
        $(".f-menu-hr .menu-item").mouseover(function() {
            $(".f-menu-hr").find(".f-popup-menu").hide();
            $(this).siblings().removeClass("menu-item--active");
            $(this).addClass("menu-item--active");
            $(this).find(".f-popup-menu").show();
        }).mouseout(function() {
            if (!isShow) {
                $(this).find(".f-popup-menu").hide();
            }
            $(this).removeClass("menu-item--active");
            isShow = false;
        }).mouseleave(function() {

            });

        $(".f-popup-menu").mouseover(function() {
            $(this).show();
            isShow = true;
        }).mouseout(function() {
            if (!isShow) {
                $(this).hide();
                isShow = false;
            }

        });

        $(".f-popup-top-layer").mouseover(function() {
            isShow = true;
            $(this).closest(".f-popup-menu").show();
        });
        
        $(".user-login-name").click(function() {
            $(this).parent().find(".menu-dropdown").show();
        });

        $(".content").click(function() {
            $(".user-login-name").parent().find(".menu-dropdown").hide();
        });
    }
};

$(document).ready(function() {
    headerVM.init();
});