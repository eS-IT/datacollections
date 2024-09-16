<?php

/**
 * @since       10.09.2024 - 19:44
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

use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Valueobjects\Classes\Database\Valueobjects\FieldnameValue;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;

class LazyLoadHelper
{


    /**
     * @param ConfigurationHelper $configHelper
     * @param LoadHelper          $loadHelper
     */
    public function __construct(
        private readonly ConfigurationHelper $configHelper,
        private readonly LoadHelper $loadHelper
    ) {
    }


    /**
     * Reicht die MapFactroy an den LazyLoader und den ConfigurationHelper weiter.
     *
     * @param CollectionFactory $collectionFactory
     *
     * @return void
     */
    public function setCollectionFactory(CollectionFactory $collectionFactory): void
    {
        $this->loadHelper->setCollectionFactory($collectionFactory);
        $this->configHelper->setCollectionFactory($collectionFactory);
    }


    /**
     * Lädt die Daten einer anderen Tabelle.
     *
     * @param TablenameValue $tablename
     * @param FieldnameValue $fieldname
     * @param mixed          $value
     *
     * @return mixed
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function loadData(TablenameValue $tablename, FieldnameValue $fieldname, int|string|array $value): mixed
    {
        if (true === $this->configHelper->isLazyLodingField($tablename, $fieldname)) {
            $foreignTable = $this->configHelper->getForeignTable($tablename, $fieldname);
            $foreignField = $this->configHelper->getForeignField($tablename, $fieldname);

            if (null !== $foreignTable && null !== $foreignField) {
                if (true === $this->configHelper->isSerialised($tablename, $fieldname)) {
                    return $this->loadHelper->loadMultiple($foreignTable, $foreignField, $value);
                }

                return $this->loadHelper->loadOne($foreignTable, $foreignField, $value);
            }
        }

        return null;
    }
}
