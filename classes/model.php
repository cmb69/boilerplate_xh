<?php

/**
 * The models.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Boilerplate
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013-2014 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Boilerplate_XH
 */

/**
 * The models.
 *
 * @category CMSimple_XH
 * @package  Boilerplate
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Boilerplate_XH
 */
class Boilerplate_Model
{
    /**
     * @var string The data folder.
     *
     * @access private
     */
    var $dataFolder;

    /**
     * Initializes a new instance.
     *
     * @param string $dataFolder The data folder.
     *
     * @access public
     */
    function Boilerplate_Model($dataFolder)
    {
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }
        $this->dataFolder = $dataFolder;
    }

    /**
     * Returns the data folder.
     *
     * @return string
     *
     * @access public
     */
    function getDataFolder()
    {
        return $this->dataFolder;
    }

    /**
     * Returns all names available in the data folder.
     *
     * @return array
     *
     * @access public
     */
    function names()
    {
        $names = array();
        $dh = opendir($this->dataFolder);
        while (($fn = readdir($dh)) !== false) {
            if ($fn[0] != '.' && pathinfo($fn, PATHINFO_EXTENSION) == 'htm') {
                $names[] = basename($fn, '.htm');
            }
        }
        natcasesort($names);
        return $names;
    }

    /**
     * Returns whether the name is valid.
     *
     * @param string $name A boilerplate name.
     *
     * @return bool
     *
     * @access public
     */
    function isValidName($name)
    {
        return !!preg_match('/^[a-z0-9_\-]+$/su', $name);
    }

    /**
     * Returns the file name of a text block.
     *
     * @param string $name A boilerplate name.
     *
     * @return string
     *
     * @access public
     */
    function filename($name)
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
     *
     * @access public
     */
    function read($name)
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
     *
     * @access public
     */
    function write($name, $content)
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
            // rename() can't overwrite the destination file
            // on Windows under PHP < 5.3.0
            if (version_compare(PHP_VERSION, '5.3.0', '<')
                && strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'
                && file_exists($dst)
            ) {
                unlink($dst);
            }
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
     *
     * @access public
     */
    function delete($name)
    {
        assert($this->isValidName($name));
        return unlink($this->filename($name));
    }
}

?>
