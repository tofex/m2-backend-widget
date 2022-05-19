<?php

namespace Tofex\BackendWidget\Controller\Backend\Object;

use Magento\Backend\App\Action;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2022 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
abstract class Base
    extends Action
{
    /**
     * Acl check for admin
     *
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed($this->getAclResourceName());
    }

    /**
     * @return string
     */
    protected function getAclResourceName(): string
    {
        return sprintf('%s::%s', $this->getModuleKey(), $this->getResourceKey());
    }

    /**
     * @return string
     */
    abstract protected function getResourceKey(): string;

    /**
     * @return void
     */
    protected function initAction()
    {
        $this->_view->loadLayout($this->getInitActionLayoutHandles());
    }

    /**
     * @return string[]
     */
    protected function getInitActionLayoutHandles(): array
    {
        return ['default', 'styles'];
    }

    /**
     * @param string $action
     */
    protected function finishAction(string $action)
    {
        $page = $this->_view->getPage();

        $page->setActiveMenu($this->getActiveMenu());

        $page->getConfig()->getTitle()->prepend(sprintf('%s > %s', $this->getTitle(), $action));
        $page->addBreadcrumb($this->getTitle(), $this->getTitle());
        $page->addBreadcrumb($action, $action);
    }

    /**
     * @return string
     */
    protected function getActiveMenu(): string
    {
        return sprintf('%s::%s', $this->getModuleKey(), $this->getMenuKey());
    }

    /**
     * @return string
     */
    abstract protected function getModuleKey(): string;

    /**
     * @return string
     */
    abstract protected function getMenuKey(): string;

    /**
     * @return string
     */
    abstract protected function getTitle(): string;

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return sprintf('%s\Model\%s', str_replace('_', '\\', $this->getModuleKey()),
            str_replace('_', '\\', $this->getObjectName()));
    }

    /**
     * @return string
     */
    protected function getModelResourceClass(): string
    {
        return sprintf('%s\Model\ResourceModel\%s', str_replace('_', '\\', $this->getModuleKey()),
            str_replace('_', '\\', $this->getObjectName()));
    }

    /**
     * @return string
     */
    abstract protected function getObjectName(): string;

    /**
     * @return string|null
     */
    protected function getObjectField(): ?string
    {
        return null;
    }

    /**
     * @return string
     */
    protected function getObjectRegistryKey(): string
    {
        return sprintf('current_%s', strtolower(str_replace('\\', '_', $this->getObjectName())));
    }

    /**
     * @return string
     */
    protected function getDeleteUrlRoute(): string
    {
        return '*/*/delete';
    }

    /**
     * @return array
     */
    protected function getDeleteUrlParams(): array
    {
        return [];
    }
}
