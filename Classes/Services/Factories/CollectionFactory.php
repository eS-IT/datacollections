<?php

/**
 * @since       05.09.2024 - 17:25
 * @author      Patrick Froch <info@easySolutionsIT.de>
 * @see         http://easySolutionsIT.de
 * @copyright   e@sy Solutions IT 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Classes\Services\Factories;

use Esit\Databaselayer\Classes\Services\Helper\DatabaseHelper;
use Esit\Databaselayer\Classes\Services\Helper\SerializeHelper;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Library\Collections\DatabaseRowCollection;
use Esit\Datacollections\Classes\Services\Helper\LazyLoadHelper;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;

class CollectionFactory
{


    /**
     * @param LazyLoadHelper $lazyLoadHelper
     * @param DatabaseHelper $dbHelper
     */
    public function __construct(
        private readonly LazyLoadHelper  $lazyLoadHelper,
        private readonly DatabaseHelper  $dbHelper,
        private readonly SerializeHelper $serialzeHelper
    ) {
        $this->lazyLoadHelper->setCollectionFactory($this);
    }


    /**
     * Erstellt eine allgemiengültige Map, ohne speziellen Typ.
     * Ist der Parameter $data leer, wird eine leere Map erstellt.
     *
     * @param array $data
     *
     * @return ArrayCollection
     */
    public function createArrayCollection(array $data = []): ArrayCollection
    {
        return new ArrayCollection($this, $this->serialzeHelper, $data);
    }


    /**
     * Erstellt eine DatabaseRowMap.
     *
     * @param TablenameValue $tablename
     * @param array $data
     *
     * @return DatabaseRowCollection
     */
    public function createDatabaseRowCollection(TablenameValue $tablename, array $data = []): DatabaseRowCollection
    {
        return new DatabaseRowCollection(
            $this,
            $this->dbHelper,
            $this->serialzeHelper,
            $this->lazyLoadHelper,
            $tablename,
            $data
        );
    }


    /**
     * Erzeugt eine ArrayMap mit einer DatabaseRowMap pro Tabellenzeile.
     *
     * @param TablenameValue $tablename
     * @param array $data
     *
     * @return ArrayCollection
     */
    public function createMultiDatabaseRowCollection(TablenameValue $tablename, array $data = []): ArrayCollection
    {
        $multiData = $this->createArrayCollection();

        if (!empty($data)) {
            foreach ($data as $i => $row) {
                $multiData->setValue($i, $this->createDatabaseRowCollection($tablename, $row));
            }
        }

        return $multiData;
    }
}
