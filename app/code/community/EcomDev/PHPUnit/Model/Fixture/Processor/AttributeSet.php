<?php

class EcomDev_PHPUnit_Model_Fixture_Processor_AttributeSet implements EcomDev_PHPUnit_Model_Fixture_ProcessorInterface
{
    const STORAGE_KEY = 'attribute_set';

    public function initialize(EcomDev_PHPUnit_Model_FixtureInterface $fixture)
    {
        $collection = Mage::getModel('eav/entity_attribute_set')->getCollection();
        $collection->walk('delete');
        return $this;
    }

    public function apply(array $data, $key, EcomDev_PHPUnit_Model_FixtureInterface $fixture)
    {
        EcomDev_PHPUnit_Test_Case_Util::app()->disableEvents();

        if ($fixture->getStorageData(self::STORAGE_KEY) !== null) {
            throw new RuntimeException('Data was not cleared after previous test');
        }

        $attributeSets = array();

        foreach ($data as $row) {

            $object = Mage::getModel('eav/entity_attribute_set')->setData($row);
            $object->isObjectNew(true);
            EcomDev_Utils_Reflection::setRestrictedPropertyValues(
                $object->getResource(),
                array(
                    '_isPkAutoIncrement' => false
                )
            );
            $object->save();
            if ($object->getId()) {
                $attributeSets[self::STORAGE_KEY][$object->getId()] = $object;
            }
        }
        EcomDev_PHPUnit_Test_Case_Util::app()->getCache()->clean();
        Mage::getSingleton('eav/config')->clear();
        $fixture->setStorageData(self::STORAGE_KEY, $attributeSets);

        EcomDev_PHPUnit_Test_Case_Util::app()->enableEvents();

        return $this;
    }



    public function discard(array $data, $key, EcomDev_PHPUnit_Model_FixtureInterface $fixture)
    {
        if ($fixture->getStorageData(self::STORAGE_KEY) === null) {
            return $this;
        }

        EcomDev_PHPUnit_Test_Case_Util::app()->disableEvents();

        $storedModels = array_reverse($fixture->getStorageData(self::STORAGE_KEY));
        $storedModels->walk('delete');

        $fixture->setStorageData(self::STORAGE_KEY, null);
        EcomDev_PHPUnit_Test_Case_Util::app()->getCache()->clean();
        Mage::getSingleton('eav/config')->clear();

        EcomDev_PHPUnit_Test_Case_Util::app()->enableEvents();
        return $this;
    }
}
