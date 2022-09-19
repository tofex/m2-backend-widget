// noinspection JSUnresolvedVariable,JSUnresolvedFunction,JSUnusedGlobalSymbols

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2022 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'domReady!'
], function ($, modal) {
    'use strict';

    // noinspection JSValidateJSDoc
    $.widget('mage.dataGridColumns', {
        options: {
            formKey: '',
            ajaxUrl: '',
            dataGridId: ''
        },

        _create: function createBundleOptionsMinMax() {
            this.dataGrid = $('table.data-grid');
            this.dataGridColumnsButton = $('.action-columns');
            this.dataGridColumnsButtonText = $('span', this.dataGridColumnsButton);
            this.dataGridColumnsButtonTextValue = this.dataGridColumnsButtonText.text();
            this.dataGridColumnsList = $('.data-grid-columns');
            this.dataGridColumnsCheckboxes = $('.data-grid-column-checkbox', this.dataGridColumnsList);
        },

        _init: function initDataGridColumns() {
            var dataGridColumns = this;

            modal({
                type: 'popup',
                responsive: true,
                modalClass: 'data-grid-fields-popup',
                title: $.mage.__('Show columns'),
                buttons: []
            }, dataGridColumns.dataGridColumnsList);

            dataGridColumns.dataGridColumnsButton.on('click', function () {
                dataGridColumns.dataGridColumnsList.modal('openModal');
            });

            dataGridColumns.dataGridColumnsCheckboxes.each(function () {
                var columnCheckbox = $(this);
                var columnId = columnCheckbox.attr('id');
                if (columnId) {
                    var fieldName = columnId.replace('data-grid-column-', '');
                    var columnClassName = 'col-' + fieldName;
                    var columnHeader = $('.' + columnClassName, dataGridColumns.dataGrid);
                    if (columnHeader.length) {
                        if (!columnHeader.hasClass('hidden')) {
                            columnCheckbox.attr('checked', true);
                        }
                        columnCheckbox.on('change', function () {
                            columnHeader.toggleClass('hidden');
                            dataGridColumns._updateButtonText();
                            dataGridColumns._saveSelection();
                        });
                    }
                }
            });

            dataGridColumns._updateButtonText();
        },

        _updateButtonText: function () {
            var checkedDataGridColumnsCheckboxes = $('.data-grid-column-checkbox:checked', this.dataGridColumnsList);
            var hiddenCount = this.dataGridColumnsCheckboxes.length - checkedDataGridColumnsCheckboxes.length;
            if (hiddenCount > 0) {
                this.dataGridColumnsButtonText.text(this.dataGridColumnsButtonTextValue +
                    ' (' + hiddenCount + ' ' + $.mage.__('hidden') + ')');
            } else {
                this.dataGridColumnsButtonText.text(this.dataGridColumnsButtonTextValue);
            }
        },

        _saveSelection: function () {
            var hiddenFieldList = [];
            var uncheckedDataGridColumnsCheckboxes =
                $('.data-grid-column-checkbox:not(:checked)', this.dataGridColumnsList);
            uncheckedDataGridColumnsCheckboxes.each(function () {
                var columnCheckbox = $(this);
                var columnId = columnCheckbox.attr('id');
                if (columnId) {
                    var fieldName = columnId.replace('data-grid-column-', '');
                    hiddenFieldList.push(fieldName);
                }
            });

            var data = {
                data_grid_id: this.options.dataGridId,
                form_key: this.options.formKey,
                hidden_field_list: hiddenFieldList
            }

            $.ajax({
                url: this.options.ajaxUrl,
                dataType: 'json',
                data: data
            });
        }
    });

    return $.mage.dataGridColumns;
});
