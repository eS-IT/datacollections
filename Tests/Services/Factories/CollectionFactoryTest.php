<?php

/**
 * @since       15.09.2024 - 08:00
 * @author      Patrick Froch <info@easySolutionsIT.de>
 * @see         http://easySolutionsIT.de
 * @copyright   e@sy Solutions IT 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Tests\Services\Factories;

use Esit\Databaselayer\Classes\Services\Helper\DatabaseHelper;
use Esit\Databaselayer\Classes\Services\Helper\SerializeHelper;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Library\Collections\AbstractDatabaseRowCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\ConfigurationHelper;
use Esit\Datacollections\Classes\Services\Helper\ConverterHelper;
use Esit\Datacollections\Classes\Services\Helper\LazyLoadHelper;
use Esit\Valueobjects\Classes\Database\Services\Factories\DatabasenameFactory;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CollectionFactoryTest extends TestCase
{


    /**
     * @var (LazyLoadHelper&MockObject)|MockObject
     */
    private $lazyLoadHelper;


    /**
     * @var (DatabaseHelper&MockObject)|MockObject
     */
    private $dbHelper;


    /**
     * @var (SerializeHelper&MockObject)|MockObject
     */
    private $serialzeHelper;


    /**
     * @var (ConverterHelper&MockObject)|MockObject
     */
    private $converterHelper;


    /**
     * @var (ArrayCollection&MockObject)|MockObject
     */
    private $arrayCollection;

    private $configHelper;


    /**
     * @var (TablenameValue&MockObject)|MockObject
     */
    private $tablename;


    /**
     * @var (DatabasenameFactory&MockObject)|MockObject
     */
    private $nameFactory;


    /**
     * @var CollectionFactory
     */
    private CollectionFactory $factory;


    protected function setUp(): void
    {
        $this->lazyLoadHelper   = $this->getMockBuilder(LazyLoadHelper::class)
                                       ->disableOriginalConstructor()
                                       ->getMock();

        $this->dbHelper         = $this->getMockBuilder(DatabaseHelper::class)
                                       ->disableOriginalConstructor()
                                       ->getMock();

        $this->serialzeHelper   = $this->getMockBuilder(SerializeHelper::class)
                                       ->disableOriginalConstructor()
                                       ->getMock();

        $this->converterHelper  = $this->getMockBuilder(ConverterHelper::class)
                                       ->disableOriginalConstructor()
                                       ->getMock();

        $this->arrayCollection  = $this->getMockBuilder(ArrayCollection::class)
                                       ->disableOriginalConstructor()
                                       ->getMock();

        $this->tablename        = $this->getMockBuilder(TablenameValue::class)
                                       ->disableOriginalConstructor()
                                       ->getMock();

        $this->nameFactory      = $this->getMockBuilder(DatabasenameFactory::class)
                                       ->disableOriginalConstructor()
                                       ->getMock();

        $this->configHelper     = $this->getMockBuilder(ConfigurationHelper::class)
                                       ->disableOriginalConstructor()
                                       ->getMock();

        $this->rowCollection    = $this->getMockBuilder(AbstractDatabaseRowCollection::class)
                                       ->disableOriginalConstructor()
                                       ->getMock();

        $this->factory          = new CollectionFactory(
            $this->lazyLoadHelper,
            $this->dbHelper,
            $this->serialzeHelper,
            $this->converterHelper,
            $this->nameFactory,
            $this->configHelper
        );
    }

    public function testCreateArrayCollection(): void
    {
        $data   = ['demo' => 'test', 'data' => 'Daten'];
        $rtn    = $this->factory->createArrayCollection($data);

        $this->assertSame(2, $rtn->count());
    }

    public function testCreateDatabaseRowCollection(): void
    {
        $data   = ['demo1' => 'test', 'data1' => 'Daten'];
        $rtn    = $this->factory->createDatabaseRowCollection($this->tablename, $data);

        $this->assertSame(2, $rtn->count());
    }

    public function testCreateMultiDatabaseRowCollection(): void
    {
        $data = [
            ['demo1' => 'test', 'data1' => 'Daten'],
            ['demo2' => 'test', 'data2' => 'Daten'],
        ];

        $rtn = $this->factory->createMultiDatabaseRowCollection($this->tablename, $data);

        $this->assertSame(2, $rtn->count());
    }

    public function testCreateCollectionIterator(): void
    {
        $data = ['test' => 'Array'];

        $this->arrayCollection->expects(self::once())
                              ->method('toArray')
                              ->willReturn($data);

        $this->assertNotNull($this->factory->createCollectionIterator($this->arrayCollection));
    }


}
