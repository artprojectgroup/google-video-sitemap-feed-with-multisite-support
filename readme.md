# APG Google Video Sitemap Feed

Contributors: artprojectgroup

Donate link: https://artprojectgroup.es/tienda/donacion

Tags: video sitemap, sitemap, youtube, vimeo, dailymotion

Requires at least: 6.0

Tested up to: 7.1

Requires PHP: 7.4

Stable tag: 3.0.0

License: GPLv3

License URI: https://www.gnu.org/licenses/gpl-3.0.html

Genera dinámicamente el archivo sitemap-video.xml, un mapa de sitio de vídeos para Google. No requiere ningún tipo de configuración.

## Descripción

Esta es la documentación en español. La [ficha del plugin en WordPress.org](https://wordpress.org/plugins/google-video-sitemap-feed-with-multisite-support/) está en inglés, que es el idioma que exige el directorio desde julio de 2025.

**APG Google Video Sitemap Feed** genera dinámicamente un mapa de sitio de vídeos para Google creando un archivo `sitemap-video.xml` virtual. 

### Características

- Prácticamente no requiere ningún tipo de configuración, por lo que funciona de forma totalmente autónoma y automática.
- Añade automáticamente todos los vídeos de YouTube, Vimeo y Dailymotion.
- Soporta todos los tipos de entradas personalizadas.
- Gestión automática de caché de datos externos de los vídeos para acelerar la creación del archivo `sitemap-video.xml`.
- Notificación por correo electrónico al administrador del sitio web en caso de que el vídeo haya sido borrado o marcado como privado para que edite la entrada donde aparece y borre la URL que ya no es válida.
- Es totalmente compatible con instalaciones de WordPress multisitio.
- Genera automáticamente múltiples sitemaps con un máximo de 50.000 páginas por cada uno de ellos.
- Actualiza el sitemap en cuanto se publica, se edita o se borra una entrada.
- Detecta también los vídeos alojados en el propio sitio web: bloque de vídeo, shortcode `[video]` y biblioteca de medios.
- Encuentra los vídeos aunque estén en campos personalizados de temas o de constructores visuales.
- Publica la duración, la fecha de publicación y el autor de cada vídeo.
- Agrupa todos los vídeos de una misma página en una sola entrada del sitemap, como espera la especificación de Google.
- Se anuncia solo en `robots.txt` y en el índice de sitemaps de WordPress.
- Avisa si Yoast, Rank Math, All in One SEO o SEOPress ya publican un sitemap de vídeos, para que no tengas dos.

### Traducciones

- Español ([**Art Project Group**](https://artprojectgroup.es/)).
- English ([**Art Project Group**](https://artprojectgroup.es/)).

### Soporte técnico

**Art Project Group** te ofrece [**Soporte técnico**](https://artprojectgroup.es/tienda/ticket-de-soporte) para configurar o instalar **APG Google Video Sitemap Feed**. 

### Origen

**APG Google Video Sitemap Feed** ha sido programado a partir de los plugins [*Google News Sitemap Feed With Multisite Support*](https://wordpress.org/plugins/google-news-sitemap-feed-with-multisite-support/) de [Tim Brandon](https://profiles.wordpress.org/timbrd/) y [*Google XML Sitemap for Videos*](https://wordpress.org/plugins/xml-sitemaps-for-videos/) de [Amit Agarwal](https://profiles.wordpress.org/labnol/), que aún siendo magníficos plugins no ofrecían todas las características que necesitábamos. Aún así su trabajo ha sido completamente imprescindible para la realización de este plugin.

También se han realizado mejoras a partir de la versión 1.0 gracias al código aportado por [Ludo Bonnet](https://twitter.com/ludobonnet) y su idea de mejorar **APG Google Video Sitemap Feed** añadiéndole soporte para Vimeo y Dailymotion. 

### Complementos

Se recomienda el uso de **APG Google Video Sitemap Feed** junto a [**APG Google Image Sitemap Feed**](https://wordpress.org/plugins/google-image-sitemap-feed-with-multisite-support/) que genera el archivo `sitemap-image.xml`, y [**Google Mobile Sitemap Feed With Multisite Support**](https://wordpress.org/plugins/google-mobile-sitemap-feed-with-multisite-support/) que genera el archivo `sitemap-mobile.xml`.

### Muy importante

Se han descrito errores al utilizarlo conjuntamente con la última versión de **Google XML Sitemaps** con soporte para WordPress multisitio. Los errores están descritos en [¿Cómo arreglar la incompatibilidad de Google XML Sitemaps con nuestros plugins?](https://artprojectgroup.es/como-arreglar-la-incompatibilidad-de-google-xml-sitemaps-con-nuestros-plugins) donde encontrarás toda la información necesaria para solucionar la incompatibilidad detectada.

### Más información

En nuestro sitio web oficial puede obtener más información sobre [**APG Google Video Sitemap Feed**](https://artprojectgroup.es/plugins-para-wordpress/apg-google-video-sitemap-feed). 

### Comentarios

No olvides dejarnos tu comentario en:

- [APG Google Video Sitemap Feed](https://artprojectgroup.es/plugins-para-wordpress/apg-google-video-sitemap-feed) en Art Project Group.
- [Art Project Group](https://www.facebook.com/artprojectgroup) en Facebook.
- [@artprojectgroup](https://twitter.com/artprojectgroup) en Twitter.

### Más plugins

Recuerda que puedes encontrar más [plugins para WordPress](https://artprojectgroup.es/plugins-para-wordpress) en [Art Project Group](https://artprojectgroup.es) y en nuestro perfil en [WordPress](https://profiles.wordpress.org/artprojectgroup/).

### GitHub

Puedes seguir el desarrollo de este plugin en [Github](https://github.com/artprojectgroup/google-video-sitemap-feed-with-multisite-support).

## Instalación

1. Puedes:
 - Subir la carpeta `google-video-sitemap-feed-with-multisite-support` al directorio `/wp-content/plugins/` vía FTP. 
 - Subir el archivo ZIP completo vía *Plugins -> Añadir nuevo -> Subir* en el Panel de Administración de tu instalación de WordPress.
 - Buscar **APG Google Video Sitemap Feed** en el buscador disponible en *Plugins -> Añadir nuevo* y pulsar el botón *Instalar ahora*.
2. Activar el plugin a través del menú *Plugins* en el Panel de Administración de WordPress.
3. Listo, ahora ya puedes disfrutar de él, y si te gusta y te resulta útil, hacer una [*donación*](https://artprojectgroup.es/tienda/donacion).

## Preguntas frecuentes

### ¿Necesita configuración?

No, el plugin es totalmente autónomo.

### ¿Es compatible con instalaciones de WordPress multisitio?

Si, es completamente compatible.

### ¿Dónde está mi sitemap de vídeos?

En `sitemap-video.xml`, en la raíz de tu sitio web. Si tienes más de 50.000 páginas con vídeos, esa dirección pasa a ser un índice que apunta a `sitemap-video-1.xml`, `sitemap-video-2.xml` y sucesivos.

### ¿Qué vídeos encuentra?

Los de YouTube, Vimeo y Dailymotion incrustados en el contenido, en el extracto o en un campo personalizado, y los alojados en tu propio sitio web mediante el bloque de vídeo, el shortcode `[video]` o la biblioteca de medios.

### Mis vídeos están en el sitemap pero Google no los indexa, ¿por qué?

Desde 2023 Google solo indexa un vídeo cuando es el contenido principal de la página. Un artículo largo con un vídeo incrustado a la mitad aparecerá en Search Console como «El vídeo no es el contenido principal de la página». Es una política de Google, no un problema del sitemap.

### El plugin me avisa de que otro plugin ya publica un sitemap de vídeos, ¿qué hago?

Lo que prefieras. Tener dos sitemaps de vídeos no está penalizado por los buscadores, pero mantener uno solo es más cómodo. El aviso te deja desactivar este plugin en un clic, u ocultar el mensaje y quedarte con los dos.

### ¿Existen incompatibilidades?

Si, se han descrito errores al utilizarlo conjuntamente con el plugin **Google XML Sitemaps**. Los errores están provocados por un orden erróneo de las reglas de redirección de WordPress, ya que **Google XML Sitemaps** interpreta todos los tipos de mapas de sitios posibles. En [¿Cómo arreglar la incompatibilidad de Google XML Sitemaps con nuestros plugins?](https://artprojectgroup.es/como-arreglar-la-incompatibilidad-de-google-xml-sitemaps-con-nuestros-plugins) encontrarás toda la información sobre esta incompatibilidad y la solución a la misma.

### Soporte técnico

Si necesitas ayuda para configurar o instalar **APG Google Video Sitemap Feed**, **Art Project Group** te ofrece su servicio de [**Soporte técnico**](https://artprojectgroup.es/tienda/ticket-de-soporte). 

*En ningún caso **Art Project Group** proporciona ningún tipo de soporte técnico gratuito.*

## Changelog

### 3.0.0

- Los vídeos de una misma página se agrupan ya en una sola entrada del sitemap.
- Añadido soporte para los vídeos alojados en el propio sitio web.
- Añadida la búsqueda de vídeos en los campos personalizados de temas y constructores visuales.
- Añadidas la duración, la fecha de publicación y el autor de cada vídeo, y la fecha de modificación de cada página.
- El sitemap se anuncia ya en robots.txt y en el índice de sitemaps de WordPress.
- Nuevo aviso cuando Yoast, Rank Math, All in One SEO o SEOPress ya publican un sitemap de vídeos, con desactivación en un clic.
- Los datos de YouTube se piden ya a su propio oEmbed, sin intermediarios.
- Corregido un fallo de la consulta que colaba en el sitemap vídeos de entradas no publicadas.
- Corregido el guardado de la configuración, que corrompía los identificadores de los vídeos ya procesados.
- Restaurado el aviso por correo de vídeos borrados o privados, que nunca llegaba a enviarse.
- Corregido el XML para cumplir la especificación actual de Google: espacio de nombres, máximo de 32 etiquetas y descripción de 2048 caracteres.
- Corregida la numeración de los sitemaps parciales, que podía servir el sitemap equivocado.
- Eliminado el aviso automático a Google y a Bing, que han retirado sus servicios de ping.
- Reforzada la seguridad: consultas preparadas, escapado de todas las salidas y comprobación de permisos.
- Mejora del rendimiento: menos regeneraciones de enlaces permanentes y menos consultas por página.
- Actualización de las traducciones.
- La caché y los enlaces permanentes se regeneran también al actualizar el plugin por FTP.
- Resueltos todos los errores y avisos de Plugin Check.
- Actualización de la compatibilidad hasta WordPress 7.1.

### 2.1

- Actualización de Action Scheduler.
- Mejora del rendimiento.
- Actualización de cabecera.
- Actualización de hoja de estilo.
- Actualización de captura de pantalla.

### 2.0.1.3

- Pequeños arreglos.

### 2.0.1.2

- Pequeños arreglos.

### 2.0.1.1

- Pequeños arreglos.

### 2.0.1

- Pequeños arreglos.

### 2.0

- Adecuación de la nueva estructura de datos.
- Generación de múltiples sitemaps por cada 50.000 vídeos.
- Corrección de errores.
- Soporte para sitios web con gran cantidad de vídeos.

### 1.8.1.1

- Actualización de cabecera.
- Actualización de hoja de estilo.
- Actualización de captura de pantalla.

### 1.8.1

- Eliminado el procesamiento adicional de descripciones cortas y extractos.

### 1.8

- Añadida búsqueda de vídeos en descripciones cortas y extractos.

### 1.7.3.3

- Arreglo de consulta SQL.

### 1.7.3.2

- Arreglo de consulta SQL.

### 1.7.3.1

- Arreglo de error que no actualizaba la configuración.

### 1.7.3

- Soporta todos los tipos de entradas personalizadas.

### 1.7.2.2

- Actualización de enlaces de soporte y pequeñas actualizaciones.

### 1.7.2.1

- Actualización del paquete de fuentes. Nuevo icono de Google+.
- Actualización de las traducciones.

### 1.7.2

- Arreglo de la URL del reproductor de YouTube.

### 1.7.1

- Ajuste del diseño sensible de la plantilla XSL.

### 1.7

- Arreglo de error detectado en la notificación y procesamiento de vídeos borrados o marcados como privados.
- Rediseño sensible de la plantilla XSL.
- Eliminación de funciones innecesarias.
- Actualización de la captura de pantalla.

### 1.6

- Actualización de la API de YouTube.
- Arreglo de las expresiones regulares. Ambos problemas reportados en [WordPress.org](https://wordpress.org/support/topic/youtube-dailymotion-videos-not-working?replies=3#post-7475198).
- Actualización de URLs de reproductores externos.
- Actualización de las traducciones.
- Mejora del archivo XSL que genera la plantilla del archivo XML.

### 1.5

- Actualización de las traducciones.
- Nueva hoja de estilo sensible.
- Modificación de la estructura interna del plugin para ajustarse a los estándares de WordPress.
- Actualización de la captura de pantalla.

### 1.4.1

- Añadido borrado de caché al publicar nuevo contenido.

### 1.4

- Arreglo de error que provocaba un mensaje de error en versiones superiores a la 5.2 de PHP.

### 1.3.1

- Arreglo de error que borraba toda la configuración al desactivar el plugin.
- Corrección menor que evita la aparición de un código de error al recopilar información sobre el plugin.

### 1.3

- Añadido un nuevo panel de administración donde poder elegir si queremos recibir correos electrónicos o no.
- Mejora en el código que envía el correo electrónico.
- Cambio del enlace de donación.

### 1.2

- Mejora y optimización del código.
- Añadida caché para los datos externos.
- Añadida función que limpia la caché cuando se borra el plugin.
- Cambio del botón y enlace de donación.

### 1.1.7

- Arreglos de pequeños errores.

### 1.1.6

- Mejora y optimización del código.
- Arreglos de pequeños errores.
- Uso de la API Transients de WordPress para mejorar las consultas.
- Mejora en la búsqueda de vídeos de Vimeo.

### 1.1.5

- Arreglo de error en nombre de variable que deja datos en blanco en el correo electrónico

### 1.1.4

- Arreglos de diversos errores en los envíos de correos electrónicos.
- Arreglos de diversos errores en el almacenamiento de datos en la caché.

### 1.1.3

- Simplificación de código duplicado.

### 1.1.2

- Arreglo de error que no reiniciaba la variable encargada de controlar los envíos de correos electrónicos.

### 1.1.1

- Arreglo del código que envía los correos electrónicos.

### 1.1

- Gestión de caché de datos externos de los vídeos.
- Envía notificaciones de error por correo electrónico en caso de que el video no exista.
- Optimización del código.
- Arreglos de pequeños errores detectados.

### 1.0

- Añadido soporte para el acortador https://youtu.be.
- Añadido soporte para Vimeo.
- Añadido soporte para Dailymotion.

### 0.9

- Añadida nueva función que limpia la base de datos al desinstalar el plugin.

### 0.8

- Arreglo en la codificación de las entidades RSS.

### 0.7

- Arreglos menores en el código.

### 0.6

- Mejora del código para mejorar la validación del archivo sitemap-video.xml

### 0.5

- Actualización de las hojas de estilo acorde al nuevo WordPress 8.
- Arreglo de pequeños errores en el código.

### 0.4

- Inclusión de nuevos botones y enlaces.

### 0.3

- Pequeños arreglos de código.
- Pequeño arreglo de la traducción.

### 0.2

- Pequeñas modificaciones y arreglos de código.
- Inclusión de enlaces.
- Actualización de los textos de información.

### 0.1

- Versión inicial.

## Traducciones

- *English*: by [**Art Project Group**](https://artprojectgroup.es/) (default language).
- *Español*: por [**Art Project Group**](https://artprojectgroup.es/).


## Soporte técnico

Dado que **APG Google Video Sitemap Feed** es totalmente gratuito, **Art Project Group** sólo proporciona el servicio de [**Soporte técnico**](https://artprojectgroup.es/tienda/ticket-de-soporte) previo pago. En ningún caso **Art Project Group** proporciona ningún tipo de soporte técnico gratuito.

## ¿Por qué esta documentación está en español?

Desde julio de 2025 WordPress.org exige que el `readme.txt` del directorio esté escrito en inglés, así que la ficha pública del plugin lo está.

Seguimos creyendo que la comunidad hispana de WordPress merece documentación en su idioma, así que mantenemos esta versión en español dentro del propio plugin y en nuestro repositorio, además de la interfaz, los tutoriales y el soporte.

Esperamos que os siga gustando nuestra iniciativa.

## Donación

¿Te ha gustado y te ha resultado útil **APG Google Video Sitemap Feed** en tu sitio web? Te agradeceríamos una [pequeña donación](https://artprojectgroup.es/tienda/donacion) que nos ayudará a seguir mejorando este plugin y a crear más plugins totalmente gratuitos para toda la comunidad WordPress.

## Gracias

- A [Tim Brandon](https://profiles.wordpress.org/timbrd/) y [Amit Agarwal](https://profiles.wordpress.org/labnol/) por sus grandes plugins que han inspirado **APG Google Video Sitemap Feed**.
- A [Ludo Bonnet](https://twitter.com/ludobonnet) por sus aportaciones al código y por su idea de añadir soporte para Vimeo y Dailymotion.
- A todos los que lo usáis.
- A todos los que ayudáis a mejorarlo.
- A todos los que realizáis donaciones.
- A todos los que nos animáis con vuestros comentarios.

¡Muchas gracias a todos!