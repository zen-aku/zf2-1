<?php
namespace Helpers\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Хелпер displayCurrentDate() возвращает текущую дату
 */
class DisplayCurrentDate extends AbstractHelper {
	/**
	 * Возвращает текущую дату
	 * @return type - Текущая дата
	 */
	function __invoke() {
		return date('d.m.Y');
	}
}
