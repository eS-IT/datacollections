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

use Doctrine\DBAL\Exception;
use Esit\Databaselayer\Classes\Services\Helper\SerializeHelper;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\ConverterHelper;
use Esit\Datacollections\Classes\Services\Helper\LazyLoadHelper;
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
     * @param CollectionFactory     $collectionFactory
     * @param SerializeHelper       $serializeHelper
     * @param ConverterHelper       $converterHelper
     * @param LazyLoadHelper        $loadHelper
     * @param TablenameValue        $tablename
     * @param array|ArrayCollection $data
     */
    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly SerializeHelper $serializeHelper,
        private readonly ConverterHelper $converterHelper,
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

        $this->lazyData = $this->collectionFactory->createArrayCollection();
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
     * @throws Exception
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
            $this->lazyData->setValue($keyName, $lazyValue);

            return $lazyValue; // Wenn keine Daten gefunden werden null, statt des skalaren Werts zurückgeben.
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
}
