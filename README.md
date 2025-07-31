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

### Model based definitions
In any model that contains sensitive data use the `Anonymizable` trait and implement the `anonymizableAttributes` method:

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
            'firstname' => $faker->firstName(),
            'surname' => $faker->lastName(),
            'phone' => $faker->e164PhoneNumber(),
            'position' => $faker->jobTitle(),
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

### Factory based definitions
To reduce the amount of necessary code, the anonymizable attributes may also be defined on your factories. 
Do note that the model still needs to implement the `Anonymizable` to work with these definitions.

#### Attributes based on the factory's definition
It is possible to use the factory's definition to use as anonymizable values. 
Implement the `anonymizableAttributes` method on your factory and return an array with keys that corresponds to the definition of the factory:

```php
<?php

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => $this->faker()->numberBetween(1, 100_000) . '@custom.dev',
            'password' => 'secret',
            'firstname' => $this->faker->firstName(),
            'surname' => $this->faker->lastName(),
            'phone' => $this->faker->e164PhoneNumber(),
            'position' => $this->faker->jobTitle(),
            'token' => null,
        ]
    }
    
    public function anonymizableAttributes(): array
    {
        return [
            'email',
            'password',
            'firstname',
            'surname',
            'phone',
            'position',
            'token',
        ]
    }
}
```
This will use reduce the amount of code you need to write if the data you want to anonymize is the same as your factory's definition.

Note that only the defined keys will be anonymized!

#### Attributes based on a custom definition
If you prefer to still use custom values, you can still define them on the factory. 
Implement the `anonymizableDefinition` method on your factory, and return a keyed array:

```php
<?php

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => $this->faker()->numberBetween(1, 100_000) . '@custom.dev',
            'password' => 'secret',
            'firstname' => $faker->firstName(),
            'surname' => $faker->lastName(),
            'phone' => $faker->e164PhoneNumber(),
            'position' => $faker->jobTitle(),
            'token' => null,
        ]
    }
    
    public function anonymizableDefinition(): array
    {
        return [
            'email' => $this->faker()->numberBetween(1, 100_000) . '@custom.dev',
            'password' => 'secret',
            'firstname' => $faker->firstName(),
            'surname' => $faker->lastName(),
            'phone' => $faker->e164PhoneNumber(),
            'position' => $faker->jobTitle(),
            'token' => $this->faker->md5(),
        ]
    }
}
```

Defining custom values is mostly useful when used in tandem with the `anonymizableAttributes` method. 
This allows certain attributes from the factory to be used, while still defining custom attributes when necessary:
```php
<?php

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => $this->faker()->numberBetween(1, 100_000) . '@custom.dev',
            'password' => 'secret',
            'firstname' => $faker->firstName(),
            'surname' => $faker->lastName(),
            'phone' => $faker->e164PhoneNumber(),
            'position' => $faker->jobTitle(),
            'token' => null,
        ]
    }
    
    public function anonymizableAttributes(): array
    {
        return [
            'email',
            'password',
            'firstname',
            'surname',
            'phone',
            'position',
        ]
    }
    
    public function anonymizableDefinition(): array
    {
        return [
            'token' => $this->faker->md5(),
        ]
    }
}
```

### Important note
>The data from the `anonymizable` methods on the factory will overwrite the data defined in the  `anonymizableAttributes` method on the model!

### Running the anonymizer
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
