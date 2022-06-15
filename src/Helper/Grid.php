<?php /** @noinspection PhpDeprecationInspection */

namespace Tofex\BackendWidget\Helper;

use Exception;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Directory\Model\Config\Source\Country;
use Magento\Store\Model\System\Store;
use Tofex\BackendWidget\Block\Grid\Column\Renderer\CustomerGroup;
use Tofex\BackendWidget\Block\Grid\Column\Renderer\Description;
use Tofex\Core\Helper\Customer;
use Tofex\Core\Helper\Template;
use Tofex\Core\Model\Config\Source\Attribute;
use Tofex\Core\Model\Config\Source\Attribute\AddressAttributeCode;
use Tofex\Core\Model\Config\Source\Attribute\CustomerAttributeCode;
use Tofex\Core\Model\Config\Source\Attribute\ProductAttributeCode;
use Tofex\Core\Model\Config\Source\Attribute\SortBy;
use Tofex\Core\Model\Config\Source\AttributeSet;
use Tofex\Core\Model\Config\Source\Categories;
use Tofex\Core\Model\Config\Source\CmsPage;
use Tofex\Core\Model\Config\Source\EntityType;
use Tofex\Core\Model\Config\Source\Operator;
use Tofex\Core\Model\Config\Source\Payment\ActiveMethods;
use Tofex\Core\Model\Config\Source\TypeId;
use Tofex\Help\Variables;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2022 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Grid
{
    /** @var Template */
    protected $templateHelper;

    /** @var Variables */
    protected $variableHelper;

    /** @var Customer */
    protected $customerHelper;

    /** @var Yesno */
    protected $sourceYesNo;

    /** @var Store */
    protected $sourceStore;

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

    /**
     * @param Template              $templateHelper
     * @param Variables             $variableHelper
     * @param Customer              $customerHelper
     * @param Yesno                 $sourceYesNo
     * @param Store                 $sourceStore
     * @param CmsPage               $sourceCmsPage
     * @param TypeId                $sourceTypeIds
     * @param Categories            $sourceCategories
     * @param Operator              $sourceOperator
     * @param Country               $sourceCountry
     * @param ActiveMethods         $sourcePaymentActiveMethods
     * @param Attribute             $sourceAttributes
     * @param AttributeSet          $sourceAttributeSets
     * @param EntityType            $sourceEntityTypes
     * @param ProductAttributeCode  $sourceProductAttributeCode
     * @param CustomerAttributeCode $sourceCustomerAttributeCode
     * @param AddressAttributeCode  $sourceAddressAttributeCode
     * @param SortBy                $sourceAttributeSortBy
     */
    public function __construct(
        Template $templateHelper,
        Variables $variableHelper,
        Customer $customerHelper,
        Yesno $sourceYesNo,
        Store $sourceStore,
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
        SortBy $sourceAttributeSortBy)
    {
        $this->templateHelper = $templateHelper;
        $this->variableHelper = $variableHelper;
        $this->customerHelper = $customerHelper;

        $this->sourceYesNo = $sourceYesNo;
        $this->sourceStore = $sourceStore;
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
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     *
     * @throws Exception
     */
    public function addTextColumn(Extended $grid, string $objectFieldName, string $label)
    {
        $grid->addColumn($objectFieldName, [
            'header' => $label,
            'index'  => $objectFieldName,
            'type'   => 'text'
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     * @param string   $filterIndex
     *
     * @throws Exception
     */
    public function addTextColumnWithFilter(Extended $grid, string $objectFieldName, string $label, string $filterIndex)
    {
        $grid->addColumn($objectFieldName, [
            'header'       => $label,
            'index'        => $objectFieldName,
            'filter_index' => $filterIndex,
            'type'         => 'text'
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     * @param mixed    $callback
     *
     * @throws Exception
     */
    public function addTextColumnWithFilterCondition(Extended $grid, string $objectFieldName, string $label, $callback)
    {
        $grid->addColumn($objectFieldName, [
            'header'                    => $label,
            'index'                     => $objectFieldName,
            'type'                      => 'text',
            'filter_condition_callback' => $callback
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     *
     * @throws Exception
     */
    public function addNumberColumn(Extended $grid, string $objectFieldName, string $label)
    {
        $grid->addColumn($objectFieldName, [
            'header' => $label,
            'index'  => $objectFieldName,
            'type'   => 'number'
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     * @param string   $filterIndex
     *
     * @throws Exception
     */
    public function addNumberColumnWithFilter(
        Extended $grid,
        string $objectFieldName,
        string $label,
        string $filterIndex)
    {
        $grid->addColumn($objectFieldName, [
            'header'       => $label,
            'index'        => $objectFieldName,
            'filter_index' => $filterIndex,
            'type'         => 'number'
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     * @param mixed    $callback
     *
     * @throws Exception
     */
    public function addNumberColumnWithFilterCondition(
        Extended $grid,
        string $objectFieldName,
        string $label,
        $callback)
    {
        $grid->addColumn($objectFieldName, [
            'header'                    => $label,
            'index'                     => $objectFieldName,
            'type'                      => 'number',
            'filter_condition_callback' => $callback
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     *
     * @throws Exception
     */
    public function addPriceColumn(Extended $grid, string $objectFieldName, string $label)
    {
        $grid->addColumn($objectFieldName, [
            'header' => $label,
            'index'  => $objectFieldName,
            'type'   => 'price'
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     * @param array    $options
     * @param mixed    $after
     *
     * @throws Exception
     */
    public function addOptionsColumn(
        Extended $grid,
        string $objectFieldName,
        string $label,
        array $options,
        $after = null)
    {
        $config = [
            'header'  => $label,
            'type'    => 'options',
            'index'   => $objectFieldName,
            'options' => $options
        ];

        if ($after) {
            $config[ 'after' ] = $after;
        }

        $grid->addColumn($objectFieldName, $config);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     * @param array    $options
     * @param string   $filterIndex
     *
     * @throws Exception
     */
    public function addOptionsColumnWithFilter(
        Extended $grid,
        string $objectFieldName,
        string $label,
        array $options,
        string $filterIndex)
    {
        $grid->addColumn($objectFieldName, [
            'header'       => $label,
            'type'         => 'options',
            'index'        => $objectFieldName,
            'filter_index' => $filterIndex,
            'options'      => $options
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     * @param array    $options
     * @param mixed    $callback
     *
     * @throws Exception
     */
    public function addOptionsColumnWithFilterCondition(
        Extended $grid,
        string $objectFieldName,
        string $label,
        array $options,
        $callback)
    {
        $grid->addColumn($objectFieldName, [
            'header'                    => $label,
            'type'                      => 'options',
            'index'                     => $objectFieldName,
            'options'                   => $options,
            'filter_condition_callback' => $callback
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     * @param array    $options
     * @param mixed    $callback
     * @param string   $renderer
     *
     * @throws Exception
     */
    public function addOptionsColumnWithFilterConditionAndRenderer(
        Extended $grid,
        string $objectFieldName,
        string $label,
        array $options,
        $callback,
        string $renderer)
    {
        $grid->addColumn($objectFieldName, [
            'header'                    => $label,
            'type'                      => 'options',
            'index'                     => $objectFieldName,
            'options'                   => $options,
            'filter_condition_callback' => $callback,
            'renderer'                  => $renderer
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     * @param array    $options
     * @param mixed    $filterCallback
     * @param mixed    $frameCallback
     *
     * @throws Exception
     */
    public function addOptionsColumnWithFilterConditionAndFrame(
        Extended $grid,
        string $objectFieldName,
        string $label,
        array $options,
        $filterCallback,
        $frameCallback)
    {
        $grid->addColumn($objectFieldName, [
            'header'                    => $label,
            'type'                      => 'options',
            'index'                     => $objectFieldName,
            'options'                   => $options,
            'filter_condition_callback' => $filterCallback,
            'frame_callback'            => $frameCallback
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     * @param array    $options
     * @param mixed    $callback
     *
     * @throws Exception
     */
    public function addOptionsColumnWithFrame(
        Extended $grid,
        string $objectFieldName,
        string $label,
        array $options,
        $callback)
    {
        $grid->addColumn($objectFieldName, [
            'header'         => $label,
            'type'           => 'options',
            'index'          => $objectFieldName,
            'options'        => $options,
            'frame_callback' => $callback
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     *
     * @throws Exception
     */
    public function addDateColumn(Extended $grid, string $objectFieldName, string $label)
    {
        $grid->addColumn($objectFieldName, [
            'header'           => $label,
            'type'             => 'date',
            'column_css_class' => 'date',
            'index'            => $objectFieldName
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     *
     * @throws Exception
     */
    public function addDatetimeColumn(Extended $grid, string $objectFieldName, string $label)
    {
        $grid->addColumn($objectFieldName, [
            'header'           => $label,
            'type'             => 'datetime',
            'column_css_class' => 'time',
            'index'            => $objectFieldName
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     *
     * @throws Exception
     */
    public function addYesNoColumn(Extended $grid, string $objectFieldName, string $label)
    {
        $this->addOptionsColumn($grid, $objectFieldName, $label, $this->sourceYesNo->toArray());
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     * @param string   $after
     *
     * @throws Exception
     */
    public function addYesNoColumnAfter(Extended $grid, string $objectFieldName, string $label, string $after)
    {
        $this->addOptionsColumn($grid, $objectFieldName, $label, $this->sourceYesNo->toArray(), $after);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     * @param mixed    $callback
     *
     * @throws Exception
     */
    public function addYesNoColumnWithFilterCondition(Extended $grid, string $objectFieldName, string $label, $callback)
    {
        $this->addOptionsColumnWithFilterCondition($grid, $objectFieldName, $label, $this->sourceYesNo->toArray(),
            $callback);
    }

    /**
     * @param Extended    $grid
     * @param string|null $label
     *
     * @throws Exception
     */
    public function addWebsiteNameColumn(Extended $grid, string $label = null)
    {
        if ($this->variableHelper->isEmpty($label)) {
            $label = __('Website');
        }

        $grid->addColumn('website_name', [
            'header'       => $label,
            'type'         => 'text',
            'index'        => 'website_name',
            'filter_index' => 'website.name'
        ]);
    }

    /**
     * @param Extended    $grid
     * @param string      $objectFieldName
     * @param string|null $label
     *
     * @throws Exception
     */
    public function addStoreColumn(Extended $grid, string $objectFieldName, string $label = null)
    {
        if (empty($label)) {
            $label = __('Store View');
        }

        $grid->addColumn($objectFieldName, [
            'header'   => $label,
            'type'     => 'options',
            'index'    => $objectFieldName,
            'options'  => $this->sourceStore->getStoreOptionHash(false),
            'sortable' => false
        ]);
    }

    /**
     * @param Extended    $grid
     * @param string      $objectFieldName
     * @param string|null $label
     *
     * @throws Exception
     */
    public function addStoreStructureColumn(Extended $grid, string $objectFieldName, string $label = null)
    {
        if (empty($label)) {
            $label = __('Store View');
        }

        $grid->addColumn($objectFieldName, [
            'header'                    => $label,
            'index'                     => $objectFieldName,
            'type'                      => 'store',
            'store_all'                 => true,
            'store_view'                => true,
            'sortable'                  => false,
            'filter_condition_callback' => [$grid, 'filterStoreCondition']
        ]);
    }

    /**
     * @param Extended    $grid
     * @param string      $objectFieldName
     * @param string|null $label
     *
     * @throws Exception
     */
    public function addStoreWithAdminStructureColumn(Extended $grid, string $objectFieldName, string $label = null)
    {
        if (empty($label)) {
            $label = __('Store View');
        }

        $grid->addColumn($objectFieldName, [
            'header'                    => $label,
            'index'                     => $objectFieldName,
            'type'                      => 'store_admin',
            'filter'                    => \Tofex\BackendWidget\Block\Grid\Column\Filter\Store::class,
            'renderer'                  => \Tofex\BackendWidget\Block\Grid\Column\Renderer\Store::class,
            'store_all'                 => true,
            'store_view'                => true,
            'sortable'                  => false,
            'filter_condition_callback' => [$grid, 'filterStoreCondition']
        ]);
    }

    /**
     * @param Extended    $grid
     * @param string      $objectFieldName
     * @param string|null $label
     *
     * @throws Exception
     */
    public function addCmsPageColumn(Extended $grid, string $objectFieldName, string $label = null)
    {
        if (empty($label)) {
            $label = __('Page');
        }

        $grid->addColumn($objectFieldName, [
            'header'   => $label,
            'type'     => 'options',
            'index'    => $objectFieldName,
            'options'  => $this->sourceCmsPage->toOptions(),
            'sortable' => false
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     *
     * @throws Exception
     */
    public function addTypeIdColumn(Extended $grid, string $objectFieldName, string $label)
    {
        $this->addOptionsColumn($grid, $objectFieldName, $label, $this->sourceTypeIds->toOptions());
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     *
     * @throws Exception
     */
    public function addTemplateColumn(Extended $grid, string $objectFieldName, string $label)
    {
        $grid->addColumn($objectFieldName, [
            'header'   => $label,
            'type'     => 'options',
            'options'  => $this->templateHelper->getAllTemplates(),
            'sortable' => false,
            'index'    => $objectFieldName,
            'renderer' => \Tofex\BackendWidget\Block\Grid\Column\Renderer\Template::class
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     *
     * @throws Exception
     */
    public function addCategoriesColumn(Extended $grid, string $objectFieldName, string $label)
    {
        $grid->addColumn($objectFieldName, [
            'header'                    => $label,
            'index'                     => $objectFieldName,
            'type'                      => 'options',
            'options'                   => $this->sourceCategories->toOptions(),
            'sortable'                  => false,
            'renderer'                  => \Tofex\BackendWidget\Block\Grid\Column\Renderer\Categories::class,
            'filter_condition_callback' => [$grid, 'filterInSet']
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     * @param string   $width
     * @param string   $height
     *
     * @throws Exception
     */
    public function addDescriptionColumn(
        Extended $grid,
        string $objectFieldName,
        string $label,
        string $width = '100%',
        string $height = '15px')
    {
        $grid->addColumn($objectFieldName, [
            'header'   => $label,
            'type'     => 'text',
            'width'    => $width,
            'height'   => $height,
            'sortable' => false,
            'index'    => $objectFieldName,
            'renderer' => Description::class
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     *
     * @throws Exception
     */
    public function addOperatorColumn(Extended $grid, string $objectFieldName, string $label)
    {
        $this->addOptionsColumn($grid, $objectFieldName, $label, $this->sourceOperator->toOptions());
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     *
     * @throws Exception
     */
    public function addCountryColumn(Extended $grid, string $objectFieldName, string $label)
    {
        $optionArray = $this->sourceCountry->toOptionArray(false);

        $options = [];

        foreach ($optionArray as $option) {
            $options[ $option[ 'value' ] ] = $option[ 'label' ];
        }

        $this->addOptionsColumn($grid, $objectFieldName, $label, $options);
    }

    /**
     * @param Extended    $grid
     * @param string      $objectFieldName
     * @param string|null $label
     *
     * @throws Exception
     */
    public function addCustomerGroupColumn(Extended $grid, string $objectFieldName, string $label = null)
    {
        if ($this->variableHelper->isEmpty($label)) {
            $label = __('Customer Group');
        }

        $this->customerGroupCollection->getSelect()->order('customer_group_code ASC');
        $this->customerGroupCollection->loadData();

        $customerGroups = [];

        /** @var Group $customerGroup */
        foreach ($this->customerGroupCollection as $customerGroup) {
            $customerGroups[ $customerGroup->getId() ] = $customerGroup->getCode();
        }

        $this->addOptionsColumn($grid, $objectFieldName, $label, $customerGroups);
    }

    /**
     * @param Extended    $grid
     * @param string      $objectFieldName
     * @param string|null $label
     *
     * @throws Exception
     */
    public function addCustomerGroupsColumn(Extended $grid, string $objectFieldName, string $label = null)
    {
        if ($this->variableHelper->isEmpty($label)) {
            $label = __('Customer Group');
        }

        $this->customerGroupCollection->getSelect()->order('customer_group_code ASC');
        $this->customerGroupCollection->loadData();

        $customerGroups = [];

        /** @var Group $customerGroup */
        foreach ($this->customerGroupCollection as $customerGroup) {
            $customerGroups[ $customerGroup->getId() ] = $customerGroup->getCode();
        }

        $this->addOptionsColumnWithFilterConditionAndRenderer($grid, $objectFieldName, $label, $customerGroups,
            [$grid, 'filterInSet'], CustomerGroup::class);
    }

    /**
     * @param Extended    $grid
     * @param string      $objectFieldName
     * @param string|null $label
     * @param bool        $allStores
     * @param bool        $withDefault
     *
     * @throws Exception
     */
    public function addPaymentActiveMethods(
        Extended $grid,
        string $objectFieldName,
        string $label = null,
        bool $allStores = false,
        bool $withDefault = true)
    {
        if ($this->variableHelper->isEmpty($label)) {
            $label = __('Payment Method');
        }

        $this->sourcePaymentActiveMethods->setAllStores($allStores);
        $this->sourcePaymentActiveMethods->setWithDefault($withDefault);

        $this->addOptionsColumn($grid, $objectFieldName, $label, $this->sourcePaymentActiveMethods->toOptions());
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     * @param bool     $customer
     * @param bool     $address
     * @param bool     $category
     * @param bool     $product
     *
     * @throws Exception
     */
    public function addEavAttributeColumn(
        Extended $grid,
        string $objectFieldName,
        string $label,
        bool $customer = false,
        bool $address = false,
        bool $category = false,
        bool $product = true)
    {
        $grid->addColumn($objectFieldName, [
            'header'  => $label,
            'type'    => 'options',
            'index'   => $objectFieldName,
            'options' => $this->sourceAttributes->toOptionsWithEntities($customer, $address, $category, $product)
        ]);
    }

    /**
     * @param \Tofex\BackendWidget\Block\Grid $grid
     * @param string                          $valueFieldName
     * @param string                          $attributeFieldName
     * @param string                          $label
     * @param bool                            $multiValue
     *
     * @throws Exception
     */
    public function addEavAttributeValueColumn(
        \Tofex\BackendWidget\Block\Grid $grid,
        string $valueFieldName,
        string $attributeFieldName,
        string $label,
        bool $multiValue = false)
    {
        $objectFieldValueName = sprintf('%s_value', $valueFieldName);

        $grid->addColumn($valueFieldName, [
            'header'                    => $label,
            'index'                     => $objectFieldValueName,
            'type'                      => 'text',
            'filter_condition_callback' => [$grid, 'filterEavAttributeOptionValue']
        ]);

        if ($multiValue) {
            $grid->addJoinAttributeMultiValues($valueFieldName, $attributeFieldName);
        } else {
            $grid->addJoinAttributeValues($valueFieldName, $attributeFieldName);
        }
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     * @param bool     $customer
     * @param bool     $address
     * @param bool     $category
     * @param bool     $product
     *
     * @throws Exception
     */
    public function addEavAttributeSetColumn(
        Extended $grid,
        string $objectFieldName,
        string $label,
        bool $customer = false,
        bool $address = false,
        bool $category = false,
        bool $product = true)
    {
        $grid->addColumn($objectFieldName, [
            'header'  => $label,
            'type'    => 'options',
            'index'   => $objectFieldName,
            'options' => $this->sourceAttributeSets->toOptionsWithEntities($customer, $address, $category, $product)
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     * @param bool     $customer
     * @param bool     $address
     * @param bool     $category
     * @param bool     $product
     *
     * @throws Exception
     */
    public function addEavEntityTypeColumn(
        Extended $grid,
        string $objectFieldName,
        string $label,
        bool $customer = false,
        bool $address = false,
        bool $category = false,
        bool $product = true)
    {
        $grid->addColumn($objectFieldName, [
            'header'  => $label,
            'type'    => 'options',
            'index'   => $objectFieldName,
            'options' => $this->sourceEntityTypes->toOptionsWithEntities($customer, $address, $category, $product)
        ]);
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     *
     * @throws Exception
     */
    public function addProductAttributeCodeColumn(
        Extended $grid,
        string $objectFieldName,
        string $label)
    {
        $this->addOptionsColumn($grid, $objectFieldName, $label, $this->sourceProductAttributeCode->toOptions());
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     *
     * @throws Exception
     */
    public function addCustomerAttributeCodeColumn(
        Extended $grid,
        string $objectFieldName,
        string $label)
    {
        $this->addOptionsColumn($grid, $objectFieldName, $label, $this->sourceCustomerAttributeCode->toOptions());
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     *
     * @throws Exception
     */
    public function addAddressAttributeCodeColumn(
        Extended $grid,
        string $objectFieldName,
        string $label)
    {
        $this->addOptionsColumn($grid, $objectFieldName, $label, $this->sourceAddressAttributeCode->toOptions());
    }

    /**
     * @param Extended $grid
     * @param string   $objectFieldName
     * @param string   $label
     *
     * @throws Exception
     */
    public function addAttributeSortByColumn(
        Extended $grid,
        string $objectFieldName,
        string $label)
    {
        $this->addOptionsColumn($grid, $objectFieldName, $label, $this->sourceAttributeSortBy->toOptions());
    }
}
