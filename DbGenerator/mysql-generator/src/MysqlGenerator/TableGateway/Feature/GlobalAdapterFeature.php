<?php

namespace MysqlGenerator\TableGateway\Feature;

use MysqlGenerator\Adapter\Adapter;
use MysqlGenerator\TableGateway\Exception;

class GlobalAdapterFeature extends AbstractFeature
{

    /**
     * @var Adapter[]
     */
    protected static $staticAdapters = array();

    /**
     * Set static adapter
     *
     * @param Adapter $adapter
     */
    public static function setStaticAdapter(Adapter $adapter)
    {
        $class = get_called_class();

        static::$staticAdapters[$class] = $adapter;
        if ($class === __CLASS__) {
            static::$staticAdapters[__CLASS__] = $adapter;
        }
    }

    /**
     * Get static adapter
     *
     * @throws Exception\RuntimeException
     * @return Adapter
     */
    public static function getStaticAdapter()
    {
        $class = get_called_class();

        // class specific adapter
        if (isset(static::$staticAdapters[$class])) {
            return static::$staticAdapters[$class];
        }

        // default adapter
        if (isset(static::$staticAdapters[__CLASS__])) {
            return static::$staticAdapters[__CLASS__];
        }

        throw new Exception\RuntimeException('No database adapter was found in the static registry.');
    }

    /**
     * after initialization, retrieve the original adapter as "master"
     */
    public function preInitialize()
    {
        $this->tableGateway->adapter = self::getStaticAdapter();
    }


}
