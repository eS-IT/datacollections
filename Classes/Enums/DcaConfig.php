<?php

/**
 * @since       12.09.2024 - 08:36
 * @author      Patrick Froch <info@easySolutionsIT.de>
 * @see         http://easySolutionsIT.de
 * @copyright   e@sy Solutions IT 2024
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Classes\Enums;

enum DcaConfig
{


    case TL_DCA;

    case fields;

    case lazyloading;

    case table;

    case field;

    case serialised;
}
