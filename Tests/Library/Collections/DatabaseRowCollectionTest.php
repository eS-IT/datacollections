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
use Esit\Datacollections\Classes\Exceptions\TypeNotAllowedException;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Library\Collections\DatabaseRowCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\LazyLoadHelper;
use Esit\Datacollections\EsitTestCase;
use Esit\Datacollections\Tests\Services\Helper\ConverterHelper;
use Esit\Valueobjects\Classes\Database\Valueobjects\FieldnameValue;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;
use PHPUnit\Framework\MockObject\MockObject;

class DatabaseRowCollectionTest extends EsitTestCase
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
     * @var (DatabaseHelper&MockObject)|MockObject
     */
    private $databaseHelper;


    /**
     * @var (SerializeHelper&MockObject)|MockObject
     */
    private $serializeHelper;


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
     * @var DatabaseRowCollection
     */
    private DatabaseRowCollection $collection;


    protected function setUp(): void
    {
        $this->collectionFactory    = $this->getMockBuilder(CollectionFactory::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->lazyData             = $this->getMockBuilder(ArrayCollection::class)
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

        $this->collection           = new DatabaseRowCollection(
            $this->collectionFactory,
            $this->serializeHelper,
            $this->convterHelper,
            $this->databaseHelper,
            $this->loadHelper,
            $this->tablename
        );
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testSave(): void
    {
        $table = 'tl_test';

        $this->tablename->expects(self::once())
                        ->method('value')
                        ->willReturn($table);

        $this->databaseHelper->expects(self::once())
                             ->method('save')
                             ->with($table, []);

        $this->collection->save();
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
                       ->with($this->fieldname)
                       ->willReturn(true);

        $this->fieldname->expects(self::once())
                        ->method('value')
                        ->willReturn($value);

        $this->lazyData->expects(self::once())
                       ->method('getValue')
                       ->with($value)
                       ->willReturn($value);

        $this->collection->getValue($this->fieldname);
    }


    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function testGetValueReturnWillRetrunArrayIfValueIsArray(): void
    {
        $value = 'testvalue';

        $this->lazyData->expects(self::exactly(2))
                       ->method('contains')
                       ->with(... $this->consecutiveParams(
                           [$value],
                           [$this->fieldname]
                       ))
                       ->willReturn(false);

        $this->fieldname->method('value')
                        ->willReturn($value);

        $this->serializeHelper->expects(self::once())
                              ->method('serialize')
                              ->with($value)
                              ->willReturn($value);

        $this->lazyData->expects(self::never())
                       ->method('getValue');

        $this->collection->setValue($this->fieldname, $value);

        $this->collection->getValue($this->fieldname);
    }

    //todo add more tests fpr getValue()!

    public function testSetValueThrowExceptionIfValueIsNotScalar(): void
    {
        $this->expectException(TypeNotAllowedException::class);
        $this->expectExceptionMessage('value have be a scalar or array');
        $this->collection->setValue($this->fieldname, $this->tablename);
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

        $this->collection->setValue($this->fieldname, $value);
    }


    public function testSetValue(): void
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

        $this->collection->setValue($this->fieldname, $value);
    }
}
