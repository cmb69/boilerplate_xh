# Boilerplate\_XH

Boilerplate\_XH facilitates to manage and (re)use
text blocks consisting of HTML on CMSimple\_XH pages. This way, you write some
text once, and can reuse it on several pages. Actually `boilerplate()` is very
similar to `newsbox()`. But while `newsbox()` is working with hidden CMSimple\_XH pages,
`boilerplate()` stores its content in separate files. So you can use this feature
to keep your content.htm small by replacing the complete page content with a
Boilerplate\_XH text block.

## Table of Contents

- [Requirements](#requirements)
- [Download](#download)
- [Installation](#installation)
- [Settings](#settings)
- [Usage](#usage)
- [License](#license)
- [Credits](#credits)

## Requirements

Boilerplate\_XH is a plugin for CMSimple\_XH ≥ 1.7.0.
It requires PHP ≥ 5.6.0 with the JSON extension.

## Download

The [lastest release](https://github.com/cmb69/boilerplate_xh/releases/latest) is available for download on Github.

## Installation

The installation is done as with many other CMSimple\_XH plugins. See the
[CMSimple\_XH Wiki](https://wiki.cmsimple-xh.org/doku.php/installation#plugins)
for further details.

1. Backup the data on your server.
2. Unzip the distribution on your computer.
3. Upload the whole directory boilerplate/ to your server into the plugins directory of CMSimple\_XH.
4. Set write permissions to the subdirectories css/ and languages/.
5. Browse to the administration of Boilerplate\_XH (*Plugins* → *Boilerplate*),
   and check if all requirements are fulfilled

## Settings

The configuration of the plugin is done as with many other CMSimple\_XH plugins in
the back-end of the website. Select *Plugins* → *Boilerplate*.

Localization is done under *Language*. You can translate the character
strings to your own language (if there is no appropriate language file
available), or customize them according to your needs.

The look of Boilerplate\_XH can be customized under *Stylesheet*.

## Usage

You can manage your text blocks in admin mode; go to *Plugins* →
*Boilerplate* → *Text Blocks*. The usage should be pretty much self explaining,
but some notes are in order:

- The names of text blocks can be choosen arbitrarily, but may contain only
  lowercase letters (a-z), digits (0-9), underscores and hyphens.
- The inputs at the right are containing the appropriate plugin call. Just
  click them once and copy the selection to the clipboard for later pasting into a
  CMSimple\_XH page.
- The text blocks are edited with the same
  editor that is used for editing CMSimple\_XH pages.
- You can use plugin calls in the text blocks, so you
  can nest the text blocks or call any other plugin you like.

Inserting of a text block on a page is done with the following plugin
call:

    {{{boilerplate('%NAME%')}}}

Replace `%NAME%` with the name of an already defined text block. The easiest way
is to copy & paste the plugin call from the administration of Boilerplate\_XH.
Note that using `boilerplate()` in the template is possible, but for
performance reasons using `newsbox()` instead is preferable.

## License

Boilerplate\_XH is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Boilerplate\_XH is distributed in the hope that it will be useful,
but *without any warranty*; without even the implied warranty of
*merchantibility* or *fitness for a particular purpose*. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Boilerplate\_XH.  If not, see http://www.gnu.org/licenses/.

© 2012-2021 Christoph M. Becker

Russian tranlation © 2012 Lubomyr Kydray  
Slovak translation © 2012 Dr. Martin Sereday

## Credits

Boilerplate\_XH was inspired by *rühgallisaniener* and *Hoffmann5928*.

The plugin icon is designed by [Mart (Marco Martin)](http://www.notmart.org/).
Many thanks for publishing this icon under GPL.

Many thanks to the community at the [CMSimple\_XH-Forum](http://www.cmsimpleforum.com/)
for tips, suggestions and testing.
Special thanks to *ustalo* for reminding me of this almost forgotten plugin.

And last but not least many thanks to [Peter Harteg](http://www.harteg.dk/),
the “father” of CMSimple, and all developers of [CMSimple\_XH](http://www.cmsimple-xh.org/)
without whom this amazing CMS would not exist.
