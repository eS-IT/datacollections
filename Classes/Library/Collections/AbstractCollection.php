<?php

/**
 * @since       05.09.2024 - 07:39
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

use Doctrine\Common\Collections\ArrayCollection;
use Esit\Databaselayer\Classes\Services\Helper\SerializeHelper;
use Esit\Datacollections\Classes\Exceptions\MethodNotAllowedException;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Datacollections\Classes\Services\Helper\ConverterHelper;

abstract class AbstractCollection extends ArrayCollection
{


    /**
     * @param CollectionFactory $collectionFactory
     * @param SerializeHelper   $serializeHelper
     * @param ConverterHelper   $converterHelper
     * @param array             $data
     */
    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly SerializeHelper $serializeHelper,
        private readonly ConverterHelper $converterHelper,
        array $data = []
    ) {
        parent::__construct($data);
    }


    /**
     * Wrapper für $this->toArray()
     * (Wird für Kompatibilität mit dem FePackage benötigt!)
     *
     * @return array
     */
    public function fetchData(): array
    {
        return $this->toArray();
    }


    /**
     * Gibt einen Wert zurück.
     * Handelt es sich um ein (serialisiertes) Array, wird eine Collection zurückgegeben.
     *
     * @return \Esit\Datacollections\Classes\Library\Collections\ArrayCollection|false|mixed
     */
    public function current(): mixed
    {
        return $this->converterHelper->convertArrayToCollection(parent::current());
    }


    /**
     * Methode darf nicht von außen aufgerufen werden und kann wegen
     * der Vererbung nicht protected gemacht werden, deshalb darf sie
     * auf den Kindklassen nicht verwendet werden, es wäre sonst möglich
     * an den Prüfungen vorbei unerwünschte Daten zu setzen!
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function set(mixed $key, mixed $value): void
    {
        $msg = 'Method was not allowed to be called on this object. Use $this->setValue() instead.';

        throw new MethodNotAllowedException($msg);
    }


    /**
     * Methode darf nicht von außen aufgerufen werden und kann wegen
     * der Vererbung nicht protected gemacht werden, deshalb darf sie
     * auf den Kindklassen nicht verwendet werden, es wäre sonst möglich
     * an den Prüfungen vorbei unerwünschte Daten zu setzen!
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function get(mixed $key): mixed
    {
        $msg = 'Method was not allowed to be called on this object. Use $this->getValue() instead.';

        throw new MethodNotAllowedException($msg);
    }


    /**
     * Gibt einen Iterator für das aktuelle Objekt zurück.
     *
     * @return \Traversable
     */
    public function getIterator(): \Traversable
    {
        return $this->collectionFactory->createCollectionIterator($this);
    }


    /**
     * Implementiert die array_column-Funktion für Collections
     *
     * @param string $column
     *
     * @return AbstractCollection
     */
    public function cloumn(string $column): self
    {
        $data = \array_column($this->toArray(), $column);

        return $this->collectionFactory->createArrayCollection($data);
    }



    /**
     * Gibt einen Wert zurück.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    protected function returnValue(mixed $key): mixed
    {
        return $this->converterHelper->convertArrayToCollection(parent::get($key));
    }


    /**
     * Setzt einen Wert.
     * Ein Array wird automatisch serialisiert.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    protected function handleValue(mixed $key, mixed $value): void
    {
        $value = $this->serializeHelper->serialize($value);

        parent::set($key, $value);
    }
}
