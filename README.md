# App Builder

Application builder for the Proximify Framework. It builds the app and its dependant modules.

## Testing

```bash
$ php src/build.php --vendor-dir=dev/tests/vendor --verbose=1

# Output
Trusted dir: /.../app-builder/dev/tests/vendor/proximify
Running 'cd .. && ls -la' on '/.../app-builder/dev/tests/vendor/proximify/package1'...
total 0
drwxr-xr-x  3 dmac  staff  96 Nov  4 14:54 .
drwxr-xr-x  3 dmac  staff  96 Nov  4 14:49 ..
drwxr-xr-x  3 dmac  staff  96 Nov  4 14:54 package1
```

---

## Contributing

This project welcomes contributions and suggestions. Most contributions require you to agree to a Contributor License Agreement (CLA) declaring that you have the right to and actually do, grant us the rights to use your contribution. For details, visit our [Contributor License Agreement](https://github.com/Proximify/community/blob/master/docs/proximify-contribution-license-agreement.pdf).

When you submit a pull request, we will determine whether you need to provide a CLA and decorate the PR appropriately (e.g., label, comment). Simply follow the instructions provided. You will only need to do this once across all repositories using our CLA.

This project has adopted the [Proximify Open Source Code of Conduct](https://github.com/Proximify/community/blob/master/docs/code_of_conduct.md). For more information see the Code of Conduct FAQ or contact support@proximify.com with any additional questions or comments.

## License

Copyright (c) Proximify Inc. All rights reserved.

Licensed under the [MIT](https://opensource.org/licenses/MIT) license.

**Software component** is made by [Proximify](https://proximify.com). We invite the community to participate.
