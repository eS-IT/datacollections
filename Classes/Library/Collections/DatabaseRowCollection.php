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
use Esit\Valueobjects\Classes\Database\Valueobjects\FieldnameValue;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;

class DatabaseRowCollection extends AbstractDatabaseRowCollection
{


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
    }


    /**
     * Gibt einen Wert zurück. Der Schlüssel muss als FieldnamesInterface
     * übergeben werden und wird in ein FieldnameValue umgewandelt.
     *
     * @param FieldnamesInterface $key
     *
     * @return mixed
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getValue(FieldnamesInterface $key): mixed
    {
        $keyValue = $this->nameFactory->createFieldnameFromInterface($key, $this->tablename);

        return $this->getValueFromNameObject($keyValue);
    }


    /**
     * Setzt einen Wert. Der Schlüssel muss als FieldnamesInterface
     * übergeben werden und wird in ein FieldnameValue umgewandelt.
     *
     * @param FieldnamesInterface $key
     * @param mixed               $value
     *
     * @return void
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function setValue(FieldnamesInterface $key, mixed $value): void
    {
        $keyValue = $this->nameFactory->createFieldnameFromInterface($key, $this->tablename);

        $this->setValueWithNameObject($keyValue, $value);
    }
}
