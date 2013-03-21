<?php

/**
 * Model class of Boilerplate_XH.
 *
 * @package   Boilerplate
 * @copyright Copyright (c) 2013 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Boilerplate_XH
 */


/**
 * The model of Boilerplate_XH.
 *
 * @package Boilerplate
 */
class Boilerplate_Model
{
    /**
     * @access private
     * @var string  The data folder.
     */
    var $dataFolder;


    /**
     * Constructor.
     *
     * @access public
     * @param string $dataFolder  The data folder.
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
     * @access public
     * @return string
     */
    function getDataFolder()
    {
        return $this->dataFolder;
    }


    /**
     * Returns all names available in the data folder.
     *
     * @access public
     * @return array
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
     * @access public
     * @param string $name  The name of the text block.
     * @return bool
     */
    function isValidName($name)
    {
        return !!preg_match('/^[a-z0-9_\-]+$/su', $name);
    }


    /**
     * Returns the file name of a text block.
     *
     * @access public
     * @param  string $name  The name of the text block.
     * @return string
     */
    function filename($name)
    {
        assert($this->isValidName($name));
        return $this->dataFolder . $name . '.htm';
    }


    /**
     * Returns the content of a text block.
     *
     * @access public
     * @param  string $name  The name of the text block.
     * @return string  The (X)HTML.
     */
    function read($name)
    {
        assert($this->isValidName($name));
        return file_get_contents($this->filename($name));
    }


    /**
     * Saves new content for a text block. Returns whether that succeded.
     *
     * @access public
     * @param  string $name  The name of the text block.
     * @param  string $content  The new content.
     * @return bool.
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
                && file_exists($dst))
            {
                unlink($dst);
            }
            $ok = rename($fn, $dst);
        }
        return $ok;
    }


    /**
     * Deletes a text block. Returns whether that succeded.
     *
     * @access public
     * @param  string $name  The name of the text block.
     * @return bool.
     */
    function delete($name)
    {
        assert($this->isValidName($name));
        return unlink($this->filename($name));
    }
}

?>
