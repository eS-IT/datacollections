<?php

/**
 * @since       15.09.2024 - 08:32
 * @author      Patrick Froch <info@easySolutionsIT.de>
 * @see         http://easySolutionsIT.de
 * @copyright   e@sy Solutions IT 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Tests\Library\Collections;

use Esit\Databaselayer\Classes\Services\Helper\SerializeHelper;
use Esit\Datacollections\Classes\Exceptions\MethodNotAllowedException;
use Esit\Datacollections\Classes\Library\Collections\AbstractCollection;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\ConverterHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


class ConcreteCollection extends AbstractCollection
{
    public function returnValue(mixed $key): mixed
    {
        return parent::returnValue($key);
    }

    public function handleValue(mixed $key, mixed $value): void
    {
        parent::handleValue($key, $value);
    }
}


class AbstractCollectionTest extends TestCase
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
    private $converterHelper;


    /**
     * @var (ArrayCollection&MockObject)|MockObject
     */
    private $arrayCollection;


    /**
     * @var ConcreteCollection
     */
    private ConcreteCollection $collection;


    protected function setUp(): void
    {
        $this->collectionFactory    = $this->getMockBuilder(CollectionFactory::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->serializeHelper      = $this->getMockBuilder(SerializeHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->converterHelper      = $this->getMockBuilder(ConverterHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->arrayCollection      = $this->getMockBuilder(ArrayCollection::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->collection           = new ConcreteCollection(
            $this->collectionFactory,
            $this->serializeHelper,
            $this->converterHelper
        );
    }


    public function testFetchData(): void
    {
        $this->assertEmpty($this->collection->fetchData());
    }


    /**
     * @return void
     * @throws \Exception
     */
    public function testSet(): void
    {
        $msg = 'Method was not allowed to be called on this object. Use $this->setValue() instead.';
        $this->expectException(MethodNotAllowedException::class);
        $this->expectExceptionMessage($msg);
        $this->collection->set('test', 'value');
    }


    public function testGet(): void
    {
        $msg = 'Method was not allowed to be called on this object. Use $this->getValue() instead.';
        $this->expectException(MethodNotAllowedException::class);
        $this->expectExceptionMessage($msg);
        $this->collection->get('test');
    }

    public function testCloumn(): void
    {
        $expected   = ['value02', 'value03', 'value04'];
        $data       = [
            ['test' => 'value02'],
            ['test' => 'value03'],
            ['test' => 'value04']
        ];

        $collection = new ConcreteCollection(
            $this->collectionFactory,
            $this->serializeHelper,
            $this->converterHelper,
            $data
        );

        $this->collectionFactory->expects($this->once())
                                ->method('createArrayCollection')
                                ->with($expected)
                                ->willReturn($this->arrayCollection);

        $rtn = $collection->cloumn('test');
        $this->assertSame($this->arrayCollection, $rtn);
    }
}
