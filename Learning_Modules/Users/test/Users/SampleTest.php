<?php
namespace UsersTest;

class SampleTest extends Framework\TestCase {

    public function testSample() {
        $this->assertInstanceOf( 'Zend\Di\LocatorInterface', $this->getLocator() );
    }
}
