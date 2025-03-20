<?php

/**
 * @since       05.09.2024 - 17:25
 *
 * @author      Patrick Froch <info@easySolutionsIT.de>
 *
 * @see         http://easySolutionsIT.de
 *
 * @copyright   e@sy Solutions IT 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Classes\Services\Factories;

use Esit\Databaselayer\Classes\Services\Helper\DatabaseHelper;
use Esit\Databaselayer\Classes\Services\Helper\SerializeHelper;
use Esit\Datacollections\Classes\Library\Cache\LazyLoadCache;
use Esit\Datacollections\Classes\Library\Collections\AbstractCollection;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Library\Collections\DatabaseRowCollection;
use Esit\Datacollections\Classes\Library\Iterator\CollectionIterator;
use Esit\Datacollections\Classes\Services\Helper\ConfigurationHelper;
use Esit\Datacollections\Classes\Services\Helper\ConverterHelper;
use Esit\Datacollections\Classes\Services\Helper\LazyLoadHelper;
use Esit\Valueobjects\Classes\Database\Services\Factories\DatabasenameFactory;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;

class CollectionFactory
{


    /**
     * @param LazyLoadHelper      $lazyLoadHelper
     * @param DatabaseHelper      $dbHelper
     * @param SerializeHelper     $serializeHelper
     * @param ConverterHelper     $converterHelper
     * @param DatabasenameFactory $nameFactory
     * @param ConfigurationHelper $configurationHelper
     */
    public function __construct(
        private readonly LazyLoadHelper $lazyLoadHelper,
        private readonly DatabaseHelper $dbHelper,
        private readonly SerializeHelper $serializeHelper,
        private readonly ConverterHelper $converterHelper,
        private readonly DatabasenameFactory $nameFactory,
        private readonly ConfigurationHelper $configurationHelper,
    ) {
        $this->lazyLoadHelper->setCollectionFactory($this);
        $this->converterHelper->setCollectionFactory($this);
    }


    /**
     * Gibt eine Instanz des LazyLoadCaches zurück.
     *
     * @return LazyLoadCache
     */
    public function getLazyLoadCache(): LazyLoadCache
    {
        $collection = $this->createArrayCollection();

        return LazyLoadCache::getInstance($collection);
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
        return new ArrayCollection($this, $this->serializeHelper, $this->converterHelper, $data);
    }


    /**
     * Erstellt eine DatabaseRowMap.
     *
     * @param TablenameValue        $tablename
     * @param array|ArrayCollection $data
     *
     * @return DatabaseRowCollection
     */
    public function createDatabaseRowCollection(
        TablenameValue $tablename,
        array|ArrayCollection $data = []
    ): DatabaseRowCollection {
        return new DatabaseRowCollection(
            $this->nameFactory,
            $this,
            $this->serializeHelper,
            $this->converterHelper,
            $this->dbHelper,
            $this->lazyLoadHelper,
            $this->configurationHelper,
            $this->getLazyLoadCache(),
            $tablename,
            $data
        );
    }


    /**
     * Erzeugt eine ArrayMap mit einer DatabaseRowMap pro Tabellenzeile.
     *
     * @param TablenameValue $tablename
     * @param array          $data
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


    /**
     * Erzeugt einen CollectionIterator.
     *
     * @param AbstractCollection $collection
     *
     * @return CollectionIterator
     */
    public function createCollectionIterator(AbstractCollection $collection): CollectionIterator
    {
        return new CollectionIterator($collection->toArray(), 0, $this->converterHelper);
    }
}
