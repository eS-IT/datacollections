<?php

/**
 * @since       19.03.2025 - 15:11
 * @author      Patrick Froch <info@netgroup.de>
 * @see         http://www.netgroup.de
 * @copyright   NetGroup GmbH 2025
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Tests\Services\Factories;

use Esit\Datacollections\Classes\Services\Factories\CacheFactory;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use PHPUnit\Framework\TestCase;

class CacheFactoryTest extends TestCase
{


    private $collectionFactory;


    private $factory;


    protected function setUp(): void
    {
        $this->collectionFactory    = $this->getMockBuilder(CollectionFactory::class)
                                           ->disableOriginalConstructor()
                                           ->getMock();

        $this->factory              = new CacheFactory($this->collectionFactory);
    }


    public function testGetLazyLoadCache(): void
    {
        $this->assertNotNull($this->factory->getLazyLoadCache());
    }
}
