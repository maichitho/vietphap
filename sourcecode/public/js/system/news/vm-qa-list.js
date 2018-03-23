var qasVM = {
    namespace: "qasVM",
    qas: [],
    categories: [],
    //companyCategoryId: {},
    createTimeFrom: " ",
    createTimeTo: " ",
    delObj: {},
    delId:"",
    selectedCateId: {},
    keyword: {},
    asker: {},
    askerEmail: {},
    init: function() {
        // init datepicker
        $(".input-date").each(function() {
            $(this).datepicker({
                dateFormat: qasVM.messages["dateFormat"],
                altField: "#" + $(this).attr("altField"),
                altFormat: "@"
            });
        });        
        
        $('.chk-set-display').click(function(){
            var id = $(this).attr('data-id');
            $('#img-loading-'+id).show();
            
            var param ={
                id: id,
                display: $(this).prop('checked')? '1':'0'
            };
            
            $.post('/system/news/async-update-display-status', param, function(data) {
                if (data.success) {                    
                }
                else{
                    alert(qasVM.messages['errorUpdateDisplayStatus']);
                }
            }, "json")
            .complete(function() {
                $('#img-loading-'+id).hide();
            });
        });
    },
    registerChanel: function() {

    },
    showDeleteDialog: function(dom,id) {
        qasVM.delObj = $(dom).parent().parent().parent().parent();
        qasVM.delId = id;
        confirmDialog.show({
            title: qasVM.messages['deleteTitle'],
            info: qasVM.messages['deleteNews'],
            callbackFn: qasVM.deleteEntry
        });
    },
    deleteEntry: function() {
        $.ajax({
            url: "/system/news/delete",
            type: "post",
            dataType: "json",
            data: {
                id: qasVM.delId
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
                    qasVM.delObj.remove();
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
    qasVM.init();
});