<?php

/**
 * @since       17.09.2024 - 07:09
 * @author      Patrick Froch <info@easySolutionsIT.de>
 * @see         http://easySolutionsIT.de
 * @copyright   e@sy Solutions IT 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Tests\Services\Helper;

use Esit\Databaselayer\Classes\Services\Helper\SerializeHelper;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;

class ConverterHelper
{


    public function __construct(
        private readonly SerializeHelper $serializeHelper,
        private readonly CollectionFactory $collectionFactory
    ) {
    }


    /**
     * Handelt es sich bei $value um ein (serialisiertes) Array,
     * wird daraus eine ArrayCollection erzeugt.
     *
     * @param mixed $value
     *
     * @return ArrayCollection
     */
    public function convertArrayToCollection(mixed $value): ArrayCollection
    {
        $converted  = $this->serializeHelper->unserialize($value);

        if (true === \is_array($converted)) {
            // Arrays immer umwandeln!
            return $this->collectionFactory->createArrayCollection($converted);
        }

        return $value;
    }
}
