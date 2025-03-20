<?php

/**
 * @since       19.03.2025 - 13:58
 *
 * @author      Patrick Froch <info@netgroup.de>
 *
 * @see         http://www.netgroup.de
 *
 * @copyright   NetGroup GmbH 2025
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Classes\Services\Factories;

use Esit\Datacollections\Classes\Library\Cache\LazyLoadCache;

class CacheFactory
{


    /**
     * @param CollectionFactory $factory
     */
    public function __construct(private readonly CollectionFactory $factory)
    {
    }


    /**
     * Gibt eine Instanz des LazyLoadCaches zurück.
     *
     * @return LazyLoadCache
     */
    public function getLazyLoadCache(): LazyLoadCache
    {
        $collection = $this->factory->createArrayCollection();

        return LazyLoadCache::getInstance($collection);
    }
}
