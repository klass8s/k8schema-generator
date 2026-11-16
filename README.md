# k8schema-generator

A command-line application that generates PHP classes from the JSON schema of the Kubernetes API. It
produces [k8schema](https://github.com/klass8s/k8schema), the Kubernetes API classes that
[Klass8s](https://github.com/klass8s/klass8s) builds manifests from: one class per API object
(`Deployment`, `Service`, …) and per nested type, typed properties with getters and setters,
`API_VERSION` and `KIND` constants, and a `jsonSerialize()` that writes the object the API server
reads.

The schemas come from [yannh/kubernetes-json-schema](https://github.com/yannh/kubernetes-json-schema),
which keeps the OpenAPI definitions of every Kubernetes release.

## Usage

The generator needs PHP 8.5. From a checkout:

```bash
composer install
bin/k8schema-generator schema:fetch v1.35 var/v1.35.json
bin/k8schema-generator generate var/v1.35.json --namespace-prefix='K8Schema' --output-dir=out/src
```

`schema:fetch` downloads the definitions of the newest patch release it knows for a Kubernetes minor
(the map in `K8S_JSON_SCHEMA_VERSIONS` in `.env`, which the environment variable of the same name
overrides), and `generate` writes the classes under the namespace prefix into the output directory.
`schema:versions [n]` lists the newest patch release upstream for each of the latest `n` minors
(four by default).

k8schema itself is produced by its own workflow, which installs this generator at a pinned commit
and regenerates one branch per Kubernetes minor; see its README.

## Contributing

Bug reports and pull requests are welcome: [CONTRIBUTING.md](CONTRIBUTING.md) says how the generator
is organised and what a pull request needs. Report a security vulnerability privately, as
[SECURITY.md](SECURITY.md) describes.

## License

k8schema-generator is released under the [MIT License](LICENSE). The portions that come from the
project it continues remain under their own MIT notice, in [NOTICE](NOTICE).
