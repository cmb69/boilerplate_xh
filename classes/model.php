<?php

/**
 * The models.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Boilerplate
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Boilerplate_XH
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
