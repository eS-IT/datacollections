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
use Esit\Datacollections\EsitTestCase;
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
    case uuid;
}

class CollectionDatabaseHelperTest extends EsitTestCase
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
                       ->method('loadOneByValue')
                       ->with($value, Fieldnames::id->name, Tablenames::tl_testtabse->name, $offset, $limit)
                       ->willReturn($data);

        $this->collectionFactory->expects(self::never())
                                ->method('createDatabaseRowCollection');

        $rtn = $this->helper->loadOneByValue($value, Fieldnames::id, Tablenames::tl_testtabse, $offset, $limit);
        $this->assertNull($rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadOneByValueReturnDatabaseRowIfDataFound(): void
    {
        $value  = 'test';
        $offset = 12;
        $limit  = 34;
        $data   = ['TestData'];

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
                       ->method('loadOneByValue')
                       ->with($value, Fieldnames::id->name, Tablenames::tl_testtabse->name, $offset, $limit)
                       ->willReturn($data);

        $this->collectionFactory->expects(self::once())
                                ->method('createDatabaseRowCollection')
                                ->with($this->tablename, $data)
                                ->willReturn($this->dbCollection);

        $rtn = $this->helper->loadOneByValue($value, Fieldnames::id, Tablenames::tl_testtabse, $offset, $limit);
        $this->assertSAme($this->dbCollection, $rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadByValueReturnArrayCollectionIfNoDataWasFound(): void
    {
        $value  = 'test';
        $offset = 12;
        $limit  = 34;
        $data   = [['TestValues']];

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

        $this->collectionFactory->expects(self::once())
                                ->method('createMultiDatabaseRowCollection')
                                ->with($this->tablename, $data)
                                ->willReturn($this->arrayCollection);

        $rtn = $this->helper->loadByValue($value, Fieldnames::id, Tablenames::tl_testtabse, $offset, $limit);
        $this->assertSame($this->arrayCollection, $rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadByValueReturnArrayCollectionIfDataWasFound(): void
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


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadByListReturnNullIfNoDataWasFound(): void
    {
        $value              = ['test', 'value'];
        $order              = 'ASC';
        $offset             = 12;
        $limit              = 34;
        $data               = [];
        $searchFieldString  = 'uuid';

        $this->dbNameFactory->expects(self::once())
                            ->method('createTablenameFromString')
                            ->with(Tablenames::tl_testtabse->name)
                            ->willReturn($this->tablename);

        $this->dbNameFactory->expects(self::exactly(2))
                            ->method('createFieldnameFromString')
                            ->with(... $this->consecutiveParams(
                                [Fieldnames::id->name, $this->tablename],
                                [Fieldnames::id->name, $this->tablename]
                            ))
                            ->willReturn($this->fieldname);

        $this->fieldname->expects(self::exactly(2))
                        ->method('value')
                        ->willReturn(Fieldnames::id->name);

        $this->tablename->expects(self::once())
                        ->method('value')
                        ->willReturn(Tablenames::tl_testtabse->name);

        $this->dbHelepr->expects(self::once())
                       ->method('loadByList')
                       ->with(
                           $value,
                           Tablenames::tl_testtabse->name,
                           Fieldnames::id->name,
                           $order,
                           $offset,
                           $limit,
                           Fieldnames::id->name
                       )
                       ->willReturn($data);

        $this->collectionFactory->expects(self::never())
                                ->method('createMultiDatabaseRowCollection');

        $rtn = $this->helper->loadByList($value, Fieldnames::id, Tablenames::tl_testtabse, $order, $offset, $limit);
        $this->assertNull($rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadByListUseSearchFieldIfItIsSet(): void
    {
        $value              = ['test', 'value'];
        $order              = 'ASC';
        $offset             = 12;
        $limit              = 34;
        $data               = [];

        $this->dbNameFactory->expects(self::once())
                            ->method('createTablenameFromString')
                            ->with(Tablenames::tl_testtabse->name)
                            ->willReturn($this->tablename);

        $this->dbNameFactory->expects(self::exactly(2))
                            ->method('createFieldnameFromString')
                            ->with(... $this->consecutiveParams(
                                [Fieldnames::id->name, $this->tablename],
                                [Fieldnames::uuid->name, $this->tablename]
                            ))
                            ->willReturn($this->fieldname);

        $this->fieldname->expects(self::exactly(2))
                        ->method('value')
                        ->willReturnOnConsecutiveCalls(Fieldnames::id->name, Fieldnames::uuid->name);

        $this->tablename->expects(self::once())
                        ->method('value')
                        ->willReturn(Tablenames::tl_testtabse->name);

        $this->dbHelepr->expects(self::once())
                       ->method('loadByList')
                       ->with(
                           $value,
                           Tablenames::tl_testtabse->name,
                           Fieldnames::id->name,
                           $order,
                           $offset,
                           $limit,
                           Fieldnames::uuid->name
                       )
                       ->willReturn($data);

        $this->collectionFactory->expects(self::never())
                                ->method('createMultiDatabaseRowCollection');

        $rtn = $this->helper->loadByList(
            $value,
            Fieldnames::id,
            Tablenames::tl_testtabse,
            $order,
            $offset,
            $limit,
            Fieldnames::uuid
        );

        $this->assertNull($rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadByListReturnArrayCollectionIfDataIsNotEmpty(): void
    {
        $value              = ['test', 'value'];
        $order              = 'ASC';
        $offset             = 12;
        $limit              = 34;
        $data               = [['test'], ['value']];

        $this->dbNameFactory->expects(self::once())
                            ->method('createTablenameFromString')
                            ->with(Tablenames::tl_testtabse->name)
                            ->willReturn($this->tablename);

        $this->dbNameFactory->expects(self::exactly(2))
                            ->method('createFieldnameFromString')
                            ->with(... $this->consecutiveParams(
                                [Fieldnames::id->name, $this->tablename],
                                [Fieldnames::uuid->name, $this->tablename]
                            ))
                            ->willReturn($this->fieldname);

        $this->fieldname->expects(self::exactly(2))
                        ->method('value')
                        ->willReturnOnConsecutiveCalls(Fieldnames::id->name, Fieldnames::uuid->name);

        $this->tablename->expects(self::once())
                        ->method('value')
                        ->willReturn(Tablenames::tl_testtabse->name);

        $this->dbHelepr->expects(self::once())
                       ->method('loadByList')
                       ->with(
                           $value,
                           Tablenames::tl_testtabse->name,
                           Fieldnames::id->name,
                           $order,
                           $offset,
                           $limit,
                           Fieldnames::uuid->name
                       )
                       ->willReturn($data);

        $this->collectionFactory->expects(self::once())
                                ->method('createMultiDatabaseRowCollection')
                                ->willReturn($this->arrayCollection);

        $rtn = $this->helper->loadByList(
            $value,
            Fieldnames::id,
            Tablenames::tl_testtabse,
            $order,
            $offset,
            $limit,
            Fieldnames::uuid
        );

        $this->assertSame($this->arrayCollection, $rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadAllReturnNullIfNoDataWasFound(): void
    {
        $order  = 'DESC';
        $offset = 12;
        $limit  = 34;
        $data   = [];

        $this->dbNameFactory->expects(self::once())
                            ->method('createTablenameFromString')
                            ->with(Tablenames::tl_testtabse->name)
                            ->willReturn($this->tablename);

        $this->dbNameFactory->expects(self::never())
                            ->method('createFieldnameFromString');

        $this->dbHelepr->expects(self::once())
                       ->method('loadAll')
                       ->with(Tablenames::tl_testtabse->name, '', $order, $offset, $limit)
                       ->willReturn($data);

        $this->collectionFactory->expects(self::never())
                                ->method('createMultiDatabaseRowCollection');

        $rtn = $this->helper->loadAll(Tablenames::tl_testtabse, null, $order, $offset, $limit);

        $this->assertNull($rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadAllReturnArrayCollectionIfDataWasFound(): void
    {
        $order  = 'DESC';
        $offset = 12;
        $limit  = 34;
        $data   = [['test'], ['value']];

        $this->dbNameFactory->expects(self::once())
                            ->method('createTablenameFromString')
                            ->with(Tablenames::tl_testtabse->name)
                            ->willReturn($this->tablename);

        $this->dbNameFactory->expects(self::never())
                            ->method('createFieldnameFromString');

        $this->dbHelepr->expects(self::once())
                       ->method('loadAll')
                       ->with(Tablenames::tl_testtabse->name, '', $order, $offset, $limit)
                       ->willReturn($data);

        $this->collectionFactory->expects(self::once())
                                ->method('createMultiDatabaseRowCollection')
                                ->with($this->tablename, $data)
                                ->willReturn($this->arrayCollection);

        $rtn = $this->helper->loadAll(Tablenames::tl_testtabse, null, $order, $offset, $limit);

        $this->assertSame($this->arrayCollection, $rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadAllSetOrderFieldIfItIsSet(): void
    {
        $order  = 'DESC';
        $offset = 12;
        $limit  = 34;
        $data   = [['test'], ['value']];

        $this->dbNameFactory->expects(self::once())
                            ->method('createTablenameFromString')
                            ->with(Tablenames::tl_testtabse->name)
                            ->willReturn($this->tablename);

        $this->dbNameFactory->expects(self::once())
                            ->method('createFieldnameFromString')
                            ->with(Fieldnames::id->name)
                            ->willReturn($this->fieldname);

        $this->dbHelepr->expects(self::once())
                       ->method('loadAll')
                       ->with(Tablenames::tl_testtabse->name, $this->fieldname, $order, $offset, $limit)
                       ->willReturn($data);

        $this->collectionFactory->expects(self::once())
                                ->method('createMultiDatabaseRowCollection')
                                ->with($this->tablename, $data)
                                ->willReturn($this->arrayCollection);

        $rtn = $this->helper->loadAll(Tablenames::tl_testtabse, Fieldnames::id, $order, $offset, $limit);

        $this->assertSame($this->arrayCollection, $rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testInsertProcessesAnArray(): void
    {
        $id     = 12;
        $values = ['test', 'value'];

        $this->dbHelepr->expects(self::once())
                       ->method('insert')
                       ->with($values, Tablenames::tl_testtabse->name)
                       ->willReturn($id);

        $this->arrayCollection->expects(self::never())
                              ->method('toArray');

        $rtn = $this->helper->insert($values, Tablenames::tl_testtabse);
        $this->assertSame($id, $rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testInsertProcessesAnArrayCollection(): void
    {
        $id     = 12;
        $values = ['test', 'value'];

        $this->dbHelepr->expects(self::once())
                       ->method('insert')
                       ->with($values, Tablenames::tl_testtabse->name)
                       ->willReturn($id);

        $this->arrayCollection->expects(self::once())
                              ->method('toArray')
                              ->willReturn($values);

        $rtn = $this->helper->insert($this->arrayCollection, Tablenames::tl_testtabse);
        $this->assertSame($id, $rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testUpdateProcessesAnArray(): void
    {
        $id     = 12;
        $values = ['test', 'value'];

        $this->dbHelepr->expects(self::once())
                       ->method('update')
                       ->with($values, $id, Tablenames::tl_testtabse->name);

        $this->arrayCollection->expects(self::never())
                              ->method('toArray');

        $this->helper->update($values, $id, Tablenames::tl_testtabse);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testUpdateProcessesAnArrayCollection(): void
    {
        $id     = 12;
        $values = ['test', 'value'];

        $this->dbHelepr->expects(self::once())
                       ->method('update')
                       ->with($values, (string) $id, Tablenames::tl_testtabse->name);

        $this->arrayCollection->expects(self::once())
                              ->method('toArray')
                              ->willReturn($values);

        $this->helper->update($this->arrayCollection, $id, Tablenames::tl_testtabse);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testDelete(): void
    {
        $value = 12;

        $this->dbHelepr->expects(self::once())
                       ->method('delete')
                       ->with($value, Fieldnames::id->name, Tablenames::tl_testtabse->name);

        $this->helper->delete($value, Fieldnames::id, Tablenames::tl_testtabse);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testSaveProcessesAnArray(): void
    {
        $id     = 12;
        $values = ['test', 'value'];

        $this->dbHelepr->expects(self::once())
                       ->method('save')
                       ->with(Tablenames::tl_testtabse->name, $values)
                       ->willReturn($id);

        $this->arrayCollection->expects(self::never())
                              ->method('toArray');

        $rtn = $this->helper->save($values, Tablenames::tl_testtabse);
        $this->assertSame($id, $rtn);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testSaveProcessesAnArrayCollection(): void
    {
        $id     = 12;
        $values = ['test', 'value'];

        $this->dbHelepr->expects(self::once())
                       ->method('save')
                       ->with(Tablenames::tl_testtabse->name, $values)
                       ->willReturn($id);

        $this->arrayCollection->expects(self::once())
                              ->method('toArray')
                              ->willReturn($values);

        $rtn = $this->helper->save($this->arrayCollection, Tablenames::tl_testtabse);
        $this->assertSame($id, $rtn);
    }
}
