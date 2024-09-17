<?php

/**
 * @since       15.09.2024 - 08:20
 * @author      Patrick Froch <info@easySolutionsIT.de>
 * @see         http://easySolutionsIT.de
 * @copyright   e@sy Solutions IT 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Tests\Library\Collections;

use Esit\Databaselayer\Classes\Services\Helper\SerializeHelper;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Tests\Services\Helper\ConverterHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ArrayCollectionTest extends TestCase
{


    /**
     * @var (CollectionFactory&MockObject)|MockObject
     */
    private $collectionFactory;


    /**
     * @var (SerializeHelper&MockObject)|MockObject
     */
    private $serializeHelper;

    private $converterHelper;


    /**
     * @var ArrayCollection
     */
    private ArrayCollection $collection;


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

        $this->collection = new ArrayCollection(
            $this->collectionFactory,
            $this->serializeHelper,
            $this->converterHelper
        );
    }

    public function testGetValue(): void
    {
        self::markTestIncomplete('Muss angepasst werden');
        $key    = 'test';
        $data   = [12, 34, 'TestValue'];

        $this->serializeHelper->expects(self::once())
                              ->method('serialize')
                              ->with($data)
                              ->willReturn(\serialize($data));

        $this->collection->setValue($key, $data);

        // $data ist hier serialisiert, da die Methoden aus AbstractCollection nicht mit gestest werden!
        $this->assertSame(\serialize($data), $this->collection->getValue($key));
    }
}
