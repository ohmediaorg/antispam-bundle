Overview
========

Offers a few methods for anti-spam in forms.

Installation
------------

Enable the bundle in `config/bundles.php`:

```php
return [
    // ...
    JstnThms\AntispamBundle\JstnThmsAntispamBundle::class => ['all' => true],
];
```


Honeypot
--------

Honeypot is enabled by default. You don't need to do anything!

If you want to disable it on a particular form,
set the `honeypot_protection` option to false.

reCAPTCHA
---------

Include `<script src="https://www.google.com/recaptcha/api.js" async defer></script>`
on the appropriate pages.

Place the test keys in `config/dev/jstnthms_antispam.yaml`
and the live keys in `config/prod/jstnthms_antispam.yaml`:

```yaml
jstnthms_antispam:
    recaptcha:
        sitekey: 'my_publishable_key'
        secretkey: 'my_secret_key'
```

Add the field to a form:

```php
use JstnThms\AntispamBundle\Form\Type\RecaptchaType;

//...
$builder->add('recaptcha', RecaptchaType::class);
```

The validation will happen automatically.
