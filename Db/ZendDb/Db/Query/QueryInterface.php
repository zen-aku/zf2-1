<?php

namespace Zend\Db\Query;

use Zend\Db\Adapter\Platform\PlatformInterface;

/**
 * 
 */
interface QueryInterface {
    
    /**
     * @param PlatformInterface $adapterPlatform
     */
    public function getSqlString( PlatformInterface $adapterPlatform = null );
}
