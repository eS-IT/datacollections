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
use Esit\Datacollections\Classes\Library\Cache\LazyLoadCache;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Library\Collections\AbstractDatabaseRowCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\ConfigurationHelper;
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
     * @var (ConfigurationHelper&MockObject)|MockObject
     */
    private $configHelper;


    /**
     * @var (LazyLoadCache&MockObject)|MockObject
     */
    private $cache;

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

        $this->configHelper         = $this->getMockBuilder(ConfigurationHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->cache                = $this->getMockBuilder(LazyLoadCache::class)
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
            $this->configHelper,
            $this->cache,
            $this->tablename
        );
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetValueFromNameObjectWillReturnLazyLoadedValueIfSet(): void
    {
        $this->cache->expects(self::once())
                       ->method('contains')
                       ->with($this->tablename, $this->fieldname)
                       ->willReturn(true);

        $this->cache->expects(self::once())
                       ->method('getValue')
                       ->with($this->tablename, $this->fieldname)
                       ->willReturn($this->lazyData);

        $this->convterHelper->expects(self::never()) // für $this->returnValue()
                            ->method('convertArrayToCollection');

        $this->configHelper->expects(self::never())
                           ->method('isLazyLodingField');

        $this->loadHelper->expects(self::never())
                         ->method('loadData');

        $this->cache->expects(self::never())
                       ->method('setValue');

        $rtn = $this->collection->getValueFromNameObject($this->fieldname);
        $this->assertSame($this->lazyData, $rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetValueFromNameObjectWillReturnValueIfItIsNotALazaLoadingField(): void
    {
        $value = 'testvalue';

        $this->cache->expects(self::once())
                       ->method('contains')
                       ->with($this->tablename, $this->fieldname)
                       ->willReturn(false);

        $this->cache->expects(self::never())
                       ->method('getValue');

        $this->convterHelper->expects(self::once()) // für $this->returnValue()
                            ->method('convertArrayToCollection')
                            ->willReturn($value);

        $this->configHelper->expects(self::once())
                           ->method('isLazyLodingField')
                           ->with($this->tablename, $this->fieldname)
                           ->willReturn(false);

        $this->loadHelper->expects(self::never())
                         ->method('loadData');

        $this->cache->expects(self::never())
                       ->method('setValue');

        $rtn = $this->collection->getValueFromNameObject($this->fieldname);
        $this->assertSame($value, $rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetValueFromNameObjectWillReturnNullIfItIsALazyLoadingFieldAndNoDateWereFound(): void
    {
        $value = 'testvalue';

        $this->cache->expects(self::once())
                       ->method('contains')
                       ->with($this->tablename, $this->fieldname)
                       ->willReturn(false);

        $this->cache->expects(self::never())
                       ->method('getValue');

        $this->convterHelper->expects(self::once()) // für $this->returnValue()
                            ->method('convertArrayToCollection')
                            ->willReturn($value);

        $this->configHelper->expects(self::once())
                           ->method('isLazyLodingField')
                           ->with($this->tablename, $this->fieldname)
                           ->willReturn(true);

        $this->loadHelper->expects(self::once())
                         ->method('loadData')
                         ->with($this->tablename, $this->fieldname)
                         ->willReturn(null);

        $this->cache->expects(self::once())
                       ->method('setValue')
                       ->with($this->tablename, $this->fieldname, null);

        $rtn = $this->collection->getValueFromNameObject($this->fieldname);
        $this->assertNull($rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetValueFromNameObjectWillReturnLazyDataIfItIsALazyLoadingFieldAndDateWereFound(): void
    {
        $value = 'testvalue';

        $this->cache->expects(self::once())
                       ->method('contains')
                       ->with($this->tablename, $this->fieldname)
                       ->willReturn(false);

        $this->cache->expects(self::never())
                       ->method('getValue');

        $this->convterHelper->expects(self::once()) // für $this->returnValue()
                            ->method('convertArrayToCollection')
                            ->willReturn($value);

        $this->configHelper->expects(self::once())
                           ->method('isLazyLodingField')
                           ->with($this->tablename, $this->fieldname)
                           ->willReturn(true);

        $this->loadHelper->expects(self::once())
                         ->method('loadData')
                         ->with($this->tablename, $this->fieldname)
                         ->willReturn($this->lazyValue);

        $this->cache->expects(self::once())
                       ->method('setValue')
                       ->with($this->tablename, $this->fieldname, $this->lazyValue);

        $rtn = $this->collection->getValueFromNameObject($this->fieldname);
        $this->assertSame($this->lazyValue, $rtn);
    }


    public function testSetValueWithNameObjectRemoveLazyLoadedValueIsIsSet(): void
    {
        $value  = 'testvalue';

        $this->cache->expects(self::once())
                       ->method('contains')
                       ->with($this->tablename, $this->fieldname)
                       ->willReturn(true);

        $this->cache->expects(self::once())
                       ->method('remove')
                       ->with($this->tablename, $this->fieldname);

        $this->collection->setValueWithNameObject($this->fieldname, $value);
    }


    public function testSetValueWithNameObjectReturnValue(): void
    {
        $value  = 'testvalue';

        $this->cache->expects(self::once())
                       ->method('contains')
                       ->with($this->tablename, $this->fieldname)
                       ->willReturn(false);

        $this->cache->expects(self::never())
                       ->method('remove');

        $this->collection->setValueWithNameObject($this->fieldname, $value);
    }
}
