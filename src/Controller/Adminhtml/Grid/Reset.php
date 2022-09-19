<?php

namespace Tofex\BackendWidget\Controller\Adminhtml\Grid;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Tofex\BackendWidget\Helper\Session;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2022 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Reset
    extends Action
{
    /** @var Session */
    protected $sessionHelper;

    /**
     * @param Context $context
     * @param Session $sessionHelper
     */
    public function __construct(Context $context, Session $sessionHelper)
    {
        parent::__construct($context);

        $this->sessionHelper = $sessionHelper;
    }

    /**
     * @return ResponseInterface
     */
    public function execute(): ResponseInterface
    {
        $this->sessionHelper->resetHiddenFieldList();

        $this->messageManager->addSuccessMessage(__('Successfully reset all columns selections.'));

        return $this->_redirect('adminhtml/system_account/index');
    }
}
