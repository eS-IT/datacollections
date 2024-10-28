# Datacollections


## Beschreibung

Bei dieser Software handelt es sich um eine Erweiterung für das Open Source CMS Contao. Die
Software stellt zwei Collections zur Verfügung und ist als Ersatz für den Einsatz für Arrays
gedacht.


## Autor

__e@sy Solutions IT:__ Patrick Froch <info@easySolutionsIT.de>


## Voraussetzungen

- php: ^8.2
- contao/core-bundle:^5.3
- esit/valueobjects: ^1.0
- esit/databaselayer: ^1.0


## Installation

Die Erweiterung kann einfach über den Manager installiert werden.


## NameInterfaces

Die NameInterfaces sind für die Verwendung der `DatabaseRowCollction` erforderlich. Damit sichergestellt ist, dass es
sich um valide Namen für Tabellen und Felder handelt, werden ValueObjects verwendet.

Es muss eine Aufzählung (`Enumeration`) mit den Tabellennamen und je eine pro Tabelle mit den Feldnamen erstellt werden.

### TablenamesInterface

Die Aufzählung, die das `TablenamesInterface` implementiert, enthält die Namen aller relevanten Tabellen im Projekt.

```php
use Esit\Valueobjects\Classes\Database\Enums\TablenamesInterface;

enum Tablenames implements TablenamesInterface
{
    case tl_content;
    case tl_test_data;
}
```

### FieldnamesInterface

Die Aufzählungen, die das `FieldnamesInterface` implementieren, enthalten die Namen aller Felder einer Tabelle. Es
muss für jede Tabelle eine Aufzählung mit den entsprechenden Feldern geben.


```php
use Esit\Valueobjects\Classes\Database\Enums\FieldnamesInterface;

enum TlContent implements FieldnamesInterface
{
    case id;
    case tstamp;
    case headline;
}

enum TlTestData implements FieldnamesInterface
{
    case id;
    case tstamp;
    case specialdata;
}
```


## Collections

### Grundfunktionen

Alle Collections erweitern die `Doctrine\Common\Collections\ArrayCollection`
und bietet so auch alle Funktionen aus [`Doctrine\Common\Collections\ArrayCollection`](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html).

Im Einzelnen sind dies die folgenden Methoden:

- [add](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#add)
- [clear](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#clear)
- [contains](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#contains)
- [containsKey](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#containsKey)
- [current](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#current)
- ~~[get](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#get)~~ => Durch `getValue` ersetzt, um die gleichen Methodennamen in allen Collections verwenden zu können.
- [getKeys](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#getkeys)
- [getValues](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#getvalues)
- [isEmpty](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#isempty)
- [first](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#first)
- [exists](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#exists)
- [findFirst](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#findfirst)
- [filter](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#filter)
- [forAll](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#forall)
- [indexOf](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#indexof)
- [key](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#key)
- [last](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#last)
- [map](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#map)
- [reduce](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#reduce)
- [next](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#next)
- [partition](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#partition)
- [remove](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#remove)
- [removeElement](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#removeelement)
- ~~[set](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#set)~~ => Durch `getValue` ersetzt, um die gleichen Methodennamen in allen Collections verwenden zu können.
- [slice](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#slice)
- [toArray](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#toarray)
- [matching](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html#matching)

### ArrayCollection

Die `ArrayCollection` ist für den direkten Ersatz von Arrays gedacht. Die Collection kann
beliebige Werte aufnehmen und bietet viele Methoden für den Umgang mit Arrays.

### DatabaseRowCollction

Die `DatabaseRowCollection` ist eine Spezialform der ArrayCollection. Sie bietet ebenfalls
viele Methoden für den Umgang mit Arrays. Ihr Zweck ist es, eine Tabellenzeile aufzunehmen.
Die Tabellenzeile kann mit `save()` gespeichert werden. Des Weiteren bietet sie ein LazyLoading
von abhängigen Daten, wenn dies im DCA konfiguriert wurde.


## Vewendung

Für die Erstellung der Collections gibt es eine Factory. Sie kann eine `ArrayCollection`, eine
`DatabaseRowCollection` und eine `ArrayCollection` mit mehreren `DatabaseRowCollection`s erstellen.

### Vewendung der ArrayCollection

Hier wird die Erstellung und einige Methoden der `ArrayCollection` gezeigt:

```php
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;

class MyClass
{

    public function __construct(private readonly CollectionFactory $factory)
    {
    }


    public function useArray(): void
    {
        $myArrayCollection = $this->factory->createArrayCollection(
            ['t1' =>'test01', 't2' => 'test02']
        );

        // getValue
        echo $myArrayCollection->getValue('t1'); // => test01

        // setValue
        $myArrayCollection->setValue('t3', 'Test03');

        // fetchData
        $data = $myArrayCollection->fetchData(); // => ['t1' =>'test01', 't2' => 'test02', 't3' => 'Test03']

        // Iterator
        foreach ($myArrayCollection as $k => $v) {
            echo "$k: $y";
        }
    }
}
```

### Vewendung der DatabaseRowCollection

Für die Verwendung der `DatabaseRowCollection` werden zunächst Enumerations vom Typ `TablenamesInterface` und
`FieldnamesInterface` benötigt (s.o.). Mit diesen können dann über die Factory die `DatabaseRowCollection`s erstellt
werden. Da intern ValueObjects für die Namen der Tabellen und Felder verwendet werden, können nur `DatabaseRowCollection`
für existierende Tabellen erstellt werden und nur auf darin wirklich enthaltene Felder zugegriffen werden.

```php
use Esit\Datacollections\Classes\Services\Factories\CollectionFactory;

class MyClass
{

    public function __construct(private readonly CollectionFactory $factory)
    {
    }


    public function useDatabaseRow(): void
    {
        // Eine leere Collection erstellen
        $myDbCollection = $this->factory->createDatabaseRowCollection(
            Tablenames::tl_test_data,
            [] // Hier können Daten als Array oder ArrayCollection übergeben werden.
        );

        // setValue
        $myDbCollection->getValue(TlTestData::specialdata, 'TestValue');

        // getValue
        echo $myDbCollection->getValue(TlTestData::specialdata); // => 'TestValue'

        // fetchData
        $data = $myDbCollection->fetchData(); // Alle Daten der Tabellenzeile als Array

        // ArrayCollection mit mehreren DatabaseRowCollections erstellen.
        $myCollections = $this->factory->createMultiDatabaseRowCollection(
            Tablenames::tl_test_data,
            [] // Hier können Daten als multidemensionales Array übergeben werden.
        );

        // Iterator ($myCollections ist eine ArrayCollection, $oneDbCollection je eine DatabaseRowCollection)
        foreach ($myCollections as $oneDbCollection) {
            var_dump($oneDbCollection); // oder alle anderen Aktionen einer DatabaseRowCollection
        }
    }
}
```

Für den Zugriff auf einen Wert wird immer ein `FieldnamesInterface` benötigt.

Auf den `DatabaseRowCollection` stehen die gleichen Methoden zur Verfügung, wie auf den `ArrayCollection`. Zusätzlich
gibt es die Methode `save` um den Datensatz zu speichern.

Arrays werden immmer als serialisierter String abgelegt und als `ArrayCollection` zurückgegeben.

Wenn im DCA das LazyLoading konfiguriert ist, werden die abhängigen Daten automatisch beim Zugriff auf das Feld
geladen und zurückgegeben.

### LazyLoading

Damit die abhängigen Daten geladen werden können, muss das LazyLoading im DCA konfiguriert werden.

```php
$table = 'tl_test_data';

$GLOBALS['TL_DCA'][$table]['fields']['author'] = [
    'label'                 => &$GLOBALS['TL_LANG'][$table]['author'],
    'exclude'               => true,
    'inputType'             => 'text',
    'foreignKey'            => 'tl_member.CONCAT(firstname,' ',lastname)',
    'lazyloading'           => ['table'=>'tl_member', 'field'=>'id', 'serialised'=>false],
    'eval'                  => ['mandatory'=>true, 'maxlength'=>255],
    'sql'                   => "varchar(255) NOT NULL default ''"
];
```

`table` gibt die Tabelle an, aus der die Daten geladen werden sollen. `field` gibt an, in welchem Feld der Fremdtabelle
der Wert gesucht wird und `serialised` gibt an, ob es sich um einen Werte (`false`) oder ein serialisiertes Array
von Werten handelt (`true`).
