# Web Response


## Table of contents

- Introduction
- Usage
    - Conversations
    - Users
    - Templates
- Design
    - [Conversations](./conversations.md)
    - [Users](./users.md)

## Introduction

**Web Response** is a tool that allows `*.rutgers.edu` visitors to submit complaints and allows the rutgers staff to view, triage and respond to those complaints in a streamlined interface.

The frontend uses
- **AngularJS 1.5.8**, **angular-ui-router** and **textAngular** for interactivity
- **Overpass** and **Font Awesome** for design
- **Bower**, **npm** and **jasmine** for administration, development and testing

The backend runs
- **PHP 2.9** and **CodeIgniter v2.2.5** (for PHP compatibility)
- **Apache 2.4.23**
- **MySQL 5.7.14**

## Usage

## Flow

### Public Users

1. Can access a form to submit a message (start a conversation)
2. Can apply for a user account to become an authenticated user

### Registered Users & Admins

1. Authenticated users can log into the service to access private content
2. Authenticated users can log out of the service
3. Authenticated users can view and search complaints
4. Authenticated users can mark complaints as read/unread/replied/unreplied
5. Authenticated users can respond to complaints and mark as replied
6. Authenticated users can move conversations to different folders
7. Authenticated users can forward conversations to outside emails
8. Authenticated users can view reply templates
9. Authenticated users can create reply templates
10. Authenticated users can use reply templates

### Admins

1. Admins can view the users of the system
2. Admins can approve/deny access to users of the system
3. Admins can approve/deny admin status of other users
