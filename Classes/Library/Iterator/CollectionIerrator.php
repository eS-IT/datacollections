<?php

/**
 * @since       16.09.2024 - 20:28
 * @author      Patrick Froch <info@easySolutionsIT.de>
 * @see         http://easySolutionsIT.de
 * @copyright   e@sy Solutions IT 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Classes\Library\Iterator;

use ArrayIterator;
use Esit\Databaselayer\Classes\Services\Helper\SerializeHelper;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;

class CollectionIerrator extends ArrayIterator
{


    /**
     * @param array|object $iterator
     * @param int $flags
     * @param serializeHelper|null $serializeHelper
     * @param CollectionFactory|null $collectionFactory
     */
    public function __construct(
        array|object $iterator,
        int $flags = 0,
        private readonly ?SerializeHelper $serializeHelper = null,
        private readonly ?CollectionFactory $collectionFactory = null
    ) {
        parent::__construct($iterator, $flags);
    }


    /**
     * @return mixed
     */
    public function current(): mixed
    {
        $value      = parent::current();
        $converted  = $this->serializeHelper->unserialize($value);

        if (true === \is_array($converted)) {
            // Arrays immer umwandeln!
            return $this->collectionFactory->createArrayCollection($converted);
        }

        return $value;
    }
}
