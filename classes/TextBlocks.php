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

class TextBlocks
{
    /** @var string */
    private $dataFolder;

    public function __construct(string $dataFolder)
    {
        $this->dataFolder = $dataFolder;
    }

    public function getDataFolder(): string
    {
        if (!is_dir($this->dataFolder)) {
            mkdir($this->dataFolder, 0777, true);
            chmod($this->dataFolder, 0777);
        }
        return $this->dataFolder;
    }

    /** @return list<string> */
    public function names(): array
    {
        $names = [];
        if ($dh = opendir($this->getDataFolder())) {
            while (($fn = readdir($dh)) !== false) {
                if ($fn[0] != '.' && pathinfo($fn, PATHINFO_EXTENSION) == 'htm') {
                    $names[] = basename($fn, '.htm');
                }
            }
        }
        natcasesort($names);
        return array_values($names);
    }

    public function isValidName(string $name): bool
    {
        return (bool) preg_match('/^[a-z0-9_\-]+$/su', $name);
    }

    public function exists(string $name): bool
    {
        assert($this->isValidName($name));
        return is_file($this->filename($name));
    }

    /** @return string|false */
    public function read(string $name)
    {
        assert($this->isValidName($name));
        return file_get_contents($this->filename($name));
    }

    public function write(string $name, string $content): bool
    {
        assert($this->isValidName($name));
        $fn = tempnam($this->getDataFolder(), 'boilerplate');
        return file_put_contents($fn, $content) !== false
            && rename($fn, $this->filename($name));
    }

    public function delete(string $name): bool
    {
        assert($this->isValidName($name));
        return unlink($this->filename($name));
    }

    private function filename(string $name): string
    {
        assert($this->isValidName($name));
        return $this->getDataFolder() . $name . '.htm';
    }
}
