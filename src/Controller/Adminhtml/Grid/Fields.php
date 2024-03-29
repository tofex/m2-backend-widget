<?php

namespace Tofex\BackendWidget\Controller\Adminhtml\Grid;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Psr\Log\LoggerInterface;
use Tofex\BackendWidget\Helper\Session;
use Tofex\Core\Controller\Adminhtml\Ajax;
use Tofex\Help\Arrays;
use Tofex\Help\Json;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2022 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Fields
    extends Ajax
{
    /** @var Session */
    protected $sessionHelper;

    /**
     * @param Arrays          $arrayHelper
     * @param Json            $jsonHelper
     * @param Context         $context
     * @param LoggerInterface $logging
     * @param Session         $sessionHelper
     */
    public function __construct(
        Arrays $arrayHelper,
        Json $jsonHelper,
        Context $context,
        LoggerInterface $logging,
        Session $sessionHelper)
    {
        parent::__construct($arrayHelper, $jsonHelper, $context, $logging);

        $this->sessionHelper = $sessionHelper;
    }

    /**
     * @return ResponseInterface
     */
    public function execute(): ResponseInterface
    {
        $request = $this->getRequest();

        $dataGridId = $request->getParam('data_grid_id');
        $hiddenFieldList = $request->getParam('hidden_field_list', []);

        $this->sessionHelper->saveHiddenFieldList($dataGridId, $hiddenFieldList);

        $this->setSuccessResponse(__('Successfully stored hidden field list'));

        return $this->getResponse();
    }
}
