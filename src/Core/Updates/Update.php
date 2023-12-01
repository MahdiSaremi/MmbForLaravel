<?php

namespace Mmb\Laravel\Core\Updates;

use Illuminate\Http\Request;
use Mmb\Laravel\Core\Data;
use Mmb\Laravel\Core\Updates\Callbacks\Callback;
use Mmb\Laravel\Core\Updates\Infos\ChatInfo;
use Mmb\Laravel\Core\Updates\Infos\UserInfo;
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

    /**
     * Find base message of update
     *
     * @return ?Message
     */
    public function getMessage()
    {
        return $this->makeCache(
            'Message',
            fn() => match (true)
            {
                null !== $this->message           => $this->message,
                null !== $this->editedMessage     => $this->editedMessage,
                null !== $this->channelPost       => $this->channelPost,
                null !== $this->editedChannelPost => $this->editedChannelPost,
                null !== $this->callbackQuery     => $this->callbackQuery->message,
                default                           => null,
            }
        );
    }

    /**
     * Find base chat of update
     *
     * @return ChatInfo|null
     */
    public function getChat()
    {
        if($message = $this->getMessage())
        {
            return $message->chat;
        }

        return null;
    }

    /**
     * Find base chat of update
     *
     * @return UserInfo|null
     */
    public function getUser()
    {
        return match (true)
        {
            null !== $this->callbackQuery             => $this->callbackQuery->from,
            // null !== $this->inlineQuery => $this->inlineQuery-> TODO
            null !== ($message = $this->getMessage()) => $message->from,
            default                                   => null,
        };
    }

    /**
     * Is update handled
     *
     * @var bool
     */
    public bool $isHandled = false;

    /**
     * Skip current handler
     *
     * @return void
     */
    public function skipHandler()
    {
        $this->isHandled = false;
    }

    /**
     * Handle update
     *
     * @return void
     */
    public function handle()
    {
        $this->bot()->handleUpdate($this);
    }

    /**
     * Response to the update
     *
     * @param       $message
     * @param array $args
     * @param mixed ...$namedArgs
     * @return ?Message
     */
    public function response($message, array $args = [], ...$namedArgs)
    {
        return $this->getMessage()->response($message, $args, ...$namedArgs);
    }

    /**
     * Response callback query
     *
     * @param       $message
     * @param array $args
     * @return ?bool
     */
    public function tell($message, array $args = [])
    {
        return $this->callbackQuery?->answer($message, $args);
    }

}
