<?php

namespace Tofex\BackendWidget\Controller\Backend\Object;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2022 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
abstract class Tab
    extends Index
{
    /**
     * @return void
     */
    public function execute()
    {
        $block = $this->createBlock();

        $response = $this->getResponse();

        $response->setBody($block->toHtml());
    }
}
