<?php

/* 
 * В zf2 реализован генератор html-элементов на основе абстрактного класса Zend\View\Helper\AbstractHtmlElement
 * Html-элемент выполнены в виде хелперов, унаслндованных от Zend\View\Helper\AbstractHtmlElement, 
 * напр. Zend\View\Helper\HtmlList. 
 * Вызов шаблоне осуществляется:
 * $this->htmllist(array $items, $ordered = false, $attribs = false, $escape = true);
 *		- $items - контент тега
 *		- $ordered - тип тэга (ol/ul, по умолчанию ul)
 *		- $attribs - массив аттрибутов тэга $items
 *		- $escape - экранировать(скорее енкодить в заданную кодировку) контент тега
 * Никаких проверок правильности набора аттрибутов.
 * Вложенность тегов затруднительна и нечитабельна из-за внедрения контента в параметр метода.
 * Матрёшки вложенных тегов невозможны и как минимум для читабельности надо было параметр контента делать последним,
 * если в скобках всё-таки попытаетесь сделать матрёшку тегов.
 * Затруднительная вложенность привела к отсутствию форматирования html-кода(отступы от родителей),
 * Максимум - перевод строки PHP_EOL.
 * Склейка тега разбита на класс тега и абстрактный класс, поэтому быстрое добавление новых классов-тегов 
 * затруднительно и нестандартизировано. Смотрим: Zend\View\Helper\HtmlObject
 * Реализованные классы-хелперы html-элементов:
 *		- HtmlFlash, HtmlList, HtmlObject, HtmlPage, HtmlQuicktime
 * Вывод: в таком виде использование html-хелперы неудобно, а учитывая значительное снижение производительности(в десятки раз), - нежелательно!!!
 * 
 * Генератор html-заголовков сделан на основе контейнера Zend\View\Helper\Placeholder\Container\AbstractStandalone. 
 * Это теги html-заголовка:
 *		- HeadLink, HeadMeata, HeadScript, HeadStyle, HeadTitle
 * Выводов в отношении заголовков пока нет, они выглядят более проработанно и по-видимому их использование целесообразно
 */

namespace Zend\View\Helper;

abstract class AbstractHtmlElement extends AbstractHelper {
    /**
     * EOL character
     * @deprecated just use PHP_EOL
     */
    const EOL = PHP_EOL;
 
    /**
     * The tag closing bracket
     * @var string
     */
    protected $closingBracket = null;

    /**
     * Get the tag closing bracket
     * @return string
     */
    function getClosingBracket() {
        if (!$this->closingBracket) {
            if ($this->isXhtml()) {
                $this->closingBracket = ' />';
            } else {
                $this->closingBracket = '>';
            }
        }
        return $this->closingBracket;
    }

    /**
     * Is doctype XHTML?
     * @return bool
     */
    protected function isXhtml() {
        return $this->getView()->plugin('doctype')->isXhtml();
    }

    /**
     * Converts an associative array to a string of tag attributes.
     * @access public
     * @param array $attribs From this array, each key-value pair is converted to an attribute name and value.
     * @return string The XHTML for the attributes.
     */
    protected function htmlAttribs($attribs) {
        $xhtml          = '';
        $escaper        = $this->getView()->plugin('escapehtml');
        $escapeHtmlAttr = $this->getView()->plugin('escapehtmlattr');

        foreach ((array) $attribs as $key => $val) {
            $key = $escaper($key);

            if (('on' == substr($key, 0, 2)) || ('constraints' == $key)) {
                // Don't escape event attributes; _do_ substitute double quotes with singles
                if (!is_scalar($val)) {
                    // non-scalar data should be cast to JSON first
                    $val = \Zend\Json\Json::encode($val);
                }
            } else {
                if (is_array($val)) {
                    $val = implode(' ', $val);
                }
            }
            $val = $escapeHtmlAttr($val);

            if ('id' == $key) {
                $val = $this->normalizeId($val);
            }
            if (strpos($val, '"') !== false) {
                $xhtml .= " $key='$val'";
            } else {
                $xhtml .= " $key=\"$val\"";
            }
        }
        return $xhtml;
    }

    /**
     * Normalize an ID
     * @param  string $value
     * @return string
     */
    protected function normalizeId($value) {
        if (strstr($value, '[')) {
            if ('[]' == substr($value, -2)) {
                $value = substr($value, 0, strlen($value) - 2);
            }
            $value = trim($value, ']');
            $value = str_replace('][', '-', $value);
            $value = str_replace('[', '-', $value);
        }
        return $value;
    }
}
///////////////////////////////////////////////////////////////////////////////////////////
/**
 * Helper for ordered and unordered lists (ol/ul)
 */
class HtmlList extends AbstractHtmlElement
{
    /**
     * Generates a 'List' element.
     * @param  array $items   Array with the elements of the list
     * @param  bool  $ordered Specifies ordered/unordered list (ol/ul); default unordered (ul)
     * @param  array $attribs Attributes for the ol/ul tag.
     * @param  bool  $escape  Escape the items.
     * @return string The list XHTML.
     */
    function __invoke( array $items, $ordered = false, $attribs = false, $escape = true ) {
        $list = '';

        foreach ($items as $item) {
            if (!is_array($item)) {
                if ($escape) {
                    $escaper = $this->getView()->plugin('escapeHtml');
                    $item    = $escaper($item);
                }
                $list .= '<li>' . $item . '</li>' . self::EOL;
            } else {
                $itemLength = 5 + strlen(self::EOL);
                if ($itemLength < strlen($list)) {
                    $list = substr($list, 0, strlen($list) - $itemLength)
                     . $this($item, $ordered, $attribs, $escape) . '</li>' . self::EOL;
                } else {
                    $list .= '<li>' . $this($item, $ordered, $attribs, $escape) . '</li>' . self::EOL;
                }
            }
        }
        if ($attribs) {
            $attribs = $this->htmlAttribs($attribs);
        } else {
            $attribs = '';
        }
        $tag = ($ordered) ? 'ol' : 'ul';

        return '<' . $tag . $attribs . '>' . self::EOL . $list . '</' . $tag . '>' . self::EOL;
    }
}

