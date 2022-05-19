<?php

namespace Tofex\BackendWidget\Controller\Backend\Object;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Model\AbstractModel;
use Psr\Log\LoggerInterface;
use Tofex\BackendWidget\Model\Backend\Session;
use Tofex\Core\Helper\Instances;
use Tofex\Core\Helper\Registry;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2022 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
abstract class MassDelete
    extends Edit
{
    /** @var LoggerInterface */
    protected $logging;

    /**
     * @param Registry        $registryHelper
     * @param Instances       $instanceHelper
     * @param Context         $context
     * @param LoggerInterface $logging
     * @param Session         $session
     */
    public function __construct(
        Registry $registryHelper,
        Instances $instanceHelper,
        Context $context,
        LoggerInterface $logging,
        Session $session)
    {
        parent::__construct($registryHelper, $instanceHelper, $context, $session);

        $this->logging = $logging;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $paramName = $this->getObjectField();

        if (empty($paramName)) {
            $paramName = 'id';
        }

        $ids = $this->getRequest()->getParam($paramName);

        if (is_array($ids)) {
            $ids = array_unique($ids);

            $counter = 0;

            try {
                foreach ($ids as $id) {
                    $object = $this->getObjectInstance();
                    $objectResource = $this->getObjectResourceInstance();

                    if ($this->initObjectWithObjectField()) {
                        $objectResource->load($object, $id, $this->getObjectField());
                    } else {
                        $objectResource->load($object, $id);
                    }

                    if ($object->getId() == $id) {
                        $this->beforeDelete($object);

                        $objectResource->delete($object);

                        $this->afterDelete($object);

                        $counter++;
                    }
                }

                $this->messageManager->addSuccessMessage(sprintf($this->getObjectsDeletedMessage(), $counter));
            } catch (Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());

                $this->logging->error($exception);
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please select at least one item.'));
        }

        $this->_redirect($this->getIndexUrlRoute(), $this->getIndexUrlParams());
    }

    /**
     * @param AbstractModel $object
     */
    protected function beforeDelete(AbstractModel $object)
    {
    }

    /**
     * @param AbstractModel $object
     *
     * @throws Exception
     */
    protected function afterDelete(AbstractModel $object)
    {
    }

    /**
     * @return string
     */
    abstract protected function getObjectsDeletedMessage(): string;
}
