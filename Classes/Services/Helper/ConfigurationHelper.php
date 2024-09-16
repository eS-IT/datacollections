<?php

/**
 * @since       12.09.2024 - 08:23
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

use Esit\Datacollections\Classes\Enums\DcaConfig;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Valueobjects\Classes\Database\Services\Factories\DatabasenameFactory;
use Esit\Valueobjects\Classes\Database\Valueobjects\FieldnameValue;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;

class ConfigurationHelper
{

    /**
     * @param DcaHelper           $dcaHelper
     * @param DatabasenameFactory $nameFactory
     */
    public function __construct(
        private readonly DcaHelper $dcaHelper,
        private readonly DatabasenameFactory $nameFactory
    ) {
    }


    /**
     * Reicht die MapFactory an den DcaHelper weiter.
     *
     * @param CollectionFactory $collectionFactory
     *
     * @return void
     */
    public function setCollectionFactory(CollectionFactory $collectionFactory): void
    {
        $this->dcaHelper->setCollectionFactory($collectionFactory);
    }


    /**
     * Prüft, ob für ein Feld LazyLoading konfiguriert ist.
     *
     * @param TablenameValue $tablename
     * @param FieldnameValue $fieldname
     *
     * @return bool
     */
    public function isLazyLodingField(TablenameValue $tablename, FieldnameValue $fieldname): bool
    {
        return null !== $this->dcaHelper->getDepandancies($tablename, $fieldname);
    }


    /**
     * Gibt ein TablenameValue für die Fremdtablle zurück.
     *
     * @param TablenameValue $tablename
     * @param FieldnameValue $fieldname
     *
     * @return TablenameValue|null
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getForeignTable(TablenameValue $tablename, FieldnameValue $fieldname): ?TablenameValue
    {
        $config = $this->dcaHelper->getDepandancies($tablename, $fieldname);

        if (null === $config) {
            return null;
        }

        return $this->nameFactory->createTablenameFromString($config->getValue(DcaConfig::table->name));
    }


    /**
     * Gibt ein FieldnameValue für das Fremdfeld zurück.
     *
     * @param TablenameValue $tablename
     * @param FieldnameValue $fieldname
     *
     * @return FieldnameValue|null
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getForeignField(TablenameValue $tablename, FieldnameValue $fieldname): ?FieldnameValue
    {
        $config = $this->dcaHelper->getDepandancies($tablename, $fieldname);

        if (null === $config) {
            return null;
        }

        return $this->nameFactory->createFieldnameFromString($config->getValue(DcaConfig::field->name), $tablename);
    }


    /**
     * Gibt zurück, ob eine Feld serialisierte Werte enthält.
     *
     * @param TablenameValue $tablename
     * @param FieldnameValue $fieldname
     *
     * @return bool
     */
    public function isSerialised(TablenameValue $tablename, FieldnameValue $fieldname): bool
    {
        $config = $this->dcaHelper->getDepandancies($tablename, $fieldname);

        if (null === $config) {
            return false;
        }

        return true === (bool) $config->getValue(DcaConfig::serialised->name);
    }
}
