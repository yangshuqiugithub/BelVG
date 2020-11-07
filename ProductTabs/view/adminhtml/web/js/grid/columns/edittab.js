define([
    'Magento_Ui/js/grid/columns/column',
    'mageUtils',
    'uiRegistry',
    'jquery',
    'mage/url',
], function (Column, utils, registry, $, urlBuilder) {
    'use strict';

    const modalId = 'product_form.product_form.product_tabs_fieldset.modal';
    const formId = 'product_form.product_form.product_tabs_fieldset.modal.create_tab_item';
    const gridDataId = 'product_tabs_listing.product_tabs_listing_data_source';

    //Set base url
    let currentUrl = window.location;
    let urlAdminPart = currentUrl.pathname.match(/.+admin\//);
    let adminUrlPath = currentUrl.origin + urlAdminPart[0];
    urlBuilder.setBaseUrl(adminUrlPath);

    return Column.extend({
        defaults: {
            link: 'link',
            bodyTmpl: 'BelVG_ProductTabs/edittab'
        },

        initTabModal: function(rowIndex) {
            registry.get(modalId).openModal();
            if (registry.get(formId).isRendered !== true) {
                registry.get(formId).render({row_id: rowIndex});
            } else {
                registry.get(formId).updateData({row_id: rowIndex});
            }
        },

        isVisible: function(row) {
            if(row.selection_of_products == 2){
                return true;
            }

            return false;
        },

        getEditText: function() {
            return 'Edit';
        },

        getDeleteText: function() {
            return 'Delete';
        },

        deleteTab: function(rowId) {
            let controllerUrl = urlBuilder.build('belvg-product-tabs/tab/deleteTabFromProductPage');

            $.ajax({
                url: controllerUrl,
                type: 'POST',
                data: {
                    rowId: rowId
                },
                success: function(data) {
                    registry.get(gridDataId).reload();
                },
                showLoader: true
            });
        },
    });
});
