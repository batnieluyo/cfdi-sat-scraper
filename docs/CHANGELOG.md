# phpcfdi/cfdi-sat-scraper CHANGELOG

## Acerca de los números de versiones

Respetamos el estándar [Versionado Semántico 2.0.0](https://semver.org/lang/es/).

En resumen, [SemVer](https://semver.org/) es un sistema de versiones de tres componentes `X.Y.Z`
que nombraremos así: ` Breaking . Feature . Fix `, donde:

- `Breaking`: Rompe la compatibilidad de código con versiones anteriores.
- `Feature`: Agrega una nueva característica que es compatible con lo anterior.
- `Fix`: Incluye algún cambio (generalmente correcciones) que no agregan nueva funcionalidad.

**Importante:** Las reglas de SEMVER no aplican si estás usando una rama (por ejemplo `master-dev`)
o estás usando una versión cero (por ejemplo `0.18.4`).

## UNRELEASED 2020-10-14

Este cambio no afecta la versión liberada y no requiere de un nuevo release.

- La construcción en Travis-CI se rompió porque PHPStan version 0.12.55 ya entiende las estructuras de control
  de PHPUnit, por lo que sabe que el código subsecuente es código muerto. Se corrigieron las pruebas con problemas.
- Se actualizó la herramienta `develop/install-development-tools`

## Version 1.0.1

- Se actualizan dependencias:
    - `symfony/dom-crawler` de `^4.2|^5.0` a `5.1`.
    - `symfony/css-selector` de `^4.2|^5.0` a `5.1`.
    - `guzzlehttp/guzzle` de `^6.3` a `7.0`.
- Se corrigen las descripciones de las clases `DownloadType`, `ComplementsOption`, `RfcOption`, `StatesVoucherOption`
  y `UuidOption`.
- Se agregó una sección en el README *Verificar datos de autenticación sin hacer una consulta* (issue #35).
- Se cambia en desarrollo la inicialización de `Dotenv` porque se deprecó la forma anterior en `symfony/dotenv: ^5.1`.
- Se cambia en desarrollo la dependencia de `symfony/dotenv` de `^4.2|^5.0` a `^5.1`.

## Version 1.0.0

- Se establece la versión mínima de PHP a 7.3.
- Se revisan las expresiones regulares y `json_encode`/`json_decode` con el paso a 7.3.
- Se cambia la versión de PHPUnit a 9.1.
- Se corrige `linguist-detectable=false` para los archivos en `tests/_files` que estaba mal puesto.

## UNRELEASED 2020-04-12

- El filtro por complemento `ComplementsOption` ya no es un `Enum`, ahora es un `MicroCatalog`.
  De esta forma se puede tener mucha más información relacionada con el complemento y por ejemplo
  poder ofrecer una lista de opciones de catálogos.
- La modificación de `ComplementsOption` es compatible con la forma de crear los objetos y de comprobar si es
  de un tipo en especial (por ejemplo: `ComplementsOption::todos()` y `ComplementsOption::isTodos()`).

## UNRELEASED 2020-02-23

En este release se cambió totalmente la librería, tanto en el exterior como en el funcionamiento interno.

Los cambios más importantes para los usuarios de la librería son:

- La consulta por filtros ya no usa `Query`, que dejó de existir, ahora se llama `QueryByFilters`.
- Los métodos para obtener el *metadata* han cambiado a `listByUuids`, `listByPeriod` y `listByDateTime`.
- El método para crear el descargador de XML ahora se llama `xmlDownloader` y recibe optionalmente todos los parámetros.
- Si se quiere personalizar el descargador se debe hacer implementando la interfaz `XmlDownloadHandlerInterface`.
- Los datos para poder autenticarse con el SAT se almacenan en un objeto `SatSessionData`.
- Se crea toda una estructura para excepciones:
    - Todas implementan la interfaz `SatException`.
    - Las excepciones usan las SPL de PHP: `RuntimeException`, `InvalidArgumentException` y `LogicException`.
    - Las excepciones lógicas indican que debes tener un error en la forma en que estás usando la aplicación.
    - Las excepciones de tiempo de ejecución es porque algo inesperado ha ocurrido pero no necesariamente es
      por un error en la implementación.
    - Los problemas relacionados con el proceso de autenticación son `LoginException`.
    - Los problemas relacionados con las transacciones HTTP con el SAT son `SatHttpGatewayException`,
      con dos especializaciones: `SatHttpGatewayClientException` y `SatHttpGatewayResponseException`.
    - Los problemas relacionados con la ejecución de la descarga de XML se son `XmlDownloadError`,
      con dos especializaciones: `XmlDownloadRequestExceptionError` y `XmlDownloadResponseError`.

Los cambios importantes al interior de la librería son:

- La estructura para obtener el listado de metadata ahora pasa por: `SatScraper` crea y ejecuta un `MetadataDownloader`
  que crea y ejecuta un `QueryResolver` que crea y ejecuta un `MetadataExtractor`.
- La generación de los datos que se envían por POST para seleccionar el tipo de consulta y ejecutarla se cambian
  a objetos `Input`. Estos objetos son especializaciones que a partir de la consulta generan los inputs adecuados.

## UNRELEASED 2020-02-14

Estos son algunos de los cambios más importantes relacionados con la compatibilidad si está usando una versión previa.

- Se crea el `SatHttpGateway` que encierra las comunicaciones con el SAT, es éste el que utiliza
  el cliente de Guzzle (`GuzzleInterface`).
- Se cambió el constructor del `SatScraper`, ahora el tercer parámetro es el resolvedor de captchas
  y el cuarto parámetro es un `SatHttpGateway` y es opcional (por si la cookie es solo de memoria).
- Se cambia el objeto `DownloadXml`, antes funcionaba con un callable, ahora funciona con una interfaz
  de tipo `DownloadXmlHandlerInterface` para forzar las firmas de los métodos. También devuelve al
  hacer `download` o `saveTo` el listado de UUID descargados.
- Se cambió el inicio de sesión en el SAT después de revisar el funcionamiento actual, ahora es más limpio
  y con menos llamadas.
- Los filtros solamente llenan los input que deberían llenar.
- El `DownloadTypesOption` ya no es un filtro, pero sigue siendo un `Enum`
- Se removieron las constantes `URLS::SAT_HOST_CFDI_AUTH`, `URLS::SAT_HOST_PORTAL_CFDI` y `URLS::SAT_URL_PORTAL_CFDI_CONSULTA`
- Se movieron las clases internas al espacio de nombres interno.

## UNRELEASED 2020-02-10

- Se cambia la interfaz `PhpCfdi\CfdiSatScraper\Contracts\CaptchaResolverInterface`, la nueva forma de uso
  sería: `$answer = $resolver->decode($image);`
    - Cambia `function decode(): ?string` a `function decode(string $base64Image): string`.
    - Se elimina `function setImage(string $base64Image): self`

## UNRELEASED 2020-02-09

- Se corrigió un bug que no permitía descargar por UUID
- Se cambió el objeto `\PhpCfdi\CfdiSatScraper\Filters\Options\RfcReceptorOption` a
  `PhpCfdi\CfdiSatScraper\Filters\Options\RfcOption`.
- Se agregaron test de integración con información basada en un *único punto de verdad*.
  Consulta la guía en `develop/docs/TestIntegracion.md`.
- Se agregó integración contínua con Travis-CI & Scrutinizer.
- Se establece análisis de código a `phpstan level 5`.

## UNRELEASED 2020-01-28

- Se corrigió el problema de descargas del SAT, la librería estaba en un estado no usable.
- Se inició con el proceso de llevar la librería a una versión mayor `1.0.0`.
