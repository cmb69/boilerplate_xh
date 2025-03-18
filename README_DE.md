# Boilerplate\_XH

Boilerplate\_XH ermöglicht die Verwaltung und
(Wieder-)Verwendung von HTML Textbausteinen auf CMSimple\_XH Seiten. Auf diese
Weise können sie Text einmal schreiben, und ihn auf mehreren Seiten
wiederverwenden. Eigentlich ist `boilerplate()` sehr ähnlich zu `newsbox()`. Aber
während `newsbox()` mit versteckten CMSimple\_XH Seiten arbeitet, speichert
`boilerplate()` seinen Inhalt in separaten Dateien. Daher können Sie diese
Möglichkeit nutzen, um Ihr content.htm klein zu halten, in dem Sie den
kompletten Seiteninhalt durch einen Boilerplate\_XH Textbaustein ersetzen.

## Inhaltsverzeichnis

- [Voraussetzungen](#voraussetzungen)
- [Download](#download)
- [Installation](#installation)
- [Einstellungen](#einstellungen)
- [Verwendung](#verwendung)
- [Problembehebung](#problembehebung)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)


## Voraussetzungen

Boilerplate_XH ist ein Plugin für [CMSimple_XH](https://www.cmsimple-xh.org/de/).
Es benötigt CMSimple_XH ≥ 1.7.0 und PHP ≥ 7.1.0.
Boilerplate_XH benötigt weiterhin [Plib_XH](https://github.com/cmb69/plib_xh) ≥ 1.2;
ist dieses noch nicht installiert (siehe `Einstellungen` → `Info`),
laden Sie das [aktuelle Release](https://github.com/cmb69/plib_xh/releases/latest)
herunter, und installieren Sie es.

## Download

Das [aktuelle Release](https://github.com/cmb69/boilerplate_xh/releases/latest)
kann von Github herunter geladen werden.

## Installation

Die Installation erfolgt wie bei vielen anderen CMSimple\_XH-Plugins auch. Im
[CMSimple_XH-Wiki](https://wiki.cmsimple-xh.org/doku.php/de:installation#plugins)
finden Sie weitere Details.

1. Sichern Sie die Daten auf Ihrem Server.
2. Entpacken Sie die ZIP-Datei auf Ihrem Rechner.
3. Laden Sie das ganze Verzeichnis boilerplate/ auf Ihren Server in das Plugin-Verzeichnis von CMSimple\_XH hoch.
4. Machen Sie die Unterverzeichnisse css/ und languages/ beschreibbar.
5. Rufen Sie die Administration von Boilerplate\_XH auf (*Plugins* → *Boilerplate*),
   und prüfen Sie, ob alle Voraussetzungen erfüllt sind.

## Einstellungen

Die Plugin-Konfiguration erfolgt wie bei vielen anderen CMSimple\_XH-Plugins
auch im Administrationsbereich der Website. Wählen Sie *Plugins* → *Boilerplate*.

Die Lokalisierung wird unter *Sprache* vorgenommen. Sie können die
Sprachtexte in Ihre eigene Sprache übersetzen (falls keine entsprechende
Sprachdatei zur Verfügung steht), oder diese Ihren Wünschen gemäß anpassen.

Das Aussehen von Boilerplate\_XH kann unter *Stylesheet* angepasst werden.

## Verwendung

Sie können Ihre Textbausteine im Administrationsbereich verwalten; gehen Sie zu
*Plugins* → *Boilerplate* → *Text-Blöcke*. Die Verwendung sollte weitgehend
selbsterklärend sein, aber ein paar Hinweise erscheinen angebracht:

- Die Namen der Textbausteine können beliebig gewählt werden, aber sie dürfen nur
  Kleinbuchstaben (a-z), Ziffern (0-9), Unter- und Bindestriche enthalten.
- Die Eingabefelder rechts enthalten den benötigten Pluginaufruf. Klicken Sie
  diese einfach an und kopieren Sie die Auswahl in die Zwischenablage, um sie später
  auf einer CMSimple\_XH Seite einfügen zu können.
- Die Textbausteine werden mit dem selben Editor
  bearbeitet, mit dem auch CMSimple\_XH Seiten bearbeitet werden.
- Pluginaufrufe können in den Textbausteinen
  verwenden werden, so dass Sie Textbausteine verschachteln können oder nach Wunsch ein
  anderes Plugin aufrufen können.

Das Einfügen eines Textbausteins auf einer Seite erfolgt mit dem folgenden
Pluginaufruf:

    {{{boilerplate('%NAME%')}}}

Ersetzen Sie `%NAME%` durch den Namen eines bereits definierten Textbausteins. Die
einfachste Möglichkeit ist es den Pluginaufruf aus der Administration von
Boilerplate\_XH per Copy&Paste zu übernehmen.

Beachten Sie, dass die Verwendung von `boilerplate()` im Template möglich ist,
aber aus Performancegründen ist hier die Verwendung von `newsbox()`
vorzuziehen.

## Problembehebung

Melden Sie Programmfehler und stellen Sie Supportanfragen entweder auf
[Github](https://github.com/cmb69/boilerplate_xh/issues)
oder im [CMSimple_XH Forum](https://cmsimpleforum.com/).

## Lizenz

Boilerplate\_XH ist freie Software. Sie können es unter den Bedingungen
der GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von Boilerplate\_XH erfolgt in der Hoffnung, daß es
Ihnen von Nutzen sein wird, aber *ohne irgendeine Garantie*, sogar ohne
die implizite Garantie der *Marktreife* oder der *Verwendbarkeit für einen
bestimmten Zweck*. Details finden Sie in der GNU General Public License.

Sie sollten ein Exemplar der GNU General Public License zusammen mit
Boilerplate\_XH erhalten haben. Falls nicht, siehe http://www.gnu.org/licenses/.

Copyright © Christoph M. Becker

Russische Übersetzung © Lubomyr Kydray<br>
Slovakische Übersetzung © Dr. Martin Sereday

## Credits

Boilerplate\_XH wurde durch *rühgallisaniener* und *Hoffmann5928* angeregt.

Das Plugin-Icon wurde von [Mart (Marco Martin)](http://www.notmart.org/) gestaltet.
Vielen Dank für die Veröffentlichung dieses Icons unter GPL.

Vielen Dank an die Community im [CMSimple\_XH-Forum](http://www.cmsimpleforum.com/)
für Hinweise, Anregungen und das Testen.
Ein besonderes Dankeschön an *ustalo*, der mich an dieses beinahe vergessenes Plugin erinnerte.

Und zu guter letzt vielen Dank an [Peter Harteg](http://www.harteg.dk/), den „Vater“ von CMSimple,
und allen Entwicklern von [CMSimple\_XH](http://www.cmsimple-xh.org/de/),
ohne die es dieses phantastische CMS nicht gäbe.
