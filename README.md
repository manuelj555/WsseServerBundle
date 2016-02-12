# WsseServerBundle
Bundle para la autenticación de servidores soap usando WSSE

La idea del bundle es facilitar la implementación de la seguridad WSSE en un servidor soap. El bundle ofrece una anotación que permite asegurar un controlador o una acción concreta, donde, añadiendo la anotación `@Ku\Bundle\WsseServerBundle\Annotation\CheckWsseSecurity()` hacemos que la acción soap requiera de las siguientes cabeceras:

 * **Username**: Nombre de usuario establecido entre servidor y cliente para la comunicación soap.
 * **PasswordDigest**: Clave generada por el cliente utilizando un nonce previamente obtenido desde el servidor, en conjunto con una fecha de creación del `PasswordDigest` y una clave secreta que comparten tanto servidor como cliente.
 * **Created** la fecha usada para crear el `PasswordDigest`.
 * **Nonce**: el valor de nonce obtenido al llamar a la acción `generateNonce` del servicor soap.

### Instalación
Agregar al composer.json:

```json
"require" : {
    "manuelj555/wsse-server-bundle": "1.0.*@dev",
}
```

Y ejecutar

```
composer update 
```

Luego de ello, registrar el bundle en el AppKernel.php:

```php
public function registerBundles()
{
    $bundles = array(
        ...
        new Ku\Bundle\WsseServerBundle\WsseServerBundle(),
    );

    ...
}
```

### Configuración

El bundle contiene una pequeña configuración para su funcionamiento:

```yaml
# app/config/config.yml

wsse_server:
    # application_repository_service: wsse_server.application.default_application_repository # Valor por defecto
    applications:
        'Nombre':
            username: nombre_de_usuario
            password: clave
            # parameters:
            #    adicional: parameter
```

Basicamente se deben/pueden configurar dos cosas:
    
 * **application_repository_service**: Servicio que representa al repositorio de aplicaciones, por defecto se usa uno del bundle, el cual devuelve la aplicación establecida en la parte de configuración `applications` buscando por el `username`. Este servicio puede ser cambiando creando uno propio que implementé la interfaz `Ku\Bundle\WsseServerBundle\Application\ApplicationRepositoryInterface`, por ejemplo para manejar las aplicaciones en una base de datos, etc.
 * **applications**: Acá se definen las posibles aplicaciones que se conectan al servidor soap (Si usamos un repositorio de aplicaciones propio, no hace falta definir ninguna aplicación acá).
