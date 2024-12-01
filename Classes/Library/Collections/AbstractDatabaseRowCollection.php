<?php

/**
 * @since       05.09.2024 - 17:31
 *
 * @author      Patrick Froch <info@easySolutionsIT.de>
 *
 * @see         http://easySolutionsIT.de
 *
 * @copyright   e@sy Solutions IT 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Classes\Library\Collections;

use Esit\Databaselayer\Classes\Services\Helper\DatabaseHelper;
use Esit\Databaselayer\Classes\Services\Helper\SerializeHelper;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\ConverterHelper;
use Esit\Datacollections\Classes\Services\Helper\LazyLoadHelper;
use Esit\Valueobjects\Classes\Database\Enums\TablenamesInterface;
use Esit\Valueobjects\Classes\Database\Valueobjects\FieldnameValue;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;

abstract class AbstractDatabaseRowCollection extends AbstractCollection implements CollectionInterface
{


    /**
     * Bei diesem Collection-Typ handelt es sich um einen Datencontainer für eine Tabellenzeile.
     * Er kann abhängige Daten nachladen und die enthaltenen Daten nach Änderungen speichern.
     */


    /**
     * Map für die nachgeladenen Daten.
     *
     * @var ArrayCollection
     */
    protected ArrayCollection $lazyData;


    /**
     * Allgemeine Daten, die nicht aus der Datenbank stammen,
     * aber z. B. für die Ausgabe o.ä. benötigt werden.
     *
     * @var ArrayCollection
     */
    protected ArrayCollection $commonData;


    /**
     * Diese ArrayCollection enthält je eine ArrayCollection mit den DatabaseRowCollections
     * der Kinddatensätze pro Eltern-Kind-Beziehung.
     *
     * @var ArrayCollection
     */
    protected ArrayCollection $childData;


    /**
     * @param CollectionFactory     $collectionFactory
     * @param SerializeHelper       $serializeHelper
     * @param ConverterHelper       $converterHelper
     * @param DatabaseHelper        $databaseHelper
     * @param LazyLoadHelper        $loadHelper
     * @param TablenameValue        $tablename
     * @param array|ArrayCollection $data
     */
    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly SerializeHelper $serializeHelper,
        private readonly ConverterHelper $converterHelper,
        private readonly DatabaseHelper $databaseHelper,
        private readonly LazyLoadHelper $loadHelper,
        private readonly TablenameValue $tablename,
        array|ArrayCollection $data = []
    ) {
        $data = $data instanceof ArrayCollection ? $data->toArray() : $data;
        parent::__construct(
            $this->collectionFactory,
            $this->serializeHelper,
            $this->converterHelper,
            $data
        );

        $this->lazyData     = $this->collectionFactory->createArrayCollection();
        $this->commonData   = $this->collectionFactory->createArrayCollection();
    }


    /**
     * @return TablenameValue
     */
    public function getTable(): TablenameValue
    {
        return $this->tablename;
    }


    /**
     * Speichet die Daten in der Datenbank.
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function save(): int
    {
        return $this->databaseHelper->save($this->tablename->value(), $this->toArray());
    }


    /**
     * Gibt einen Wert zurück.
     * Wenn der Wert mit Daten in einer anderen Tabelle verbunden sind,
     * werden diese Daten geladen und zurückgegeben.
     *
     * @param FieldnameValue $key
     *
     * @return mixed
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getValueFromNameObject(FieldnameValue $key): mixed
    {
        $keyName = $key->value();

        if (true === $this->lazyData->contains($keyName)) {
            return $this->lazyData->getValue($keyName);
        }

        $value = $this->returnValue($keyName);

        if (true === \is_scalar($value)) {
            $lazyValue = $this->loadHelper->loadData($this->tablename, $key, $value);

            if (null !== $lazyValue) {
                $this->lazyData->setValue($keyName, $lazyValue);

                return $lazyValue;
            }
        }

        return $value;
    }


    /**
     * Setzt einen Wert.
     *
     * @param FieldnameValue $key
     * @param mixed          $value
     *
     * @return void
     */
    public function setValueWithNameObject(FieldnameValue $key, mixed $value): void
    {
        $value = $value instanceof ArrayCollection ? $value->toArray() : $value;

        if (true === $this->lazyData->contains($key->value())) {
            // Nachgeladene Daten entfernen, wenn der Wert neu gesetzt wird!
            $this->lazyData->remove($key->value());
        }

        $this->handleValue($key->value(), $value);
    }


    /**
     * @return array
     */
    public function getCommonDataAsArray(): array
    {
        return $this->commonData->toArray();
    }


    /**
     * Gibt einen Wert aus den allgemeinen Daten zurück.
     *
     * @param string|int $key
     *
     * @return mixed
     */
    public function getCommonValue(string|int $key): mixed
    {
        return $this->commonData->getValue($key);
    }


    /**
     * Speichert einen Wert in den allgemeinen Daten.
     *
     * @param string|int $key
     * @param mixed      $commonData
     *
     * @return void
     */
    public function setCommonValue(string|int $key, mixed $commonData): void
    {
        $this->commonData->setValue($key, $commonData);
    }


    /**
     * Lädt die Kinddatensätze anhand der Id dieses Datensatzes und der im DCA definierten Kindtabelle.
     *
     * @param TablenamesInterface $table
     *
     * @return ArrayCollection|null
     */
    public function getChildData(TablenamesInterface $table): ?ArrayCollection
    {
        if (true === $this->childData->contains($table)) {
            return $this->childData->getValue($table->name);
        }

        $lazyValues = $this->loadHelper->loadChildData($table, (int) $this->returnValue('id'));

        if (null !== $lazyValues) {
            $this->childData->setValue($table->name, $lazyValues);
        }

        return $lazyValues;
    }
}
