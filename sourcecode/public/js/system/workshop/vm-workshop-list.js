var workshopListVM = {
    namespace: "workshopListVM",
    createTimeFrom: " ",
    createTimeTo: " ",
    delObj: {},
    delId:"",
    workshops: [],
    keyword: {},
    init: function() {
        // init datepicker
        $(".input-date").each(function() {
            $(this).datepicker({
                dateFormat: workshopListVM.messages["dateFormat"],
                altField: "#" + $(this).attr("altField"),
                altFormat: "@"
            });
        });        
    },
    registerChanel: function() {

    },
    showDeleteDialog: function(dom,id) {
        workshopListVM.delObj = $(dom).parent().parent().parent().parent();
        workshopListVM.delId = id;
        confirmDialog.show({
            title: workshopListVM.messages['deleteTitle'],
            info: workshopListVM.messages['deleteWorkshop'],
            callbackFn: workshopListVM.deleteWorkshop
        });
    },
    deleteWorkshop: function() {
        $.ajax({
            url: "/system/workshop/delete",
            type: "post",
            dataType: "json",
            data: {
                id: workshopListVM.delId
            },
            error: function() {
                confirmDialog.hide();
                $("#alert_error").show();
            },
            beforeSend: function() {
            // do smth before sending
            },
            complete: function() {
            // do smth when complete action
            },
            success: function(data) {
                if (data.status) {
                    workshopListVM.delObj.remove();
                    confirmDialog.hide();
                    $("#alert_success").show();
                } else {
                    confirmDialog.hide();
                    $("#alert_error").show();
                }
            }
        });
    }
};

$(document).ready(function() {
    workshopListVM.init();
});