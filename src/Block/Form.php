<?php /** @noinspection PhpDeprecationInspection */

namespace Tofex\BackendWidget\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Registry;
use Tofex\Help\Arrays;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2022 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
abstract class Form
    extends Generic
{
    /** @var \Tofex\Core\Helper\Registry */
    protected $registryHelper;

    /** @var \Tofex\BackendWidget\Helper\Form */
    protected $formHelper;

    /** @var string */
    protected $moduleKey;

    /** @var string */
    protected $objectName;

    /** @var string */
    protected $objectField;

    /** @var string */
    protected $objectRegistryKey;

    /** @var string */
    protected $saveUrlRoute;

    /** @var array */
    protected $saveUrlParams;

    /** @var AbstractModel */
    private $object;

    /**
     * @param Context                          $context
     * @param Registry                         $registry
     * @param FormFactory                      $formFactory
     * @param Arrays                           $arrayHelper
     * @param \Tofex\Core\Helper\Registry      $registryHelper
     * @param \Tofex\BackendWidget\Helper\Form $formHelper
     * @param array                            $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Arrays $arrayHelper,
        \Tofex\Core\Helper\Registry $registryHelper,
        \Tofex\BackendWidget\Helper\Form $formHelper,
        array $data = [])
    {
        $this->moduleKey = $arrayHelper->getValue($data, 'module_key', 'adminhtml');
        $this->objectName = $arrayHelper->getValue($data, 'object_name', 'empty');
        $this->objectField = $arrayHelper->getValue($data, 'object_field', 'id');
        $this->objectRegistryKey = $arrayHelper->getValue($data, 'object_registry_key');
        $this->saveUrlRoute = $arrayHelper->getValue($data, 'save_url_route', '*/*/save');
        $this->saveUrlParams = $arrayHelper->getValue($data, 'save_url_params', []);

        $this->registryHelper = $registryHelper;
        $this->formHelper = $formHelper;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return AbstractModel
     */
    protected function getObject(): AbstractModel
    {
        if ($this->object === null) {
            $this->object = $this->registryHelper->registry($this->objectRegistryKey);
        }

        return $this->object;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\Backend\Block\Widget\Form
     * @throws LocalizedException
     */
    protected function _prepareForm(): \Magento\Backend\Block\Widget\Form
    {
        $form = $this->createForm();

        $this->prepareFields($form);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param \Magento\Framework\Data\Form $form
     */
    abstract protected function prepareFields(\Magento\Framework\Data\Form $form);

    /**
     * @return \Magento\Framework\Data\Form
     * @throws LocalizedException
     */
    protected function createForm(): \Magento\Framework\Data\Form
    {
        return $this->formHelper->createPostForm($this->saveUrlRoute, $this->saveUrlParams, $this->isUploadForm(),
            'edit_form', preg_replace('/[^a-z0-9_]*/i', '', $this->objectName), $this->getObject(),
            $this->getObjectField());
    }

    /**
     * @return bool
     */
    protected function isUploadForm(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function getObjectField(): string
    {
        return $this->objectField;
    }

    /**
     * @param Fieldset $fieldSet
     * @param string   $objectFieldName
     * @param string   $label
     * @param bool     $required
     * @param bool     $readOnly
     * @param bool     $disabled
     */
    protected function addTextField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addTextField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset $fieldSet
     * @param string   $objectFieldName
     * @param string   $label
     * @param string   $after
     * @param bool     $required
     * @param bool     $readOnly
     * @param bool     $disabled
     */
    protected function addTextFieldAfter(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label,
        string $after,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addTextFieldAfter($fieldSet, $this->objectRegistryKey, $objectFieldName, $label, $after,
            $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset $fieldSet
     * @param string   $objectFieldName
     * @param string   $label
     * @param bool     $required
     * @param bool     $readOnly
     * @param bool     $disabled
     */
    protected function addTextareaField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addTextareaField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset $fieldSet
     * @param string   $objectFieldName
     * @param string   $label
     * @param string   $comment
     * @param bool     $required
     * @param bool     $readOnly
     * @param bool     $disabled
     */
    protected function addTextareaWithCommentField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label,
        string $comment,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addTextareaWithCommentField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $comment, $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset $fieldSet
     * @param string   $objectFieldName
     * @param string   $label
     * @param array    $options
     * @param mixed    $defaultValue
     * @param bool     $required
     * @param bool     $readOnly
     * @param bool     $disabled
     */
    protected function addOptionsField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label,
        array $options,
        $defaultValue,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addOptionsField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label, $options,
            $defaultValue, $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectFieldName
     * @param string             $label
     * @param array              $options
     * @param mixed              $defaultValue
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addOptionsMultiSelectField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label,
        array $options,
        $defaultValue,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addOptionsMultiSelectField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $options, $defaultValue, $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset $fieldSet
     * @param string   $objectFieldName
     * @param string   $label
     * @param bool     $required
     * @param bool     $readOnly
     * @param bool     $disabled
     */
    protected function addYesNoField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addYesNoField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset $fieldSet
     * @param string   $objectFieldName
     * @param string   $label
     * @param string   $after
     * @param bool     $required
     * @param bool     $readOnly
     * @param bool     $disabled
     */
    protected function addYesNoFieldAfter(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label,
        string $after,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addYesNoFieldAfter($fieldSet, $this->objectRegistryKey, $objectFieldName, $label, $after,
            $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset $fieldSet
     * @param string   $objectFieldName
     * @param string   $label
     * @param mixed    $defaultValue
     * @param bool     $required
     * @param bool     $readOnly
     * @param bool     $disabled
     */
    protected function addYesNoWithDefaultField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label,
        $defaultValue,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addYesNoWithDefaultField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $defaultValue, $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset    $fieldSet
     * @param string      $objectFieldName
     * @param string|null $label
     * @param bool        $readOnly
     * @param bool        $disabled
     */
    protected function addWebsiteSelectField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label = null,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addWebsiteSelectField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $this->getObject(), $readOnly, $disabled);
    }

    /**
     * @param Fieldset    $fieldSet
     * @param string      $objectFieldName
     * @param string|null $label
     * @param bool        $readOnly
     * @param bool        $disabled
     */
    protected function addWebsiteMultiselectField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label = null,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addWebsiteMultiselectField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $this->getObject(), $readOnly, $disabled);
    }

    /**
     * @param Fieldset    $fieldSet
     * @param string      $objectFieldName
     * @param string|null $label
     * @param bool        $readOnly
     * @param bool        $disabled
     * @param bool        $all
     */
    protected function addStoreSelectField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label = null,
        bool $readOnly = false,
        bool $disabled = false,
        bool $all = true)
    {
        try {
            $this->formHelper->addStoreSelectField($this->getLayout(), $fieldSet, $this->objectRegistryKey,
                $objectFieldName, $label, $this->getObject(), $readOnly, $disabled, $all);
        } catch (LocalizedException $exception) {
            $this->_logger->error($exception);
        }
    }

    /**
     * @param Fieldset    $fieldSet
     * @param string      $objectFieldName
     * @param string|null $label
     * @param bool        $readOnly
     * @param bool        $disabled
     */
    protected function addStoreMultiselectField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label = null,
        bool $readOnly = false,
        bool $disabled = false)
    {
        try {
            $this->formHelper->addStoreMultiselectField($this->getLayout(), $fieldSet, $this->objectRegistryKey,
                $objectFieldName, $label, $this->getObject(), $readOnly, $disabled);
        } catch (LocalizedException $exception) {
            $this->_logger->error($exception);
        }
    }

    /**
     * @param Fieldset    $fieldSet
     * @param string      $objectFieldName
     * @param string|null $label
     * @param bool        $required
     * @param bool        $readOnly
     * @param bool        $disabled
     */
    protected function addStoreWithAdminSelectField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label = null,
        bool $required = true,
        bool $readOnly = false,
        bool $disabled = false)
    {
        try {
            $this->formHelper->addStoreWithAdminSelectField($this->getLayout(), $fieldSet, $this->objectRegistryKey,
                $objectFieldName, $label, $this->getObject(), $required, $readOnly, $disabled);
        } catch (LocalizedException $exception) {
            $this->_logger->error($exception);
        }
    }

    /**
     * @param Fieldset    $fieldSet
     * @param string      $objectFieldName
     * @param string|null $label
     * @param null        $defaultValue
     * @param bool        $required
     * @param bool        $readOnly
     * @param bool        $disabled
     */
    protected function addCmsBlockSelectField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label = null,
        $defaultValue = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addCmsBlockSelectField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $defaultValue, $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset    $fieldSet
     * @param string      $objectFieldName
     * @param string|null $label
     * @param null        $defaultValue
     * @param bool        $required
     * @param bool        $readOnly
     * @param bool        $disabled
     */
    protected function addCmsPageSelectField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label = null,
        $defaultValue = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addCmsPageSelectField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $defaultValue, $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset    $fieldSet
     * @param string      $objectRegistryKey
     * @param string      $objectFieldName
     * @param string      $label
     * @param string|null $defaultValue
     * @param bool        $required
     * @param bool        $readOnly
     * @param bool        $disabled
     */
    protected function addTypeIdField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        string $defaultValue = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addTypeIdField($fieldSet, $objectRegistryKey, $objectFieldName, $label, $defaultValue,
            $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset $fieldSet
     * @param string   $objectFieldName
     * @param string   $label
     * @param bool     $required
     * @param bool     $readOnly
     * @param bool     $disabled
     */
    protected function addTemplateField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addTemplateField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset $fieldSet
     * @param string   $objectFieldName
     * @param string   $label
     * @param bool     $required
     * @param bool     $readOnly
     * @param bool     $disabled
     */
    public function addCategoriesField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addCategoriesField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset $fieldSet
     * @param string   $objectFieldName
     * @param string   $label
     * @param bool     $required
     * @param bool     $readOnly
     * @param bool     $disabled
     */
    protected function addOperatorField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addOperatorField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset $fieldSet
     * @param string   $objectFieldName
     * @param string   $label
     * @param bool     $required
     * @param bool     $readOnly
     * @param bool     $disabled
     */
    protected function addDateIsoField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label,
        bool $required = true,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addDateIsoField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset $fieldSet
     * @param string   $objectFieldName
     * @param string   $label
     * @param bool     $required
     */
    protected function addFileField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label,
        bool $required = true)
    {
        $this->formHelper->addFileField($fieldSet, $objectFieldName, $label, $required);
    }

    /**
     * @param Fieldset $fieldSet
     * @param string   $objectFieldName
     * @param string   $label
     * @param bool     $required
     * @param bool     $readOnly
     * @param bool     $disabled
     */
    protected function addCountryField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addCountryField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectFieldName
     * @param string             $label
     * @param AbstractModel|null $object
     * @param bool               $required
     */
    protected function addImageField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label,
        AbstractModel $object = null,
        bool $required = false)
    {
        $this->formHelper->addImageField($fieldSet, $objectFieldName, $label, $this->getObject(), $required);
    }

    /**
     * @param Fieldset    $fieldSet
     * @param string      $objectFieldName
     * @param string|null $label
     * @param bool        $required
     * @param bool        $readOnly
     * @param bool        $disabled
     */
    public function addCustomerGroupField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addCustomerGroupField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset    $fieldSet
     * @param string      $objectFieldName
     * @param string|null $label
     * @param bool        $required
     * @param bool        $readOnly
     * @param bool        $disabled
     */
    public function addCustomerGroupMultiSelectField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addCustomerGroupMultiSelectField($fieldSet, $this->objectRegistryKey, $objectFieldName,
            $label, $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset    $fieldSet
     * @param string      $objectFieldName
     * @param string|null $label
     * @param bool        $required
     * @param bool        $readOnly
     * @param bool        $disabled
     * @param bool        $allStores
     * @param bool        $withDefault
     *
     * @throws LocalizedException
     */
    public function addPaymentActiveMethodsField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false,
        bool $allStores = false,
        bool $withDefault = true)
    {
        $this->formHelper->addPaymentActiveMethodsField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $this->getObject(), $required, $readOnly, $disabled, $allStores, $withDefault);
    }

    /**
     * @param Fieldset    $fieldSet
     * @param string      $objectFieldName
     * @param string|null $label
     * @param bool        $required
     * @param bool        $readOnly
     * @param bool        $disabled
     */
    public function addProductTypeField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->formHelper->addProductTypeField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $this->getObject(), $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset $fieldSet
     * @param string   $objectFieldName
     * @param string   $label
     */
    protected function addWysiwygField(Fieldset $fieldSet, string $objectFieldName, string $label)
    {
        $this->formHelper->addWysiwygField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $this->getObject());
    }

    /**
     * @param Fieldset $fieldSet
     * @param string   $objectFieldName
     * @param string   $label
     */
    protected function addEditorField(Fieldset $fieldSet, string $objectFieldName, string $label)
    {
        $this->formHelper->addEditorField($fieldSet, $this->objectRegistryKey, $objectFieldName, $label,
            $this->getObject());
    }
}
