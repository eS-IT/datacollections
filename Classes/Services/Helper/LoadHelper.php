<?php

/**
 * @since       12.09.2024 - 17:25
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

use Esit\Databaselayer\Classes\Services\Helper\DatabaseHelper;
use Esit\Databaselayer\Classes\Services\Helper\SerializeHelper;
use Esit\Datacollections\Classes\Library\Collections\AbstractDatabaseRowCollection;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Valueobjects\Classes\Database\Valueobjects\FieldnameValue;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;

class LoadHelper
{


    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;


    /**
     * @param DatabaseHelper  $dbHelper
     * @param SerializeHelper $serializeHelper
     */
    public function __construct(
        private readonly DatabaseHelper $dbHelper,
        private readonly SerializeHelper $serializeHelper
    ) {
    }


    /**
     * Setzt die MapFactory,
     * kann wegen Ringbezug nich injected werden!
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
     * Lädt einen Datensatz aus einer Fremdtabelle.
     *
     * @param TablenameValue $foreignTable
     * @param FieldnameValue $foreignField
     * @param mixed          $value
     *
     * @return AbstractDatabaseRowCollection|null
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function loadOne(
        TablenameValue $foreignTable,
        FieldnameValue $foreignField,
        mixed $value
    ): ?AbstractDatabaseRowCollection {
        $data = $this->dbHelper->loadOneByValue($value, $foreignField->value(), $foreignTable->value());

        if (!empty($data)) {
            return $this->collectionFactory->createDatabaseRowCollection($foreignTable, $data);
        }

        return null;
    }


    /**
     * Lädt mehrere Daten aus einer Fremdtabelle.
     *
     * @param TablenameValue $foreignTable
     * @param FieldnameValue $foreignField
     * @param string|array   $value
     *
     * @return ArrayCollection|null
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function loadMultiple(
        TablenameValue $foreignTable,
        FieldnameValue $foreignField,
        string|array $value
    ): ?ArrayCollection {
        $search = $this->serializeHelper->unserialize($value);

        if (true === \is_array($search)) {
            $data = $this->dbHelper->loadByList($search, $foreignField->value(), $foreignTable->value());

            if (!empty($data)) {
                return $this->collectionFactory->createMultiDatabaseRowCollection($foreignTable, $data);
            }
        }

        return null;
    }


    /**
     * Lädt mehrere Datensätze zu einer Id.
     *
     * @param TablenameValue $foreignTable
     * @param FieldnameValue $foreignField
     * @param int            $id
     *
     * @return ArrayCollection|null
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function loadMultipleById(
        TablenameValue $foreignTable,
        FieldnameValue $foreignField,
        int $id
    ): ?ArrayCollection {
        $rows = $this->dbHelper->loadByValue($pid, $foreignField->value(), $foreignTable->value());

        if (!empty($rows)) {
            return $this->collectionFactory->createMultiDatabaseRowCollection($foreignTable, $rows);
        }
    }
}
