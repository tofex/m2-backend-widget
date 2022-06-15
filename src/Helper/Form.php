<?php

namespace Tofex\BackendWidget\Helper;

use Exception;
use IntlDateFormatter;
use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Config\Model\Config\Source\Website;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Directory\Model\Config\Source\Country;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\System\Store;
use Tofex\BackendWidget\Block\Config\Form\DateIso;
use Tofex\BackendWidget\Block\Config\Form\Wysiwyg;
use Tofex\BackendWidget\Model\Backend\Session;
use Tofex\Core\Helper\Customer;
use Tofex\Core\Helper\Template;
use Tofex\Core\Helper\Url;
use Tofex\Core\Model\Config\Source\Attribute;
use Tofex\Core\Model\Config\Source\Attribute\AddressAttributeCode;
use Tofex\Core\Model\Config\Source\Attribute\CustomerAttributeCode;
use Tofex\Core\Model\Config\Source\Attribute\ProductAttributeCode;
use Tofex\Core\Model\Config\Source\Attribute\SortBy;
use Tofex\Core\Model\Config\Source\AttributeSet;
use Tofex\Core\Model\Config\Source\Categories;
use Tofex\Core\Model\Config\Source\CmsBlock;
use Tofex\Core\Model\Config\Source\CmsPage;
use Tofex\Core\Model\Config\Source\EntityType;
use Tofex\Core\Model\Config\Source\Operator;
use Tofex\Core\Model\Config\Source\Payment\ActiveMethods;
use Tofex\Core\Model\Config\Source\TypeId;
use Tofex\Help\Arrays;
use Tofex\Help\Variables;
use Zend\Stdlib\Parameters;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2022 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Form
{
    /** @var Variables */
    protected $variableHelper;

    /** @var Arrays */
    protected $arrayHelper;

    /** @var Template */
    protected $templateHelper;

    /** @var Url */
    protected $urlHelper;

    /** @var Customer */
    protected $customerHelper;

    /** @var \Tofex\Core\Helper\Attribute */
    protected $attributeHelper;

    /** @var Session */
    protected $adminhtmlSession;

    /** @var FormFactory */
    protected $formFactory;

    /** @var Yesno */
    protected $sourceYesNo;

    /** @var Website */
    protected $sourceWebsite;

    /** @var Store */
    protected $sourceStore;

    /** @var \Tofex\BackendWidget\Model\Store\System\Store */
    protected $sourceStoreWithAdmin;

    /** @var CmsBlock */
    protected $sourceCmsBlock;

    /** @var CmsPage */
    protected $sourceCmsPage;

    /** @var TypeId */
    protected $sourceTypeIds;

    /** @var Categories */
    protected $sourceCategories;

    /** @var Operator */
    protected $sourceOperator;

    /** @var Country */
    protected $sourceCountry;

    /** @var ActiveMethods */
    protected $sourcePaymentActiveMethods;

    /** @var Attribute */
    protected $sourceAttributes;

    /** @var AttributeSet */
    protected $sourceAttributeSets;

    /** @var EntityType */
    protected $sourceEntityTypes;

    /** @var ProductAttributeCode */
    protected $sourceProductAttributeCode;

    /** @var CustomerAttributeCode */
    protected $sourceCustomerAttributeCode;

    /** @var AddressAttributeCode */
    protected $sourceAddressAttributeCode;

    /** @var SortBy */
    protected $sourceAttributeSortBy;

    /** @var Collection */
    protected $customerGroupCollection;

    /** @var string */
    protected $dateFormatIso;

    /** @var Type */
    protected $productType;

    /** @var Config */
    protected $wysiwygConfig;

    /**
     * @param Variables                                     $variableHelper
     * @param Arrays                                        $arrayHelper
     * @param Template                                      $templateHelper
     * @param Url                                           $urlHelper
     * @param Customer                                      $customerHelper
     * @param \Tofex\Core\Helper\Attribute                  $attributeHelper
     * @param Session                                       $adminhtmlSession
     * @param FormFactory                                   $formFactory
     * @param Yesno                                         $sourceYesNo
     * @param Website                                       $sourceWebsite
     * @param Store                                         $sourceStore
     * @param \Tofex\BackendWidget\Model\Store\System\Store $sourceStoreWithAdmin
     * @param CmsBlock                                      $sourceCmsBlock
     * @param CmsPage                                       $sourceCmsPage
     * @param TypeId                                        $sourceTypeIds
     * @param Categories                                    $sourceCategories
     * @param Operator                                      $sourceOperator
     * @param Country                                       $sourceCountry
     * @param ActiveMethods                                 $sourcePaymentActiveMethods
     * @param Attribute                                     $sourceAttributes
     * @param AttributeSet                                  $sourceAttributeSets
     * @param EntityType                                    $sourceEntityTypes
     * @param ProductAttributeCode                          $sourceProductAttributeCode
     * @param CustomerAttributeCode                         $sourceCustomerAttributeCode
     * @param AddressAttributeCode                          $sourceAddressAttributeCode
     * @param SortBy                                        $sourceAttributeSortBy
     * @param TimezoneInterface                             $localeDate
     * @param Type                                          $productType
     * @param Config                                        $wysiwygConfig
     */
    public function __construct(
        Variables $variableHelper,
        Arrays $arrayHelper,
        Template $templateHelper,
        Url $urlHelper,
        Customer $customerHelper,
        \Tofex\Core\Helper\Attribute $attributeHelper,
        Session $adminhtmlSession,
        FormFactory $formFactory,
        Yesno $sourceYesNo,
        Website $sourceWebsite,
        Store $sourceStore,
        \Tofex\BackendWidget\Model\Store\System\Store $sourceStoreWithAdmin,
        CmsBlock $sourceCmsBlock,
        CmsPage $sourceCmsPage,
        TypeId $sourceTypeIds,
        Categories $sourceCategories,
        Operator $sourceOperator,
        Country $sourceCountry,
        ActiveMethods $sourcePaymentActiveMethods,
        Attribute $sourceAttributes,
        AttributeSet $sourceAttributeSets,
        EntityType $sourceEntityTypes,
        ProductAttributeCode $sourceProductAttributeCode,
        CustomerAttributeCode $sourceCustomerAttributeCode,
        AddressAttributeCode $sourceAddressAttributeCode,
        SortBy $sourceAttributeSortBy,
        TimezoneInterface $localeDate,
        Type $productType,
        Config $wysiwygConfig)
    {
        $this->variableHelper = $variableHelper;
        $this->arrayHelper = $arrayHelper;
        $this->templateHelper = $templateHelper;
        $this->urlHelper = $urlHelper;
        $this->customerHelper = $customerHelper;
        $this->attributeHelper = $attributeHelper;

        $this->adminhtmlSession = $adminhtmlSession;
        $this->formFactory = $formFactory;
        $this->sourceYesNo = $sourceYesNo;
        $this->sourceWebsite = $sourceWebsite;
        $this->sourceStore = $sourceStore;
        $this->sourceStoreWithAdmin = $sourceStoreWithAdmin;
        $this->sourceCmsBlock = $sourceCmsBlock;
        $this->sourceCmsPage = $sourceCmsPage;
        $this->sourceTypeIds = $sourceTypeIds;
        $this->sourceCategories = $sourceCategories;
        $this->sourceOperator = $sourceOperator;
        $this->sourceCountry = $sourceCountry;
        $this->sourcePaymentActiveMethods = $sourcePaymentActiveMethods;
        $this->sourceAttributes = $sourceAttributes;
        $this->sourceAttributeSets = $sourceAttributeSets;
        $this->sourceEntityTypes = $sourceEntityTypes;
        $this->sourceProductAttributeCode = $sourceProductAttributeCode;
        $this->sourceCustomerAttributeCode = $sourceCustomerAttributeCode;
        $this->sourceAddressAttributeCode = $sourceAddressAttributeCode;
        $this->sourceAttributeSortBy = $sourceAttributeSortBy;

        $this->customerGroupCollection = $this->customerHelper->getCustomerGroupCollection();
        $this->dateFormatIso = $localeDate->getDateTimeFormat(IntlDateFormatter::MEDIUM);
        $this->productType = $productType;
        $this->wysiwygConfig = $wysiwygConfig;
    }

    /**
     * @param string             $saveUrlRoute
     * @param array              $saveUrlParams
     * @param bool               $isUpload
     * @param string             $formId
     * @param string|null        $htmlIdPrefix
     * @param AbstractModel|null $object
     * @param string|null        $objectField
     *
     * @return \Magento\Framework\Data\Form
     * @throws LocalizedException
     */
    public function createPostForm(
        string $saveUrlRoute,
        array $saveUrlParams,
        bool $isUpload = false,
        string $formId = 'edit_form',
        string $htmlIdPrefix = null,
        AbstractModel $object = null,
        string $objectField = null): \Magento\Framework\Data\Form
    {
        if (empty($objectField)) {
            $objectField = 'id';
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->formFactory->create();

        if ($object && $object->getId()) {
            $saveUrlParams[ $objectField ] = $object->getId();
        }

        $form->setData('id', $formId);
        $form->setData('action', $this->urlHelper->getBackendUrl($saveUrlRoute, $saveUrlParams));
        $form->setData('method', 'post');
        $form->setData('use_container', true);

        if ($isUpload) {
            $form->setData('enctype', 'multipart/form-data');
        }

        if ( ! $this->variableHelper->isEmpty($htmlIdPrefix)) {
            $form->setData('html_id_prefix', sprintf('%s_', $htmlIdPrefix));
        }

        return $form;
    }

    /**
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param mixed              $defaultValue
     * @param AbstractModel|null $object
     * @param string|null        $splitObjectValueSeparator
     *
     * @return mixed
     */
    public function getFieldValue(
        string $objectRegistryKey,
        string $objectFieldName,
        $defaultValue,
        AbstractModel $object = null,
        string $splitObjectValueSeparator = null)
    {
        $formData = $this->adminhtmlSession->getData(sprintf('%s_form_%s', $objectRegistryKey,
            $object && $object->getId() ? $object->getId() : 'add'));

        if ($formData instanceof Parameters) {
            $formData = $formData->toArray();
        }

        if ($this->variableHelper->isEmpty($formData)) {
            $formData = [];
        }

        if (array_key_exists($objectFieldName, $formData)) {
            return $this->arrayHelper->getValue($formData, $objectFieldName);
        }

        if ($object instanceof AbstractModel && $object->getId()) {
            $objectValue = $object->getDataUsingMethod($objectFieldName);

            if ( ! $this->variableHelper->isEmpty($splitObjectValueSeparator)) {
                $objectValue = explode(',', $objectValue);
            }

            return $objectValue;
        }

        return $defaultValue;
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string             $label
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     * @param mixed              $after
     */
    public function addTextField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false,
        $after = false)
    {
        $config = [
            'name'     => $objectFieldName,
            'label'    => $label,
            'value'    => $this->getFieldValue($objectRegistryKey, $objectFieldName, '', $object),
            'required' => $required
        ];

        if ($readOnly) {
            $config[ 'readonly' ] = true;
        }

        if ($disabled) {
            $config[ 'disabled' ] = true;
        }

        $fieldSet->addField($objectFieldName, 'text', $config, $after);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string             $label
     * @param string             $after
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addTextFieldAfter(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        string $after,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->addTextField($fieldSet, $objectRegistryKey, $objectFieldName, $label, $object, $required, $readOnly,
            $disabled, $after);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string             $label
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addTextareaField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $config = [
            'name'     => $objectFieldName,
            'label'    => $label,
            'value'    => $this->getFieldValue($objectRegistryKey, $objectFieldName, '', $object),
            'required' => $required
        ];

        if ($readOnly) {
            $config[ 'readonly' ] = true;
        }

        if ($disabled) {
            $config[ 'disabled' ] = true;
        }

        $fieldSet->addField($objectFieldName, 'textarea', $config);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string             $label
     * @param string             $comment
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addTextareaWithCommentField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        string $comment,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $config = [
            'name'               => $objectFieldName,
            'label'              => $label,
            'value'              => $this->getFieldValue($objectRegistryKey, $objectFieldName, '', $object),
            'required'           => $required,
            'after_element_html' => sprintf('<div>%s</div>', nl2br($comment))
        ];

        if ($readOnly) {
            $config[ 'readonly' ] = true;
        }

        if ($disabled) {
            $config[ 'disabled' ] = true;
        }

        $fieldSet->addField($objectFieldName, 'textarea', $config);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string             $label
     * @param array              $options
     * @param mixed              $defaultValue
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     * @param mixed              $after
     */
    public function addOptionsField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        array $options,
        $defaultValue,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false,
        $after = false)
    {
        $config = [
            'name'     => $objectFieldName,
            'label'    => $label,
            'title'    => $label,
            'value'    => $this->getFieldValue($objectRegistryKey, $objectFieldName, $defaultValue, $object),
            'values'   => $options,
            'required' => $required
        ];

        if ($readOnly) {
            $config[ 'readonly' ] = true;
        }

        if ($disabled) {
            $config[ 'disabled' ] = true;
        }

        $fieldSet->addField($objectFieldName, 'select', $config, $after);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
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
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        array $options,
        $defaultValue,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $config = [
            'name'     => $objectFieldName,
            'label'    => $label,
            'title'    => $label,
            'value'    => $this->getFieldValue($objectRegistryKey, $objectFieldName, $defaultValue, $object),
            'values'   => $options,
            'required' => $required
        ];

        if ($readOnly) {
            $config[ 'readonly' ] = true;
        }

        if ($disabled) {
            $config[ 'disabled' ] = true;
        }

        $fieldSet->addField($objectFieldName, 'multiselect', $config);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string             $label
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addYesNoField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->addOptionsField($fieldSet, $objectRegistryKey, $objectFieldName, $label,
            $this->sourceYesNo->toOptionArray(), 1, $object, $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectFieldName
     * @param string             $objectRegistryKey
     * @param string             $label
     * @param string             $after
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addYesNoFieldAfter(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        string $after,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->addOptionsField($fieldSet, $objectRegistryKey, $objectFieldName, $label,
            $this->sourceYesNo->toOptionArray(), 1, $object, $required, $readOnly, $disabled, $after);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string             $label
     * @param mixed              $defaultValue
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addYesNoWithDefaultField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        $defaultValue,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->addOptionsField($fieldSet, $objectRegistryKey, $objectFieldName, $label,
            $this->sourceYesNo->toOptionArray(), $defaultValue, $object, $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string|null        $label
     * @param AbstractModel|null $object
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addWebsiteSelectField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label = null,
        AbstractModel $object = null,
        bool $readOnly = false,
        bool $disabled = false)
    {
        if ($this->variableHelper->isEmpty($label)) {
            $label = __('Website');
        }

        $config = [
            'name'     => $objectFieldName,
            'label'    => $label,
            'value'    => $this->getFieldValue($objectRegistryKey, $objectFieldName, 0, $object),
            'values'   => $this->sourceWebsite->toOptionArray(),
            'required' => true
        ];

        if ($readOnly) {
            $config[ 'readonly' ] = true;
        }

        if ($disabled) {
            $config[ 'disabled' ] = true;
        }

        $fieldSet->addField('website_id', 'select', $config);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string|null        $label
     * @param AbstractModel|null $object
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addWebsiteMultiselectField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label = null,
        AbstractModel $object = null,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->addWebsiteMultiselectFieldWithValue($fieldSet, $objectFieldName,
            $this->getFieldValue($objectRegistryKey, $objectFieldName, 0, $object), $label, $readOnly, $disabled);
    }

    /**
     * @param Fieldset    $fieldSet
     * @param mixed       $value
     * @param string      $objectFieldName
     * @param string|null $label
     * @param bool        $readOnly
     * @param bool        $disabled
     */
    public function addWebsiteMultiselectFieldWithValue(
        Fieldset $fieldSet,
        string $objectFieldName,
        $value = null,
        string $label = null,
        bool $readOnly = false,
        bool $disabled = false)
    {
        if ($this->variableHelper->isEmpty($label)) {
            $label = __('Website');
        }

        $config = [
            'name'     => $objectFieldName,
            'label'    => $label,
            'value'    => $value,
            'values'   => $this->sourceWebsite->toOptionArray(),
            'required' => true
        ];

        if ($readOnly) {
            $config[ 'readonly' ] = true;
        }

        if ($disabled) {
            $config[ 'disabled' ] = true;
        }

        $fieldSet->addField('website_id', 'multiselect', $config);
    }

    /**
     * @param LayoutInterface    $layout
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string|null        $label
     * @param AbstractModel|null $object
     * @param bool               $readOnly
     * @param bool               $disabled
     * @param bool               $all
     */
    public function addStoreSelectField(
        LayoutInterface $layout,
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label = null,
        AbstractModel $object = null,
        bool $readOnly = false,
        bool $disabled = false,
        bool $all = true)
    {
        if (empty($label)) {
            $label = __('Store View');
        }

        $config = [
            'name'     => $objectFieldName,
            'label'    => $label,
            'title'    => $label,
            'value'    => $this->getFieldValue($objectRegistryKey, $objectFieldName, 0, $object),
            'values'   => $this->sourceStore->getStoreValuesForForm(false, $all),
            'required' => true
        ];

        if ($readOnly) {
            $config[ 'readonly' ] = true;
        }

        if ($disabled) {
            $config[ 'disabled' ] = true;
        }

        $field = $fieldSet->addField($objectFieldName, 'multiselect', $config);

        /** @var Element $renderer */
        $renderer = $layout->createBlock(Element::class);

        if ($renderer) {
            $field->setRenderer($renderer);
        }
    }

    /**
     * @param LayoutInterface    $layout
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string|null        $label
     * @param AbstractModel|null $object
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addStoreMultiselectField(
        LayoutInterface $layout,
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label = null,
        AbstractModel $object = null,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->addStoreMultiselectFieldWithValue($layout, $fieldSet, $objectFieldName, $label,
            $this->getFieldValue($objectRegistryKey, $objectFieldName, 0, $object), $readOnly, $disabled);
    }

    /**
     * @param LayoutInterface $layout
     * @param Fieldset        $fieldSet
     * @param string          $objectFieldName
     * @param string|null     $label
     * @param mixed           $value
     * @param bool            $readOnly
     * @param bool            $disabled
     */
    public function addStoreMultiselectFieldWithValue(
        LayoutInterface $layout,
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label = null,
        $value = null,
        bool $readOnly = false,
        bool $disabled = false)
    {
        if (empty($label)) {
            $label = __('Store View');
        }

        $config = [
            'name'     => sprintf('%s[]', $objectFieldName),
            'label'    => $label,
            'title'    => $label,
            'value'    => $value,
            'values'   => $this->sourceStore->getStoreValuesForForm(false, true),
            'required' => true
        ];

        if ($readOnly) {
            $config[ 'readonly' ] = true;
        }

        if ($disabled) {
            $config[ 'disabled' ] = true;
        }

        $field = $fieldSet->addField($objectFieldName, 'multiselect', $config);

        /** @var Element $renderer */
        $renderer = $layout->createBlock(Element::class);

        if ($renderer) {
            $field->setRenderer($renderer);
        }
    }

    /**
     * @param LayoutInterface    $layout
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string|null        $label
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addStoreWithAdminSelectField(
        LayoutInterface $layout,
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label = null,
        AbstractModel $object = null,
        bool $required = true,
        bool $readOnly = false,
        bool $disabled = false)
    {
        if (empty($label)) {
            $label = __('Store View');
        }

        $config = [
            'name'     => $objectFieldName,
            'label'    => $label,
            'title'    => $label,
            'value'    => $this->getFieldValue($objectRegistryKey, $objectFieldName, 0, $object),
            'values'   => $this->sourceStoreWithAdmin->getStoreValuesForForm(false, false),
            'required' => $required
        ];

        if ($readOnly) {
            $config[ 'readonly' ] = true;
        }

        if ($disabled) {
            $config[ 'disabled' ] = true;
        }

        $field = $fieldSet->addField($objectFieldName, 'select', $config);

        /** @var Element $renderer */
        $renderer = $layout->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');

        if ($renderer) {
            $field->setRenderer($renderer);
        }
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string|null        $label
     * @param null               $defaultValue
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addCmsBlockSelectField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label = null,
        $defaultValue = null,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        if (empty($label)) {
            $label = __('Block');
        }

        $this->addOptionsField($fieldSet, $objectRegistryKey, $objectFieldName, $label,
            $this->sourceCmsBlock->getAllOptions(), $defaultValue, $object, $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string|null        $label
     * @param null               $defaultValue
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addCmsPageSelectField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label = null,
        $defaultValue = null,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        if (empty($label)) {
            $label = __('Page');
        }

        $this->addOptionsField($fieldSet, $objectRegistryKey, $objectFieldName, $label,
            $this->sourceCmsPage->getAllOptions(), $defaultValue, $object, $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string             $label
     * @param mixed              $defaultValue
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addTypeIdField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        $defaultValue = null,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->addOptionsField($fieldSet, $objectRegistryKey, $objectFieldName, $label,
            $this->sourceTypeIds->toOptionArray(), $defaultValue, $object, $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string             $label
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addTemplateField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->addOptionsField($fieldSet, $objectRegistryKey, $objectFieldName, $label,
            $this->templateHelper->getAllTemplates(), null, $object, $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string             $label
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addCategoriesField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->addOptionsMultiSelectField($fieldSet, $objectRegistryKey, $objectFieldName, $label,
            $this->sourceCategories->toOptionArray(), null, $object, $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string             $label
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addOperatorField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->addOptionsField($fieldSet, $objectRegistryKey, $objectFieldName, $label,
            $this->sourceOperator->toOptionArray(), '==', $object, $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string             $label
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addDateIsoField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        AbstractModel $object = null,
        bool $required = true,
        bool $readOnly = false,
        bool $disabled = false)
    {
        // convert the date to local time
        $fieldSet->addType('date_iso', DateIso::class);

        $config = [
            'label'    => $label,
            'value'    => $this->getFieldValue($objectRegistryKey, $objectFieldName, null, $object),
            'format'   => $this->dateFormatIso,
            'required' => $required
        ];

        if ($readOnly) {
            $config[ 'readonly' ] = true;
        }

        if ($disabled) {
            $config[ 'disabled' ] = true;
        }

        $fieldSet->addField($objectFieldName, 'date_iso', $config);
    }

    /**
     * @param Fieldset $fieldSet
     * @param string   $objectFieldName
     * @param string   $label
     * @param bool     $required
     */
    public function addFileField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label,
        bool $required = true)
    {
        $fieldSet->addField($objectFieldName, 'file', [
            'name'     => $objectFieldName,
            'label'    => $label,
            'class'    => 'disable',
            'required' => $required
        ]);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string             $label
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addCountryField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        $this->addOptionsField($fieldSet, $objectRegistryKey, $objectFieldName, $label,
            $this->sourceCountry->toOptionArray(false), null, $object, $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectFieldName
     * @param string             $label
     * @param AbstractModel|null $object
     * @param bool               $required
     */
    public function addImageField(
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label,
        AbstractModel $object = null,
        bool $required = false)
    {
        $fieldSet->addField($objectFieldName, 'image', [
            'name'     => $objectFieldName,
            'label'    => $label,
            'value'    => $object && $object->getId() ? $object->getDataUsingMethod($objectFieldName) : null,
            'required' => $required
        ]);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string|null        $label
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addCustomerGroupField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label = null,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        if ($this->variableHelper->isEmpty($label)) {
            $label = __('Customer Group');
        }

        $this->customerGroupCollection->getSelect()->order('customer_group_code ASC');
        $this->customerGroupCollection->loadData();

        $customerGroups = [['value' => '', 'label' => __('--Please Select--')]];

        /** @var Group $customerGroup */
        foreach ($this->customerGroupCollection as $customerGroup) {
            $customerGroups[] = ['value' => $customerGroup->getId(), 'label' => $customerGroup->getCode()];
        }

        $this->addOptionsField($fieldSet, $objectRegistryKey, $objectFieldName, $label, $customerGroups, null, $object,
            $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string|null        $label
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addCustomerGroupMultiSelectField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label = null,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        if ($this->variableHelper->isEmpty($label)) {
            $label = __('Customer Group');
        }

        $this->customerGroupCollection->getSelect()->order('customer_group_code ASC');
        $this->customerGroupCollection->loadData();

        $customerGroups = [['value' => '', 'label' => __('--Please Select--')]];

        /** @var Group $customerGroup */
        foreach ($this->customerGroupCollection as $customerGroup) {
            $customerGroups[] = ['value' => $customerGroup->getId(), 'label' => $customerGroup->getCode()];
        }

        $this->addOptionsMultiSelectField($fieldSet, $objectRegistryKey, $objectFieldName, $label, $customerGroups,
            null, $object, $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string|null        $label
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     * @param bool               $allStores
     * @param bool               $withDefault
     *
     * @throws LocalizedException
     */
    public function addPaymentActiveMethodsField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label = null,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false,
        bool $allStores = false,
        bool $withDefault = true)
    {
        if ($this->variableHelper->isEmpty($label)) {
            $label = __('Payment Method');
        }

        $this->sourcePaymentActiveMethods->setAllStores($allStores);
        $this->sourcePaymentActiveMethods->setWithDefault($withDefault);

        $this->addOptionsField($fieldSet, $objectRegistryKey, $objectFieldName, $label,
            $this->sourcePaymentActiveMethods->toOptionArray(), null, $object, $required, $readOnly, $disabled);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string|null        $label
     * @param AbstractModel|null $object
     * @param bool               $required
     * @param bool               $readOnly
     * @param bool               $disabled
     */
    public function addProductTypeField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label = null,
        AbstractModel $object = null,
        bool $required = false,
        bool $readOnly = false,
        bool $disabled = false)
    {
        if ($this->variableHelper->isEmpty($label)) {
            $label = __('Apply To');
        }

        $config = [
            'name'        => sprintf('%s[]', $objectFieldName),
            'label'       => $label,
            'value'       => $this->getFieldValue($objectRegistryKey, $objectFieldName, 'all', $object),
            'values'      => $this->productType->getOptions(),
            'mode_labels' => [
                'all'    => __('All Product Types'),
                'custom' => __('Selected Product Types')
            ],
            'required'    => $required
        ];

        if ($readOnly) {
            $config[ 'readonly' ] = true;
        }

        if ($disabled) {
            $config[ 'disabled' ] = true;
        }

        $fieldSet->addField($objectFieldName, 'apply', $config);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string             $label
     * @param AbstractModel|null $object
     */
    public function addWysiwygField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        AbstractModel $object = null)
    {
        $fieldSet->addType('wysiwyg', Wysiwyg::class);

        $fieldSet->addField($objectFieldName, 'wysiwyg', [
            'name'  => $objectFieldName,
            'label' => $label,
            'value' => $this->getFieldValue($objectRegistryKey, $objectFieldName, null, $object)
        ]);
    }

    /**
     * @param Fieldset           $fieldSet
     * @param string             $objectRegistryKey
     * @param string             $objectFieldName
     * @param string             $label
     * @param AbstractModel|null $object
     */
    public function addEditorField(
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        AbstractModel $object = null)
    {
        $fieldSet->addField($objectFieldName, 'editor', [
            'name'   => $objectFieldName,
            'label'  => $label,
            'state'  => 'html',
            'value'  => $this->getFieldValue($objectRegistryKey, $objectFieldName, null, $object),
            'style'  => 'height: 400px;',
            'config' => $this->wysiwygConfig->getConfig()
        ]);
    }

    /**
     * @param AbstractModel $object
     * @param Fieldset      $fieldSet
     * @param string        $objectRegistryKey
     * @param string        $objectFieldName
     * @param string        $label
     * @param bool          $required
     * @param bool          $customer
     * @param bool          $address
     * @param bool          $category
     * @param bool          $product
     */
    public function addEavAttributeField(
        AbstractModel $object,
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        bool $required = false,
        bool $customer = false,
        bool $address = false,
        bool $category = false,
        bool $product = true)
    {
        $fieldSet->addField($objectFieldName, 'select', [
            'name'     => $objectFieldName,
            'label'    => $label,
            'value'    => $this->getFieldValue($objectRegistryKey, $objectFieldName, null, $object),
            'values'   => $this->sourceAttributes->toOptionArrayWithEntities($customer, $address, $category, $product),
            'required' => $required
        ]);
    }

    /**
     * @param AbstractModel $object
     * @param Fieldset      $fieldSet
     * @param string        $objectFieldName
     * @param string        $label
     * @param bool          $required
     * @param bool          $customer
     * @param bool          $address
     * @param bool          $category
     * @param bool          $product
     */
    protected function addEavAttributeMultiselectField(
        AbstractModel $object,
        Fieldset $fieldSet,
        string $objectFieldName,
        string $label,
        bool $required = false,
        bool $customer = false,
        bool $address = false,
        bool $category = false,
        bool $product = true)
    {
        $fieldSet->addField($objectFieldName, 'multiselect', [
            'name'     => $objectFieldName,
            'label'    => $label,
            'value'    => $object->getDataUsingMethod($objectFieldName),
            'values'   => $this->sourceAttributes->toOptionArrayWithEntities($customer, $address, $category, $product),
            'required' => $required
        ]);
    }

    /**
     * @param AbstractModel $object
     * @param string        $objectName
     * @param Fieldset      $fieldSet
     * @param string        $objectRegistryKey
     * @param string        $objectFieldName
     * @param string        $label
     * @param array         $targetFieldNames
     * @param bool          $required
     */
    public function addEavAttributeFieldWithUpdate(
        AbstractModel $object,
        string $objectName,
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        array $targetFieldNames,
        bool $required = false)
    {
        $onChangeFieldId = sprintf('%s_%s', $objectName, $objectFieldName);

        $onChange = [];

        foreach ($targetFieldNames as $targetFieldName) {
            $targetFieldId = sprintf('%s_%s', $objectName, $targetFieldName);

            $onChange[] = $this->getUpdateEavAttributeFormElementJs($onChangeFieldId, $targetFieldId);
        }

        $fieldSet->addField($objectFieldName, 'select', [
            'name'     => $objectFieldName,
            'label'    => $label,
            'value'    => $this->getFieldValue($objectRegistryKey, $objectFieldName, null, $object),
            'values'   => $this->sourceAttributes->toOptionArray(),
            'required' => $required,
            'onchange' => implode(';', $onChange)
        ]);
    }

    /**
     * @param AbstractModel $object
     * @param Fieldset      $fieldSet
     * @param string        $objectRegistryKey
     * @param string        $objectAttributeFieldName
     * @param string        $objectFieldName
     * @param string        $label
     * @param bool          $required
     * @param bool          $multiSelect
     *
     * @throws Exception
     */
    public function addEavAttributeValueField(
        AbstractModel $object,
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectAttributeFieldName,
        string $objectFieldName,
        string $label,
        bool $required = false,
        bool $multiSelect = false)
    {
        $valueOptions = [];

        if ($object->getId()) {
            $attributeId = $object->getDataUsingMethod($objectAttributeFieldName);

            if ($attributeId) {
                $attribute = $this->attributeHelper->getAttribute(Product::ENTITY, $attributeId);

                $valueOptions = $attribute->getSource()->getAllOptions();
            }
        }

        if ($this->variableHelper->isEmpty($valueOptions)) {
            $this->addTextField($fieldSet, $objectRegistryKey, $objectFieldName, $label, $object, $required);
        } else {
            if ($multiSelect) {
                $this->addOptionsMultiSelectField($fieldSet, $objectRegistryKey, $objectFieldName, $label,
                    $valueOptions, null, $object, $required);
            } else {
                $this->addOptionsField($fieldSet, $objectRegistryKey, $objectFieldName, $label, $valueOptions, null,
                    $object, $required);
            }
        }
    }

    /**
     * @param AbstractModel $object
     * @param Fieldset      $fieldSet
     * @param string        $objectRegistryKey
     * @param string        $objectFieldName
     * @param string        $label
     * @param bool          $required
     * @param bool          $customer
     * @param bool          $address
     * @param bool          $category
     * @param bool          $product
     */
    public function addEavAttributeSetField(
        AbstractModel $object,
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        bool $required = false,
        bool $customer = false,
        bool $address = false,
        bool $category = false,
        bool $product = true)
    {
        $fieldSet->addField($objectFieldName, 'select', [
            'name'     => $objectFieldName,
            'label'    => $label,
            'value'    => $this->getFieldValue($objectRegistryKey, $objectFieldName, null, $object),
            'values'   => $this->sourceAttributeSets->toOptionArrayWithEntities($customer, $address, $category,
                $product),
            'required' => $required
        ]);
    }

    /**
     * @param AbstractModel $object
     * @param Fieldset      $fieldSet
     * @param string        $objectRegistryKey
     * @param string        $objectFieldName
     * @param string        $label
     * @param bool          $required
     * @param bool          $customer
     * @param bool          $address
     * @param bool          $category
     * @param bool          $product
     */
    public function addEavEntityTypeField(
        AbstractModel $object,
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        bool $required = false,
        bool $customer = false,
        bool $address = false,
        bool $category = false,
        bool $product = true)
    {
        $fieldSet->addField($objectFieldName, 'select', [
            'name'     => $objectFieldName,
            'label'    => $label,
            'value'    => $this->getFieldValue($objectRegistryKey, $objectFieldName, null, $object),
            'values'   => $this->sourceEntityTypes->toOptionArrayWithEntities($customer, $address, $category, $product),
            'required' => $required
        ]);
    }

    /**
     * @param AbstractModel $object
     * @param Fieldset      $fieldSet
     * @param string        $objectRegistryKey
     * @param string        $objectFieldName
     * @param string        $label
     * @param bool          $required
     */
    public function addProductAttributeCodeField(
        AbstractModel $object,
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        bool $required = false)
    {
        $fieldSet->addField($objectFieldName, 'select', [
            'name'     => $objectFieldName,
            'label'    => $label,
            'value'    => $this->getFieldValue($objectRegistryKey, $objectFieldName, null, $object),
            'values'   => $this->sourceProductAttributeCode->toOptionArray(),
            'required' => $required
        ]);
    }

    /**
     * @param AbstractModel $object
     * @param Fieldset      $fieldSet
     * @param string        $objectRegistryKey
     * @param string        $objectFieldName
     * @param string        $label
     * @param bool          $required
     */
    public function addCustomerAttributeCodeField(
        AbstractModel $object,
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        bool $required = false)
    {
        $fieldSet->addField($objectFieldName, 'select', [
            'name'     => $objectFieldName,
            'label'    => $label,
            'value'    => $this->getFieldValue($objectRegistryKey, $objectFieldName, null, $object),
            'values'   => $this->sourceCustomerAttributeCode->toOptionArray(),
            'required' => $required
        ]);
    }

    /**
     * @param AbstractModel $object
     * @param Fieldset      $fieldSet
     * @param string        $objectRegistryKey
     * @param string        $objectFieldName
     * @param string        $label
     * @param bool          $required
     */
    public function addAddressAttributeCodeField(
        AbstractModel $object,
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        bool $required = false)
    {
        $fieldSet->addField($objectFieldName, 'select', [
            'name'     => $objectFieldName,
            'label'    => $label,
            'value'    => $this->getFieldValue($objectRegistryKey, $objectFieldName, null, $object),
            'values'   => $this->sourceAddressAttributeCode->toOptionArray(),
            'required' => $required
        ]);
    }

    /**
     * @param AbstractModel $object
     * @param Fieldset      $fieldSet
     * @param string        $objectRegistryKey
     * @param string        $objectFieldName
     * @param string        $label
     * @param bool          $required
     */
    public function addAttributeSortByField(
        AbstractModel $object,
        Fieldset $fieldSet,
        string $objectRegistryKey,
        string $objectFieldName,
        string $label,
        bool $required = false)
    {
        $fieldSet->addField($objectFieldName, 'select', [
            'name'     => $objectFieldName,
            'label'    => $label,
            'value'    => $this->getFieldValue($objectRegistryKey, $objectFieldName, null, $object),
            'values'   => $this->sourceAttributeSortBy->toOptionArray(),
            'required' => $required
        ]);
    }

    /**
     * @param string $sourceElementId
     * @param string $targetElementId
     *
     * @return string
     */
    protected function getUpdateEavAttributeFormElementJs(string $sourceElementId, string $targetElementId): string
    {
        return sprintf('updateEavAttributeFormElement(\'%s\', \'%s\', \'%s\');',
            urlencode($this->urlHelper->getBackendUrl('tofex_backendwidget/attribute_option/values')), $sourceElementId,
            $targetElementId);
    }
}
