var userChangePassWordVM = {
    namespace: "userChangePassWordVM",
    user: {},
    init: function() {
        $("input[name='password']").keypress(function() {
            if ($(this).val() != "") {
                $("#currentPassword").hide();
            }
        });
    },
    registerChanel: function() {

    },
    updateValueForm: function() {
        if ($("input[name='newPassword']").val() != $("input[name='confirmPassword']").val()) {
            $("#confirmPassword").show();
            return false;
        }
        return true;
    }
};

$(document).ready(function() {
    userChangePassWordVM.init();
});