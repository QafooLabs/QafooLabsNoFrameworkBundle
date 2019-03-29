# 3.0-alpha2

- Remove JMS Serializer View Engine
- Bump requirements to Symfony 4.0 and PHP 7.2
- Add support for callables as controller

# 3.0-alpha1

- Add Generator/yield support for Response metadata

# 2.6

- Symfony 4 compatibility

# 2.5

- Symfony 3 compatibility

# 2.4

- Update Symfony dependencies to require at least 2.6
- Fix deprecation warning when using SecurityContext (thanks @KingCrunch)

# 2.0

- BC Break: Renamed FrameworkContext to TokenContext
  https://github.com/QafooLabs/QafooLabsNoFrameworkBundle/commit/f4ab7d9bb046e724840c052fb5fc6bb13ac480b9

- BC Break: Added TokenContext#assertIsGranted method
