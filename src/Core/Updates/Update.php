<?php

namespace Mmb\Laravel\Core\Updates;

use Mmb\Laravel\Core\Data;
use Mmb\Laravel\Core\Updates\Callbacks\Callback;
use Mmb\Laravel\Core\Updates\Messages\Message;
use Mmb\Laravel\Core\Updates\Poll\Poll;
use Mmb\Laravel\Core\Updates\Poll\PollAnswer;

/**
 * @property int                 $id
 * @property ?Message            $message
 * @property ?Message            $editedMessage
 * @property ?Message            $channelPost
 * @property ?Message            $editedChannelPost
 * @property ?InlineQuery        $inlineQuery
 * @property ?ChosenInlineResult $chosenInlineResult
 * @property ?Callback           $callbackQuery
 * @property ?Poll               $poll
 * @property ?PollAnswer         $pollAnswer
 * @property ?ChatMemberUpdated  $myChatMember
 * @property ?ChatMemberUpdated  $chatMember
 * @property ?ChatJoinRequest    $chatJoinRequest
 */
class Update extends Data
{

    protected function dataRules() : array
    {
        return [
            'update_id'            => 'int',
            'message'              => 'nullable|array',
            'edited_message'       => 'nullable|array',
            'channel_post'         => 'nullable|array',
            'edited_channel_post'  => 'nullable|array',
            'inline_query'         => 'nullable|array',
            'chosen_inline_result' => 'nullable|array',
            'callback_query'       => 'nullable|array',
            'poll'                 => 'nullable|array',
            'poll_answer'          => 'nullable|array',
            'my_chat_member'       => 'nullable|array',
            'chat_member'          => 'nullable|array',
            'chat_join_request'    => 'nullable|array',
        ];
    }

    protected function dataCasts() : array
    {
        return [
            'update_id'           => 'int',
            'message'             => Message::class,
            'edited_message'      => Message::class,
            'channel_post'        => Message::class,
            'edited_channel_post' => Message::class,
            // 'inline_query'         => Message::class,
            // 'chosen_inline_result' => Message::class,
            'callback_query'      => Callback::class,
            'poll'                => Poll::class,
            'poll_answer'         => PollAnswer::class,
            // 'my_chat_member'       => Message::class,
            // 'chat_member'          => Message::class,
            // 'chat_join_request'    => Message::class,
        ];
    }

    protected function dataShortAccess() : array
    {
        return [
            'id' => 'update_id',
        ];
    }

}