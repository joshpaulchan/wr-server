# Conversations
## Model

**Key**
* **+** public and modifiable
* **#** public, non-modifiable
* **-** private, non-modifiable

### Conversation

<table>
<thead>
    <tr>
        <th style="width: 1%">-</th>
        <th style="width: 24%">name</th>
        <th>type</th>
        <th style="width: 50%">description</th>
    </tr>
</thead>
<tbody>
    <tr>
        <th>#</th>
        <th>id</th>
        <td>String</td>
        <td>Unique id referring to this **Conversation** record</td>
    </tr>
    <tr>
        <th>#</th>
        <th>emailFrom</th>
        <td>String</td>
        <td>The email of the participant that started this conversation</td>
    </tr>
    <tr>
        <th>#</th>
        <th>subject</th>
        <td>String</td>
        <td>The subject of the complaint conversation</td>
    </tr>
    <tr>
        <th>#</th>
        <th>unread</th>
        <td>Boolean</td>
        <td>
            Whether or not this message has been read.
        </td>
    </tr>
    <tr>
        <th>#</th>
        <th>unreplied</th>
        <td>Boolean</td>
        <td>
            Whether or not this message has been replied to.
        </td>
    </tr>
    <tr>
        <th>+</th>
        <th>location</th>
        <td>String (Enum)</td>
        <td>
            The folder this conversation is currently located in. Can be one of the following **String** values:
            <ul>
                <li>**inbox** - whether or not this message is unread or not</li>
                <li>**spam** - whether or not this message is unreplied or not</li>
                <li>**trash** - whether or not this message is unreplied or not</li>
            </ul>
        </td>
    </tr>

    <tr>
        <th>#</th>
        <th>userAgent</th>
        <td>String</td>
        <td>The user agent of the browser that sent the complaint message</td>
    </tr>
    <tr>
        <th>#</th>
        <th>browser</th>
        <td>String</td>
        <td>The browser (detected from the user agent)</td>
    </tr>

    <tr>
        <th>#</th>
        <th>os</th>
        <td>String</td>
        <td>The os (detected from the user agent)</td>
    </tr>
    <tr>
        <th>#</th>
        <th>ip</th>
        <td>string(ip address)</td>
        <td>The origin IP address of the complain message</td>
    </tr>
    <tr>
        <th>#</th>
        <th>referrer</th>
        <td>Date</td>
        <td>The original page the user submitted the complaint from</td>
    </tr>
    <tr>
        <th>#</th>
        <th>createdAt</th>
        <td>Date</td>
        <td>When the conversation was created</td>
    </tr>
    <tr>
        <th>#</th>
        <th>lastUpdated</th>
        <td>Date</td>
        <td>When the conversation was last updated</td>
    </tr>
</tbody>
</table>

### Message

<table>
<thead>
    <tr>
        <th style="width: 1%">-</th>
        <th style="width: 24%">name</th>
        <th>type</th>
        <th style="width: 50%">description</th>
    </tr>
</thead>
<tbody>
    <tr>
        <th>#</th>
        <th>id</th>
        <td>String</td>
        <td>Unique id referring to this **Message** record</td>
    </tr>
    <tr>
        <th>#</th>
        <th>emailFrom</th>
        <td>String</td>
        <td>The email of the participant that sent this message</td>
    </tr>
    <tr>
        <th>#</th>
        <th>emailTo</th>
        <td>String</td>
        <td>The email of the participant that this message is meant for</td>
    </tr>
    <tr>
        <th>#</th>
        <th>createdAt</th>
        <td>Date</td>
        <td>When this message was created</td>
    </tr>
    <tr>
        <th>#</th>
        <th>lastUpdated</th>
        <td>Date</td>
        <td>When this message was last updated</td>
    </tr>
    <tr>
        <th>#</th>
        <th>body</th>
        <td>String</td>
        <td>A UTF-8 encoded, HTML-safe string representation of the message body</td>
    </tr>
    <tr>
        <th>#</th>
        <th>conversation</th>
        <td>String</td>
        <td>Id of the **Conversation** record this message is part of</td>
    </tr>
</tbody>
</table>

## API (Version 1.0)

### Routes
**Note** - all endpoints are *JSON* endpoints unless otherwise specified.

<table>
<thead>
    <tr>
        <th style="width: 25%">method and url</th>
        <th>description</th>
        <th style="width: 50%">notes</th>
    </tr>
</thead>
<tbody>
    <tr>
        <td>**GET** `/conversations/list`</td>
        <td>retrieve a page of conversations or search results</td>
        <td>
            **query params**
            <ul>
                <li>**page** *[optional] [default 0]* - the page of conversations to retrieve</li>
                <li>**n** *[optional] [default 25]* - the number of items to return per page</li>
                <li>**q** *[optional]* - the string to search for in the conversations</li>
            </ul>
            retrieves a list of conversations with meta-information
        </td>
    </tr>
    <tr>
        <td>**POST** `/conversations/create`</td>
        <td>create a new conversation</td>
        <td>
            **Note:** This is only a webhook used for creating new conversations<br/>
            retrieves a list of conversations with meta-information
        </td>
    </tr>
    <tr>
        <td>**PUT** `/conversations/update`</td>
        <td>update multiple conversations at once</td>
        <td>
            **body**
            <ul>
            <li>**ids** - ids of conversations to update to update</li>
            <li>**options** - JSON-encoded hash-map of valid key-value pairs to set for each given conversation</li>
            </ul>
        </td>
    </tr>
    <tr>
        <td>**GET** `/conversation/:id`</td>
        <td>retrieve a conversation and its messages</td>
        <td>
            **id** - id of conversation to fetch
        </td>
    </tr>
    <tr>
        <td>**POST** `/conversation/reply/:id`</td>
        <td>respond to a conversation (send an email)</td>
        <td>
            **id** - id of conversation to respond to<br/>
            **body**
            <ul>
            <li>**message** - message to send as response</li>
            </ul>
        </td>
    </tr>
    <tr>
        <td>**PUT** `/conversation/update/:id`</td>
        <td>update a conversation</td>
        <td>
            **id** - id of conversation to update<br/>
            **body**
            <ul>
            <li>**options** - JSON-encoded hash-map of valid key-value pairs to set for each given conversation</li>
            </ul>
        </td>
    </tr>
    <tr>
        <td>**DELETE** `/conversation/delete/:id`</td>
        <td>delete a conversation</td>
        <td>
            **id** - id of conversation to delete
        </td>
    </tr>
</tbody>
</table>

### Authentication

The application protects against CSRF by randomly generating a token for each session (log-in). This token ***must*** be sent with the request in the header, either as `X-CSRF-TOKEN` or `X-XSRF-TOKEN`.

### Example Responses

**GET** `/conversations`

**Notice**: the *messages* for each conversation are not included in the bulk request.

```bash
$ curl -x GET 'http://localhost:3000/api/v1/conversations'
{
    "per_page": 25,
    "current_page": 1,
    "next_page_url": "http://localhost/srvr/public/conversations?page=2",
    "prev_page_url": null,
    "data": [
        {
            "id": 1,
            "emailFrom": "agustin30@hotmail.com",
            "subject": "Ea rerum tempore assumenda qui animi.",
            "unread": 1,
            "unreplied": 0,
            "location": "trash",
            "userAgent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_6 rv:2.0; sl-SI) AppleWebKit/534.30.5 (KHTML, like Gecko) Version/4.0.4 Safari/534.30.5",
            "browser": "Safari 10.0",
            "os": "Mac OSX",
            "ip": "250.46.131.165",
            "referrer": "http://towne.com/sed-voluptates-non-et-nisi-blanditiis-ut-alias-voluptas.html",
            "created_at": "2016-12-08 17:37:55",
            "updated_at": "2016-12-08 17:37:55"
        },
        ...
    ]
}
```

**GET** `/conversations/{id}`

```bash
$ curl -x GET 'http://localhost:3000/api/v1/conversations/12'
{
    "data": {
        "id": 1,
        "emailFrom": "agustin30@hotmail.com",
        "subject": "Ea rerum tempore assumenda qui animi.",
        "unread": 1,
        "unreplied": 0,
        "location": "trash",
        "userAgent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_6 rv:2.0; sl-SI) AppleWebKit/534.30.5 (KHTML, like Gecko) Version/4.0.4 Safari/534.30.5",
        "browser": "Safari 10.0",
        "os": "Mac OSX",
        "ip": "250.46.131.165",
        "referrer": "http://towne.com/sed-voluptates-non-et-nisi-blanditiis-ut-alias-voluptas.html",
        "created_at": "2016-12-08 17:37:55",
        "updated_at": "2016-12-08 17:37:55",
        "messages" : [
            {
                "id": 117,
                "emailFrom": "jweber@yahoo.com",
                "emailTo": "tomasa85@yahoo.com",
                "body": "Soluta iure sapiente enim nam autem quidem laboriosam. Illo a suscipit nulla beatae. Corrupti et dolorem voluptatibus quasi voluptatem suscipit quia.",
                "conversation_id": 30,
                "created_at": "2016-12-08 17:37:56",
                "updated_at": "2016-12-08 17:37:56"
            },
            ...
        ]
    }
}
```

**POST** `/conversations/{id}`

```bash
$ curl -x POST 'http://localhost:3000/api/v1/conversations/12' -H message='<p>Nice</p>'
{
    "id": 117,
    "emailFrom": "we",
    "emailTo": "tomasa85@yahoo.com",
    "body": "<p>Nice</p>",
    "conversation_id": 30,
    "created_at": "2016-12-08 17:37:56",
    "updated_at": "2016-12-08 17:37:56"
}
```
## Design

1. Conversations are created when a user submits a complaint to the online webform. A Converation record is created with the metadata and a message record is also created with the new submitted writing.
2. A cron job should run every 6 hours to pull from the office 360 REST API and see which conversations got new messages.
    - message-conversation linking is done by including the id of the conversation in sent emails (and hoping replies also contain the previous emails) by including them in the body or the subject field or both
3. Messages will be sanitized on input and stored on the databased, then escaped on output (as pre http://lukeplant.me.uk/blog/posts/why-escape-on-input-is-a-bad-idea/).

## References
