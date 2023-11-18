<?php

namespace Mmb\Laravel\Tests\Supports;

use BadMethodCallException;
use Mmb\Laravel\Core\Updates\Files\Document;
use Mmb\Laravel\Core\Updates\Files\Photo;
use Mmb\Laravel\Core\Updates\Files\PhotoCollection;
use Mmb\Laravel\Tests\TestCase;

class MacroableTest extends TestCase
{

    public function test_macro_is_working()
    {
        Photo::macro('test', fn() => 'OK');

        $this->assertSame(Photo::test(), 'OK');
        $this->assertSame(PhotoCollection::test(), 'OK');

        $this->expectException(BadMethodCallException::class);
        Document::test();
    }

}