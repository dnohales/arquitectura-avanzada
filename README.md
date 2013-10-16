Proyecto de Arquitectura Avanzada
=================================

Este es el proyecto de Arquitectura avanzada de la Universidad CAECE sede
Mar del Plata.

Integrantes

  * Damián Nohales
  * Emiliano Lippolis
  * Mikel Arbide
  * Joaquín Rodriguez

Este proyecto utiliza [Symfony 2][1] como framework de desarrollo.

Instrucciones de instalación y uso
----------------------------------

### Descargar las dependencias

Deben descargarse las dependencias con [Composer][2], para esto, primero debe
descargar Composer.

**IMPORTANTE:** Ejecute estos comandos dentro del directorio del proyecto.

    curl -s http://getcomposer.org/installer | php

Luego, instalar las dependencias

    php composer.phar install

### Configurar ambiente

Cuando se ejecutó el comando `php composer.phar install` para instalar las
dependencias, un prompt debió haberse ejecutado pidiendo la configuración del
ambiente, tales como la configuración de la base de datos o el mailer. De no
haber aparecido este prompt, debe copiarse el archivo
`app/config/parameters.yml.dist` con el nombre `app/config/parameters.yml` y
cambiar los parámetros como corresponda.

### Iniciar monitor de PIC

Ahora debe ejecutarse un comando de consola que leerá los datos del
microcontrolador y guardará las lecturas en la base de datos. Este comando se
ejecuta de la siguiente manera.

    php app/console app:pic:monitor

Puede ejecutar este comando para consultar opciones adicionales de este comando,
tales como el monitor dummy que no requiere que el microcontrolador esté
presente, entre otras cosas.

    php app/console help app:pic:monitor

### Iniciar servidor WebSocket

Luego debe iniciar el servidor WebSocket que es el que utilizará el navegador
para obtener actualizaciones en tiempo real.

    php app/console app:websocket:server

### Ejecutar la aplicación

Para ejecutar la aplicación en el navegador, debe ejecutar en el navegador el
archivo `web/app_dev.php` para el modo desarrollador o `web/app.php` para el
modo producción.

Configuración adicional para el desarrollador
---------------------------------------------



[1]:  http://symfony.com/
[2]:  http://getcomposer.org/
