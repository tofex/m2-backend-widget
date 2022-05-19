<?php /** @noinspection PhpDeprecationInspection */

namespace Tofex\BackendWidget\Controller\Adminhtml\Wysiwyg\Images;

use Magento\Backend\App\Action\Context;
use Magento\Cms\Helper\Wysiwyg\Images;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Tofex\Core\Helper\Stores;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2022 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class OnInsert
    extends \Magento\Cms\Controller\Adminhtml\Wysiwyg\Images\OnInsert
{
    /** @var Stores */
    protected $storeHelper;

    /** @var Images */
    protected $imagesHelper;

    /**
     * @param Stores     $storeHelper
     * @param Images     $helper
     * @param Context    $context
     * @param Registry   $coreRegistry
     * @param RawFactory $resultRawFactory
     */
    public function __construct(
        Stores $storeHelper,
        Images $helper,
        Context $context,
        Registry $coreRegistry,
        RawFactory $resultRawFactory)
    {
        parent::__construct($context, $coreRegistry, $resultRawFactory);

        $this->storeHelper = $storeHelper;
        $this->imagesHelper = $helper;
    }

    /**
     * Fire when select image
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $filename = $this->getRequest()->getParam('filename');

        $filename = $this->imagesHelper->idDecode($filename);

        $fileUrl = $this->imagesHelper->getCurrentUrl() . $filename;

        $mediaPath = str_replace($this->storeHelper->getMediaUrl(), '', $fileUrl);

        /** @var Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();

        return $resultRaw->setContents($mediaPath);
    }
}
