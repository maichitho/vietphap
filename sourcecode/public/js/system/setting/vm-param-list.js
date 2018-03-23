var paramListVM = {
    namespace: "paramListVM",
    generalParams: [],
    imageParams: [],
    seoParams: [],
    snetworkParams: [],
    init: function() {
        $(".param-value").autogrow();
    },
    registerChanel: function() {

    },
    saveParam: function(id, key, dom) {
        var type = $(dom).attr('data-type');
        var value;
        if(type == paramListVM.messages['typeCheckbox']){
            value=$(dom).closest(".data-row").find(".param-value").is(':checked')?'1':'0';
        }
        else{
            value = $("#param_" + id).val();
            if (value === "") {
                confirmDialog.show({
                    title: paramListVM.messages['title'],
                    info: paramListVM.messages['paramRequired'],
                    isAlert: true
                });
                return;
            }
        }
        
        $(dom).closest(".data-row").find(".img-loading").show();
        $.post("/system/setting/async-update-param", {
            key: key,
            value: value
        }, function(data) {
            if (data.status) {                
                if(type == paramListVM.messages['typeHtml']){
                    $(dom).closest(".data-row").find(".div-demo").html(value);
                    $(dom).closest(".data-row").find(".param-value").hide();
                    $(dom).closest(".data-row").find(".div-demo").show();
                }
                $(dom).parent().parent().parent().find(".param-update").css("display", "table");
                $(dom).closest(".data-row").find(".param-value").prop('disabled', true);
                $(dom).parent().parent().hide();
            }
        }, "json")
        .complete(function() {
            $(dom).closest(".data-row").find(".img-loading").hide();
        });
    },
    cancelParam: function(dom, paramType ) {
        if(paramType == paramListVM.messages['pTypeGeneral']){
            paramListVM.updateValue("generalParams", paramListVM.generalParams);
        }
        if(paramType == paramListVM.messages['pTypeImage']){
            paramListVM.updateValue("imageParams", paramListVM.imageParams);
        }
        if(paramType == paramListVM.messages['pTypeSeo']){
            paramListVM.updateValue("seoParams", paramListVM.seoParams);
        }
        if(paramType == paramListVM.messages['pTypeSNetwork']){
            paramListVM.updateValue("snetworkParams", paramListVM.snetworkParams);
        }
        
        if($(dom).attr('data-type') == paramListVM.messages['typeHtml']){
            $(dom).closest(".data-row").find(".param-value").hide();
            $(dom).closest(".data-row").find(".div-demo").show();
        }
                
        $(dom).parent().parent().parent().find(".param-update").css("display", "table");
        $(dom).closest(".data-row").find(".param-value").prop('disabled', true);
        $(dom).parent().parent().hide();
    },
    updateParam: function(dom) {
        var type = $(dom).attr('data-type');
        
        $(dom).parent().parent().hide();
        $(dom).parent().parent().parent().find(".param-save").css("display", "table");
        
        if(type != paramListVM.messages['typeImg']){
            if(type == paramListVM.messages['typeHtml']){
                $(dom).closest(".data-row").find(".param-value").show();
                $(dom).closest(".data-row").find(".div-demo").hide();
            }
            
            $(dom).closest(".data-row").find(".param-value").removeAttr("disabled");
            $(dom).closest(".data-row").find(".param-value").focus();            
            $(".param-value").autogrow();
        }
        else{                                       // type Image
            var dataId = $(dom).attr('data-id');
            $('#a-link-delete'+dataId).show();
            $('#btn-upload-param'+dataId).show();

            uploadUtil.upload('upload-param'+dataId, {
                done: function(e, data) {
                    $.each(data.result.files, function(index, file) {
                        $("#img-"+dataId).attr("src", file.url);
                        $("#param_"+dataId).val(file.url);
                        uploadUtil.hideLoading();
                    });
                }
            });
        }
    },
    deleteParamConfirm: function(name, key, dom) {
        paramListVM.key = key;
        confirmDialog.show({
            title: paramListVM.messages['title'],
            info: paramListVM.messages['deleteParam'],
            errorMessage: paramListVM.messages['deleteParamError'],
            successMessage: paramListVM.messages['deleteParamSuccess'],
            callbackFn: paramListVM.deleteParam
        });
    },
    deleteParam: function() {
        // should use ajax
        document.location.href = "/system/setting/delete?key=" + paramListVM.key;
    },
    menuItemClick: function(shortName) {
        $(".menu-item").removeClass("tab-active");
        $(".menu-"+shortName+"-info").addClass("tab-active");
        $(".div-param-content").hide();
        $(".div-"+shortName+"-content").show();
    }
};

$(document).ready(function() {
    paramListVM.init();
});