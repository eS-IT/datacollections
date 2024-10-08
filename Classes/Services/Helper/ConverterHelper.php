<?php

/**
 * @since       17.09.2024 - 07:09
 *
 * @author      Patrick Froch <info@easySolutionsIT.de>
 *
 * @see         http://easySolutionsIT.de
 *
 * @copyright   e@sy Solutions IT 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Classes\Services\Helper;

use Esit\Databaselayer\Classes\Services\Helper\SerializeHelper;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;

class ConverterHelper
{

    private CollectionFactory $collectionFactory;


    public function __construct(
        private readonly SerializeHelper $serializeHelper
    ) {
    }


    /**
     * Setzt die CollectionFactory.
     * Kann wegen Ringbezug nicht injected werden.s
     *
     * @param CollectionFactory $collectionFactory
     *
     * @return void
     */
    public function setCollectionFactory(CollectionFactory $collectionFactory): void
    {
        $this->collectionFactory = $collectionFactory;
    }


    /**
     * Handelt es sich bei $value um ein (serialisiertes) Array,
     * wird daraus eine ArrayCollection erzeugt.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function convertArrayToCollection(mixed $value): mixed
    {
        $converted = $this->serializeHelper->unserialize($value);

        if (true === \is_array($converted)) {
            // Arrays immer umwandeln!
            return $this->collectionFactory->createArrayCollection($converted);
        }

        return $value;
    }
}
