<?php

/**
 * @since       17.09.2024 - 09:38
 * @author      Patrick Froch <info@netgroup.de>
 * @see         http://www.netgroup.de
 * @copyright   NetGroup GmbH 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Tests\Library\Iterator;

use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Library\Iterator\CollectionIerrator;
use Esit\Datacollections\Classes\Services\Helper\ConverterHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CollectionIerratorTest extends TestCase
{


    /**
     * @var (ConverterHelper&MockObject)|MockObject
     */
    private $converterHelper;


    /**
     * @var (ArrayCollection&MockObject)|MockObject
     */
    private $collection;


    /**
     * @var array
     */
    private array $data = [[12, 34, 'TestValue']];


    /**
     * @var CollectionIerrator
     */
    private CollectionIerrator $iterator;


    protected function setUp(): void
    {
        $this->converterHelper  = $this->getMockBuilder(ConverterHelper::class)
                                       ->disableOriginalConstructor()
                                       ->getMock();

        $this->collection       = $this->getMockBuilder(ArrayCollection::class)
                                       ->disableOriginalConstructor()
                                       ->getMock();

        $this->iterator         = new CollectionIerrator($this->data, 0, $this->converterHelper);
    }


    public function testCurrent(): void
    {
        $this->converterHelper->expects(self::once())
                              ->method('convertArrayToCollection')
                              ->with($this->data[0])
                              ->willReturn($this->collection);

        $this->assertSame($this->collection, $this->iterator->current());
    }
}
