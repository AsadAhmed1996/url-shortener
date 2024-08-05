# Url Shortening Service

Please make sure you have Docker Desktop and Postman installed.

## Clone repository

In terminal, go to your chosen directory and run:

```
git clone https://github.com/AsadAhmed1996/url-shortener.git
```

## Navigate inside project

```
cd url-shortener
```

## Create .env file

```
cp .env.example .env
```

## Install composer

```
composer install
```

## Initialise docker

```
./vendor/bin/sail up
```

## Docker desktop access

Open docker desktop. Go to containers => url-shortener and open laravel.test-1. Open the terminal and run:

```
bash
```

## Run migrations

From within the laravel.test-1 terminal, run the following command:

```
php artisan migrate
```

## Run PHPUnit tests

To run unit tests, use the following command in the laravel.test-1 terminal:

```
./vendor/bin/phpunit
```

## Test in postman

### Encoding

- Use this endpoint:

```
http://localhost/api/encode
```

- Ensure you have 'Accept' header set with value of 'application/json'.
- Select 'POST' method.
- Pass in your url in the 'Body' section, selecting the 'x-www-form-urlencoded' option.
- The key is 'url' and the value is your url that you want to shorten.
- The provided url must be a valid url and at least 34 characters long to allow ample shortening.
- Hit 'Send' and you should see a JSON response containing your shortened url.

### Decoding

- Use this endpoint:

```
http://localhost/api/decode
```

- Ensure you have 'Accept' header set with value of 'application/json'.
- Select 'POST' method.
- Pass in your url in the 'Body' section, selecting the x-www-form-urlencoded option.
- The key is 'url' and the value is your short url that you want to decode.
- The provided url must be a valid url and a url that has been shortened by this service.
- Hit 'Send' and you should see a JSON response containing your original url.


