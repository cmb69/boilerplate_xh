<?php

/**
 * Testing the model.
 *
 * PHP version 5
 *
 * @category  Testing
 * @package   Boilerplate
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Boilerplate_XH
 */

namespace Boilerplate;

require_once './vendor/autoload.php';
require_once './classes/Model.php';

use PHPUnit_Framework_TestCase;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;

/**
 * Testing the model.
 *
 * @category CMSimple_XH
 * @package  Boilerplate
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Boilerplate_XH
 */
class ModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * The test content.
     *
     * @var string
     */
    const CONTENT = '<p>foo bar</p>';

    /**
     * The path of the data folder.
     *
     * @var string
     */
    protected $dataFolder;

    /**
     * The test subject.
     *
     * @var Model
     */
    protected $subject;

    /**
     * Sets up the test fixture.
     *
     * @return void
     */
    public function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));
        $this->dataFolder = vfsStream::url('test/data/');
        $this->subject = new Model($this->dataFolder);
        touch($this->dataFolder . 'block1.dat');
        touch($this->dataFolder . 'block10.htm');
        touch($this->dataFolder . 'block2.htm');
    }

    /**
     * Tests that the data folder is created.
     *
     * @return void
     */
    public function testDataFolderIsCreated()
    {
        $this->assertFileExists($this->dataFolder);
    }

    /**
     * Tests that the correct data folder is returned.
     *
     * @return void
     */
    public function testReturnsCorrectDataFolder()
    {
        $this->assertEquals($this->dataFolder, $this->subject->getDataFolder());
    }

    /**
     * Tests that it finds the boilerplates.
     *
     * @return void
     */
    public function testFindsBoilerplates()
    {
        $this->assertEquals(['block2', 'block10'], $this->subject->names());
    }

    /**
     * Tests valid names.
     *
     * @param string $name    A boilerplate name.
     * @param bool   $isValid Whether the name is valid.
     *
     * @return void
     *
     * @dataProvider validNamesData
     */
    public function testValidNames($name, $isValid)
    {
        $this->assertEquals($isValid, $this->subject->isValidName($name));
    }

    /**
     * Returns the data for testValidNames().
     *
     * @return array
     */
    public function validNamesData()
    {
        return [
            ['block-1', true],
            ['block_2', true],
            ['Block_3', false],
            ['block!', false]
        ];
    }

    /**
     * Tests that a stored boilerplate is read.
     *
     * @return void
     */
    public function testReadsStoredBoilerplate()
    {
        file_put_contents($this->dataFolder . 'test.htm', self::CONTENT);
        $this->assertEquals(self::CONTENT, $this->subject->read('test'));
    }

    /**
     * Tests that a boilerplate is deleted.
     *
     * @return void
     */
    public function testDeletesBoilerplate()
    {
        $filename = $this->dataFolder . 'foo.htm';
        touch($filename);
        $this->subject->delete('foo');
        $this->assertFileNotExists($filename);
    }
}
