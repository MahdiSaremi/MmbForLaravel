<?php

namespace Mmb\Laravel\Tests\Updates\Basic;

use Mmb\Laravel\Core\Updates\Update;
use Mmb\Laravel\Tests\TestCase;

class BasicMakeDataTest extends TestCase
{

    public function test_access_to_data()
    {
        $update = Update::make([
            'update_id' => 10,
            'message' => [
                'message_id' => 20,
                'caption' => "Hello World",
            ],
        ]);

        $this->assertNotNull($update);
        $this->assertSame($update->update_id, 10);
        $this->assertSame($update->updateId, 10);
        $this->assertSame($update->id, 10);

        $this->assertNotNull($update->message);
        $this->assertSame($update->message->id, 20);
        $this->assertSame($update->message->text, "Hello World");
    }

}