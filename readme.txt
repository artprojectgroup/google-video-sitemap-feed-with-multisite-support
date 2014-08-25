=== Google Video Sitemap Feed With Multisite Support ===
Contributors: artprojectgroup 
Donate link: http://www.artprojectgroup.es/tienda/donacion
Tags: google, google Video, google video sitemap, video sitemap, sitemap video, sitemap, sitemap-video.xml, youtube, vimeo, dailymotion
Requires at least: 2.6
Tested up to: 3.9.2
Stable tag: 1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Genera dinámicamente el archivo sitemap-video.xml, un mapa de sitio de vídeos para Google. No requiere ningún tipo de configuración.

== Description ==
[*Español*](http://wordpress.org/plugins/google-video-sitemap-feed-with-multisite-support/) - [*English*](http://goo.gl/93lWhz) - [*Italiano*](http://goo.gl/jIv71W) - [*Français*](http://goo.gl/Gbquf8) - [*Deutsch*](http://goo.gl/kuCgCa) 

**Google Video Sitemap Feed With Multisite Support** genera dinámicamente un mapa de sitio de vídeos para Google creando un archivo `sitemap-video.xml` virtual. 

= Características =
* Prácticamente no requiere ningún tipo de configuración, por lo que funciona de forma totalmente autónoma y automática.
* Añade automáticamente todos los vídeos de YouTube, Vimeo y Dailymotion.
* Gestión automática de caché de datos externos de los vídeos para acelerar la creación del archivo `sitemap-video.xml`.
* Notificación por correo electrónico al administrador del sitio web en caso de que el vídeo haya sido borrado o marcado como privado para que edite la entrada donde aparece y borre la URL que ya no es válida.
* Es totalmente compatible con instalaciones de WordPress multisitio.
* Informa automáticamente a Google y a Bing cada vez que publiquemos una nueva entrada o página.

= Origen =
**Google Video Sitemap Feed With Multisite Support** ha sido programado a partir de los plugins [*Google News Sitemap Feed With Multisite Support*](http://wordpress.org/plugins/google-news-sitemap-feed-with-multisite-support/) de [Tim Brandon](http://profiles.wordpress.org/users/timbrd/) y [*Google XML Sitemap for Videos*](http://wordpress.org/plugins/xml-sitemaps-for-videos/) de [Amit Agarwal](http://profiles.wordpress.org/labnol/), que aún siendo magníficos plugins no ofrecían todas las características que necesitábamos. Aún así su trabajo ha sido completamente imprescindible para la realización de este plugin.

También se han realizado mejoras a partir de la versión 1.0 gracias al código aportado por [Ludo Bonnet](https://twitter.com/ludobonnet) y su idea de mejorar **Google Video Sitemap Feed With Multisite Support** añadiéndole soporte para Vimeo y Dailymotion. 

= Complementos =
Se recomienda el uso de **Google Video Sitemap Feed With Multisite Support** junto a [**Google Image Sitemap Feed With Multisite Support**](http://wordpress.org/plugins/google-image-sitemap-feed-with-multisite-support/) que genera el archivo `sitemap-image.xml`, y [**Google Mobile Sitemap Feed With Multisite Support**](http://wordpress.org/plugins/google-mobile-sitemap-feed-with-multisite-support/) que genera el archivo `sitemap-mobile.xml`.

= Muy importante =
Se han descrito errores al utilizarlo conjuntamente con la última versión de **Google XML Sitemaps** con soporte para WordPress multisitio. Los errores están descritos en [¿Cómo arreglar la incompatibilidad de Google XML Sitemaps con nuestros plugins?](http://www.artprojectgroup.es/como-arreglar-la-incompatibilidad-de-google-xml-sitemaps-con-nuestros-plugins) donde encontrarás toda la información necesaria para solucionar la incompatibilidad detectada.

= Más información =
En nuestro sitio web oficial puede obtener más información sobre [**Google Video Sitemap Feed With Multisite Support**](http://www.artprojectgroup.es/plugins-para-wordpress/google-video-sitemap-feed-with-multisite-support). 

= Comentarios =
No olvides dejarnos tu comentario en:

* [Google Video Sitemap Feed With Multisite Support](http://www.artprojectgroup.es/plugins-para-wordpress/google-video-sitemap-feed-with-multisite-support) en Art Project Group.
* [Art Project Group](https://www.facebook.com/artprojectgroup) en Facebook.
* [@artprojectgroup](https://twitter.com/artprojectgroup) en Twitter.
* [+ArtProjectGroupES](https://plus.google.com/+ArtProjectGroupES/) en Google+.

= Más plugins =
Recuerda que puedes encontrar más [plugins para WordPress](http://www.artprojectgroup.es/plugins-para-wordpress) en [Art Project Group](http://www.artprojectgroup.es) y en nuestro perfil en [WordPress](http://profiles.wordpress.org/artprojectgroup/).

== Installation ==
1. Puedes:
 * Subir la carpeta `google-video-sitemap-feed-with-multisite-support` al directorio `/wp-content/plugins/` vía FTP. 
 * Subir el archivo ZIP completo vía *Plugins -> Añadir nuevo -> Subir* en el Panel de Administración de tu instalación de WordPress.
 * Buscar **Google Video Sitemap Feed With Multisite Support** en el buscador disponible en *Plugins -> Añadir nuevo* y pulsar el botón *Instalar ahora*.
2. Activar el plugin a través del menú *Plugins* en el Panel de Administración de WordPress.
3. Listo, ahora ya puedes disfrutar de él, y si te gusta y te resulta útil, hacer una [*donación*](http://www.artprojectgroup.es/tienda/donacion).

== Frequently Asked Questions ==
= ¿Necesita configuración? =
No, el plugin es totalmente autónomo.

= ¿Es compatible con instalaciones de WordPress multisitio? =
Si, es completamente compatible.

= ¿Existen incompatibilidades? =
Si, se han descrito errores al utilizarlo conjuntamente con el plugin **Google XML Sitemaps**. Los errores están provocados por un orden erróneo de las reglas de redirección de WordPress, ya que **Google XML Sitemaps** interpreta todos los tipos de mapas de sitios posibles. En [¿Cómo arreglar la incompatibilidad de Google XML Sitemaps con nuestros plugins?](http://www.artprojectgroup.es/como-arreglar-la-incompatibilidad-de-google-xml-sitemaps-con-nuestros-plugins) encontrarás toda la información sobre esta incompatibilidad y la solución a la misma.

== Screenshots ==
1. Captura de pantalla de **Google Video Sitemap Feed With Multisite Support**.
2. Captura de pantalla del archivo `sitemap-video.xml`.

== Changelog ==
= 1.4 =
* Arreglo de error que provocaba un mensaje de error en versiones superiores a la 5.2 de PHP.
= 1.3.1 =
* Arreglo de error que borraba toda la configuración al desactivar el plugin.
* Corrección menor que evita la aparición de un código de error al recopilar información sobre el plugin.
= 1.3 =
* Añadido un nuevo panel de administración donde poder elegir si queremos recibir correos electrónicos o no.
* Mejora en el código que envía el correo electrónico.
* Cambio del enlace de donación.
= 1.2 =
* Mejora y optimización del código.
* Añadida caché para los datos externos.
* Añadida función que limpia la caché cuando se borra el plugin.
* Cambio del botón y enlace de donación.
= 1.1.7 =
* Arreglos de pequeños errores.
= 1.1.6 =
* Mejora y optimización del código.
* Arreglos de pequeños errores.
* Uso de la API Transients de WordPress para mejorar las consultas.
* Mejora en la búsqueda de vídeos de Vimeo.
= 1.1.5 =
* Arreglo de error en nombre de variable que deja datos en blanco en el correo electrónico
= 1.1.4 =
* Arreglos de diversos errores en los envíos de correos electrónicos.
* Arreglos de diversos errores en el almacenamiento de datos en la caché.
= 1.1.3 =
* Simplificación de código duplicado.
= 1.1.2 =
* Arreglo de error que no reiniciaba la variable encargada de controlar los envíos de correos electrónicos.
= 1.1.1 =
* Arreglo del código que envía los correos electrónicos.
= 1.1 =
* Gestión de caché de datos externos de los vídeos.
* Envía notificaciones de error por correo electrónico en caso de que el video no exista.
* Optimización del código.
* Arreglos de pequeños errores detectados.
= 1.0 =
* Añadido soporte para el acortador http://youtu.be.
* Añadido soporte para Vimeo.
* Añadido soporte para Dailymotion.
= 0.9 =
* Añadida nueva función que limpia la base de datos al desinstalar el plugin.
= 0.8 =
* Arreglo en la codificación de las entidades RSS.
= 0.7 =
* Arreglos menores en el código.
= 0.6 =
* Mejora del código para mejorar la validación del archivo sitemap-video.xml
= 0.5 =
* Actualización de las hojas de estilo acorde al nuevo WordPress 8.
* Arreglo de pequeños errores en el código.
= 0.4 =
* Inclusión de nuevos botones y enlaces.
= 0.3 =
* Pequeños arreglos de código.
* Pequeño arreglo de la traducción.
= 0.2 =
* Pequeñas modificaciones y arreglos de código.
* Inclusión de enlaces.
* Actualización de los textos de información.
= 0.1 =
* Versión inicial.

== Upgrade Notice ==
= 1.4 =
* Arreglo de error que provocaba un mensaje de error en versiones superiores a la 5.2 de PHP.
= 1.3.1 =
* Arreglo de error que borraba toda la configuración al desactivar el plugin.
* Corrección menor que evita la aparición de un código de error al recopilar información sobre el plugin.
= 1.3 =
* Añadido un nuevo panel de administración donde poder elegir si queremos recibir correos electrónicos o no.
* Mejora en el código que envía el correo electrónico.
* Cambio del enlace de donación.
= 1.2 =
* Mejora y optimización del código.
* Añadida caché para los datos externos.
* Añadida función que limpia la caché cuando se borra el plugin.
* Cambio del botón y enlace de donación.
= 1.1.7 =
* Arreglos de pequeños errores.
= 1.1.6 =
* Mejora y optimización del código.
* Arreglos de pequeños errores.
* Uso de la API Transients de WordPress para mejorar las consultas.
* Mejora en la búsqueda de vídeos de Vimeo.
= 1.1.5 =
* Arreglo de error en nombre de variable que deja datos en blanco en el correo electrónico
= 1.1.4 =
* Arreglos de diversos errores en los envíos de correos electrónicos.
* Arreglos de diversos errores en el almacenamiento de datos en la caché.
= 1.1.3 =
* Simplificación de código duplicado.
= 1.1.2 =
* Arreglo de error que no reiniciaba la variable encargada de controlar los envíos de correos electrónicos.
= 1.1.1 =
* Arreglo del código que envía los correos electrónicos.
= 1.1 =
* Gestión de caché de datos externos de los vídeos.
* Envía notificaciones de error por correo electrónico en caso de que el video no exista.
* Optimización del código.
* Arreglos de pequeños errores detectados.
= 1.0 =
* Añadido soporte para el acortador http://youtu.be.
* Añadido soporte para Vimeo.
* Añadido soporte para Dailymotion.
= 0.9 =
* Añadida nueva función que limpia la base de datos al desinstalar el plugin.
= 0.8 =
* Arreglo en la codificación de las entidades RSS.
= 0.7 =
* Arreglos menores en el código.
= 0.6 =
* Mejora del código para mejorar la validación del archivo sitemap-video.xml
= 0.5 =
* Actualización de las hojas de estilo acorde al nuevo WordPress 8.
* Arreglo de pequeños errores en el código.
= 0.4 =
* Inclusión de nuevos botones y enlaces.
= 0.3 =
* Pequeños arreglos de código.
* Pequeño arreglo de la traducción.
= 0.2 =
* Pequeñas modificaciones y arreglos de código.
* Inclusión de enlaces.
* Actualización de los textos de información.

==Traducciones ==
* *English*: by **Art Project Group** (default language).
* *Español*: por **Art Project Group**.

== ¿Por qué está esta página en español? ==
Mientras WordPress no nos permita a los desarrolladores realizar esta página en más de un idioma, elegiremos siempre el español.

A pesar de que es una apuesta muy arriesgada y de que reduce mucho las posibilidades de propagación de nuestros plugins, creemos que la comunidad hispana de WordPress es lo suficientemente amplia como para abocarla al idioma inglés hasta el final de los tiempos.

Por ello regalamos a esa gran comunidad hispana nuestros plugins con interfaces, instrucciones, tutoriales, soporte y páginas web en *WordPress.org* en español.

Esperamos que os guste nuestra iniciativa.

== Donación ==
¿Te ha gustado y te ha resultado útil **Google Video Sitemap Feed With Multisite Support** en tu sitio web? Te agradeceríamos una [pequeña donación](http://www.artprojectgroup.es/tienda/donacion) que nos ayudará a seguir mejorando este plugin y a crear más plugins totalmente gratuitos para toda la comunidad WordPress.

== Gracias ==
* A [Tim Brandon](http://profiles.wordpress.org/users/timbrd/) y [Amit Agarwal](http://profiles.wordpress.org/labnol/) por sus grandes plugins que han inspirado **Google Video Sitemap Feed With Multisite Support**.
* A [Ludo Bonnet](https://twitter.com/ludobonnet) por sus aportaciones al código y por su idea de añadir soporte para Vimeo y Dailymotion.
* A todos los que lo usáis.
* A todos los que ayudáis a mejorarlo.
* A todos los que realizáis donaciones.
* A todos los que nos animáis con vuestros comentarios.

¡Muchas gracias a todos!
