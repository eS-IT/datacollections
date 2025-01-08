<?php

/**
 * @since       15.09.2024 - 16:06
 * @author      Patrick Froch <info@easySolutionsIT.de>
 * @see         http://easySolutionsIT.de
 * @copyright   e@sy Solutions IT 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Tests\Library\Collections;

use Esit\Databaselayer\Classes\Services\Helper\DatabaseHelper;
use Esit\Databaselayer\Classes\Services\Helper\SerializeHelper;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Library\Collections\AbstractDatabaseRowCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\ConverterHelper;
use Esit\Datacollections\Classes\Services\Helper\LazyLoadHelper;
use Esit\Datacollections\EsitTestCase;
use Esit\Valueobjects\Classes\Database\Enums\TablenamesInterface;
use Esit\Valueobjects\Classes\Database\Valueobjects\FieldnameValue;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;
use PHPUnit\Framework\MockObject\MockObject;

class ConcreteDatabaseRowCollection extends AbstractDatabaseRowCollection
{
}

enum Tablenames implements TablenamesInterface
{
    case tl_test;
}

class AbstractDatabaseRowCollectionTest extends EsitTestCase
{


    /**
     * @var (CollectionFactory&MockObject)|MockObject
     */
    private $collectionFactory;


    /**
     * @var (ArrayCollection&MockObject)|MockObject
     */
    private $lazyData;


    /**
     * @var (AbstractDatabaseRowCollection&MockObject)|MockObject
     */
    private $lazyValue;


    /**
     * @var (SerializeHelper&MockObject)|MockObject
     */
    private $serializeHelper;


    /**
     * @var (ConverterHelper&MockObject)|MockObject
     */
    private $convterHelper;


    /**
     * @var (LazyLoadHelper&MockObject)|MockObject
     */
    private $loadHelper;


    /**
     * @var (TablenameValue&MockObject)|MockObject
     */
    private $tablename;


    /**
     * @var (FieldnameValue&MockObject)|MockObject
     */
    private $fieldname;


    /**
     * @var ConcreteDatabaseRowCollection
     */
    private ConcreteDatabaseRowCollection $collection;


    protected function setUp(): void
    {
        $this->collectionFactory    = $this->getMockBuilder(CollectionFactory::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->lazyData             = $this->getMockBuilder(ArrayCollection::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->lazyValue            = $this->getMockBuilder(ArrayCollection::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->childData            = $this->getMockBuilder(ArrayCollection::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->databaseHelper       = $this->getMockBuilder(DatabaseHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->serializeHelper      = $this->getMockBuilder(SerializeHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->convterHelper        = $this->getMockBuilder(ConverterHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->loadHelper           = $this->getMockBuilder(LazyLoadHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->tablename            = $this->getMockBuilder(TablenameValue::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->fieldname            = $this->getMockBuilder(FieldnameValue::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->collectionFactory->method('createArrayCollection')
                                ->willReturn($this->lazyData);

        $this->collection           = new ConcreteDatabaseRowCollection(
            $this->collectionFactory,
            $this->serializeHelper,
            $this->convterHelper,
            $this->loadHelper,
            $this->tablename
        );
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetValueReturnLazyLoadedValueIfSet(): void
    {
        $value = 'testvalue';

        $this->lazyData->expects(self::once())
                       ->method('contains')
                       ->with($value)
                       ->willReturn(true);

        $this->fieldname->expects(self::once())
                        ->method('value')
                        ->willReturn($value);

        $this->lazyData->expects(self::once())
                       ->method('getValue')
                       ->with($value)
                       ->willReturn($value);

        $this->lazyData->expects(self::never())
                       ->method('setValue');

        $rtn = $this->collection->getValueFromNameObject($this->fieldname);
        $this->assertSame($value, $rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetValueWillReturnValueIfItIsNotScalar(): void
    {

        $key    = 'testkey';
        $value  = new \StdClass();

        $this->lazyData->expects(self::exactly(2))
                       ->method('contains')
                       ->with($key)
                       ->willReturn(false);

        $this->fieldname->method('value')
                        ->willReturn($key);

        $this->serializeHelper->method('serialize') // für setValue()
                              ->willReturn($value);

        $this->convterHelper->method('convertArrayToCollection') // returnValue()
                            ->willReturn($value);

        $this->lazyData->expects(self::never())
                       ->method('getValue');

        $this->loadHelper->expects(self::never())
                         ->method('loadData');

        $this->lazyData->expects(self::never())
                       ->method('setValue');

        $this->collection->setValueWithNameObject($this->fieldname, $value);

        $rtn = $this->collection->getValueFromNameObject($this->fieldname);
        $this->assertSame($value, $rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetValueWillTryToLazyLoadValueIfItIsScalar(): void
    {

        $key    = 'testkey';
        $value  = 'TestValue';

        $this->lazyData->expects(self::exactly(2))
                       ->method('contains')
                       ->with($key)
                       ->willReturn(false);

        $this->fieldname->method('value')
                        ->willReturn($key);

        $this->serializeHelper->method('serialize') // für setValue()
                              ->willReturn($value);

        $this->convterHelper->method('convertArrayToCollection') // returnValue()
                            ->willReturn($value);

        $this->lazyData->expects(self::never())
                       ->method('getValue');

        $this->loadHelper->expects(self::once())
                         ->method('loadData')
                         ->with($this->tablename, $this->fieldname, $value)
                         ->willReturn(null);

        $this->lazyData->expects(self::once())
                       ->method('setValue')
                       ->with($key, null);

        $this->collection->setValueWithNameObject($this->fieldname, $value);

        $rtn = $this->collection->getValueFromNameObject($this->fieldname);
        $this->assertNull($rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetValueWillSetLazyLoadedValueIfItIsFound(): void
    {

        $key    = 'testkey';
        $value  = 'TestValue';

        $this->lazyData->expects(self::exactly(2))
                       ->method('contains')
                       ->with($key)
                       ->willReturn(false);

        $this->fieldname->method('value')
                        ->willReturn($key);

        $this->serializeHelper->method('serialize') // für setValue()
                              ->willReturn($value);

        $this->convterHelper->method('convertArrayToCollection') // returnValue()
                            ->willReturn($value);

        $this->lazyData->expects(self::never())
                       ->method('getValue');

        $this->loadHelper->expects(self::once())
                         ->method('loadData')
                         ->with($this->tablename, $this->fieldname, $value)
                         ->willReturn($this->lazyValue);

        $this->lazyData->expects(self::once())
                       ->method('setValue')
                       ->with($key, $this->lazyValue);

        $this->collection->setValueWithNameObject($this->fieldname, $value);

        $rtn = $this->collection->getValueFromNameObject($this->fieldname);
        $this->assertSame($this->lazyValue, $rtn);
    }


    public function testSetValueRemoveLazyLoadedValueIsIsSet(): void
    {
        $key    = 'testfield';
        $value  = 'testvalue';

        $this->fieldname->expects(self::exactly(3))
                        ->method('value')
                        ->willReturn($key);

        $this->lazyData->expects(self::once())
                       ->method('contains')
                       ->with($key)
                       ->willReturn(true);

        $this->lazyData->expects(self::once())
                       ->method('remove')
                       ->with($key);

        $this->collection->setValueWithNameObject($this->fieldname, $value);
    }


    public function testSetValueWithNameObjectReturnValue(): void
    {
        $key    = 'testfield';
        $value  = 'testvalue';

        $this->fieldname->expects(self::exactly(2))
                        ->method('value')
                        ->willReturn($key);

        $this->lazyData->expects(self::once())
                       ->method('contains')
                       ->with($key)
                       ->willReturn(false);

        $this->lazyData->expects(self::never())
                       ->method('remove');

        $this->collection->setValueWithNameObject($this->fieldname, $value);
    }
}
