<?php

/**
 * @since       10.09.2024 - 19:52
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
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;
use Esit\Valueobjects\Classes\Database\Valueobjects\FieldnameValue;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;

class DcaHelper
{


    /**
     * Kann wegen Ringbezug nicht injected werden!
     *
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;


    /**
     * @param CollectionFactory $collectionFactory
     *
     * @return void
     */
    public function setCollectionFactory(CollectionFactory $collectionFactory): void
    {
        $this->collectionFactory = $collectionFactory;
    }


    /**
     * Gibt eine ArrayMap mit den Informationen für das LayzLoading zurück.
     *
     * @param TablenameValue $tablename
     * @param FieldnameValue $fieldname
     *
     * @return ArrayCollection|null
     */
    public function getDepandancies(TablenameValue $tablename, FieldnameValue $fieldname): ?ArrayCollection
    {
        if (!empty($GLOBALS[DcaConfig::TL_DCA->name][$tablename->value()][DcaConfig::fields->name])) {
            $dcaField = $GLOBALS[DcaConfig::TL_DCA->name][$tablename->value()][DcaConfig::fields->name];

            if (!empty($dcaField[$fieldname->value()][DcaConfig::lazyloading->name])) {
                $lazyLoading = $dcaField[$fieldname->value()][DcaConfig::lazyloading->name];

                if (!empty($lazyLoading)) {
                    return $this->collectionFactory->createArrayCollection($lazyLoading);
                }
            }
        }

        return null;
    }
}
