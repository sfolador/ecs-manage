<img src="https://banners.beyondco.de/ECS%20Manage.png?theme=light&packageManager=composer+require&packageName=sfolador%2Fecs-manage&pattern=architect&style=style_1&description=Manage+your+ECS+tasks+with+a+simple+Artisan+command&md=1&showWatermark=1&fontSize=100px&images=terminal&widths=700">

# Easily manage your ECS Cluster with this simple artisan command

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sfolador/ecs-manage.svg?style=flat-square)](https://packagist.org/packages/sfolador/ecs-manage)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sfolador/ecs-manage/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/sfolador/ecs-manage/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/sfolador/ecs-manage/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/sfolador/ecs-manage/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sfolador/ecs-manage.svg?style=flat-square)](https://packagist.org/packages/sfolador/ecs-manage)

This package allows you to easily manage your ECS Cluster with a simple artisan command.

## Requirements

You can use this package with Laravel 10.x, and you should have the AWS CLI installed and configured.


    Please check if your AWS CLI is configured with the correct region and credentials.
    Refer to this link: https://docs.aws.amazon.com/cli/v1/userguide/install-macos.html if you need more information on that specific subject.

## Installation

You can install the package via composer:

```bash
composer require sfolador/ecs-manage
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="ecs-manage-config"
```

This is the contents of the published config file:

```php
return [
     
     'environments' => [
        'staging',
        'production',
    ],
    
     'default_terminal' => 'iTerm'
];
```

## Usage

```bash
php artisan ecs:manage
```

you will see an output that looks like this:

```shell
Select a cluster [mycluster-ecs]:
[0] mycluster-ecs
```
You should select the cluster you want to use. 
After selecting a cluster you should select and "environment":

```shell
 Select an environment:
  [0] staging
  [1] production
```
These options can be customized in the config file and will act as a filter on services.
Once you select an "environment", you will see a list of services that match the selected "environment".

So, for example, if you selected "staging", the list of services will be:

This works very well if you have a naming convention for your services.

```shell
Select a service [sfolador-zero-staging-FARGATE]:
  [0 ] sfolador-zero-staging-FARGATE
  [1 ] sfolador-one-staging-FARGATE
  [2 ] sfolador-two-staging-FARGATE
  [3 ] sfolador-three-staging-EC2
```
Select a service and you will see a list of tasks that match the selected service:

```shell
 Select a task [xxxxxxxxxxxxxx]:
  [0] xxxxxxxxxxxxxx
```

Once you select a task, a terminal window will open and will try to connect to the selected task.
The command that will launched in the terminal window is:

```shell 
aws ecs execute-command --region SELECTED_AWS_REGION  --cluster SELECTED_CLUSTER --task SELECTED_TASK --command \"/bin/sh\" --interactive
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Simone Folador](https://github.com/sfolador)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
