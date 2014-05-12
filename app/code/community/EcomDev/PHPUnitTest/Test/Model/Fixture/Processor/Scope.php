<?php

class EcomDev_PHPUnitTest_Test_Model_Fixture_Processor_Scope extends EcomDev_PHPUnit_Test_Case
{

    protected $_processor = null;

    protected function setUp()
    {
        $this->_processor = Mage::getModel('ecomdev_phpunit/fixture_processor_scope');
    }

    /**
     * @loadFixture testAllowUpdatingExistingScope.yaml
     */
    public function testAllowUpdatingExistingScope()
    {
        $this->assertEquals(
            'Default Store View', Mage::app()->getStore(Mage_Core_Model_App::DISTRO_STORE_ID)->getName()
        );
        $this->assertEquals('2', Mage::app()->getStore(Mage_Core_Model_App::DISTRO_STORE_ID)->getRootCategoryId());

        $this->_applyScopeFixture('fixtures/update-existing.yaml');

        $this->assertEquals('English', Mage::app()->getStore(Mage_Core_Model_App::DISTRO_STORE_ID)->getName());
        $this->assertEquals('3', Mage::app()->getStore(Mage_Core_Model_App::DISTRO_STORE_ID)->getRootCategoryId());

    }

    protected function _applyScopeFixture($file)
    {
        $fixtureFile = $this->getFixture()->getVfs()->url($file);
        $data = json_decode(file_get_contents($fixtureFile), true);
        foreach ($data as $key => $values) {
            $this->_processor->apply($values, $key, $this->getFixture());
        }
    }
}
