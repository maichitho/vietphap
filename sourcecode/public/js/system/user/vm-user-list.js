var userListVM = {
    namespace: "userListVM",
    users: {},
    user: {},
    statuses: [],
    userTypes: [],
    selObj: {},
    type: "list",
    isExistUser: false,
    oldName: "",
    oldEmail: "",
    isExistEmail: false,
    userType: "system",
    isLoaded: false,
    isSupplier: false,
    init: function() {
        $("#username").keyup(function() {
            $("#message_for_user").hide();
            $("#user_loading_img").hide();
        }).blur(function() {
            if ($(this).val()) {
                userListVM.checkExistingUser();
            }
        });


        $("#email").keyup(function() {
            $("#message_for_email").hide();
            $("#email_loading_img").hide();
        }).blur(function() {
            if ($(this).val()) {
                userListVM.checkExistingEmail();
            }
        });

        $("#add_user").click(function() {
            userListVM.showAddUser();
            $("input[name='password']").val("").parent().parent().show();
            $("input[name='repassword']").val("").parent().parent().show();
            $("#reset_password").hide();
            $("input[name='username']").val("");
            $("input[name='fullName']").val("");
            $("input[name='email']").val("");
        });

        $("input[name='repassword']").keypress(function() {
            if ($(this).val() != "") {
                $("#re_password").hide();
            }
        });


        $("input[name='password']").keypress(function() {
            if ($(this).val() != "") {
                $("#password").hide();
            }
        });

        if (userListVM.type === "update") {
            userListVM.showAddUser();
            if (userListVM.user.status !== "") {
                $("select[name='status']").val(userListVM.user.status);
            }
            if (userListVM.user.type !== "") {
                $("select[name='userType']").val(userListVM.user.type);
            }
            this.oldName = this.user.username;
            this.oldEmail = this.user.email;
            $("input[name='password']").hide();
            $("#reset_password").show();
            $("input[name='repassword']").parent().parent().hide();
            $("input[name='password']").attr("value", userListVM.user.password);
            $("input[name='repassword']").attr("value", userListVM.user.password);
        } else {
            $("input[name='password']").parent().parent().hide();
            $("input[name='repassword']").parent().parent().hide();
        }
        if (userListVM.userType == "supplier") {
            $("#userSupplier").prop("checked", true);
        } else {
            $("#userSystem").prop("checked", true);
        }

        $("#supplier_filter").keyup(function() {
            var id = 0;
            return function(e) {
                clearTimeout(id);
                userListVM.isSupplier = false;
                id = setTimeout(userListVM.updateSearchItems(e.target.value), 500);
            };
        }());

        $("#supplier_list_filter").keyup(function() {
            var id = 0;
            return function(e) {
                clearTimeout(id);
                userListVM.isSupplier = true;
                id = setTimeout(userListVM.updateSearchItems(e.target.value), 500);
            };
        }());

        $("#supplier_filter").keypress(function(event) {
            var value = $("#supplier_filter").val();
            if (event.which === 13 && userListVM.isLoaded) {
                userListVM.isSupplier = false;
                userListVM.loadData(value);
            }
        });

        $("#supplier_list_filter").keypress(function(event) {
            var value = $("#supplier_list_filter").val();
            if (event.which === 13 && userListVM.isLoaded) {
                userListVM.isSupplier = true;
                userListVM.loadData(value);
            }
        });

    },
    registerChanel: function() {

    },
    updateSearchItems: function(value) {
        if (!userListVM.isSupplier) {
            this.users.forEach(function(e) {
                var show = (e.username.toLowerCase().indexOf(value) > -1 || e.fullName.toLowerCase().indexOf(value) > -1);
                $("#user_list tr[data-index=" + e.$index + "]").toggle(show);
            });
        } else {
            this.users.forEach(function(e) {
                var show = (e.supplierName.toLowerCase().indexOf(value) > -1);
                $("#user_list tr[data-index=" + e.$index + "]").toggle(show);
            });
        }
        if ($("#dialog_selection").find("tr:visible").length <= 0) {
            userListVM.isLoaded = true;
        } else {
            userListVM.isLoaded = false;
        }
    },
    loadData: function(value) {
        var isSupplier = "";
        if (userListVM.isSupplier) {
            isSupplier = "isSupplier";
        }
        var data = {
            keyword: value,
            isSupplier: isSupplier
        };

        $(".icon-loading").show();

        if (typeof userListVM.request !== undefined && userListVM.request) {
            userListVM.request.abort();
            delete userListVM.request;
        }

        userListVM.request = $.ajax({
            url: "/system/user/async-list",
            type: "get",
            dataType: "json",
            data: data,
            error: function() {
                userListVM.isLoaded = false;
                $(".icon-loading").hide();
            },
            beforeSend: function() {
                // do smth before sending
            },
            complete: function() {
                // do smth when complete action
            },
            success: function(data) {
                $(".icon-loading").hide();
                userListVM.users = data.users;
                userListVM.updateValue("users", userListVM.users);
                userListVM.isLoaded = false;
            }
        });
    },
    showDeleteUserDialog: function(dom, id) {
        userListVM.selObj.dom = $(dom);
        userListVM.selObj.id = id;
        confirmDialog.show({
            title: userListVM.messages['deleteTitle'],
            info: userListVM.messages['deleteUser'],
            errorMessage: userListVM.messages['deleteUserError'],
            successMessage: userListVM.messages['deleteUserSuccess'],
            callbackFn: userListVM.deleteUser
        });
    },
    deleteUser: function() {
        confirmDialog.showLoading();
        var url = "/system/user/async-delete";
        $.post(url, {
            id: userListVM.selObj.id
        },
        function(data)
        {
            confirmDialog.hideLoading();
            if (data.status) {
                confirmDialog.showMessage();
                userListVM.selObj.dom.closest(".user-row").remove();
            } else {
                confirmDialog.showError();
            }
        }, "json");
    },
    showAddUser: function() {
        $("input[name='username']").removeAttr("disabled");
        $("input[name='fullName']").removeAttr("disabled");
        $("input[name='password']").removeAttr("disabled");
        $("input[name='repassword']").removeAttr("disabled");
        $("input[name='email']").removeAttr("disabled");
        $("select[name='status']").removeAttr("disabled");
        $("select[name='userType']").removeAttr("disabled");
        $("input[name='fullName']").focus();
        $("#add_user").hide();
        $("#cancel_user").show();
        $("#save_user").show();
    },
    updateValueForm: function() {
        if (userListVM.isExistUser) {
            $("#message_for_user").show();
            return false;
        }
        if (userListVM.isExistEmail) {
            $("#message_for_email").show();
            return false;
        }
        if (userListVM.type != "update") {
            var reVal = true;
            if ($("input[name='password']").val() != $("input[name='repassword']").val()) {
                $("#re_password").show();
                reVal = false;
            }

            if (!$("input[name='password']").val()) {
                $("#password").show();
                reVal = false;
            }

            if (!reVal) {
                return reVal;
            }
        }
        return true;
    },
    checkExistingUser: function() {
        if ($("#username").val() && (userListVM.type == "create" || (userListVM.type == "update" && userListVM.oldName != $("#username").val()))) {
            $("#user_loading_img").show();
            $.ajax({
                url: "/system/user/is-existing-user",
                type: "post",
                dataType: "json",
                data: {
                    username: $("#username").val()
                },
                error: function() {

                },
                beforeSend: function() {
                    // do smth before sending
                },
                complete: function() {
                    // do smth when complete action
                },
                success: function(data) {
                    if (data.status) {
                        $("#message_for_user").show();
                        userListVM.isExistUser = true;
                        $("#user_loading_img").hide();
                    } else {
                        $("#message_for_user").hide();
                        userListVM.isExistUser = false;
                        $("#user_loading_img").hide();
                    }
                }
            });
        }
    },
    checkExistingEmail: function() {
        if ($("#email").val() && (userListVM.type == "create" || (userListVM.type == "update" && userListVM.oldEmail != $("#email").val()))) {
            $("#email_loading_img").show();
            $.ajax({
                url: "/system/user/is-existing-email",
                type: "post",
                dataType: "json",
                data: {
                    email: $("#email").val()
                },
                error: function() {

                },
                beforeSend: function() {
                    // do smth before sending
                },
                complete: function() {
                    // do smth when complete action
                },
                success: function(data) {
                    if (data.status) {
                        $("#message_for_email").show();
                        userListVM.isExistEmail = true;
                        $("#email_loading_img").hide();
                    } else {
                        $("#message_for_email").hide();
                        userListVM.isExistEmail = false;
                        $("#email_loading_img").hide();
                    }
                }
            });
        }
    },
    refreshList: function() {
        var type = $("input[name='userType']:checked").val();
        document.location.href = "/system/user/list?userType=" + type;
    },
    showResetPasswordDialog: function() {
        confirmDialog.show({
            title: userListVM.messages['resetTitle'],
            info: userListVM.messages['resetMessage'],
            errorMessage: userListVM.messages['ressetError'],
            successMessage: userListVM.messages['ressetSuccess'],
            callbackFn: userListVM.resetPassword
        });
    },
    resetPassword: function() {
        confirmDialog.showLoading();
        $.ajax({
            url: "/system/user/async-reset-password-user",
            type: "post",
            dataType: "json",
            data: {
                userId: userListVM.user.id
            },
            error: function() {
                confirmDialog.showError();
            },
            beforeSend: function() {
                // do smth before sending
            },
            complete: function() {
                // do smth when complete action
            },
            success: function(data) {
                if (data.status) {
                    confirmDialog.showMessage();
                } else {
                    confirmDialog.showError();
                }
            }
        });
    },
};

$(document).ready(function() {
    userListVM.init();
});