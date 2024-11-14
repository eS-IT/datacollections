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

use Esit\Databaselayer\Classes\Services\Helper\DatabaseHelper;
use Esit\Databaselayer\Classes\Services\Helper\SerializeHelper;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\ConverterHelper;
use Esit\Datacollections\Classes\Services\Helper\LazyLoadHelper;
use Esit\Valueobjects\Classes\Database\Enums\FieldnamesInterface;
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
    private ArrayCollection $commonData;


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
            $this->databaseHelper,
            $this->loadHelper,
            $this->tablename,
            $data
        );

        $this->commonData = $this->collectionFactory->createArrayCollection();
    }


    /**
     * Gibt einen Wert zurück. Der Schlüssel muss als FieldnamesInterface
     * übergeben werden und wird in ein FieldnameValue umgewandelt.
     *
     * @param FieldnamesInterface|string $key
     *
     * @return mixed
     *
     * @throws \Doctrine\DBAL\Exception
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
     * @throws \Doctrine\DBAL\Exception
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
}
