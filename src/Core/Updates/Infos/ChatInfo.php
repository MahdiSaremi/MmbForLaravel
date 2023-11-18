<?php

namespace Mmb\Laravel\Core\Updates\Infos;

use Mmb\Laravel\Core\Data;
use Mmb\Laravel\Core\Updates\Messages\Message;

class ChatInfo extends Data
{

    protected function dataCasts() : array
    {
        return [
            'id'                                      => 'int',
            'type'                                    => 'string',
            'title'                                   => 'string',
            'username'                                => 'string',
            'first_name'                              => 'string',
            'last_name'                               => 'string',
            'is_forum'                                => 'bool',
            // 'photo' => ChatPhoto::class,
            'emoji_status_custom_emoji_id'            => 'string',
            'emoji_status_expiration_date'            => 'date',
            'bio'                                     => 'string',
            'has_private_forwards'                    => 'bool',
            'has_restricted_voice_and_video_messages' => 'bool',
            'join_to_send_messages'                   => 'bool',
            'join_by_request'                         => 'bool',
            'description'                             => 'string',
            'invite_link'                             => 'string',
            'pinned_message'                          => Message::class,
            // 'permissions' => CharPermissions::class,
            'slow_mode_delay'                         => 'int',
            'message_auto_delete_time'                => 'int',
            'has_aggressive_anti_spam_enabled'        => 'bool',
            'has_hidden_members'                      => 'bool',
            'has_protected_content'                   => 'bool',
            'sticker_set_name'                        => 'string',
            'can_set_sticker_set'                     => 'bool',
            'linked_chat_id'                          => 'int',
            // 'location'                                => Location::class,
        ];
    }

}