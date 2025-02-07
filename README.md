# Laravel Remote API Login

## Index

- [Introduction](#introduction)
- [Requirements](#requirements)
- [Installation](#installation)
- [How it works](#how-it-works)
- [Configuration](#configuration)
- [The Event](#the-event)
- [Troubleshooting](#troubleshooting)
- [Examples](#functional-examples)
- [Licence](#license)
- [Feedback and contributions](#feedback-and-contributions)


## Introduction
Welcome to the documentation for Laravel Remote API Login, a package designed for Laravel that provides a new authentication method via API for your applications against a Laravel backend.

## Purpose of the Package

This package is designed to offer an alternative login method for various devices, particularly those with the following characteristics:

- **IoT Devices Without Keyboard Access**: This package allows IoT devices without a keyboard to authenticate against a Laravel backend in a simple and direct way.
- **Devices with Limited or Difficult Keyboard Access**: Some devices may have a keyboard, but it might be inconvenient or difficult to use, such as a TV where users must navigate key by key through an unfriendly input system.
- **Diverse MFA Requirements**: Applications often need to support multiple authentication methods across different user pools ([Okta](https://www.okta.com/), [Google](https://developers.google.com/identity), [GitHub](https://docs.github.com/en/authentication), [Azure](https://learn.microsoft.com/en-us/azure/active-directory/), [Apple](https://developer.apple.com/sign-in-with-apple/), etc.), each with its own MFA system. This package allows you to delegate the MFA process directly to the authentication source, abstracting away the specific MFA system used and regaining control only after successful authentication.

## Requirements

To use this package, your system must meet the following minimum requirements:

- **[Laravel Framework](https://laravel.com/docs/10.x)** (v10 or higher)
- **PHP 8**
- **[Laravel Broadcasting](https://laravel.com/docs/10.x/broadcasting)**

Since the login system operates via WebSockets, your backend must have broadcasting enabled and fully functional, regardless of the broadcasting system you use ([Laravel Reverb](https://laravel.com/docs/10.x/reverb), [Pusher](https://pusher.com/), etc.).


## Installation

To install the package, run the following command:

```sh
composer require wergh/remote-api-login
```

This will install the package and its dependencies.

Once installed, publish the configuration file to customize the package’s settings:

```sh
php artisan vendor:publish --tag="remote-api-login-config"
```

After publishing the configuration, it is necessary to run the migrations. However, before doing so, please make sure to modify the database table name for the package if you wish to do so.

```sh
php artisan migrate
```

## How It Works

To better illustrate how the package functions, let's go step by step through the workflow of how an application performs login against your backend using this package.

### 1. The API Request

On your device’s login screen, you must add a method (e.g., a button) that triggers an API request to an endpoint provided by the package. This request initiates the login process. The backend will return a response containing three elements: a **code**, a **temporary user_id**, and a **temporary token**. You must store these three variables for later use.

### 2. WebSocket Connection

Once the response is received, the device must subscribe to a WebSocket channel and wait for an event (the event name can be customized in the configuration). This channel is where the backend will notify the device that the user has logged in and can now retrieve their authentication token.

### 3. Displaying the Authentication URL

The core functionality of this package allows users to authenticate using any other device. This could be through Laravel's built-in authentication system or an external provider. The next step is to inform the user where they should go to log in. You can provide a clickable link, a QR code, or any other method. The crucial part is that the user must enter the **code** received in the API response.

If you provide a direct link or QR code, it's best to append the code as a query parameter so that your login page can prefill it automatically. For example, instead of displaying `https://www.example.com/login`, show `https://www.example.com/login?code=AAAAAAAA`.

### 4. Creating the Login Page

You need to create a simple login page within your Laravel application. This page should allow users to authenticate either through Laravel's built-in system or external providers like Google, Azure, or Okta. Here’s how to handle each case:

#### 4.1 Logging in via Laravel

Simply add your standard authentication fields (e.g., username and password) along with a field for the login code. If you followed the previous suggestion, you can retrieve the code from the URL using `Request::get('code')`. Once filled out, submit the form to the backend for authentication.

#### 4.2 Logging in via an External Provider

With Laravel Socialite, you can easily implement authentication via external providers. Most providers require specifying a **callback URL** where the user will be redirected after logging in. Ensure this callback URL also receives the **login code** (either via URL parameters or included in the request payload). Each provider handles this differently, so consult their documentation accordingly.

### 5. Dispatching the Authentication Event

After authentication—whether via Laravel or an external provider—you will now have a logged-in user along with the **login code** they used. At this point, it's time for the package to take over. Simply dispatch the package’s built-in event, passing in the code and the authenticated user instance.

```php
use Wergh\RemoteApiLogin\Events\RemoteApiLoginSendLoginSuccessfullEvent;

broadcast(new RemoteApiLoginSendLoginSuccessfullEvent($authenticableInstance, $code));
```

Since not all applications use Laravel’s default `User` model for authentication, the package is designed to support authentication via any entity in your application.

Remember the queues must be working!

```sh
php artisan queue:work
```

### 6. Notifying the Device via WebSocket

The package will now associate the **code** with the authenticated user and notify the corresponding device via WebSocket that the authentication process is complete. The device will receive an event through its subscribed channel, indicating it can now retrieve the authentication token.

### 7. Retrieving the Authentication Token

Once the device receives the event, it must send a second API request to another endpoint provided by the package. In this request, the device must include both the **temporary_token** and **temporary_user_id** received in the initial API response.

For security reasons, the authentication token is never sent over WebSocket to prevent data leaks. Instead, the verification endpoint ensures that the request originates from the correct user before issuing the final authentication token.

### 8. Token Generation

The package generates and returns the authentication token according to your application’s preferred method. If you’re using Laravel’s built-in API token systems like [Laravel Passport](https://laravel.com/docs/10.x/passport) or [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum), the package handles token creation automatically.

For custom authentication systems, you can specify in the configuration which class and method the package should call to generate the token. The package will return whatever is provided by your custom method.


## Configuration

Below is the list of configuration options available and their descriptions, along with their default values:

| Option | Description | Default |
|---------|-------------|---------|
| `table_name` | **The name of the database table** where login requests will be stored. | `ral_request_data` |
| `code_length` | **The number of characters** in the generated authentication code. | `8` |
| `expiration_time_in_seconds` | **Expiration time** for the login request. After this time, the code will expire and cannot be used. | `300` (5 minutes) |
| `token_length` | **Length of the temporary token** generated for the session. | `32` |
| `request_url` | **The URL** that the device should call to initiate the login request. | `/api/login-request` |
| `token_url` | **The URL** that the device should call to retrieve the token once the user has logged in. | `/api/get-token` |
| `channel_socket_name` | **The WebSocket channel name** used for communication between the backend and the device. | `remote-login` |
| `broadcast_event` | **The event name** that the package will emit once the user has successfully logged in. | `LoginSuccessfully` |
| `auth_driver` | **The authentication driver** to be used. The package supports Laravel's standard API token systems: Passport and Sanctum. It can also work with other systems. Available options are: `'sanctum'`, `'passport'`, or `'custom'`. | `passport` |
| `custom.class` | If using the `auth_driver` as `custom`, this is the **class** where the token generation method is defined. | - |
| `custom.method` | If using the `auth_driver` as `custom`, this is the **method** that the package will call to generate the token. The package will pass the **authenticated user instance** as an argument to this method. | - |
| `returned_params` | This array specifies the **field names** you want to receive in the token response. The value you set here will determine the field name used in the response sent to your device. For Sanctum, only the `access_token` will be returned. For Passport, three fields will be returned (`access_token`, `refresh_token`, `expires_in`). If you don’t want any of these fields, simply comment out the relevant lines in the array. For `custom`, these fields will not be used, as the response will be whatever is returned by your custom method. | - |
| `access_token_expiration_time` | **Expiration time** for the `access_token` in Passport. This field is needed to calculate the `expires_in` value. | - |
| `refresh_token_expiration_time` | **Validity time** for the `refresh_token` in Passport. Defines how long the `refresh_token` will remain valid. | - |

---

### The Event

To ensure the package works as expected, you need to trigger the following event:

**`RemoteApiLoginSendLoginSuccessfullEvent`**

You can trigger the event as follows:

```php
use Wergh\RemoteApiLogin\Events\RemoteApiLoginSendLoginSuccessfullEvent;

broadcast(new RemoteApiLoginSendLoginSuccessfullEvent($authenticableInstance, $code));
```

Where:

- **`$authenticableInstance`**: This is the authenticated user instance. You can directly send `Auth::user()` as the instance.
- **`$code`**: The code that the user entered when logging in.

This event notifies the system that the user has successfully logged in, and the device can proceed with obtaining the authentication token.

Remember, queues must be working

  ```sh
  php artisan queue:work
  ```

---

### Troubleshooting

Here are some common issues and how to solve them:

#### 1. **WebSocket Connection Issues**

If your WebSocket connection isn't working properly, make sure that:

- Your WebSocket server is up and running.
- You've correctly configured your broadcasting service (e.g., Pusher or Laravel Echo Server).
- The correct WebSocket channel name is used in both the backend and frontend configuration.
- Check if your environment variables (`BROADCAST_DRIVER`, `PUSHER_APP_ID`, `PUSHER_APP_KEY`, `PUSHER_APP_SECRET`, etc.) are properly set.

#### 2. **Code Expiration Problems**

If you are getting an "expired code" error, ensure that:

- The `expiration_time_in_seconds` value is set correctly in your configuration file.
- You are not exceeding the expiration time limit from when the code was generated.

#### 3. **Token Not Returning**

If the token isn’t returned after a successful login, verify that:

- You have properly configured your authentication driver (`sanctum`, `passport`, or `custom`).
- If you're using Passport or Sanctum, the user is correctly authenticated and your `access_token_expiration_time` is set up.

#### 4. **WebSocket Event Not Triggered**

If your device is not receiving the WebSocket event, check the following:

- Ensure the queues are working the php artisan command
  ```sh
  php artisan queue:work
  ```
- Ensure the `RemoteApiLoginSendLoginSuccessfullEvent` event is correctly dispatched on the backend.
- Verify that the WebSocket channel name and event name match those configured in the backend and frontend.
- Confirm that the broadcasting service (e.g., Pusher, Reverb) is working and your keys are correctly set up.

If you're still having trouble, try checking the Laravel logs for more detailed error information.

---

### Functional Examples

A **functional example** is provided to see the package working in real-time. This example includes both a **frontend webpage** (acting as the device that wants to authenticate) and a **fresh Laravel installation** with the package installed and a login view to act as the backend.

In this example, Pusher is used for the WebSocket connection. To use Pusher, you need to sign up on the [Pusher website](https://pusher.com/) and create an application to obtain your API keys. Pusher offers a generous free tier, so there won’t be any cost involved for most use cases.

However, if you prefer to use **Reverb** or your own custom WebSocket system, the package is fully compatible with those as well.

[Laravel fresh installation](https://github.com/wergh/laravel-remote-api-login-example)

[Frontend acting as external device](https://github.com/wergh/frontend-remote-api-login)

---

### License

This package is licensed under the **MIT License**, which means you can freely use it for both personal and commercial purposes. However, **use it at your own risk**. The author does not take responsibility for any security issues or failures that may occur as a result of using this package.

### Feedback and Contributions

Any feedback, criticism, or suggestions for improvement are highly appreciated. The author plans to continue updating and improving this package, and your contributions will help make it better.

---

Best regards,  
**Carlos López**  
Laravel Developer since Laravel 4, PHP programmer since 2006  
Email: [carlos.cousillas@gmail.com](mailto:carlos.cousillas@gmail.com)


