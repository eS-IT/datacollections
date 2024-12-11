<?php

/**
 * @since       08.10.2024 - 06:50
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

use Doctrine\DBAL\Exception;
use Esit\Databaselayer\Classes\Services\Helper\DatabaseHelper;
use Esit\Databaselayer\Classes\Services\Helper\SerializeHelper;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\ConverterHelper;
use Esit\Datacollections\Classes\Services\Helper\LazyLoadHelper;
use Esit\Valueobjects\Classes\Database\Enums\FieldnamesInterface;
use Esit\Valueobjects\Classes\Database\Enums\TablenamesInterface;
use Esit\Valueobjects\Classes\Database\Services\Factories\DatabasenameFactory;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;

class DatabaseRowCollection extends AbstractDatabaseRowCollection
{


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
     * @param DatabasenameFactory   $nameFactory
     * @param CollectionFactory     $collectionFactory
     * @param SerializeHelper       $serializeHelper
     * @param ConverterHelper       $converterHelper
     * @param DatabaseHelper        $databaseHelper
     * @param LazyLoadHelper        $loadHelper
     * @param TablenameValue        $tablename
     * @param array|ArrayCollection $data
     */
    public function __construct(
        private readonly DatabasenameFactory $nameFactory,
        private readonly CollectionFactory $collectionFactory,
        private readonly SerializeHelper $serializeHelper,
        private readonly ConverterHelper $converterHelper,
        private readonly DatabaseHelper $databaseHelper,
        private readonly LazyLoadHelper $loadHelper,
        private readonly TablenameValue $tablename,
        array|ArrayCollection $data = []
    ) {
        parent::__construct(
            $this->collectionFactory,
            $this->serializeHelper,
            $this->converterHelper,
            $this->loadHelper,
            $this->tablename,
            $data
        );

        $this->commonData   = $this->collectionFactory->createArrayCollection();
        $this->childData    = $this->collectionFactory->createArrayCollection();
    }


    /**
     * {@inheritDoc}
     *
     * @param array $elements
     *
     * @return $this
     */
    public function createFrom(array $elements): self
    {
        return new static(
            $this->nameFactory,
            $this->collectionFactory,
            $this->serializeHelper,
            $this->converterHelper,
            $this->databaseHelper,
            $this->loadHelper,
            $this->tablename,
            $elements
        );
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
     * @throws Exception
     */
    public function save(): int
    {
        return $this->databaseHelper->save($this->tablename->value(), $this->toArray());
    }


    /**
     * Gibt einen Wert zurück. Der Schlüssel muss als FieldnamesInterface
     * übergeben werden und wird in ein FieldnameValue umgewandelt.
     *
     * @param FieldnamesInterface|string $key
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getValue(FieldnamesInterface|string $key): mixed
    {
        $keyValue = $this->nameFactory->createFieldnameFromStringOrInterface($key, $this->tablename);

        return $this->getValueFromNameObject($keyValue);
    }


    /**
     * Setzt einen Wert. Der Schlüssel muss als FieldnamesInterface
     * übergeben werden und wird in ein FieldnameValue umgewandelt.
     *
     * @param FieldnamesInterface|string $key
     * @param mixed                      $value
     *
     * @return void
     *
     * @throws Exception
     */
    public function setValue(FieldnamesInterface|string $key, mixed $value): void
    {
        $keyValue = $this->nameFactory->createFieldnameFromStringOrInterface($key, $this->tablename);

        $this->setValueWithNameObject($keyValue, $value);
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
     * @param TablenamesInterface $childTable
     *
     * @return ArrayCollection|null
     *
     * @throws Exception
     */
    public function getChildData(TablenamesInterface $childTable): ?ArrayCollection
    {
        if (true === $this->childData->contains($childTable)) {
            return $this->childData->getValue($childTable->name);
        }

        $lazyValues = $this->loadHelper->loadChildData($this->tablename, $childTable, (int) $this->returnValue('id'));

        if (null !== $lazyValues) {
            $this->childData->setValue($childTable->name, $lazyValues);
        }

        return $lazyValues;
    }
}
