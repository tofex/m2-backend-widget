<?php

namespace Tofex\BackendWidget\Block\Grid;

use Magento\Backend\Block\Template;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2022 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Fields
    extends Template
{
    /** @var string */
    private $dataGridId;

    /** @var string[] */
    private $fieldList = [];

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->setTemplate('Tofex_BackendWidget::grid/fields.phtml');

        parent::_construct();
    }

    /**
     * @return string
     */
    public function getDataGridId(): string
    {
        return $this->dataGridId;
    }

    /**
     * @param string $dataGridId
     */
    public function setDataGridId(string $dataGridId): void
    {
        $this->dataGridId = $dataGridId;
    }

    /**
     * @return string[]
     */
    public function getFieldList(): array
    {
        return $this->fieldList;
    }

    /**
     * @param string[] $fieldList
     */
    public function setFieldList(array $fieldList): void
    {
        $this->fieldList = $fieldList;
    }

    /**
     * @return string
     */
    public function getAjaxUrl(): string
    {
        return $this->getUrl('tofex_backendwidget/grid/fields');
    }
}