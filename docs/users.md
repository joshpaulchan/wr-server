# Users and Authentication

## Flows

### Log in
Pre-authorized users can log-in via the `/login` page.

1. Navigate to the login page (when visiting web response, it is the first and default page)
2. Enter authorized netid email and password
3. Click login
4. If successful, browser will navigate to conversations page
5. If not, browser will show generic error message

### Logout

1. Go to the settings page
2. Press logout -> browser will redirect back to the login page

### Registration
The registration flow is two-part, sort of like an inverted invite system. A person attempting to create a user account must apply, and an admin of the system will can  then choose to approve the user application.

**Applicant**

1. Navigate to the login page (when visiting web response, it is the first and default page)
2. Click register -> browser will navigate to registration page
3. Enter netid email, password, and password confirmation
4. If successful, will present confirmation page.

**Admin**

1. Navigate to the users page
2. From the users page, you should be able to see users that are pending approval
3. Select user you want to approve by clicking the button saying approve

### Removing a user/yourself
Admins can remove users as they please, users can only remove a user if it is himself/herself that is being removed.

1. Go to user page
2. Hover over user you would like to remove
3. Click the x button that appears and verify confirmation

### Change email

1. Go to settings page -> profile is the first tab
2. Change the listed email in the input field
3. Enter it

### Change password

1. Go to settings page -> profile is the first tab
2. Type old password
3. Type new password and password confirm
4. Enter it



## Wireframes

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
            **@pre** - the user attempting to log in must be approved.</br>
            **body**
            <ul>
            <li>**email** - email for the account</li>
            <li>**password** - password for the account</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>GET `/auth/logout`</th>
        <td>attempt to log out of web response</td>
        <td>
            user attempting to logout is recognized from session data.
        </td>
    </tr>
    <tr>
        <th>GET `/auth/loggedIn`</th>
        <td>check is user is logged in to web response</td>
        <td>
            session data is checked for user info.
        </td>
    </tr>
    <tr>
        <th>POST `/auth/register`</th>
        <td>submits an account creation request, pending approval by web response admins</td>
        <td>
            **body**
            <ul>
            <li>**email** - intended email for the account</li>
            <li>**password** - intended password for the account</li>
            </ul>
        </td>
    </tr>
    <tr>
        <th>POST `/auth/escalate/:id`</th>
        <td>escalates a regular user to admin statuss</td>
        <td>
            **@pre** - user submitting the request has to be an admin.<br/>
            **id** - id of **User** record to escalate
        </td>
    </tr>
    <tr>
        <th>POST `/auth/deescalate/:id`</th>
        <td>de-escalates an admin user to regular user status</td>
        <td>
            **@pre** - user submitting the request has to be an admin.<br/>
            **id** - id of **User** record to deescalate
        </td>
    </tr>
    <tr>
        <th>POST `/auth/approve/:id`</th>
        <td>approves an applicant and makes them a regular user</td>
        <td>
            **@pre** - user submitting the request has to be an admin.<br/>
            **id** - id of **User** record to approve
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
        <th>GET `/users?page={page}&n={}&approval={}`</td>
        <td>get a list of users registered with web response, filtered by approval satus</td>
        <td>
            **query params**
            <ul>
                <li>**page** *[optional] [default 0]* - the page of conversations to retrieve</li>
                <li>**n** *[optional] [default 25]* - the number of items to return per page</li>
                <li>**approval** *[default true]* - the approval status to filter for</li>
            </ul>
            retrieves a list of users, by default returns a paginated list of approved users.
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
        <td>update a user's *email* or *password*</td>
        <td>
            **@pre** - to change `email` or `password`, the user submitting the request must be the same as the user specified via `id`.<br/>
            **id** - id of **User** record to modify<br/>
            **body**
            <ul>
                <li>**email** *[optional]* - the email to change to</li>
                <li>**password** *[optional]* - the password to change to</li>
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

1. Primary backbone for authentication is sessions.
2. You'll notice that while there is a `/escalate` and a corresponding `/deescalate`, there is not pair for `/approve`. That is because once a user has been approved for use, she can only be removed or escalated/deescalated.
