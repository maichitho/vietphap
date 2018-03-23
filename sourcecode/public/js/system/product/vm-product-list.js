var productVM = {
    namespace: "productVM",
    products: [],
    categories: [],
    categoryId: {},
    selObj: {},
    selDom: {},
    init: function() {

    },
    registerChanel: function() {

    },
    showDeleteProductDialog: function(dom, obj) {
        productVM.selObj = obj;
        productVM.selDom = dom;

        confirmDialog.show({
            title: productVM.messages.deleteTitle,
            info: productVM.messages.deleteProduct,
            errorMessage: productVM.messages.deleteProductError,
            callbackFn: productVM.deleteProduct
        });
    },
    deleteProduct: function() {
        if (typeof productVM.request !== undefined && productVM.request) {
            productVM.request.abort();
            delete productVM.request;
        }

        productVM.request = $.ajax({
            url: "/system/product/async-delete",
            type: "post",
            dataType: "json",
            data: {
                id: productVM.selObj.id
            },
            error: function() {
//                confirmDialog.showError();
            },
            beforeSend: function() {
                // do smth before sending
            },
            complete: function() {
                // do smth when complete action
            },
            success: function(data) {
                if (data.status) {
                    productVM.removeProduct(productVM.selObj);
                    $(productVM.selDom).closest(".data-row").remove();
                    confirmDialog.hide();
                } else {
                    confirmDialog.showError();
                }
            }
        });
    },
    removeProduct: function(product) {
        for (var i = 0; i < productVM.products.length; i++) {
            if (product.id == productCnuVM.products[i].id) {
                productCnuVM.products.splice(i, 1);
            }
        }
    }
};

$(document).ready(function() {
    productVM.init();
});