# Users and Authentication
## Model

**Key**
* **+** public and modifiable
* **#** public, non-modifiable
* **-** private, non-modifiable

### User

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
        <td>Unique id referring to this **User** record</td>
    </tr>
    <tr>
        <th>+</th>
        <th>email</th>
        <td>String</td>
        <td>The email of the participant that started this conversation</td>
    </tr>
    <tr>
        <th>-</th>
        <th>password</th>
        <td>String</td>
        <td>The subject of the complaint conversation</td>
    </tr>
    <tr>
        <th>#</th>
        <th>admin</th>
        <td>Boolean</td>
        <td>The email of the participant that started this conversation</td>
    </tr>
    <tr>
        <th>#</th>
        <th>approved</th>
        <td>Boolean</td>
        <td>Whether or not this account was approved for use</td>
    </tr>
    <tr>
        <th>-</th>
        <th>createdAt</th>
        <td>String</td>
        <td>The subject of the complaint conversation</td>
    </tr>
    <tr>
        <th>-</th>
        <th>lastUpdated</th>
        <td>String</td>
        <td>The subject of the complaint conversation</td>
    </tr>
</tbody>
</table>

## API (Version 1.0)

### Routes

**Notes**
<ul>
    <li>all endpoints are require authorization except for those specified in `auth`.</li>
    <li>all endpoints are *JSON* endpoints unless otherwise specified.</li>
</ul>

#### Authentication and Authorization

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
        <th>POST `/auth/login`</th>
        <td>attempt to log in to web response</td>
        <td>
            **body**
            <ul>
            <li>**username** - username for the account</li>
            <li>**password** - password for the account</li>
            </ul>
            Returns a JWT for future authentication use
        </td>
    </tr>
    <tr>
        <th>POST `/auth/register`</th>
        <td>submits an account creation request, pending approval by web response admins</td>
        <td>
            **body**
            <ul>
            <li>**username** - intended username for the account</li>
            <li>**password** - intended password for the account</li>
            </ul>
        </td>
    </tr>
</tbody>
</table>

#### User

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
        <th>GET `/users`</td>
        <td>get a list of users registered with web response</td>
        <td>
            retrieves a list of users with meta-information
        </td>
    </tr>
    <tr>
        <th>GET `/users/`</td>
        <td>get a list of users registered with web response</td>
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
        <th>GET `/user/:id`</td>
        <td>retrieve a specific user's data from web response</td>
        <td>
            **id** - id of **User** record to retrieve
        </td>
    </tr>
    <tr>
        <th>PUT `/user/:id`</td>
        <td>update a user's *username*, *password* or *admin* status</td>
        <td>
            **@pre** - to change `username` or `password`, the user submitting the request must be the same as the user specified via `id`.<br/>
            **@pre** - to change `admin` or `approved`, the user submitting the request must be an admin.<br/><br/>
            **id** - id of **User** record to modify<br/>
            **body**
            <ul>
                <li>**username** *[optional]* - the username to change to</li>
                <li>**password** *[optional]* - the password to change to</li>
                <li>**admin** *[optional]* - the admin status of the account</li>
                <li>**approved** *[optional]* - the approval status of the account</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>DELETE `/user/:id`</td>
        <td>delete a specific user</td>
        <td>
            **@pre** - to delete a user, the user submitting the request must be the same as the user specified via `id` OR be an admin.<br/><br/>
            **id** - id of **User** record to delete
        </td>
    </tr>
</tbody>
</table>

### Example Responses

**GET** `/conversations`

**Notice**: the *messages* for each conversation are not included in the bulk request.

```bash
$ curl -x POST 'http://localhost:3000/api/v1/auth/login'
```

```bash
$ curl -x POST 'http://localhost:3000/api/v1/auth/register'
```

```bash
$ curl -x GET 'http://localhost:3000/api/v1/users'
```

```bash
$ curl -x GET 'http://localhost:3000/api/v1/user/12'
```

```bash
$ curl -x PUT 'http://localhost:3000/api/v1/user/12'
```

```bash
$ curl -x DELETE 'http://localhost:3000/api/v1/user/12'
```

## Design

1. Primary backbone for authentication is JWTs.
