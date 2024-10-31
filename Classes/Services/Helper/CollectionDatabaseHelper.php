<?php

/**
 * @since       20.09.2024 - 16:32
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

use Doctrine\DBAL\Exception;
use Esit\Databaselayer\Classes\Services\Helper\DatabaseHelper;
use Esit\Datacollections\Classes\Library\Collections\AbstractDatabaseRowCollection;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Valueobjects\Classes\Database\Enums\FieldnamesInterface;
use Esit\Valueobjects\Classes\Database\Enums\TablenamesInterface;
use Esit\Valueobjects\Classes\Database\Services\Factories\DatabasenameFactory;

class CollectionDatabaseHelper
{


    /**
     * @param DatabaseHelper      $dbHelepr
     * @param DatabasenameFactory $dbNameFactory
     * @param CollectionFactory   $collectionFactory
     */
    public function __construct(
        private readonly DatabaseHelper $dbHelepr,
        private readonly DatabasenameFactory $dbNameFactory,
        private readonly CollectionFactory $collectionFactory,
    ) {
    }


    /**
     * @return DatabaseHelper
     */
    public function getDatabaseHelper(): DatabaseHelper
    {
        return $this->dbHelepr;
    }


    /**
     * @return DatabasenameFactory
     */
    public function getDatabasenameFactory(): DatabasenameFactory
    {
        return $this->dbNameFactory;
    }


    /**
     * @return CollectionFactory
     */
    public function getCollectionFactory(): CollectionFactory
    {
        return $this->collectionFactory;
    }


    /**
     * Fassade für DatabaseHelper::loadOneByValue()
     *
     * @param int|string          $value
     * @param FieldnamesInterface $field
     * @param TablenamesInterface $table
     * @param int                 $offset
     * @param int                 $limit
     *
     * @return AbstractDatabaseRowCollection|null
     *
     * @throws Exception
     */
    public function loadOneByValue(
        int|string $value,
        FieldnamesInterface $field,
        TablenamesInterface $table,
        int $offset = 0,
        int $limit = 0
    ): ?AbstractDatabaseRowCollection {
        $tablename  = $this->dbNameFactory->createTablenameFromInterface($table);
        $fieldname  = $this->dbNameFactory->createFieldnameFromInterface($field, $tablename);
        $data       = $this->dbHelepr->loadOneByValue(
            $value,
            $fieldname->value(),
            $tablename->value(),
            $offset,
            $limit
        );

        if (empty($data)) {
            return null;
        }

        return $this->collectionFactory->createDatabaseRowCollection($tablename, $data);
    }


    /**
     * Fassade für DatabaseHelper::loadByValue()
     *
     * @param int|string          $value
     * @param FieldnamesInterface $field
     * @param TablenamesInterface $table
     * @param int                 $offset
     * @param int                 $limit
     *
     * @return AbstractDatabaseRowCollection|null
     *
     * @throws Exception
     */
    public function loadByValue(
        int|string $value,
        FieldnamesInterface $field,
        TablenamesInterface $table,
        int $offset = 0,
        int $limit = 0
    ): ?ArrayCollection {
        $tablename  = $this->dbNameFactory->createTablenameFromInterface($table);
        $fieldname  = $this->dbNameFactory->createFieldnameFromInterface($field, $tablename);
        $data       = $this->dbHelepr->loadByValue($value, $fieldname->value(), $tablename->value(), $offset, $limit);

        if (empty($data)) {
            return null;
        }

        return $this->collectionFactory->createMultiDatabaseRowCollection($tablename, $data);
    }


    /**
     * Fassade für DatabaseHelper::loadByList()
     *
     * @param array                    $valueList
     * @param FieldnamesInterface      $orderField
     * @param TablenamesInterface      $table
     * @param string                   $order
     * @param int                      $offset
     * @param int                      $limit
     * @param FieldnamesInterface|null $searchField
     *
     * @return ArrayCollection|null
     *
     * @throws Exception
     */
    public function loadByList(
        array $valueList,
        FieldnamesInterface $orderField,
        TablenamesInterface $table,
        string $order = 'ASC',
        int $offset = 0,
        int $limit = 0,
        ?FieldnamesInterface $searchField = null
    ): ?ArrayCollection {
        $tablename          = $this->dbNameFactory->createTablenameFromInterface($table);
        $orderFieldname     = $this->dbNameFactory->createFieldnameFromInterface($orderField, $tablename);
        $searchFieldString  = null !== $searchField ? $searchField->name : 'id';
        $searchFieldname    = $this->dbNameFactory->createFieldnameFromString($searchFieldString, $tablename);
        $data               = $this->dbHelepr->loadByList(
            $valueList,
            $tablename->value(),
            $orderFieldname->value(),
            $order,
            $offset,
            $limit,
            $searchFieldname->value()
        );

        if (empty($data)) {
            return null;
        }

        return $this->collectionFactory->createMultiDatabaseRowCollection($tablename, $data);
    }


    /**
     * Fassade für DatabaseHelper::loadByValue()
     *
     * @param TablenamesInterface      $table
     * @param FieldnamesInterface|null $orderField
     * @param string                   $order
     * @param int                      $offset
     * @param int                      $limit
     *
     * @return AbstractDatabaseRowCollection|null
     *
     * @throws Exception
     */
    public function loadAll(
        TablenamesInterface $table,
        ?FieldnamesInterface $orderField,
        string $order = 'ASC',
        int $offset = 0,
        int $limit = 0
    ): ?ArrayCollection {
        $tablename      = $this->dbNameFactory->createTablenameFromInterface($table);
        $orderFieldname = null;

        if (null !== $orderField) {
            $orderFieldname = $this->dbNameFactory->createFieldnameFromInterface($orderField, $tablename);
        }

        $data = $this->dbHelepr->loadAll($table->name, $orderFieldname?->value() ?: '', $order, $offset, $limit);

        if (empty($data)) {
            return null;
        }

        return $this->collectionFactory->createMultiDatabaseRowCollection($tablename, $data);
    }


    /**
     * Wrapper für DatabaseHelper::insert()
     *
     * @param array|ArrayCollection $values
     * @param TablenamesInterface   $table
     *
     * @return int
     *
     * @throws Exception
     */
    public function insert(array|ArrayCollection $values, TablenamesInterface $table): int
    {
        $dbValues = !\is_array($values) ? $values->toArray() : $values;

        return $this->dbHelepr->insert($dbValues, $table->name);
    }


    /**
     * Wrapper für DatabaseHelper::update()
     *
     * @param array|ArrayCollection $values
     * @param int                   $id
     * @param TablenamesInterface   $table
     *
     * @return void
     *
     * @throws Exception
     */
    public function update(array|ArrayCollection $values, int $id, TablenamesInterface $table): void
    {
        $dbValues = !\is_array($values) ? $values->toArray() : $values;

        $this->dbHelepr->update($dbValues, $id, $table->name);
    }


    /**
     * Wrapper für DatabaseHelper::delete()
     *
     * @param string|int          $value
     * @param FieldnamesInterface $field
     * @param TablenamesInterface $table
     *
     * @return void
     *
     * @throws Exception
     */
    public function delete(string|int $value, FieldnamesInterface $field, TablenamesInterface $table): void
    {
        $this->dbHelepr->delete((string) $value, $field->name, $table->name);
    }


    /**
     * Wrapper für DatabaseHelper::save()
     *
     * @param array|ArrayCollection $values
     * @param TablenamesInterface   $table
     *
     * @return int
     *
     * @throws Exception
     */
    public function save(array|ArrayCollection $values, TablenamesInterface $table): int
    {
        $dbValues = !\is_array($values) ? $values->toArray() : $values;

        return $this->dbHelepr->save($table->name, $dbValues);
    }
}
