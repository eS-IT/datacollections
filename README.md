# Datacollections


## Beschreibung

Bei dieser Software handelt es sich um eine Erweiterung für das Open Source CMS Contao. Die
Software stellt zwei Collections zur Verfügung und ist als Ersatz für den Einsatz für Arrays
gedacht.


## Autor

__e@sy Solutions IT:__ Patrick Froch <info@easySolutionsIT.de>


## Voraussetzungen

- php: ^8.2
- ext-ds: *
- contao/core-bundle:^5.3
- esit/valueobjects: ^1.0
- esit/databaselayer: ^1.0


## Installation

Die Erweiterung kann einfach über den Manager installiert werden.


## NameInterfaces

Die NameInterfaces sind für die Verwendung der DatabaseRowCollction erforderlich. Damit sichergestellt ist, dass es
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

Die Aufzählungen, die die das `FieldnamesInterface` implementieren, enthalten die Namen aller Felder einer Tabelle. Es
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
von abhängigen Daten.


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

...