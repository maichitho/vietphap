var menuListVM = {
    namespace: "menuListVM",
    types: [],
    type: {},
    init: function() {
        $('.chk-display').click(function(){
            var id = $(this).attr('data-id');
            $('#img-loading-'+id).show();
            
            var params = {
                id: id,
                isCheck: $(this).prop('checked')? '1':'0'
            };
            $.post('/system/setting/async-update-menu-status', params, function(data) {
                if (data.success) {
                }
            }, 'json')
            .success(function() {
                })
            .error(function(xhr, ajaxOptions, thrownError) {
                alert(ajaxOptions);
            })
            .complete(function() {
                $('#img-loading-'+id).hide();
            });
        });
    },
    registerChanel: function() {

    },
    addNewMenu: function(){
        var href = location.protocol + '//' + location.hostname+'/system/setting/create-menu?type='+$('#sl-select-type').val();
        window.location.assign(href);
    }
};

$(document).ready(function() {
    menuListVM.init();
});