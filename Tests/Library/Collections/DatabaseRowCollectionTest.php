<?php

/**
 * @since       08.10.2024 - 20:26
 * @author      Patrick Froch <info@easySolutionsIT.de>
 * @see         http://easySolutionsIT.de
 * @copyright   e@sy Solutions IT 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Tests\Library\Collections;

use Esit\Databaselayer\Classes\Services\Helper\DatabaseHelper;
use Esit\Databaselayer\Classes\Services\Helper\SerializeHelper;
use Esit\Datacollections\Classes\Library\Cache\LazyLoadCache;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Library\Collections\DatabaseRowCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\ConfigurationHelper;
use Esit\Datacollections\Classes\Services\Helper\ConverterHelper;
use Esit\Datacollections\Classes\Services\Helper\LazyLoadHelper;
use Esit\Valueobjects\Classes\Database\Enums\FieldnamesInterface;
use Esit\Valueobjects\Classes\Database\Enums\TablenamesInterface;
use Esit\Valueobjects\Classes\Database\Services\Factories\DatabasenameFactory;
use Esit\Valueobjects\Classes\Database\Valueobjects\FieldnameValue;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

enum MyFieldnames implements FieldnamesInterface {
    case myfield;
}

enum MyTablenames implements TablenamesInterface {
    case tl_test;
}

class DatabaseRowCollectionTest extends TestCase
{


    /**
     * @var (CollectionFactory&MockObject)|MockObject
     */
    private $collectionFactory;


    /**
     * @var (SerializeHelper&MockObject)|MockObject
     */
    private $serializeHelper;


    /**
     * @var (ConverterHelper&MockObject)|MockObject
     */
    private $convterHelper;


    /**
     * @var (DatabaseHelper&MockObject)|MockObject
     */
    private $databaseHelper;


    /**
     * @var (LazyLoadHelper&MockObject)|MockObject
     */
    private $loadHelper;


    /**
     * @var (DatabasenameFactory&MockObject)|MockObject
     */
    private $nameFactory;


    /**
     * @var (TablenameValue&MockObject)|MockObject
     */
    private $tablename;


    /**
     * @var (FieldnameValue&MockObject)|MockObject
     */
    private $fieldname;


    /**
     * @var (ArrayCollection&MockObject)|MockObject
     */
    private $arrayCollection;


    /**
     * @var (ConfigurationHelper&MockObject)|MockObject
     */
    private $configHelper;


    /**
     * @var (LazyLoadCache&MockObject)|MockObject
     */
    private $cache;


    /**
     * @var DatabaseRowCollection
     */
    private DatabaseRowCollection $dbRowCollection;


    protected function setUp(): void
    {
        $this->collectionFactory    = $this->getMockBuilder(CollectionFactory::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->serializeHelper      = $this->getMockBuilder(SerializeHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->convterHelper        = $this->getMockBuilder(ConverterHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->databaseHelper       = $this->getMockBuilder(DatabaseHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->loadHelper           = $this->getMockBuilder(LazyLoadHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->nameFactory          = $this->getMockBuilder(DatabasenameFactory::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->tablename            = $this->getMockBuilder(TablenameValue::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->fieldname            = $this->getMockBuilder(FieldnameValue::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->arrayCollection      = $this->getMockBuilder(ArrayCollection::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->configHelper         = $this->getMockBuilder(ConfigurationHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->cache                = $this->getMockBuilder(LazyLoadCache::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->collectionFactory->method('createArrayCollection')
                                ->willReturn($this->arrayCollection);

        $this->dbRowCollection      = $this->getMockBuilder(DatabaseRowCollection::class)
                                           ->setConstructorArgs([
                                               $this->nameFactory,
                                               $this->collectionFactory,
                                               $this->serializeHelper,
                                               $this->convterHelper,
                                               $this->databaseHelper,
                                               $this->loadHelper,
                                               $this->configHelper,
                                               $this->cache,
                                               $this->tablename
                                           ])
                                           ->onlyMethods(['getValueFromNameObject', 'setValueWithNameObject'])
                                           ->getMock();
    }


    public function testGetTable(): void
    {
        $this->assertSame($this->tablename, $this->dbRowCollection->getTable());
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testSave(): void
    {
        $table  = 'tl_test';
        $id     = 12;
        $this->tablename->expects(self::once())
                        ->method('value')
                        ->willReturn($table);

        $this->databaseHelper->expects(self::once())
                             ->method('save')
                             ->with($table, [])
                             ->willReturn($id);

        $this->assertSame($id, $this->dbRowCollection->save());
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetValue(): void
    {
        $value = 'Test';

        $this->nameFactory->expects(self::once())
                          ->method('createFieldnameFromStringOrInterface')
                          ->with(MyFieldnames::myfield, $this->tablename)
                          ->willReturn($this->fieldname);

        $this->dbRowCollection->expects(self::once())
                              ->method('getValueFromNameObject')
                              ->with($this->fieldname)
                              ->willReturn($value);

        $rtn = $this->dbRowCollection->getValue(MyFieldnames::myfield);

        $this->assertSame($value, $rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testSetValue(): void
    {
        $value = 'Test';

        $this->nameFactory->expects(self::once())
                          ->method('createFieldnameFromStringOrInterface')
                          ->with(MyFieldnames::myfield, $this->tablename)
                          ->willReturn($this->fieldname);

        $this->dbRowCollection->setValue(MyFieldnames::myfield, $value);
    }

    public function testGetCommonDataAsArray(): void
    {
        $expected = ['TestKey' => 'TestValue'];

        $this->arrayCollection->expects(self::once())
                         ->method('toArray')
                         ->willReturn($expected);

        $this->assertSame($expected, $this->dbRowCollection->getCommonDataAsArray());
    }


    public function testGetCommonData(): void
    {
        $key    = 'TestKey';
        $value  = 'TestValue';

        $this->arrayCollection->expects(self::once())
                         ->method('getValue')
                         ->with($key)
                         ->willReturn($value);

        $this->assertSame($value, $this->dbRowCollection->getCommonValue($key));
    }


    public function testSetCommonData(): void
    {
        $key    = 'TestKey';
        $value  = 'TestValue';

        $this->arrayCollection->expects(self::once())
                         ->method('setValue')
                         ->with($key, $value);

        $this->dbRowCollection->setCommonValue($key, $value);
    }


    public function testCreateFrom(): void
    {
        $rtn = $this->dbRowCollection->createFrom(['test', 'elements']);
        $this->assertNotNull($rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetChildDataReturnchildDataIfIsSet(): void
    {
        $this->arrayCollection->expects(self::once())
                        ->method('contains')
                        ->with(MyTablenames::tl_test)
                        ->willReturn(true);

        $this->arrayCollection->expects(self::once())
                        ->method('getValue')
                        ->with(MyTablenames::tl_test->name)
                        ->willReturn($this->arrayCollection);

        $this->loadHelper->expects(self::never())
                         ->method('loadChildData');

        $this->convterHelper->expects(self::never())
                            ->method('convertArrayToCollection');

        $this->arrayCollection->expects(self::never())
                        ->method('setValue');

        $this->assertSame($this->arrayCollection, $this->dbRowCollection->getChildData(MyTablenames::tl_test));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetChildDataReturnchildReturnNullIfChildDataCouldNotBeLoaded(): void
    {
        $id = 12;

        $this->arrayCollection->expects(self::once())
                              ->method('contains')
                              ->with(MyTablenames::tl_test)
                              ->willReturn(false);

        $this->arrayCollection->expects(self::never())
                              ->method('getValue');

        $this->loadHelper->expects(self::once())
                         ->method('loadChildData')
                         ->with($this->tablename, MyTablenames::tl_test, $id)
                         ->willReturn(null);

        $this->convterHelper->expects(self::once())
                            ->method('convertArrayToCollection')
                            ->willReturn($id);

        $this->arrayCollection->expects(self::never())
                              ->method('setValue');

        $this->assertNull($this->dbRowCollection->getChildData(MyTablenames::tl_test));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetChildDataReturnchildReturnArrayCollectionIfChildDataCouldBeLoaded(): void
    {
        $id = 12;

        $this->arrayCollection->expects(self::once())
                              ->method('contains')
                              ->with(MyTablenames::tl_test)
                              ->willReturn(false);

        $this->arrayCollection->expects(self::never())
                              ->method('getValue');

        $this->loadHelper->expects(self::once())
                         ->method('loadChildData')
                         ->with($this->tablename, MyTablenames::tl_test, $id)
                         ->willReturn($this->arrayCollection);

        $this->convterHelper->expects(self::once())
                            ->method('convertArrayToCollection')
                            ->willReturn($id);

        $this->arrayCollection->expects(self::once())
                              ->method('setValue')
                              ->with(MyTablenames::tl_test->name, $this->arrayCollection);

        $this->assertSame($this->arrayCollection, $this->dbRowCollection->getChildData(MyTablenames::tl_test));
    }
}
