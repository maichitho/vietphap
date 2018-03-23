var locationListVM = {
    namespace: "locationListVM",
    locations: [], // contain origin data
    parentLocations: [],
    location: {},
    isUpdate: false,
    type: {},
    selObj: {},
    mainLocation: [],
    selectedParentId: {},
    init: function() {
        // check create/update or list status
        if (locationListVM.type == "list") {
            $(".category-cnu").hide();
            $(".category-list").show();
            locationListVM.isUpdate = false;
        } else {
            $(".category-cnu").show();
            $(".category-list").hide();

            if ($('#id-sl-parent-category').length && locationListVM.type == 'update') {
                $('#id-sl-parent-category').val(locationListVM.location.parentId);
                $('#tr-sl-parent-category').hide();
            }
        }

        $("#category_close").click(function() {
            if (locationListVM.type != "create") {
                $(".category-cnu").hide();
                $(".category-list").show();
                locationListVM.isUpdate = false;
            }
            else {
                window.location.href = "/system/category/create-location";
            }
        });

//        $("html, body").css("overflow", "");
    },
    registerChanel: function() {

    },
    updateLocation: function(dom) {
        var id = $(dom).attr("id");
        locationListVM.isUpdate = true;
        locationListVM.location = locationListVM.getLocationById(id);
        locationListVM.updateValue("location", locationListVM.location);
        if ($('#id-sl-parent-category').length)
            $('#id-sl-parent-category').val(locationListVM.location.parentId);
        CKEDITOR.instances['category_des'].setData(locationListVM.location.description);

        $(".category-cnu").fadeIn("fast");
        $(".category-list").hide();
    },
    getLocationById: function(id) {
        var reVal;
        for (var i = 0; i < locationListVM.locations.length; i++) {
            if (locationListVM.locations[i]["id"] == id) {
                reVal = locationListVM.locations[i];
            }
        }
        return reVal;
    },
    showDialogConfirm: function(dom, id) {
        var delObj = $(dom).parent().parent().parent().parent();
        locationListVM.selObj["id"] = id;
        locationListVM.selObj["obj"] = delObj;
        $("#delete_dialog").showDialog({
            width: "400px"
        });
    },
    deleteLocation: function() {
        var id = locationListVM.selObj.id;
        var delObj = locationListVM.selObj.obj;
        $(".loading").show();
        $.ajax({
            url: "/system/category/delete-location",
            type: "post",
            dataType: "json",
            data: {
                id: id
            },
            error: function() {
                $("#delete_dialog").hideDialog();
                $(".loading").hide();
                $("#delete_dialog").hideDialog();
                $(".loading").hide();
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
                    //delObj.remove();
                    $("#delete_dialog").hideDialog();
                    $(".loading").hide();
                    $("#alert_success").show();
                    location.reload();
                } else {
                    $("#delete_dialog").hideDialog();
                    $(".loading").hide();
                    $("#alert_error").show();
                }
            }
        });
    },
    updateValueForm: function() {
        return true;
    },
    addNewLocation: function() {
        var href = location.protocol + '//' + location.hostname + '/system/category/create-location?selectedParentId=' + $('#sl-select-city').val();
        window.location.assign(href);
    }
//    checkExistMainCategoryInList: function (mainCateId){
//        for(var i = 0; i < locationListVM.categories.length; i++){
//            if(locationListVM.categories[i].parentId == mainCateId)
//                return true;
//        }
//        return false;
//    },
//    filterByMainCategory: function (mainCateId){
//        var result = [];
//        for(var i = 0; i < locationListVM.categories.length; i++){
//            if(locationListVM.categories[i].parentId == mainCateId)
//                result.push(locationListVM.categories[i]);
//        }
//        return result;
//    }
};

$(document).ready(function() {
    locationListVM.init();
});