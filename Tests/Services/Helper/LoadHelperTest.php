<?php

/**
 * @since       14.09.2024 - 11:39
 * @author      Patrick Froch <info@easySolutionsIT.de>
 * @see         http://easySolutionsIT.de
 * @copyright   e@sy Solutions IT 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Tests\Services\Helper;

use Esit\Databaselayer\Classes\Services\Helper\DatabaseHelper;
use Esit\Databaselayer\Classes\Services\Helper\SerializeHelper;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Library\Collections\DatabaseRowCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\LoadHelper;
use Esit\Valueobjects\Classes\Database\Valueobjects\FieldnameValue;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LoadHelperTest extends TestCase
{


    /**
     * @var (DatabaseHelper&MockObject)|MockObject
     */
    private $dbHelper;


    /**
     * @var (SerializeHelper&MockObject)|MockObject
     */
    private $serializeHelper;


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


    private $arrayCollecttion;


    /**
     * @var LoadHelper
     */
    private LoadHelper $helper;


    protected function setUp(): void
    {
        $this->dbHelper             = $this->getMockBuilder(DatabaseHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->serializeHelper      = $this->getMockBuilder(SerializeHelper::class)
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

        $this->arrayCollecttion     = $this->getMockBuilder(ArrayCollection::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->helper               = new LoadHelper($this->dbHelper, $this->serializeHelper);

        $this->helper->setCollectionFactory($this->collectionFactory);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadOneReturnNullIfNoDataFound(): void
    {
        $value      = 'testValue';
        $fieldname  = 'testfield';
        $tablename  = 'tl_testtable';
        $data       = [];

        $this->fieldname->expects(self::once())
                        ->method('value')
                        ->willReturn($fieldname);

        $this->tablename->expects(self::once())
                        ->method('value')
                        ->willReturn($tablename);

        $this->dbHelper->expects(self::once())
                       ->method('loadOneByValue')
                       ->with($value, $fieldname, $tablename)
                       ->willReturn($data);

        $this->collectionFactory->expects(self::never())
                                ->method('createDatabaseRowCollection');

        $this->assertNull($this->helper->loadOne($this->tablename, $this->fieldname, $value));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadOneReturnDatabaseRowCollectionIfDataFound(): void
    {
        $value      = 'testValue';
        $fieldname  = 'testfield';
        $tablename  = 'tl_testtable';
        $data       = ['test' => 'Data'];

        $this->fieldname->expects(self::once())
                        ->method('value')
                        ->willReturn($fieldname);

        $this->tablename->expects(self::once())
                        ->method('value')
                        ->willReturn($tablename);

        $this->dbHelper->expects(self::once())
                       ->method('loadOneByValue')
                       ->with($value, $fieldname, $tablename)
                       ->willReturn($data);

        $this->collectionFactory->expects(self::once())
                                ->method('createDatabaseRowCollection')
                                ->with($this->tablename, $data)
                                ->willReturn($this->databaserow);

        $this->assertSame($this->databaserow, $this->helper->loadOne($this->tablename, $this->fieldname, $value));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadMultipleReturnNullIfValueISNotSerialized(): void
    {
        $value = 'testValue';

        $this->serializeHelper->expects(self::once())
                              ->method('unserialize')
                              ->with($value)
                              ->willReturn($value);

        $this->fieldname->expects(self::never())
                        ->method('value');

        $this->tablename->expects(self::never())
                        ->method('value');

        $this->dbHelper->expects(self::never())
                       ->method('loadByList');

        $this->collectionFactory->expects(self::never())
                                ->method('createMultiDatabaseRowCollection');

        $this->assertNull($this->helper->loadMultiple($this->tablename, $this->fieldname, $value));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadMultipleReturnNullIfNoDataFound(): void
    {
        $value      = 'testValue';
        $search     = [$value];
        $data       = [];
        $fieldname  = 'testfield';
        $tablename  = 'tl_testtable';

        $this->serializeHelper->expects(self::once())
                              ->method('unserialize')
                              ->with($value)
                              ->willReturn($search);

        $this->fieldname->expects(self::once())
                        ->method('value')
                        ->willReturn($fieldname);

        $this->tablename->expects(self::once())
                        ->method('value')
                        ->willReturn($tablename);

        $this->dbHelper->expects(self::once())
                       ->method('loadByList')
                       ->with($search, $fieldname, $tablename)
                       ->willReturn($data);

        $this->collectionFactory->expects(self::never())
                                ->method('createMultiDatabaseRowCollection');

        $this->assertNull($this->helper->loadMultiple($this->tablename, $this->fieldname, $value));
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testLoadMultipleReturnCollectionIfDataFound(): void
    {
        $value = 'testValue';
        $search = [$value];
        $data   = ['test' => 'Data'];
        $fieldname  = 'testfield';
        $tablename  = 'tl_testtable';

        $this->serializeHelper->expects(self::once())
                              ->method('unserialize')
                              ->with($value)
                              ->willReturn($search);

        $this->fieldname->expects(self::once())
                        ->method('value')
                        ->willReturn($fieldname);

        $this->tablename->expects(self::once())
                        ->method('value')
                        ->willReturn($tablename);

        $this->dbHelper->expects(self::once())
                       ->method('loadByList')
                       ->with($search, $fieldname, $tablename)
                       ->willReturn($data);

        $this->collectionFactory->expects(self::once())
                                ->method('createMultiDatabaseRowCollection')
                                ->with($this->tablename, $data)
                                ->willReturn($this->arrayCollecttion);

        $rtn = $this->helper->loadMultiple($this->tablename, $this->fieldname, $value);
        $this->assertSame($this->arrayCollecttion, $rtn);
    }
}
