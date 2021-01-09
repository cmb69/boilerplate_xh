<?php

/**
 * Copyright 2013-2021 Christoph M. Becker
 *
 * This file is part of Boilerplate_XH.
 *
 * Boilerplate_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Boilerplate_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Boilerplate_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Boilerplate;

/**
 * The models.
 *
 * @category CMSimple_XH
 * @package  Boilerplate
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Boilerplate_XH
 */
class Model
{
    /**
     * The data folder.
     *
     * @var string
     */
    private $dataFolder;

    /**
     * Initializes a new instance.
     *
     * @param string $dataFolder The data folder.
     *
     * @return void
     */
    public function __construct($dataFolder)
    {
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
            chmod($dataFolder, 0777);
        }
        $this->dataFolder = $dataFolder;
    }

    /**
     * Returns the data folder.
     *
     * @return string
     */
    public function getDataFolder()
    {
        return $this->dataFolder;
    }

    /**
     * Returns all names available in the data folder.
     *
     * @return array
     */
    public function names()
    {
        $names = array();
        if ($dh = opendir($this->dataFolder)) {
            while (($fn = readdir($dh)) !== false) {
                if ($fn[0] != '.' && pathinfo($fn, PATHINFO_EXTENSION) == 'htm') {
                    $names[] = basename($fn, '.htm');
                }
            }
        }
        natcasesort($names);
        return array_values($names);
    }

    /**
     * Returns whether the name is valid.
     *
     * @param string $name A boilerplate name.
     *
     * @return bool
     */
    public function isValidName($name)
    {
        return (bool) preg_match('/^[a-z0-9_\-]+$/su', $name);
    }

    /**
     * Returns the file name of a text block.
     *
     * @param string $name A boilerplate name.
     *
     * @return string
     */
    public function filename($name)
    {
        assert($this->isValidName($name));
        return $this->dataFolder . $name . '.htm';
    }

    /**
     * Returns the content of a text block.
     *
     * @param string $name A boilerplate name.
     *
     * @return string (X)HTML.
     */
    public function read($name)
    {
        assert($this->isValidName($name));
        return file_get_contents($this->filename($name));
    }

    /**
     * Saves new content for a text block. Returns whether that succeded.
     *
     * @param string $name    A boilerplate name.
     * @param string $content A content.
     *
     * @return bool
     */
    public function write($name, $content)
    {
        assert($this->isValidName($name));
        $fn = tempnam($this->dataFolder, 'boilerplate');
        $ok = ($fp = fopen($fn, 'w')) !== false
            && fwrite($fp, $content) !== false;
        if ($fp) {
            fclose($fp);
        }
        if ($ok) {
            $dst = $this->filename($name);
            $ok = rename($fn, $dst);
        }
        return $ok;
    }

    /**
     * Deletes a text block. Returns whether that succeded.
     *
     * @param string $name A boilerplate name.
     *
     * @return bool
     */
    public function delete($name)
    {
        assert($this->isValidName($name));
        return unlink($this->filename($name));
    }
}
