<?php

namespace Tofex\BackendWidget\Block\Grid\Column\Filter;

use Magento\Backend\Block\Context;
use Magento\Framework\DB\Helper;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2022 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Store
    extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Store
{
    /** @var \Tofex\BackendWidget\Model\Store\System\Store */
    protected $tofexSystemStore;

    /**
     * @param Context                                       $context
     * @param Helper                                        $resourceHelper
     * @param \Magento\Store\Model\System\Store             $systemStore
     * @param \Tofex\BackendWidget\Model\Store\System\Store $tofexSystemStore
     * @param array                                         $data
     */
    public function __construct(
        Context $context,
        Helper $resourceHelper,
        \Magento\Store\Model\System\Store $systemStore,
        \Tofex\BackendWidget\Model\Store\System\Store $tofexSystemStore,
        array $data = [])
    {
        parent::__construct($context, $resourceHelper, $systemStore, $data);

        $this->tofexSystemStore = $tofexSystemStore;
    }

    /**
     * Render HTML of the element
     *
     * @return string
     */
    public function getHtml(): string
    {
        $websiteCollection = $this->tofexSystemStore->getWebsiteCollection();
        $groupCollection = $this->tofexSystemStore->getGroupCollection();
        $storeCollection = $this->tofexSystemStore->getStoreCollection();

        $html = '<select class="admin__control-select" name="' . $this->escapeHtml($this->_getHtmlName()) . '" ' .
            $this->getColumn()->getData('validate_class') . $this->getUiId('filter', $this->_getHtmlName()) . '>';

        $value = $this->getColumn()->getData('value');

        $html .= sprintf('<option value="" %s></option>', ! $value ? 'selected="selected"' : '');

        foreach ($websiteCollection as $website) {
            $websiteShow = false;

            foreach ($groupCollection as $group) {
                if ($group->getWebsiteId() != $website->getId()) {
                    continue;
                }

                $groupShow = false;

                foreach ($storeCollection as $store) {
                    if ($store->getGroupId() != $group->getId()) {
                        continue;
                    }

                    if ( ! $websiteShow) {
                        $websiteShow = true;

                        $html .= sprintf('<optgroup label="%s"></optgroup>', $this->escapeHtml($website->getName()));
                    }

                    if ( ! $groupShow) {
                        $groupShow = true;

                        $html .= sprintf('<optgroup label="&nbsp;&nbsp;&nbsp;&nbsp;%s">',
                            $this->escapeHtml($group->getName()));
                    }

                    $value = $this->getData('value');

                    $selected = $value == $store->getId() ? ' selected="selected"' : '';

                    $html .= sprintf('<option value="%s" %s>&nbsp;&nbsp;&nbsp;&nbsp;%s</option>', $store->getId(),
                        $selected, $this->escapeHtml($store->getName()));
                }
                if ($groupShow) {
                    $html .= '</optgroup>';
                }
            }
        }

        if ($this->getColumn()->getData('display_deleted')) {
            $selected = $this->getData('value') == '_deleted_' ? ' selected' : '';

            $html .= sprintf('<option value="_deleted_" %s>%s</option>', $selected, __('[ deleted ]'));
        }

        $html .= '</select>';

        return $html;
    }
}
