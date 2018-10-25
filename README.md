# SlimPHP Proxy system

Proxy your requests using [Slim PHP](https://www.slimphp.com).

## Usage

Use the Sloxy host name instead of the real host name, and set the host it should proxy to.

`https://sloxy.example.com/slimphp/Slim/blob/3.x/README.md?__host=https://github.com`

### NOTE

Sloxy does not handle rewriting of URLs in the content.

### Setting the host it should proxy to

You can either set a `X-NEXT-HOST` header or a `__host` query parameter.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover security related issues, please email dlundgren@syberisle.com instead of using the issue tracker.

## Credits

- [David Lundgren](https://github.com/dlundgren)
- [All Contributors](../../contributors)

## License

The Slim Framework is licensed under the MIT license. See [License File](LICENSE.md) for more information.
