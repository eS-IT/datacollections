<?php

/**
 * @since       18.11.2024 - 16:34
 *
 * @author      Patrick Froch <info@netgroup.de>
 *
 * @see         http://www.netgroup.de
 *
 * @copyright   NetGroup GmbH 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Classes\Services\Helper;

use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Library\Collections\DatabaseRowCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory as BaseCollectionFactory;
use Esit\Valueobjects\Classes\Database\Enums\TablenamesInterface;
use Esit\Valueobjects\Classes\Database\Services\Factories\DatabasenameFactory;

class CollectionFactoryHelper
{


    /**
     * @param BaseCollectionFactory $collectionFactory
     * @param DatabasenameFactory   $dbNameFactory
     */
    public function __construct(
        private readonly BaseCollectionFactory $collectionFactory,
        private readonly DatabasenameFactory $dbNameFactory
    ) {
    }


    /**
     * Erzeugt aus einem TablenamesInterface und einem Datenarray eine DatabaseRowCollection.
     *
     * @param TablenamesInterface $table
     * @param array               $data
     *
     * @return DatabaseRowCollection
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function createDatabaseRowCollection(TablenamesInterface $table, array $data): DatabaseRowCollection
    {
        $tablename = $this->dbNameFactory->createTablenameFromInterface($table);

        return $this->collectionFactory->createDatabaseRowCollection($tablename, $data);
    }


    /**
     * Erzeugt aus einem TablenamesInterface und einem Array (oder eine ArrayCollection) eine ArrayCollection mit
     * DatabaseRowCollections.
     *
     * @param TablenamesInterface   $table
     * @param array|ArrayCollection $data
     *
     * @return ArrayCollection
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function createMultiDatabaseRowCollection(
        TablenamesInterface $table,
        array|ArrayCollection $data
    ): ArrayCollection {
        $tablename = $this->dbNameFactory->createTablenameFromInterface($table);

        return $this->collectionFactory->createMultiDatabaseRowCollection($tablename, $data);
    }
}
