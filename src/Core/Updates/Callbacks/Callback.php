<?php

namespace Mmb\Laravel\Core\Updates\Callbacks;

use Mmb\Laravel\Core\Data;
use Mmb\Laravel\Core\Updates\Infos\UserInfo;
use Mmb\Laravel\Core\Updates\Messages\InlineMessage;
use Mmb\Laravel\Core\Updates\Messages\Message;

/**
 * @property string         $id
 * @property UserInfo       $from
 * @property ?Message       $message
 * @property ?int           $inlineMessageId
 * @property string         $chatInstance
 * @property string         $data
 * @property ?string        $gameShortName
 *
 * @property ?InlineMessage $inlineMessage
 */
class Callback extends Data
{

    protected function dataCasts() : array
    {
        return [
            'id'                => 'string',
            'from'              => UserInfo::class,
            'message'           => Message::class,
            'inline_message_id' => 'string',
            'chat_instance'     => 'string',
            'data'              => 'string',
            'game_short_name'   => 'string',
        ];
    }

    protected function getInlineMessageAttribute()
    {
        return $this->makeCache('inlineMessage', fn() => InlineMessage::make($this->inlineMessageId));
    }

    public function answer($message = null, array $args = [], ...$namedArgs)
    {
        $args = $this->mergeMultiple(
            [
                'callbackQueryId' => $this->id,
                'text'            => $message,
            ],
            $args + $namedArgs
        );

        return $this->bot()->answerCallback($args);
    }

    public function alert($message = null, array $args = [], ...$namedArgs)
    {
        $args = $this->mergeMultiple(
            [
                'callbackQueryId' => $this->id,
                'showAlert'       => true,
                'text'            => $message,
            ],
            $args + $namedArgs
        );

        return $this->bot()->answerCallback($args);
    }

}
