<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Thread;
use App\Message;
use App\Participant;
use App\User;

class MessagesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() {
        parent::setUp();
        
        $this->user1 = factory(User::class)->create([
            'name' => 'John Doe',
        ]);
        $this->user2 = factory(User::class)->create([
            'name' => 'Mary Jane',
        ]);
    }

    /**
     * Should list all the received messages
     *
     * @return void
     */
    public function testReceviedMessages()
    {
        $thread = factory(Thread::class)->create();
        $message = $thread->messages()->save(factory(Message::class)->make([
            'user_id' => $this->user1->id,
        ]));
        $thread->messages()->save(factory(Participant::class)->make([
            'user_id' => $this->user1->id,
        ]));
        $thread->messages()->save(factory(Participant::class)->make([
            'user_id' => $this->user2->id,
        ]));

        $this->authenticate($this->user2);

        $response = $this->withAuthenticationHeaders()
                         ->json('GET', $this->base.'/mailbox/messages/received');
        
        $response->assertStatus(200);
        $response->assertJson([
            "success"   =>  true,
            "threads"     => [
                [
                    "id"      => $thread->id,
                    "title"   => $thread->title,
                    "public"  => false,
                    "excerpt" => substr($message->content, 0, 100),
                    "author"  => [
                        "id"    => $this->user1->id,
                        "name"  => $this->user1->name,
                        "image" => $this->user1->image,
                    ],
                    "time" => [
                        "timezone" => "UTC",
                    ],
                ],
            ],
        ]);
    }

    /**
     * Should list all the sent messages by the user
     *
     * @return void
     */
    public function testSentMessages()
    {
        $thread = factory(Thread::class)->create();
        $message = $thread->messages()->save(factory(Message::class)->make([
            'user_id' => $this->user1->id,
        ]));
        $thread->messages()->save(factory(Participant::class)->make([
            'user_id' => $this->user1->id,
        ]));
        $thread->messages()->save(factory(Participant::class)->make([
            'user_id' => $this->user2->id,
        ]));

        $this->authenticate($this->user1);

        $response = $this->withAuthenticationHeaders()
                         ->json('GET', $this->base.'/mailbox/messages/sent');
        
        $response->assertStatus(200);
        $response->assertJson([
            "success"   =>  true,
            "threads"     => [
                [
                    "id"      => $thread->id,
                    "title"   => $thread->title,
                    "public"  => false,
                    "excerpt" => substr($message->content, 0, 100),
                    "author"  => [
                        "id"    => $this->user2->id,
                        "name"  => $this->user2->name,
                        "image" => $this->user2->image,
                    ],
                    "time" => [
                        "timezone" => "UTC",
                    ],
                ],
            ],
        ]);
    }

    /**
     * Should create a new thread and add the selected participant
     *
     * @return void
     */
    public function testSendMessageToUser()
    {
        $this->authenticate($this->user1);
        $response = $this->withAuthenticationHeaders()
                         ->json('POST', $this->base.'/mailbox/messages', [
                            "title" => "Hi there",
                            "content" => "Testing thread creation",
                            "recipients" => [ ["id" => $this->user2->id] ],
                         ]);
        
        $thread = Thread::where('title', 'Hi there')->first();

        $response->assertStatus(200);
        $response->assertJson([
            "success"   =>  true,
            "thread"     => [
                "id"    =>$thread->id,
            ],
        ]);
    }

    /**
     * Should validate existing of title, content and recipients
     *
     * @return void
     */
    public function testValidateRequiredFields()
    {
        $this->authenticate($this->user1);
        $response = $this->withAuthenticationHeaders()
                         ->json('POST', $this->base.'/mailbox/messages', []);

        $response->assertStatus(400);
        $response->assertJson([
            "success"   =>  false,
            "errors"     => [
                "The title field is required.",
                "The content field is required.",
                "The recipients field is required.",
            ],
        ]);
    }

    /**
     * Should return an error if recipient doesn't exist
     *
     * @return void
     */
    public function testValidateWrongRecepient()
    {
        $this->authenticate($this->user1);
        $response = $this->withAuthenticationHeaders()
                         ->json('POST', $this->base.'/mailbox/messages', [
                             "title" => "Hi there",
                            "content" => "Testing thread creation",
                            "recipients" => [ ["id" => 99999] ],
                         ]);

        $response->assertStatus(400);
        $response->assertJson([
            "success"   =>  false,
            "errors"     => [
                "The user you are trying to send this message doesn't exist.",
            ],
        ]);
    }

    /**
     * Should return the giving message
     *
     * @return void
     */
    public function testShowThread() {
        $thread = factory(Thread::class)->create();
        $message = $thread->messages()->save(factory(Message::class)->make([
            'user_id' => $this->user1->id,
        ]));
        $thread->messages()->save(factory(Participant::class)->make([
            'user_id' => $this->user1->id,
        ]));
        $thread->messages()->save(factory(Participant::class)->make([
            'user_id' => $this->user2->id,
        ]));

        $this->authenticate($this->user2);
        $response = $this->withAuthenticationHeaders()
                         ->json('GET', $this->base.'/mailbox/messages/'.$thread->id);

        $response->assertStatus(200);
        $response->assertJson([
            "success"   =>  true,
            "thread"    => [
                "id"    => $thread->id,
                "public"=> false,
                "title" => $thread->title,
                "messages" => [
                    [
                        "id"        => $message->id,
                        "content"   => $message->content,
                        "author"    => [
                            "id"    => $this->user1->id,
                            "name"  => $this->user1->name,
                            "image" => $this->user1->image,
                        ],
                        "time" => [
                            "timezone" => "UTC",
                        ],
                        "read" => false,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Should return a 404 error if thread is not found
     *
     * @return void
     */
    public function testReturn404IfNotExist() {
        $this->authenticate($this->user2);
        $response = $this->withAuthenticationHeaders()
                         ->json('GET', $this->base.'/mailbox/messages/999');

        $response->assertStatus(404);
        $response->assertJson([
            "success"   =>  false,
            "errors"    => [
                "Conversation not found",
            ],
        ]);
    }

    /**
     * Should return a 403 error if user is not part of the thread
     *
     * @return void
     */
    public function testReturnErrorIfUserDoesntBelongToConversation() {
        $user = factory(User::class)->create([
            'name' => 'Carl Totti',
        ]);
        $thread = factory(Thread::class)->create();
        $message = $thread->messages()->save(factory(Message::class)->make([
            'user_id' => $this->user1->id,
        ]));
        $thread->messages()->save(factory(Participant::class)->make([
            'user_id' => $this->user1->id,
        ]));
        $thread->messages()->save(factory(Participant::class)->make([
            'user_id' => $this->user2->id,
        ]));

        $this->authenticate($user);
        $response = $this->withAuthenticationHeaders()
                         ->json('GET', $this->base.'/mailbox/messages/'.$thread->id);

        $response->assertStatus(403);
        $response->assertJson([
            "success"   =>  false,
            "errors"    => [
                "You are not part of this conversation",
            ],
        ]);
    }

    /**
     * Should return and empty response for the given keyword
     *
     * @return void
     */
    public function testReturnEmptyResponse() {
        $user1 = factory(User::class)->create([
            'name' => 'Carl Totti',
        ]);
        $user2 = factory(User::class)->create([
            'name' => 'Daniel Johnsy',
        ]);
        $user3 = factory(User::class)->create([
            'name' => 'John McDown',
        ]);
        $user4 = factory(User::class)->create([
            'name' => 'Johnny Doe',
        ]);
        $user5 = factory(User::class)->create([
            'name' => 'Sarah',
        ]);
        

        $this->authenticate($user1);
        $response = $this->withAuthenticationHeaders()
                         ->json('GET', $this->base.'/mailbox/users?search=test');

        $response->assertStatus(200);
        $response->assertJson([
            "success"   =>  true,
            "users"    => [],
        ]);
    }

    /**
     * Should return and empty response for an empty keyword
     *
     * @return void
     */
    public function testReturnEmptyResponseEmptyKeyword() {
        $user1 = factory(User::class)->create([
            'name' => 'Carl Totti',
        ]);
        $user2 = factory(User::class)->create([
            'name' => 'Daniel Johnsy',
        ]);
        $user3 = factory(User::class)->create([
            'name' => 'John McDown',
        ]);
        $user4 = factory(User::class)->create([
            'name' => 'Johnny Doe',
        ]);
        $user5 = factory(User::class)->create([
            'name' => 'Sarah',
        ]);
        

        $this->authenticate($user1);
        $response = $this->withAuthenticationHeaders()
                         ->json('GET', $this->base.'/mailbox/users?search=');

        $response->assertStatus(200);
        $response->assertJson([
            "success"   =>  true,
            "users"    => [],
        ]);
        $data = $response->decodeResponseJson();

        $this->assertTrue(count($data['users']) == 0);
    }

    /**
     * Should return the matched users for the given keyword
     *
     * @return void
     */
    public function testReturnUsersByKeyword() {
        $user1 = factory(User::class)->create([
            'name' => 'Carl Totti',
        ]);
        $user2 = factory(User::class)->create([
            'name' => 'Daniel Hassy',
        ]);
        $user3 = factory(User::class)->create([
            'name' => 'Hassle McDown',
        ]);
        $user4 = factory(User::class)->create([
            'name' => 'Hasby Doe',
        ]);
        $user5 = factory(User::class)->create([
            'name' => 'Sarah',
        ]);
        

        $this->authenticate($user1);
        $response = $this->withAuthenticationHeaders()
                         ->json('GET', $this->base.'/mailbox/users?search=has');

        $response->assertStatus(200);
        $response->assertJson([
            "success"   =>  true,
            "users"    => [
                [
                    "id" => $user2->id,
                    "name" => $user2->name,
                    "image" => $user2->image,
                    "occupation" => $user2->occupation,
                ],
                [
                    "id" => $user4->id,
                    "name" => $user4->name,
                    "image" => $user4->image,
                    "occupation" => $user4->occupation,
                ],
                [
                    "id" => $user3->id,
                    "name" => $user3->name,
                    "image" => $user3->image,
                    "occupation" => $user3->occupation,
                ],
            ],
        ]);
    }
}
