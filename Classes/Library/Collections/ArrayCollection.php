<?php

/**
 * @since       05.09.2024 - 17:25
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

class ArrayCollection extends AbstractCollection
{


    /*
     * Dieser Maptyp ist als allgemeiner Ersatz für Arrays gedacht.
     * Für spezielle Anwendungsfälle, sollten entsprechende Maptypen
     * erstellt werden.
     */


    /**
     * Wrapper für $this->returnValue().
     * Damit ArrayCollection und DatabaseRowCollection einheitliche Zugriffsmethoden haben.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function getValue(mixed $key): mixed
    {
        return $this->returnValue($key);
    }


    /**
     * Wrapper für $this->handleValue().
     * Damit ArrayCollection und DatabaseRowCollection einheitliche Zugriffsmethoden haben.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function setValue(mixed $key, mixed $value): void
    {
        $this->handleValue($key, $value);
    }
}
