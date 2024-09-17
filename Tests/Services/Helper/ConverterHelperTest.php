<?php

/**
 * @since       17.09.2024 - 09:45
 * @author      Patrick Froch <info@netgroup.de>
 * @see         http://www.netgroup.de
 * @copyright   NetGroup GmbH 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Tests\Services\Helper;

use Esit\Databaselayer\Classes\Services\Helper\SerializeHelper;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\ConverterHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConverterHelperTest extends TestCase
{


    /**
     * @var (SerializeHelper&MockObject)|MockObject
     */
    private $serializeHelper;


    /**
     * @var (CollectionFactory&MockObject)|MockObject
     */
    private $collectionFactory;


    /**
     * @var (ArrayCollection&MockObject)|MockObject
     */
    private $collection;


    /**
     * @var ConverterHelper
     */
    private ConverterHelper $helper;


    protected function setUp(): void
    {
        $this->serializeHelper      = $this->getMockBuilder(SerializeHelper::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->collectionFactory    = $this->getMockBuilder(CollectionFactory::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->collection           = $this->getMockBuilder(ArrayCollection::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->helper               = new ConverterHelper($this->serializeHelper);
        
        $this->helper->setCollectionFactory($this->collectionFactory);
    }


    public function testConvertArrayToCollectionReturnCollectionIfValueIsASerializedArray(): void
    {
        $value = \serialize(['test' => 'value', 'test1' => 'value1']);

        $this->serializeHelper->expects(self::once())
                              ->method('unserialize')
                              ->with($value)
                              ->willReturn(\unserialize($value));

        $this->collectionFactory->expects(self::once())
                                ->method('createArrayCollection')
                                ->with(\unserialize($value))
                                ->willReturn($this->collection);

        $this->assertSame($this->collection, $this->helper->convertArrayToCollection($value));
    }


    public function testConvertArrayToCollectionReturnCollectionIfValueIsAArray(): void
    {
        $value = ['test' => 'value', 'test1' => 'value1'];

        $this->serializeHelper->expects(self::once())
                              ->method('unserialize')
                              ->with($value)
                              ->willReturn($value);

        $this->collectionFactory->expects(self::once())
                                ->method('createArrayCollection')
                                ->with($value)
                                ->willReturn($this->collection);

        $this->assertSame($this->collection, $this->helper->convertArrayToCollection($value));
    }


    public function testConvertArrayToCollectionReturnValueIfValueIsNotAArray(): void
    {
        $value = 'value1';

        $this->serializeHelper->expects(self::once())
                              ->method('unserialize')
                              ->with($value)
                              ->willReturn($value);

        $this->collectionFactory->expects(self::never())
                                ->method('createArrayCollection');

        $this->assertSame($value, $this->helper->convertArrayToCollection($value));
    }
}
