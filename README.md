# PHP-Mail

> Small self-hosted PHP contact form endpoint as a drop-in for static websites. It supports DNSBL and rate limitiing.
> 

## Install

1. Clone this repository
```
git clone https://github.com/vwochnik/php-mail.git
```

2. CD into the directory
```
cd php-mail
```

3. Run composer
```
composer install
```

4. Copy `.env.example` to `.env` and insert SMTP configuration there.

5. Upload to your webserver such that public is inside document root.

## Usage
```
POST /mail/
Content-Type: application/json

{
  "name": "Example",
  "email": "example@example.com",
  "subject": "Test",
  "message": "Hello World!"
}
```

## License
MIT
