<?php /** @noinspection PhpDeprecationInspection */

namespace Tofex\BackendWidget\Block\Grid\Column\Renderer;

use Magento\Backend\Block\Context;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2022 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Store
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Store
{
    /** @var \Tofex\BackendWidget\Model\Store\System\Store */
    protected $tofexSystemStore;

    /**
     * @param Context                                       $context
     * @param \Magento\Store\Model\System\Store             $systemStore
     * @param \Tofex\BackendWidget\Model\Store\System\Store $tofexSystemStore
     * @param array                                         $data
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\System\Store $systemStore,
        \Tofex\BackendWidget\Model\Store\System\Store $tofexSystemStore,
        array $data = [])
    {
        parent::__construct($context, $systemStore, $data);

        $this->tofexSystemStore = $tofexSystemStore;

        $this->_skipAllStoresLabel = true;
        $this->_skipEmptyStoresLabel = true;
    }

    /**
     * @return \Magento\Store\Model\System\Store
     */
    protected function _getStoreModel()
    {
        return $this->tofexSystemStore;
    }
}
