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
use Esit\Datacollections\Classes\Library\Collections\DatabaseRowCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\ConverterHelper;
use Esit\Datacollections\Classes\Services\Helper\LazyLoadHelper;
use Esit\Valueobjects\Classes\Database\Enums\FieldnamesInterface;
use Esit\Valueobjects\Classes\Database\Services\Factories\DatabasenameFactory;
use Esit\Valueobjects\Classes\Database\Valueobjects\FieldnameValue;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

enum MyFieldnames implements FieldnamesInterface {
    case myfield;
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

    private $fieldname;


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

        $this->dbRowCollection      = $this->getMockBuilder(DatabaseRowCollection::class)
                                           ->setConstructorArgs([
                                               $this->nameFactory,
                                               $this->collectionFactory,
                                               $this->serializeHelper,
                                               $this->convterHelper,
                                               $this->databaseHelper,
                                               $this->loadHelper,
                                               $this->tablename
                                           ])->onlyMethods(['getValueFromNameObject', 'setValueWithNameObject'])
                                           ->getMock();
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetValue(): void
    {
        $value = 'Test';

        $this->nameFactory->expects(self::once())
                          ->method('createFieldnameFromInterface')
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
}
