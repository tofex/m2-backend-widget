<?php

namespace Tofex\BackendWidget\Plugin\Backend\Block\System\Account;

use Magento\Framework\View\LayoutInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2022 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Edit
{
    /**
     * @param \Magento\Backend\Block\System\Account\Edit $subject
     * @param LayoutInterface                            $layout
     *
     * @return array
     */
    public function beforeSetLayout(\Magento\Backend\Block\System\Account\Edit $subject, LayoutInterface $layout): array
    {
        $subject->addButton('reset_columns_selection', [
            'label'   => __('Reset Columns Selection'),
            'onclick' => sprintf('setLocation(\'%s\')', $subject->getUrl('tofex_backendwidget/grid/reset')),
            'class'   => 'action-secondary'
        ], -1, 10);

        return [$layout];
    }
}
