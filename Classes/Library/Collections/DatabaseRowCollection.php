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
use Esit\Datacollections\Classes\Exceptions\TypeNotAllowedException;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\LazyLoadHelper;
use Esit\Valueobjects\Classes\Database\Valueobjects\FieldnameValue;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;

class DatabaseRowCollection extends AbstractCollection
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
     * @param CollectionFactory $collectionFactory
     * @param DatabaseHelper    $databaseHelper
     * @param SerializeHelper   $serializeHelper
     * @param LazyLoadHelper    $loadHelper
     * @param TablenameValue    $tablename
     * @param array             $data
     */
    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly DatabaseHelper $databaseHelper,
        private readonly SerializeHelper $serializeHelper,
        private readonly LazyLoadHelper $loadHelper,
        private readonly TablenameValue $tablename,
        array $data = []
    ) {
        parent::__construct($this->collectionFactory, $this->serializeHelper, $data);
        $this->lazyData = $this->collectionFactory->createArrayCollection();
    }


    /**
     * Speichet die Daten in der Datenbank.
     *
     * @return void
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function save(): void
    {
        $this->databaseHelper->save($this->tablename->value(), $this->toArray());
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
    public function getValue(FieldnameValue $key): mixed
    {
        if (true === $this->lazyData->contains($key)) {
            return $this->lazyData->getValue($key->value());
        }

        $value = $this->returnValue($key->value());

        if (true === \is_scalar($value)) {
            $value = $this->loadHelper->loadData($this->tablename, $key, $this->returnValue($key->value()));

            if (null !== $value) {
                $this->lazyData->handleValue($key->value(), $value);

                return $value;
            }
        }

        return $this->returnValue($key->value());
    }


    /**
     * Setzt einen Wert.
     *
     * @param FieldnameValue $key
     * @param mixed          $value
     *
     * @return void
     */
    public function setValue(FieldnameValue $key, mixed $value): void
    {
        if (false === \is_scalar($value) && false === \is_array($value)) {
            throw new TypeNotAllowedException('value have be a scalar or array');
        }

        if (true === $this->lazyData->contains($key->value())) {
            // Nachgeladene Daten entfernen, wenn der Wert neu gesetzt wird!
            $this->lazyData->remove($key->value());
        }

        $this->handleValue($key->value(), $value);
    }
}
