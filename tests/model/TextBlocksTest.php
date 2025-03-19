<?php

namespace Boilerplate\Model;

use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class TextBlocksTest extends TestCase
{
    const CONTENT = '<p>foo bar</p>';

    /** @var string */
    protected $dataFolder;

    /** @var TextBlocks */
    protected $subject;

    public function setUp(): void
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));
        $dataFolder = vfsStream::url('test/data/');
        $this->subject = new TextBlocks($dataFolder);
        $this->dataFolder = $this->subject->getDataFolder();
        touch($this->dataFolder . 'block1.dat');
        touch($this->dataFolder . 'block10.htm');
        touch($this->dataFolder . 'block2.htm');
    }

    public function testDataFolderIsCreated(): void
    {
        $this->assertFileExists($this->dataFolder);
    }

    public function testReturnsCorrectDataFolder(): void
    {
        $this->assertEquals($this->dataFolder, $this->subject->getDataFolder());
    }

    public function testFindsBoilerplates(): void
    {
        $this->assertEquals(['block2', 'block10'], $this->subject->names());
    }

    /** @dataProvider validNamesData */
    public function testValidNames(string $name, bool $isValid): void
    {
        $this->assertEquals($isValid, $this->subject->isValidName($name));
    }

    public function validNamesData(): array
    {
        return [
            ['block-1', true],
            ['block_2', true],
            ['Block_3', false],
            ['block!', false]
        ];
    }

    public function testBoilerplateExists(): void
    {
        $this->assertTrue($this->subject->exists("block2"));
    }

    public function testReadsStoredBoilerplate(): void
    {
        file_put_contents($this->dataFolder . 'test.htm', self::CONTENT);
        $this->assertEquals(self::CONTENT, $this->subject->read('test'));
    }

    public function testWritesBoilerplate(): void
    {
        $this->subject->write("new", self::CONTENT);
        $this->assertEquals(self::CONTENT, file_get_contents($this->dataFolder . "new.htm"));
    }

    public function testOverwritesBoilerplate(): void
    {
        $this->subject->write("new", self::CONTENT);
        $this->subject->write("new", "");
        $this->assertEquals("", file_get_contents($this->dataFolder . "new.htm"));
    }

    public function testDeletesBoilerplate(): void
    {
        $filename = $this->dataFolder . 'foo.htm';
        touch($filename);
        $this->subject->delete('foo');
        $this->assertFileDoesNotExist($filename);
    }
}
