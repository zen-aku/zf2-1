<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Query\Platform\Mysql;

use Zend\Db\Query\Platform\AbstractPlatform;

class Mysql extends AbstractPlatform
{
    public function __construct()
    {
        $this->setTypeDecorator('Zend\Db\Query\Select', new SelectDecorator());
        $this->setTypeDecorator('Zend\Db\Query\Ddl\CreateTable', new Ddl\CreateTableDecorator());
    }
}
