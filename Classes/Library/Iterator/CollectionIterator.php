<?php

/**
 * @since       16.09.2024 - 20:28
 *
 * @author      Patrick Froch <info@easySolutionsIT.de>
 *
 * @see         http://easySolutionsIT.de
 *
 * @copyright   e@sy Solutions IT 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Classes\Library\Iterator;

use Esit\Datacollections\Classes\Services\Helper\ConverterHelper;

class CollectionIerrator extends \ArrayIterator
{


    /**
     * @param array|object         $iterator
     * @param int                  $flags
     * @param ConverterHelper|null $converterHelper
     */
    public function __construct(
        array|object $iterator,
        int $flags = 0,
        private readonly ?ConverterHelper $converterHelper = null
    ) {
        parent::__construct($iterator, $flags);
    }


    /**
     * @return mixed
     */
    public function current(): mixed
    {
        return $this->converterHelper->convertArrayToCollection(parent::current());
    }
}
