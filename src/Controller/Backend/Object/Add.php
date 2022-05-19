<?php

namespace Tofex\BackendWidget\Controller\Backend\Object;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2022 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
abstract class Add
    extends Base
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
