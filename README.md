# Laravel Data Anonymization

This Laravel package facilitates data anonymization, which helps organizations protect privacy, comply with regulations, reduce the risk of data breaches, and enable safe data sharing:
1. Protecting privacy: Data contains sensitive information about individuals, such as their name, address, email, phone number, and other personally identifiable information (PII). Data anonymization helps protect the privacy of individuals by removing or masking their PII from the dataset.

2. Compliance with regulations: Many countries and industries have regulations that require organizations to protect the privacy of individuals by anonymizing their data. For example, the General Data Protection Regulation (GDPR) in the European Union requires organizations to protect the privacy of individuals by anonymizing their data.

3. Reducing the risk of data breaches: Data breaches can have serious consequences for organizations and individuals, including financial loss, reputational damage, and identity theft. By anonymizing data, organizations can reduce the risk of data breaches and minimize the impact of any data breaches that do occur.

4. Enabling data sharing: Anonymized data can be shared with other organizations or researchers without violating the privacy of individuals. This can help promote collaboration and innovation in fields such as healthcare, finance, and social sciences.

## Installation
You can install the package via composer:

```bash
composer require outsidaz/laravel-data-anonymization
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-data-anonymization-config"
```

This is the contents of the published config file:

```php
return [
    'locale' => 'en_US',
    'chunk_size' => 1000,
    'models_path' => app_path('Models'),
    'models_namespace' => '\\App\\Models',
]
```

## Usage

In any model that contains sensitive data use `Anonymizable` trait and implement `anonymizableAttributes` method:

```php
<?php
class User extends Authenticatable
{
    use Anonymizable;
    
    <...>

    public function anonymizableAttributes(Generator $faker): array
    {
        return [
            'email' => $this->id . '@custom.dev',
            'password' => 'secret',
            'firstname' => $faker->firstName,
            'surname' => $faker->lastName,
            'phone' => $faker->e164PhoneNumber,
            'position' => $faker->jobTitle,
            'token' => null,
        ];
    }
    
    // optional
    public function anonymizableCondition(): Builder
    {
        return self::withTrashed()->where('something', '=>', '...');
    }
}
```
Anonymization is performed using command:

```bash
php artisan db:anonymize
```

Or on specific models:
```bash
php artisan db:anonymize --model=\\App\User --model=\\App\\Profile
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.