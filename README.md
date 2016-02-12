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
 * **applications**: Acá se definen las posibles aplicaciones que se conectan al servidor soap (Si usamos un repositorio de aplicaciones propio, no hace falta definir ninguna aplicación acá). La idea es darle un nombre a la aplicación y dentro definir
    * **username**: Nombre de usuario que el cliente envia en las credenciales (Este valor es el usado por el Repositorio de aplicaciones para obtener y devolver los datos de conexión de la aplicación).
    * **password**: Contraseña interna establecida entre cliente y servidor para crear el `PasswordDigest`.
    * **parameters** (opcional): Un arreglo de datos para manejar información adicional que se requiera.

### Uso

Este bundle requiere de que el servidor soap se haya creado utilizando el bundle `BeSimpleSoapBundle`, y basicamente para activar la seguridad en una acción de un controlador soap se debe incluir la anotación `@Ku\Bundle\WsseServerBundle\Annotation\CheckWsseSecurity()` de la siguiente forma:

```php
<?php

namespace AppBundle\Controller;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use Ku\Bundle\WsseServerBundle\Annotation\CheckWsseSecurity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WebServiceController extends Controller
{
    /**
     *
     * @Soap\Method("AlgunaAccionSoap")
     * @Soap\Param("parametro1", phpType = "string")
     
     * @Soap\Result(phpType = "string")
     *
     * @CheckWsseSecurity() Acá está la mágia
     */
    public function accion1Action($parametro1)
    {
        // ... Método protegido por wsse
    }
    
        /**
     *
     * @Soap\Method("otraAccionSoap")
     
     * @Soap\Result(phpType = "string")
     *
     * @CheckWsseSecurity()
     */
    public function accion2Action($_application)
    {
        // ... Método protegido por wsse
        // Si agregamos un argumento de nombre $_application tendremos disponible el objeto
        // Applicacion devuelto por el ApplicationRepository, y podremos
        // Obtener información relevante para la aplicación.
    }

    /**
     * @Soap\Method("otraAccion2Soap")
     *
     * @Soap\Result(phpType = "int")
     */
    public function accion3Action()
    {
        // ... Método que no está protegido con wsse
    }
}
```

El código anterior muestra como podemos asegurar varios métodos de un servidor soap de manera simple con el uso de la anotación `CheckWsseSecurity`. 

**Importante**: Si queremos asegurar un todos los métodos de un controlador soap, basta con añadir la anotación en la clase:

```php
<?php

namespace AppBundle\Controller;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use Ku\Bundle\WsseServerBundle\Annotation\CheckWsseSecurity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @CheckWsseSecurity() Con esto todos los métodos del controlador estarán protegidos.
 */
class WebServiceController extends Controller
{
   // ...
}
```

Por último es importante resaltar que el hecho de añadir la anotación a un método o a la clase de un controlador Soap, provoca que se cree una acción llamada `generateNonce` de forma automatica en el wsdl, para que el cliente puede obtener el valor del  nonce que se usa para la creación del `PasswordDigest`.

#### TODO:
 * Añadir información de como se crea el `PasswordDigest` en el cliente.
 * Crear libreria para facilitar la creación de los headers Soap para el cliente en conjunto con el `PasswordDigest`.
