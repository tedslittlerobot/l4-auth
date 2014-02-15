L4 Auth
=======

> Some user functionality for Laravel 4

## Installation

Add the following to your composer.json file:

```json
"repositories": [
    {
        "type": "git",
        "url": "https://github.com/tedslittlerobot/l4-auth"
    }
],
```

Then, you can `composer require tlr/l4-auth` to add this to your composer.json and download it.

## User

The user model is the core of this package. Make sure that your user model extends `Tlr\Auth\User`.

#### Migration

Run the following to put a migration into your migrations folder.

    php artisan migrate:publish tlr/l4-auth

This is the base minimum this exact user model needs to run properly. Feel free to add / modify anything in this copied file to suit your application.

#### Passwords

Simply assigning a password will automatically hash it:

```php
$user->password = 'password1';
echo $user->password; // This will echo the hashed password
```

#### Names

Likewise, the firstname and lastname attributes will auto-capitalise the first letters of each word in them. Additionally, a [pseudo-property](http://laravel.com/docs/eloquent#accessors-and-mutators), `name`, will return the first and last names, concatenated together:

```php
$user->firstname = 'helena';
$user->lastname = 'bonham carter';

echo $user->name; // => Helena Bonham Carter
```

#### Permissions

###### Add / Remove

Permissions are stored in the database as a json array of strings, representing each permission the user has. The array is automatically converted between PHP and JSON. For Example:

```php
$user->permissions = ['admin', 'edit-posts']; // This user can now access the admin panel, and edit posts
$user->addPermission('delete-users'); // This user can now additionally delete users
$user->addPermission(['add-users', 'accept-payments']); // This user can now add users and accept payments
$user->denyPermission('add-users'); // This user can no longer add users. Like the addPermission method, this can take an array of permissions to deny
```

To keep track of any auth permissions that may be added, there is an array, Auth::$_LEVELS, where you can store them as and when you create them.

###### Ninjas

There is a secret permission, `ninja`. A user with the ninja permission will automatically pass any auth checks.

###### Sync Permissions

Finally, there is also a `syncPermissions` method, that takes a list of permissions you want the user to have, and syncs it with the Auth::$_LEVELS array, preserving the 'ninja' permission. This is one to use, for example, if you have a user management form, that loops over the $_LEVELS array. You can pass the checked inputs into this to sync them.

#### Auth Checking

To see if a user is allowed to see or do something, there is the `can` method. Its first argument is either a string, or an array of strings, which are the permission(s) to check for. The second argument defaults to true, where all permissions must exist on the user to pass. If set to false, any matches will yield a positive result.

```php
$user->permissions = [ 'users', 'pages' ];

$user->can('users'); // => true
$user->can(['users', 'articles']); // => false
$user->can(['users', 'pages']); // => true
$user->can(['users', 'articles']); // => true
```

There is a global helper function, `can`, that will test permissions on the currently logged in user, returning false if no user is logged in.
