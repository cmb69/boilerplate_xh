<?php

include '../classes/model.php';


class ModelTest extends PHPUnit_Framework_TestCase
{
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
