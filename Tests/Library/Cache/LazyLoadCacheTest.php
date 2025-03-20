<?php

/**
 * @since       19.03.2025 - 15:13
 * @author      Patrick Froch <info@netgroup.de>
 * @see         http://www.netgroup.de
 * @copyright   NetGroup GmbH 2025
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Tests\Library\Cache;

use Esit\Datacollections\Classes\Library\Cache\LazyLoadCache;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Valueobjects\Classes\Database\Valueobjects\FieldnameValue;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LazyLoadCacheTest extends TestCase
{


    /**
     * @var (ArrayCollection&MockObject)|MockObject
     */
    private $collection;


    /**
     * @var (TablenameValue&MockObject)|MockObject
     */
    private $tablename;


    /**
     * @var (FieldnameValue&MockObject)|MockObject
     */
    private $fieldname;


    /**
     * @var (ArrayCollection&MockObject)|MockObject
     */
    private $row;


    /**
     * @var LazyLoadCache
     */
    private LazyLoadCache $cache;


    protected function setUp(): void
    {
        $this->collection   = $this->getMockBuilder(ArrayCollection::class)
                                   ->disableOriginalConstructor()
                                   ->getMock();

        $this->tablename    = $this->getMockBuilder(TablenameValue::class)
                                   ->disableOriginalConstructor()
                                   ->getMock();

        $this->fieldname    = $this->getMockBuilder(FieldnameValue::class)
                                   ->disableOriginalConstructor()
                                   ->getMock();

        $this->row          = $this->getMockBuilder(ArrayCollection::class)
                                   ->disableOriginalConstructor()
                                   ->getMock();

        $this->cache        = LazyLoadCache::getInstance($this->collection);
    }


    public function tearDown(): void
    {
        $this->cache->tearDown();
    }


    public function testContainsReturnFalseIfNoValueIsFound(): void
    {
        $tablename  = 'tl_test';
        $fieldname  = 'testfield';
        $name       = $tablename. '_' . $fieldname;

        $this->tablename->expects($this->once())
                        ->method('value')
                        ->willReturn($tablename);

        $this->fieldname->expects($this->once())
                        ->method('value')
                        ->willReturn($fieldname);

        $this->collection->expects($this->once())
                         ->method('getValue')
                         ->with($name)
                         ->willReturn(null);

        $this->assertFalse($this->cache->contains($this->tablename, $this->fieldname));
    }


    public function testContainsReturnTrueIfValueIsFound(): void
    {
        $value      = ['data'];
        $tablename  = 'tl_test';
        $fieldname  = 'testfield';
        $name       = $tablename. '_' . $fieldname;

        $this->tablename->expects($this->once())
                        ->method('value')
                        ->willReturn($tablename);

        $this->fieldname->expects($this->once())
                        ->method('value')
                        ->willReturn($fieldname);

        $this->collection->expects($this->once())
                         ->method('getValue')
                         ->with($name)
                         ->willReturn($value);

        $this->assertTrue($this->cache->contains($this->tablename, $this->fieldname));
    }


    public function testRemove(): void
    {
        $tablename  = 'tl_test';
        $fieldname  = 'testfield';
        $name       = $tablename. '_' . $fieldname;

        $this->tablename->expects($this->once())
                        ->method('value')
                        ->willReturn($tablename);

        $this->fieldname->expects($this->once())
                        ->method('value')
                        ->willReturn($fieldname);

        $this->collection->expects($this->once())
                         ->method('remove')
                         ->with($name);

        $this->cache->remove($this->tablename, $this->fieldname);
    }


    public function testSetValue(): void
    {
        $tablename  = 'tl_test';
        $fieldname  = 'testfield';
        $name       = $tablename. '_' . $fieldname;

        $this->tablename->expects($this->once())
                        ->method('value')
                        ->willReturn($tablename);

        $this->fieldname->expects($this->once())
                        ->method('value')
                        ->willReturn($fieldname);

        $this->collection->expects($this->once())
                         ->method('setValue')
                         ->with($name, $this->row);

        $this->cache->setValue($this->tablename, $this->fieldname, $this->row);
    }


    public function testGetValue(): void
    {
        $tablename  = 'tl_test';
        $fieldname  = 'testfield';
        $name       = $tablename. '_' . $fieldname;

        $this->tablename->expects($this->once())
                        ->method('value')
                        ->willReturn($tablename);

        $this->fieldname->expects($this->once())
                        ->method('value')
                        ->willReturn($fieldname);

        $this->collection->expects($this->once())
                         ->method('getValue')
                         ->with($name)
                         ->willReturn($this->row);

        $this->assertSame($this->row, $this->cache->getValue($this->tablename, $this->fieldname));
    }
}
