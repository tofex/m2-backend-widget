<?php

namespace Tofex\BackendWidget\Block\Grid;

use Exception;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Tofex\BackendWidget\Block\Grid;
use Zend_Db_Expr;
use Zend_Db_Select;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2022 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
abstract class GroupBy
    extends Grid
{
    /**
     * @param AbstractDb $collection
     */
    protected function followUpCollection(AbstractDb $collection)
    {
        $groupBy = $this->getParam('group_by');

        if ( ! $this->variableHelper->isEmpty($groupBy)) {
            $groupBy = base64_decode($groupBy);

            $select = $collection->getSelect();

            $columns = explode(',', $groupBy);

            $select->reset(Zend_Db_Select::COLUMNS);
            $select->columns($columns);
            $select->columns([new Zend_Db_Expr('COUNT(*) AS row_count')]);
            $select->group($columns);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function followUpFields()
    {
        parent::followUpFields();

        $groupBy = $this->getParam('group_by');

        if ( ! $this->variableHelper->isEmpty($groupBy)) {
            $this->addNumberColumn('row_count', __('Count'));

            foreach ($this->getNotGroupableFieldNames() as $fieldName) {
                $this->removeColumn($fieldName);
            }
        }
    }

    /**
     * @return Fields
     * @throws LocalizedException
     */
    protected function getFieldsBlock(): Fields
    {
        $fields = parent::getFieldsBlock();

        $fields->setGroupByFieldList($this->getGroupByFieldList());

        $groupBy = $this->getParam('group_by');

        if ( ! $this->variableHelper->isEmpty($groupBy)) {
            $groupBy = base64_decode($groupBy);

            $fieldNames = explode(',', $groupBy);

            $fields->setActiveGroupByFieldList($fieldNames);
        }

        return $fields;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    protected function getGroupByFieldList(): array
    {
        $notGroupableFieldNames = array_flip($this->getNotGroupableFieldNames());

        $fieldList = $this->getFieldList();

        foreach ($fieldList as $name => $label) {
            if (array_key_exists($name, $notGroupableFieldNames) || $name === 'row_count') {
                unset($fieldList[ $name ]);
            }
        }

        return $fieldList;
    }

    /**
     * @return array
     */
    abstract function getNotGroupableFieldNames(): array;
}
