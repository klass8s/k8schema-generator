# Security policy

## Reporting a vulnerability

Please do not report a vulnerability in a public issue. Report it privately through GitHub's
private vulnerability reporting: the repository's **Security** tab, **Report a vulnerability**
(<https://github.com/klass8s/k8schema-generator/security/advisories/new>).

Say what is affected (the schema download, the generated code, a command), the commit of the
generator you ran, and the input or the steps that show the problem. The fix is coordinated in the
advisory, and the advisory is published with the fix and the k8schema releases regenerated with it.

## Supported versions

The generator is released as its `main` branch. Security fixes go into `main`, and k8schema picks
them up when its generator pin is updated.
