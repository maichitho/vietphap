var homeVM = {
    namespace: "homeVM",
    headerTreeMenus: [],
    footerTreeMenus: [],
    init: function() {
        $('.tr-tree-menu').hover(function(){
            $(this).find('.img-tree-menu').show();
            $(this).css('background-color', '#F8F8F8');
        },  function() {
            $(this).find('.img-tree-menu').hide();
            $(this).css('background-color', 'white');
        })
        .click(function(){
            window.location.href = "/system/setting/update-menu?id="+$(this).attr('data-id');
        });
    }
};

$(document).ready(function() {
    homeVM.init();
});