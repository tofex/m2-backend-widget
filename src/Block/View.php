<?php

namespace Tofex\BackendWidget\Block;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2022 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
abstract class View
    extends Form
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setReadOnlyAll(true);
    }
}
