<?php

/**
 * @since       14.09.2024 - 12:39
 * @author      Patrick Froch <info@easySolutionsIT.de>
 * @see         http://easySolutionsIT.de
 * @copyright   e@sy Solutions IT 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Tests\Services\Helper;

use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Library\Collections\DatabaseRowCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\ConfigurationHelper;
use Esit\Datacollections\Classes\Services\Helper\LazyLoadHelper;
use Esit\Datacollections\Classes\Services\Helper\LoadHelper;
use Esit\Valueobjects\Classes\Database\Valueobjects\FieldnameValue;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LazyLoadHelperTest extends TestCase
{


    /**
     * @var (ConfigurationHelper&MockObject)|MockObject
     */
    private $configHelper;


    /**
     * @var (LoadHelper&MockObject)|MockObject
     */
    private $loadHelper;


    /**
     * @var (CollectionFactory&MockObject)|MockObject
     */
    private $collectionFactory;


    /**
     * @var (TablenameValue&MockObject)|MockObject
     */
    private $tablename;


    /**
     * @var (FieldnameValue&MockObject)|MockObject
     */
    private $fieldname;


    /**
     * @var (DatabaseRowCollection&MockObject)|MockObject
     */
    private $databaserow;


    /**
     * @var (ArrayCollection&MockObject)|MockObject
     */
    private $arrayCollection;


    /**
     * @var LazyLoadHelper
     */
    private LazyLoadHelper $helper;


    protected function setUp(): void
    {
        $this->configHelper         = $this->getMockBuilder(ConfigurationHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->loadHelper           = $this->getMockBuilder(LoadHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->collectionFactory    = $this->getMockBuilder(CollectionFactory::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->tablename            = $this->getMockBuilder(TablenameValue::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->fieldname            = $this->getMockBuilder(FieldnameValue::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->databaserow          = $this->getMockBuilder(DatabaseRowCollection::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->arrayCollection             = $this->getMockBuilder(ArrayCollection::class)
                                                  ->disableOriginalConstructor()
                                                  ->getMock();

        $this->helper               = new LazyLoadHelper($this->configHelper, $this->loadHelper);
    }


    public function testSetCollectionFactory(): void
    {
        $this->loadHelper->expects(self::once())
                         ->method('setCollectionFactory')
                         ->with($this->collectionFactory);

        $this->configHelper->expects(self::once())
                           ->method('setCollectionFactory')
                           ->with($this->collectionFactory);

        $this->helper->setCollectionFactory($this->collectionFactory);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadDataReturnNullIfLazayLoadingIsNotConfigured(): void
    {
        $value = 'TestValue';

        $this->configHelper->expects(self::once())
                           ->method('isLazyLodingField')
                           ->willReturn(false);

        $this->configHelper->expects(self::never())
                           ->method('getForeignTable');

        $this->configHelper->expects(self::never())
                           ->method('getForeignField');

        $this->configHelper->expects(self::never())
                           ->method('isSerialised');

        $this->loadHelper->expects(self::never())
                         ->method('loadMultiple');

        $this->loadHelper->expects(self::never())
                         ->method('loadOne');

        $this->assertNull($this->helper->loadData($this->tablename, $this->fieldname, $value));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadDataReturnNullIfForeignTableIsNotConfigured(): void
    {
        $value = 'TestValue';

        $this->configHelper->expects(self::once())
                           ->method('isLazyLodingField')
                           ->willReturn(true);

        $this->configHelper->expects(self::once())
                           ->method('getForeignTable')
                           ->with($this->tablename, $this->fieldname)
                           ->willReturn(null);

        $this->configHelper->expects(self::once())
                           ->method('getForeignField')
                           ->with($this->tablename, $this->fieldname)
                           ->willReturn($this->fieldname);

        $this->configHelper->expects(self::never())
                           ->method('isSerialised');

        $this->loadHelper->expects(self::never())
                         ->method('loadMultiple');

        $this->loadHelper->expects(self::never())
                         ->method('loadOne');

        $this->assertNull($this->helper->loadData($this->tablename, $this->fieldname, $value));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadDataReturnNullIfForeignFieldIsNotConfigured(): void
    {
        $value = 'TestValue';

        $this->configHelper->expects(self::once())
                           ->method('isLazyLodingField')
                           ->willReturn(true);

        $this->configHelper->expects(self::once())
                           ->method('getForeignTable')
                           ->with($this->tablename, $this->fieldname)
                           ->willReturn($this->tablename);

        $this->configHelper->expects(self::once())
                           ->method('getForeignField')
                           ->with($this->tablename, $this->fieldname)
                           ->willReturn(null);

        $this->configHelper->expects(self::never())
                           ->method('isSerialised');

        $this->loadHelper->expects(self::never())
                         ->method('loadMultiple');

        $this->loadHelper->expects(self::never())
                         ->method('loadOne');

        $this->assertNull($this->helper->loadData($this->tablename, $this->fieldname, $value));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadDataReturnDatabaseRowCollectionIfOneRowFound(): void
    {
        $value = 'TestValue';

        $this->configHelper->expects(self::once())
                           ->method('isLazyLodingField')
                           ->willReturn(true);

        $this->configHelper->expects(self::once())
                           ->method('getForeignTable')
                           ->with($this->tablename, $this->fieldname)
                           ->willReturn($this->tablename);

        $this->configHelper->expects(self::once())
                           ->method('getForeignField')
                           ->with($this->tablename, $this->fieldname)
                           ->willReturn($this->fieldname);

        $this->configHelper->expects(self::once())
                           ->method('isSerialised')
                           ->with($this->tablename, $this->fieldname)
                           ->willReturn(false);

        $this->loadHelper->expects(self::never())
                         ->method('loadMultiple');

        $this->loadHelper->expects(self::once())
                         ->method('loadOne')
                         ->with($this->tablename, $this->fieldname, $value)
                         ->willReturn($this->databaserow);

        $this->assertSame($this->databaserow, $this->helper->loadData($this->tablename, $this->fieldname, $value));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadDataReturnArrayCollectionIfOneRowFound(): void
    {
        $value = 'TestValue';

        $this->configHelper->expects(self::once())
                           ->method('isLazyLodingField')
                           ->willReturn(true);

        $this->configHelper->expects(self::once())
                           ->method('getForeignTable')
                           ->with($this->tablename, $this->fieldname)
                           ->willReturn($this->tablename);

        $this->configHelper->expects(self::once())
                           ->method('getForeignField')
                           ->with($this->tablename, $this->fieldname)
                           ->willReturn($this->fieldname);

        $this->configHelper->expects(self::once())
                           ->method('isSerialised')
                           ->with($this->tablename, $this->fieldname)
                           ->willReturn(true);

        $this->loadHelper->expects(self::once())
                         ->method('loadMultiple')
                         ->with($this->tablename, $this->fieldname, $value)
                         ->willReturn($this->arrayCollection);

        $this->loadHelper->expects(self::never())
                         ->method('loadOne');

        $this->assertSame($this->arrayCollection, $this->helper->loadData($this->tablename, $this->fieldname, $value));
    }
}
