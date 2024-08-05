# Url Shortening Service

## Initialise docker

From inside the project folder:
```
./vendor/bin/sail up
```

## Run migrations

```
php artisan migrate
```

## Run PHPUnit tests

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


