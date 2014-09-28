<?php

/**
 * Testing the model.
 *
 * PHP version 5
 *
 * @category  Testing
 * @package   Boilerplate
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Boilerplate_XH
 */

require_once './classes/model.php';

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
     * Tests it.
     *
     * @return void
     */
    public function test()
    {
        $model = new Boilerplate_Model('./data/');
        $name = 'test';
        $str = '<h1>Ein Text zum Testen.<h1><p>Blah blah blah</p>';
        $this->assertTrue($model->write($name, $str));
        $str1 = $model->read($name);
        $this->assertEquals($str, $str1);
    }
}

?>
