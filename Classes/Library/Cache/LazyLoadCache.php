<?php

/**
 * @since       19.03.2025 - 13:48
 *
 * @author      Patrick Froch <info@netgroup.de>
 *
 * @see         http://www.netgroup.de
 *
 * @copyright   NetGroup GmbH 2025
 * @license     EULA
 */

declare(strict_types=1);

namespace Esit\Datacollections\Classes\Library\Cache;

use Esit\Datacollections\Classes\Library\Collections\AbstractDatabaseRowCollection;
use Esit\Datacollections\Classes\Library\Collections\ArrayCollection;
use Esit\Valueobjects\Classes\Database\Valueobjects\FieldnameValue;
use Esit\Valueobjects\Classes\Database\Valueobjects\TablenameValue;

class LazyLoadCache
{


    /**
     * @var self|null
     */
    private static ?LazyLoadCache $instance = null;


    /**
     * @param ArrayCollection $collection
     */
    public function __construct(private readonly ArrayCollection $collection)
    {
    }


    /**
     * Setzt das Objekt zurück
     * (Wichtig für UnitTests!)
     *
     * @return void
     */
    public function tearDown(): void
    {
        self::$instance = null;
    }


    /**
     * Gbit eine Instanz des Caches zurück.
     *
     * @param ArrayCollection $collection
     *
     * @return self
     */
    public static function getInstance(ArrayCollection $collection): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($collection);
        }

        return self::$instance;
    }


    /**
     * Prüft, ob ein Wert mit einem bestimmten Namen vorhanden ist.
     *
     * @param TablenameValue $table
     * @param FieldnameValue $name
     * @param int            $id
     *
     * @return bool
     */
    public function contains(TablenameValue $table, FieldnameValue $name, int $id): bool
    {
        return !empty($this->collection->getValue($table->value() . '_' . $name->value() . '_' . $id));
    }


    /**
     * Entfernt einen Wert aus dem Cache.
     *
     * @param TablenameValue $table
     * @param FieldnameValue $name
     * @param int            $id
     *
     * @return void
     */
    public function remove(TablenameValue $table, FieldnameValue $name, int $id): void
    {
        $this->collection->remove($table->value() . '_' . $name->value() . '_' . $id);
    }


    /**
     * Speichert einen Wert aus dem Cache.
     *
     * @param TablenameValue                                     $table
     * @param FieldnameValue                                     $name
     * @param int                                                $id
     * @param AbstractDatabaseRowCollection|ArrayCollection|null $row
     *
     * @return void
     */
    public function setValue(
        TablenameValue $table,
        FieldnameValue $name,
        int $id,
        AbstractDatabaseRowCollection|ArrayCollection|null $row
    ): void {
        $this->collection->setValue($table->value() . '_' . $name->value() . '_' . $id, $row);
    }


    /**
     * Gibt einen Wert aus dem Cache zurück.
     *
     * @param TablenameValue $table
     * @param FieldnameValue $name
     * @param int            $id
     *
     * @return AbstractDatabaseRowCollection|ArrayCollection|null
     */
    public function getValue(
        TablenameValue $table,
        FieldnameValue $name,
        int $id,
    ): AbstractDatabaseRowCollection|ArrayCollection|null {
        return $this->collection->getValue($table->value() . '_' . $name->value() . '_' . $id);
    }
}
