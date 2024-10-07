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


## Verwendung

### ArrayCollection

Die `ArrayCollection` ist für den direkten Ersatz von Arrays gedacht. Die Collection kann
beliebige Werte aufnehmen und bietet viele Methoden für den Umgang mit Arrays.

### DatabaseRowCollction

Die `DatabaseRowCollection` ist eine Spezialform der ArrayCollection. Sie bietet ebenfalls
viele Methoden für den Umgang mit Arrays. Ihr Zweck ist es, eine Tabellenzeile aufzunehmen.
Die Tabellenzeile kann mit `save()` gespeichert werden. Des Weiteren bietet sie ein LazyLoading
von abhängigen Daten.

### Erstellung einer Collection

Für die Erstellung der Collections gibt es eine Factory. Sie kann eine `ArrayCollection`, eine
`DatabaseRowCollection` und eine `ArrayCollection` mit mehreren `DatabaseRowCollection`s erstellen.
