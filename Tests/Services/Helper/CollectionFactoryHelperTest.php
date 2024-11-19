<?php

/**
 * @since       18.11.2024 - 16:36
 *
 * @author      Patrick Froch <info@netgroup.de>
 *
 * @see         http://www.netgroup.de
 *
 * @copyright   NetGroup GmbH 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Tests\Services\Helper;

use Esit\Datacollections\Classes\Enums\TablenamesInterface;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Library\Collections\DatabaseRowCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\CollectionFactoryHelper;
use Esit\Valueobjects\Classes\Database\Services\Factories\DatabasenameFactory;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

enum CollectionFactoryHelperTestTablenames implements TablenamesInterface {
    case test;
}

class CollectionFactoryHelperTest extends TestCase
{

    /**
     * @var (CollectionFactory&MockObject)|MockObject
     */
    private $collectionFactory;


    /**
     * @var (DatabasenameFactory&MockObject)|MockObject
     */
    private $dbNameFactory;


    /**
     * @var (TablenameValue&MockObject)|MockObject
     */
    private $tablenameValue;


    /**
     * @var (DatabaseRowCollection&MockObject)|MockObject
     */
    private $dbRow;


    private $arrayCollection;


    /**
     * @var array|string[]
     */
    private array $data = ['test', 'data'];


    /**
     * @var CollectionFactoryHelper
     */
    private CollectionFactoryHelper $helper;


    protected function setUp(): void
    {
        $this->collectionFactory    = $this->getMockBuilder(CollectionFactory::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->dbNameFactory        = $this->getMockBuilder(DatabasenameFactory::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->tablenameValue       = $this->getMockBuilder(TablenameValue::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->dbRow                = $this->getMockBuilder(DatabaseRowCollection::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->arrayCollection      = $this->getMockBuilder(ArrayCollection::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->helper               = new CollectionFactoryHelper($this->collectionFactory, $this->dbNameFactory);
    }


    public function testCreateArrayCollection(): void
    {
        $this->collectionFactory->expects(self::once())
                                ->method('createArrayCollection')
                                ->with($this->data)
                                ->willReturn($this->arrayCollection);

        $this->assertSame($this->arrayCollection, $this->helper->createArrayCollection($this->data));
    }


    /**
     * @return void
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function testCreateDatabaseRowCollection(): void
    {
        $this->dbNameFactory->expects(self::once())
                            ->method('createTablenameFromInterface')
                            ->with(CollectionFactoryHelperTestTablenames::test)
                            ->willReturn($this->tablenameValue);

        $this->collectionFactory->expects(self::once())
                                ->method('createDatabaseRowCollection')
                                ->with($this->tablenameValue, $this->data)
                                ->willReturn($this->dbRow);

        $rtn = $this->helper->createDatabaseRowCollection(CollectionFactoryHelperTestTablenames::test, $this->data);

        $this->assertSame($this->dbRow, $rtn);
    }


    /**
     * @return void
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function testCreateMultiDatabaseRowCollection(): void
    {
        $this->dbNameFactory->expects(self::once())
                            ->method('createTablenameFromInterface')
                            ->with(CollectionFactoryHelperTestTablenames::test)
                            ->willReturn($this->tablenameValue);

        $this->collectionFactory->expects(self::once())
                                ->method('createMultiDatabaseRowCollection')
                                ->with($this->tablenameValue, $this->data)
                                ->willReturn($this->arrayCollection);

        $rtn = $this->helper->createMultiDatabaseRowCollection(
            CollectionFactoryHelperTestTablenames::test,
            $this->data
        );

        $this->assertSame($this->arrayCollection, $rtn);
    }
}
