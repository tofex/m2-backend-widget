<?php

namespace Tofex\BackendWidget\Block\Adminhtml\Wysiwyg\Images;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2022 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Content
    extends \Magento\Cms\Block\Adminhtml\Wysiwyg\Images\Content
{
    /**
     * New directory action target URL
     *
     * @return string
     */
    public function getOnInsertUrl(): string
    {
        return $this->getUrl('*/*/onInsert');
    }
}
