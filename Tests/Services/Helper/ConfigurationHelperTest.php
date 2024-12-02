<?php

/**
 * @since       14.09.2024 - 16:09
 * @author      Patrick Froch <info@easySolutionsIT.de>
 * @see         http://easySolutionsIT.de
 * @copyright   e@sy Solutions IT 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Tests\Services\Helper;

use Esit\Datacollections\Classes\Enums\DcaConfig;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\ConfigurationHelper;
use Esit\Datacollections\Classes\Services\Helper\DcaHelper;
use Esit\Valueobjects\Classes\Database\Enums\TablenamesInterface;
use Esit\Valueobjects\Classes\Database\Services\Factories\DatabasenameFactory;
use Esit\Valueobjects\Classes\Database\Valueobjects\FieldnameValue;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


enum MyTablenames implements TablenamesInterface
{
    case tl_test;
}

class ConfigurationHelperTest extends TestCase
{


    /**
     * @var (DcaHelper&MockObject)|MockObject
     */
    private $dcaHelper;


    /**
     * @var (DatabasenameFactory&MockObject)|MockObject
     */
    private $nameFactory;


    /**
     * @var (CollectionFactory&MockObject)|MockObject
     */
    private $collectionFactory;


    /**
     * @var (TablenameValue&MockObject)|MockObject
     */
    private $table;


    /**
     * @var (FieldnameValue&MockObject)|MockObject
     */
    private $field;


    /**
     * @var (ArrayCollection&MockObject)|MockObject
     */
    private $arrayCollection;


    /**
     * @var ConfigurationHelper
     */
    private ConfigurationHelper $helper;


    protected function setUp(): void
    {
        $this->dcaHelper            = $this->getMockBuilder(DcaHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->nameFactory          = $this->getMockBuilder(DatabasenameFactory::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->collectionFactory    = $this->getMockBuilder(CollectionFactory::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->table                = $this->getMockBuilder(TablenameValue::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->field                = $this->getMockBuilder(FieldnameValue::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->arrayCollection      = $this->getMockBuilder(ArrayCollection::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->helper               = new ConfigurationHelper($this->dcaHelper, $this->nameFactory);

        $this->helper->setCollectionFactory($this->collectionFactory);
    }


    public function testIsLazyLodingFieldReturnFalseIfDepandancysAreNull(): void
    {
        $this->dcaHelper->expects(self::once())
                        ->method('getDepandancies')
                        ->with($this->table, $this->field)
                        ->willReturn(null);

        $this->assertFalse($this->helper->isLazyLodingField($this->table, $this->field));
    }


    public function testIsLazyLodingFieldReturnTrueIfDepandancysAreNotNull(): void
    {
        $this->dcaHelper->expects(self::once())
                        ->method('getDepandancies')
                        ->with($this->table, $this->field)
                        ->willReturn($this->arrayCollection);

        $this->assertTrue($this->helper->isLazyLodingField($this->table, $this->field));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetForeignTableRetrunNullIfDepandendysAreEmpty(): void
    {
        $this->dcaHelper->expects(self::once())
                        ->method('getDepandancies')
                        ->with($this->table, $this->field)
                        ->willReturn(null);

        $this->arrayCollection->expects(self::never())
                              ->method('getValue');

        $this->nameFactory->expects(self::never())
                          ->method('createTablenameFromString');

        $this->assertNull($this->helper->getForeignTable($this->table, $this->field));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetForeignTableRetrunObjectIfDepandendysAreNotEmpty(): void
    {
        $tablename = 'tl_test';

        $this->dcaHelper->expects(self::once())
                        ->method('getDepandancies')
                        ->with($this->table, $this->field)
                        ->willReturn($this->arrayCollection);

        $this->arrayCollection->expects(self::once())
                              ->method('getValue')
                              ->with(DcaConfig::table->name)
                              ->willReturn($tablename);

        $this->nameFactory->expects(self::once())
                          ->method('createTablenameFromString')
                          ->with($tablename)
                          ->willReturn($this->table);

        $this->assertSame($this->table, $this->helper->getForeignTable($this->table, $this->field));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetForeignFieldRetrunNullIfDepandendysAreEmpty(): void
    {
        $this->dcaHelper->expects(self::once())
                        ->method('getDepandancies')
                        ->with($this->table, $this->field)
                        ->willReturn(null);

        $this->arrayCollection->expects(self::never())
                              ->method('getValue');

        $this->nameFactory->expects(self::never())
                          ->method('createFieldnameFromString');

        $this->assertNull($this->helper->getForeignField($this->table, $this->field));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetForeignFieldRetrunObjectIfDepandendysAreNotEmpty(): void
    {
        $fieldname = 'testfield';

        $this->dcaHelper->expects(self::once())
                        ->method('getDepandancies')
                        ->with($this->table, $this->field)
                        ->willReturn($this->arrayCollection);

        $this->arrayCollection->expects(self::once())
                              ->method('getValue')
                              ->with(DcaConfig::field->name)
                              ->willReturn($fieldname);

        $this->nameFactory->expects(self::once())
                          ->method('createFieldnameFromString')
                          ->with($fieldname, $this->table)
                          ->willReturn($this->field);

        $this->assertSame($this->field, $this->helper->getForeignField($this->table, $this->field));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testIsSerialisedReturnFalseIfDepandendysAreEmpty(): void
    {
        $this->dcaHelper->expects(self::once())
                        ->method('getDepandancies')
                        ->with($this->table, $this->field)
                        ->willReturn(null);

        $this->arrayCollection->expects(self::never())
                              ->method('getValue');

        $this->assertFalse($this->helper->isSerialised($this->table, $this->field));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testIsSerialisedReturnFalseIfFieldIsNotSerialised(): void
    {
        $this->dcaHelper->expects(self::once())
                        ->method('getDepandancies')
                        ->with($this->table, $this->field)
                        ->willReturn($this->arrayCollection);

        $this->arrayCollection->expects(self::once())
                              ->method('getValue')
                              ->with(DcaConfig::serialised->name)
                              ->willReturn(false);

        $this->assertFalse($this->helper->isSerialised($this->table, $this->field));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testIsSerialisedReturnTrueIfFieldIsSerialised(): void
    {
        $this->dcaHelper->expects(self::once())
                        ->method('getDepandancies')
                        ->with($this->table, $this->field)
                        ->willReturn($this->arrayCollection);

        $this->arrayCollection->expects(self::once())
                              ->method('getValue')
                              ->with(DcaConfig::serialised->name)
                              ->willReturn(true);

        $this->assertTrue($this->helper->isSerialised($this->table, $this->field));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testIsSerialisedReturnTrueIfFieldIsSerialisedAndValueIsAString(): void
    {
        $this->dcaHelper->expects(self::once())
                        ->method('getDepandancies')
                        ->with($this->table, $this->field)
                        ->willReturn($this->arrayCollection);

        $this->arrayCollection->expects(self::once())
                              ->method('getValue')
                              ->with(DcaConfig::serialised->name)
                              ->willReturn('true');

        $this->assertTrue($this->helper->isSerialised($this->table, $this->field));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testIsSerialisedReturnTrueIfFieldIsSerialisedAndValueIsOne(): void
    {
        $this->dcaHelper->expects(self::once())
                        ->method('getDepandancies')
                        ->with($this->table, $this->field)
                        ->willReturn($this->arrayCollection);

        $this->arrayCollection->expects(self::once())
                              ->method('getValue')
                              ->with(DcaConfig::serialised->name)
                              ->willReturn(1);

        $this->assertTrue($this->helper->isSerialised($this->table, $this->field));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetChildFieldReturnNullIfChildFieldIsNotDefined(): void
    {
        $fieldname = '';

        $this->nameFactory->expects(self::once())
                          ->method('createTablenameFromInterface')
                          ->with(MyTablenames::tl_test)
                          ->willReturn($this->table);

        $this->dcaHelper->expects(self::once())
                        ->method('getChildDepandancies')
                        ->with($this->table)
                        ->willReturn($fieldname);

        $this->nameFactory->expects(self::never())
                          ->method('createFieldnameFromString');

        $this->assertNull($this->helper->getChildField(MyTablenames::tl_test, $this->table));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetChildFieldReturnFieldNameIfChildFieldIsDefined(): void
    {
        $fieldname = 'pid';

        $this->nameFactory->expects(self::once())
                          ->method('createTablenameFromInterface')
                          ->with(MyTablenames::tl_test)
                          ->willReturn($this->table);

        $this->dcaHelper->expects(self::once())
                        ->method('getChildDepandancies')
                        ->with($this->table)
                        ->willReturn($fieldname);

        $this->nameFactory->expects(self::once())
                          ->method('createFieldnameFromString')
                          ->with($fieldname, $this->table)
                          ->willReturn($this->field);

        $this->assertSame($this->field, $this->helper->getChildField(MyTablenames::tl_test, $this->table));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetChildTable(): void
    {
        $this->nameFactory->expects(self::once())
                          ->method('createTablenameFromInterface')
                          ->with(MyTablenames::tl_test)
                          ->willReturn($this->table);

        $this->assertSame($this->table, $this->helper->getChildTable(MyTablenames::tl_test));
    }
}
