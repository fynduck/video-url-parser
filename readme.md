# VideoUrlParser

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/fynduck/video-url-parser.svg?style=flat-square)](https://packagist.org/packages/fynduck/video-url-parser)
[![Total Downloads](https://img.shields.io/packagist/dt/fynduck/video-url-parser.svg?style=flat-square)](https://packagist.org/packages/fynduck/video-url-parser)

## Install
`composer require fynduck/video-url-parser`

## Check is valid video url (youtube, rutube, vimeo)
```
$returnServiceNameIfValid = (new VideoUrlParse())->isValidURL('url') //invalid return false
```

## Get src link (ex. for iframe)
```
$srcLink = (new VideoUrlParse())->returnSrcForEmbed($url, $domain = false)
```
if your know domain this link (youtube, rutube, vimeo), put domain.

if your don't know domain, put url only, script will find domain name (if is correct)

## Testing
Run the tests with:

``` bash
vendor/bin/phpunit
```

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License
The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.
