README
======

This directory should be used to place project specfic documentation including
but not limited to project notes, generated API/phpdoc documentation, or
manual files generated or hand written.  Ideally, this directory would remain
in your development environment only and should not be deployed with your
application to it's final production location.


Setting Up Your VHOST
=====================

The following is a sample VHOST you might want to consider for your project.

<VirtualHost *:80>
   DocumentRoot "I:/Projects/NSS/Dev/public"
   ServerName .local

   # This should be omitted in the production environment
   SetEnv APPLICATION_ENV development

   <Directory "I:/Projects/NSS/Dev/public">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>

</VirtualHost>

 showDeleteImageDialog: function(dom, filePath, url) {
        supplierCnuVM.selObj.dom = $(dom).parent().find("img");
        supplierCnuVM.selObj.url = url;
        supplierCnuVM.selObj.filePath = filePath;
        supplierCnuVM.selObj.fieldName = $(dom).parent().find(".thumbnail input");
        confirmDialog.show({
            title: supplierCnuVM.messages['deleteTitle'],
            info: supplierCnuVM.messages['deleteImage'],
            errorMessage: supplierCnuVM.messages['deleteImageError'],
            callbackFn: supplierCnuVM.deleteImage
        });
    },
    deleteImage: function() {
        confirmDialog.showLoading();
        var url = "/system/file/delete-file";

        $.post(url, {
            filePath: supplierCnuVM.selObj.filePath
        },
        function(data)
        {
            confirmDialog.hideLoading()
            if (data.status) {
                confirmDialog.hide();
                supplierCnuVM.selObj.dom.attr("src", supplierCnuVM.selObj.url);
                $(".link-delete").hide();
                supplierCnuVM.selObj.fieldName.val("")
            } else {
                confirmDialog.hide();
                confirmDialog.showError();
            }
        }, "json");
    }


## --- CSS --
== arrow to right ==
.category-header .category-main:after{
    position: absolute;
    top: 0;
    right: -18px;
    width: 0;
    height: 0;
    content: " ";
    border-left: 18px solid #009900;
    border-right: none;
    border-top: 18px solid transparent;
    border-bottom: 18px solid transparent;
}

== ease out ==
.category-header .category-main:hover{
    background-color: #009400;
    -webkit-transition: background-color 200ms ease-out;
    -moz-transition: background-color 200ms ease-out;
    -o-transition: background-color 200ms ease-out;
    transition: background-color 200ms ease-out;
}

== round ==
-webkit-border-bottom-left-radius: 3px; 
-moz-border-bottom-left-radius: 3px; 
border-bottom-left-radius: 3px; 
-webkit-border-top-left-radius: 3px; 
-moz-border-top-left-radius: 3px; 
border-top-left-radius: 3px; 