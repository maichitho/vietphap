var workshopVM = {
    namespace: "workshopVM",
    workshop: [],
    type: {},
    workshopId: {},
    init: function() {
        // init datepicker
        $(".input-date").each(function() {
            $(this).datepicker({
                dateFormat: workshopVM.messages["dateFormat"],
                altField: "#" + $(this).attr("altField"),
                altFormat: "@"
            });
        });
        
        $("#news_content").height(400);
    },
    validateInput: function(){
        if($("#title").val().length <= 0){
            return false;
        }
        $('#isTopValue').val($('#chk-isTop').prop('checked')? '1':'');
        return true;
    },
    insertImages: function() {
        if (uploadDialogVM.screens !== undefined) {
            for (var i = 0; i < uploadDialogVM.screens.length; i++) {
                var url = uploadDialogVM.screens[i].url;
                var imageHtml = "<br/><img src='" + url + "' style='width:60%; position:relative; margin: 5px auto;'/>";
                CKEDITOR.instances["news_content"].insertHtml(imageHtml);
            }
        }
    }
};

$(document).ready(function() {
    workshopVM.init();
});