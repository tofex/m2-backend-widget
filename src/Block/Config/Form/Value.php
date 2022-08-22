<?php

namespace Tofex\BackendWidget\Block\Config\Form;

use Magento\Framework\Data\Form\Element\Text;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2022 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Value
    extends Text
{
    /**
     * @return string
     */
    public function getElementHtml(): string
    {
        $html = '';

        $value = $this->getDataUsingMethod('value');

        if (is_array($value)) {
            foreach ($value as $valueValue) {
                $html .= $this->getValueHtml($this->_escape($valueValue));
            }
        } else {
            $html .= $this->getValueHtml($this->getEscapedValue());
        }

        return $html;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function getValueHtml($value): string
    {
        return sprintf('<div id="%s" %s>%s</div>', $this->getHtmlId(), $this->serialize($this->getHtmlAttributes()),
            $value);
    }
}
