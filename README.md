![bkash](https://user-images.githubusercontent.com/23190775/167891034-698eafcb-7ff0-443b-b511-8ea8275e7b84.png)

## Requirements

-   App key
-   App secret
-   Username
-   Password

## Build Setup
- Clone your project
- Go to the folder application using cd command on your cmd or terminal
- Run <code>composer install</code> on your cmd or terminal
- Copy .env.example file to .env on the root folder. You can type <code>copy .env.example .env</code> if using command prompt Windows or <code>cp .env.example .env</code> if using terminal, Ubuntu
- Run <code>php artisan key:generate</code>
- Go to the <code>app/Http/Controllers/Api/BkashController</code> and change flowing values
    <br><code>$app_key = "";</code>
    <br><code>$app_secret = ""; </code>
    <br><code>$username = "";</code>
    <br><code>$password = "";</code><br>
- Run <code>npm install</code>
- Run <code>npm run watch</code>
- Run <code>php artisan serve</code>
- Go to http://127.0.0.1:8000/

<a href="https://developer.bka.sh/docs">Bkash Official Documentation</a>
