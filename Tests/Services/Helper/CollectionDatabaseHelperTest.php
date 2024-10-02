<?php

/**
 * @since       23.09.2024 - 20:09
 * @author      Patrick Froch <info@easySolutionsIT.de>
 * @see         http://easySolutionsIT.de
 * @copyright   e@sy Solutions IT 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Tests\Services\Helper;

use Esit\Databaselayer\Classes\Services\Helper\DatabaseHelper;
use Esit\Datacollections\Classes\Enums\FieldnamesInterface;
use Esit\Datacollections\Classes\Enums\TablenamesInterface;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Library\Collections\DatabaseRowCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\CollectionDatabaseHelper;
use Esit\Valueobjects\Classes\Database\Services\Factories\DatabasenameFactory;
use Esit\Valueobjects\Classes\Database\Valueobjects\FieldnameValue;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


enum Tablenames implements TablenamesInterface {
    case tl_testtabse;
}

enum Fieldnames implements FieldnamesInterface {
    case id;
}

class CollectionDatabaseHelperTest extends TestCase
{


    /**
     * @var (DatabaseHelper&MockObject)|MockObject
     */
    private $dbHelepr;


    /**
     * @var (DatabasenameFactory&MockObject)|MockObject
     */
    private $dbNameFactory;


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
    private $dbCollection;


    /**
     * @var (ArrayCollection&MockObject)|MockObject
     */
    private $arrayCollection;


    /**
     * @var CollectionDatabaseHelper
     */
    private CollectionDatabaseHelper $helper;


    protected function setUp(): void
    {
        $this->dbHelepr             = $this->getMockBuilder(DatabaseHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->dbNameFactory        = $this->getMockBuilder(DatabasenameFactory::class)
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

        $this->dbCollection         = $this->getMockBuilder(DatabaseRowCollection::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->arrayCollection      = $this->getMockBuilder(ArrayCollection::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->helper = new CollectionDatabaseHelper(
            $this->dbHelepr,
            $this->dbNameFactory,
            $this->collectionFactory
        );


    }


    public function testGetDatabaseHelper(): void
    {
        $this->assertSame($this->dbHelepr, $this->helper->getDatabaseHelper());
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadOneByValueReturnNullIfNoDataFound(): void
    {
        $value  = 'test';
        $offset = 12;
        $limit  = 34;
        $data   = [];

        $this->dbNameFactory->expects(self::once())
                            ->method('createTablenameFromString')
                            ->with(Tablenames::tl_testtabse->name)
                            ->willReturn($this->tablename);

        $this->dbNameFactory->expects(self::once())
                            ->method('createFieldnameFromString')
                            ->with(Fieldnames::id->name, $this->tablename)
                            ->willReturn($this->fieldname);

        $this->fieldname->expects(self::once())
                        ->method('value')
                        ->willReturn(Fieldnames::id->name);

        $this->tablename->expects(self::once())
                        ->method('value')
                        ->willReturn(Tablenames::tl_testtabse->name);

        $this->dbHelepr->expects(self::once())
                       ->method('loadByValue')
                       ->with($value, Fieldnames::id->name, Tablenames::tl_testtabse->name, $offset, $limit)
                       ->willReturn($data);

        $this->collectionFactory->expects(self::never())
                                ->method('createMultiDatabaseRowCollection');

        $rtn = $this->helper->loadByValue($value, Fieldnames::id, Tablenames::tl_testtabse, $offset, $limit);
        $this->assertNull($rtn);
    }


    public function testLoadByValue(): void
    {
        self::markTestIncomplete('Not implemented yet');
    }


    public function testLoadByList(): void
    {
        self::markTestIncomplete('Not implemented yet');
    }


    public function testLoadAll(): void
    {
        self::markTestIncomplete('Not implemented yet');
    }


    public function testInsert(): void
    {
        self::markTestIncomplete('Not implemented yet');
    }


    public function testUpdate(): void
    {
        self::markTestIncomplete('Not implemented yet');
    }


    public function testDelete(): void
    {
        self::markTestIncomplete('Not implemented yet');
    }


    public function testSave(): void
    {
        self::markTestIncomplete('Not implemented yet');
    }
}
