<?php
//Igual no deberías poder abrirme
defined( 'ABSPATH' ) || exit;

/**
 * Clase que controla todo lo relacionado con el XML.
 * 
 */
class APG_Video_Sitemap {

    /**
     * Instancia activa, necesaria para poder quitar sus filtros al desactivar.
     *
     * @var APG_Video_Sitemap|null
     */
    protected static $instancia = null;

    /**
     * Acción de la cola que se está ejecutando, para poder escribir en su registro.
     *
     * @var int
     */
    protected static $accion_actual = 0;

    /**
     * Si la última consulta dijo que el vídeo ya no está disponible.
     *
     * @var bool
     */
    protected static $perdido = false;

    /**
     * Textos que delatan un vídeo en la base de datos. Acotan la consulta; el
     * reconocimiento real lo hacen las expresiones regulares de busca_videos().
     *
     * Son siete, y consulta() lleva siete marcadores LIKE por columna: si se añade o se
     * quita alguno hay que actualizar también las dos consultas.
     *
     * @var string[]
     */
    const PATRONES = [ 'youtube.com', 'youtube-nocookie.com', 'youtu.be', 'dailymotion.com', 'vimeo.com', '<video', '[video' ];

    /**
     * Extensiones de vídeo autoalojado que se publican como content_loc.
     *
     * @var string[]
     */
    const EXTENSIONES = [ 'mp4', 'm4v', 'mov', 'webm', 'ogv', 'flv', 'wmv', 'avi', 'mpg', 'mpeg', '3gp', '3g2' ];

    /**
     * Constructor.
     *
     */
	public function __construct() {
        self::$instancia = $this;

		add_action( 'init', [ $this, 'init' ] );
        add_action( 'do_feed_sitemap-video', [ $this, 'carga_plantilla' ], 10, 1 );
        add_filter( 'generate_rewrite_rules', [ $this, 'rewrite' ] );
        add_filter( 'query_vars', [ $this, 'query_vars' ] );
        //Descubrimiento del sitemap, ahora que Google y Bing ya no aceptan pings
        add_filter( 'robots_txt', [ $this, 'robots_txt' ], 10, 2 );
        add_filter( 'wp_sitemaps_index_entries', [ $this, 'indice_del_core' ] );
        //Sólo se invalida cuando cambia una entrada que contiene o contenía vídeos
        add_action( 'post_updated', [ $this, 'entrada_actualizada' ], 999, 3 );
        add_action( 'save_post', [ $this, 'entrada_guardada' ], 999, 2 );
        add_action( 'deleted_post', [ $this, 'entrada_borrada' ], 999, 2 );
        add_action( 'trashed_post', [ $this, 'entrada_borrada' ], 999, 1 );
        //La revisión de vídeos cuelga de la cola, nunca de una carga de página
        add_action( 'apg_video_sitemap_revision', [ __CLASS__, 'procesamiento' ] );
        add_action( 'action_scheduler_before_execute', [ __CLASS__, 'recuerda_accion' ] );
        //También fuera del escritorio: si no, un sitio donde nadie entra a wp-admin nunca
        //llegaría a programar la pasada diaria. La guarda es una opción autocargada.
        add_action( 'init', [ __CLASS__, 'asegura_revision_diaria' ], 20 );
	}

    /**
     * Funciones iniciales del plugin.
     *
     * @return void
     */	
    public function init() {
		if ( defined( 'QT_LANGUAGE' ) ) {
			add_filter( 'xml_sitemap_url', [ $this, 'qtranslate' ], 99 );
		}
        
	}
    
	//Carga la plantilla del XML
	public function carga_plantilla() {
		load_template( plugin_dir_path( __FILE__ ) . 'contenido-xml.php' );
	}

    /**
     * Registra la variable que identifica cada sitemap parcial.
     *
     * @param array $variables Variables públicas de la consulta.
     * @return array
     */
    public function query_vars( $variables ) {
        $variables[] = 'sitemap_video_feed';

        return $variables;
    }

    /**
     * Devuelve las direcciones del sitemap, ya sea el índice o los parciales.
     *
     * @return string[]
     */
    static public function dame_direcciones() {
        $paginas = (int) get_option( 'apg_video_sitemap_paginas' );
        if ( $paginas < 2 ) {
            return [ home_url( '/sitemap-video.xml' ) ];
        }

        $direcciones = [];
        for ( $i = 1; $i <= $paginas; $i++ ) {
            $direcciones[] = home_url( "/sitemap-video-$i.xml" );
        }

        return $direcciones;
    }

    /**
     * Anuncia el sitemap en robots.txt.
     *
     * @param string $salida  Contenido de robots.txt.
     * @param bool   $publico Si el sitio web es visible para los buscadores.
     * @return string
     */
    public function robots_txt( $salida, $publico ) {
        if ( ! $publico ) {
            return $salida;
        }

        //robots.txt admite un índice de sitemaps, así que basta con la dirección principal.
        return $salida . 'Sitemap: ' . esc_url_raw( home_url( '/sitemap-video.xml' ) ) . PHP_EOL;
    }

    /**
     * Añade el sitemap de vídeos al índice de sitemaps de WordPress.
     *
     * @param array $entradas Entradas del índice.
     * @return array
     */
    public function indice_del_core( $entradas ) {
        //Un índice de sitemaps no puede anidar otro índice: se listan los parciales.
        foreach ( self::dame_direcciones() as $direccion ) {
            $entradas[] = [ 'loc' => $direccion ];
        }

        return $entradas;
    }

	//Añade el sitemap a los enlaces permanentes
	public function rewrite( $wp_rewrite ) {
        $feed_rules = [ 
            'sitemap-video\.xml$' => $wp_rewrite->index . '?feed=sitemap-video' 
        ];
        $entradas   = get_transient( 'xml_video_sitemap_consulta' );
        if ( ! empty( $entradas ) && is_array( $entradas ) ) {
            $paginas = (int) ceil( count( $entradas ) / APG_VIDEO_SITEMAP_MAXIMO );
            for ( $i = 1; $i <= $paginas; $i++ ) {
                //Cada sitemap parcial lleva su número en una variable propia, no en la URL a pelo.
                $feed_rules[ "sitemap-video-$i\.xml$" ] = $wp_rewrite->index . "?feed=sitemap-video&sitemap_video_feed=$i";
            }
        }
		$wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;
	}

    //qTranslate
	public function qtranslate( $input ) {
		global $q_config;

        $return = [];
		if ( is_array( $input ) ) { // got an array? return one!
			foreach ( $input as $url ) {
				foreach( $q_config[ 'enabled_languages' ] as $language ) {
					$return[] = qtrans_convertURL( $url, $language );
				}
			}
		} else {
			$return = qtrans_convertURL( $input ); // not an array? just convert the string.
		}

		return $return;
	}

	//Invalida la caché del sitemap cuando cambia el contenido
	public function limpia_cache() {
		delete_transient( 'xml_video_sitemap_consulta' );
		self::programa_revision();
	}

    /**
     * Comprueba si un texto contiene algo que parezca un vídeo.
     *
     * Es una criba barata en memoria: evita rehacer una consulta de casi un segundo
     * cada vez que se guarda cualquier cosa.
     *
     * @param string $texto Texto a examinar.
     * @return bool
     */
    static public function tiene_video( $texto ) {
        $texto = (string) $texto;
        foreach ( self::PATRONES as $patron ) {
            if ( false !== stripos( $texto, $patron ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Decide si una entrada puede llegar a salir en el sitemap.
     *
     * @param WP_Post|null $entrada Entrada.
     * @return bool
     */
    static protected function entrada_cuenta( $entrada ) {
        if ( ! $entrada instanceof WP_Post ) {
            return false;
        }

        //Las revisiones y los autoguardados no se publican nunca.
        if ( wp_is_post_revision( $entrada ) || wp_is_post_autosave( $entrada ) ) {
            return false;
        }

        return in_array( $entrada->post_type, self::dame_tipos_de_entradas(), true );
    }

    /**
     * Devuelve el texto de una entrada donde puede haber vídeos.
     *
     * @param WP_Post $entrada Entrada.
     * @return string
     */
    static protected function dame_texto( $entrada ) {
        return $entrada->post_content . ' ' . $entrada->post_excerpt;
    }

    /**
     * Invalida la caché al editar una entrada, sólo si el cambio afecta al sitemap.
     *
     * @param int     $entrada_id Identificador de la entrada.
     * @param WP_Post $despues    Entrada después de guardar.
     * @param WP_Post $antes      Entrada antes de guardar.
     * @return void
     */
    public function entrada_actualizada( $entrada_id, $despues, $antes ) {
        if ( ! self::entrada_cuenta( $despues ) ) {
            return;
        }

        //Cuenta tanto si ahora tiene vídeo como si lo tenía y ha dejado de tenerlo.
        $ahora  = 'publish' === $despues->post_status && self::tiene_video( self::dame_texto( $despues ) );
        $estaba = $antes instanceof WP_Post && 'publish' === $antes->post_status && self::tiene_video( self::dame_texto( $antes ) );

        if ( $ahora || $estaba ) {
            self::limpia_cache();
        }
    }

    /**
     * Invalida la caché al crear una entrada con vídeos.
     *
     * @param int     $entrada_id Identificador de la entrada.
     * @param WP_Post $entrada    Entrada guardada.
     * @return void
     */
    public function entrada_guardada( $entrada_id, $entrada ) {
        if ( ! self::entrada_cuenta( $entrada ) || 'publish' !== $entrada->post_status ) {
            return;
        }

        if ( self::tiene_video( self::dame_texto( $entrada ) ) ) {
            self::limpia_cache();
        }
    }

    /**
     * Invalida la caché al borrar o enviar a la papelera una entrada con vídeos.
     *
     * @param int          $entrada_id Identificador de la entrada.
     * @param WP_Post|null $entrada    Entrada borrada, si el gancho la pasa.
     * @return void
     */
    public function entrada_borrada( $entrada_id, $entrada = null ) {
        $entrada = ( $entrada instanceof WP_Post ) ? $entrada : get_post( $entrada_id );

        if ( ! self::entrada_cuenta( $entrada ) ) {
            return;
        }

        if ( self::tiene_video( self::dame_texto( $entrada ) ) ) {
            self::limpia_cache();
        }
    }

	//Desactiva el plugin
	public static function desactivar() {
		global $wp_rewrite;

        if ( self::$instancia ) {
            remove_filter( 'generate_rewrite_rules', [ self::$instancia, 'rewrite' ] );
        }
        //Limpia el evento de ping a buscadores que usaban las versiones anteriores.
        wp_clear_scheduled_hook( 'enviar_ping' );
		$wp_rewrite->flush_rules();
	}
    
    /**
     * Devuelve los tipos de entrada públicos que hay que recorrer.
     *
     * @return string[]
     */
    static public function dame_tipos_de_entradas() {
        $tipos_de_entradas = get_post_types( [ 'public' => true ], 'names' );

        return array_values( array_filter( array_map( 'sanitize_key', (array) $tipos_de_entradas ) ) );
    }

    /**
     * Campos personalizados donde buscar vídeos.
     *
     * Es una lista blanca a propósito. Recorrer toda la tabla de metadatos excluyendo unas
     * pocas claves obliga a leerla entera: medido en producción, 413 ms frente a 2 ms con
     * IN sobre el índice de meta_key. Además, lo único que encontraba el recorrido completo
     * eran copias de seguridad de constructores y schema generado a partir del propio vídeo,
     * que no son contenido vivo y pueden resucitar vídeos ya borrados de la entrada.
     *
     * @return string[]
     */
    static public function dame_campos_incluidos() {
        $incluidos = [
            '_elementor_data',          //Elementor
            'wpex_post_oembed',         //Total
            'panels_data',              //SiteOrigin Page Builder
            '_et_pb_old_content',       //Divi
            'fusion_builder_content',   //Avada, el contenido vivo y no la copia de seguridad
        ];

        $incluidos = array_map( 'strval', (array) apply_filters( 'apg_video_sitemap_campos_incluidos', $incluidos ) );

        return array_values( array_unique( array_filter( $incluidos ) ) );
    }

    /**
     * Devuelve los patrones LIKE, listos para $wpdb->prepare().
     *
     * @return string[]
     */
    static protected function dame_patrones() {
        global $wpdb;

        $patrones = [];
        foreach ( self::PATRONES as $patron ) {
            $patrones[] = '%' . $wpdb->esc_like( $patron ) . '%';
        }

        return $patrones;
    }

    /**
     * Devuelve las entradas publicadas que contienen algún vídeo, agrupadas por entrada.
     *
     * La consulta va escrita entera, sin fragmentos montados a trozos: los siete patrones
     * LIKE se corresponden uno a uno con self::PATRONES, y las listas variables (tipos de
     * entrada y campos excluidos) viajan como un único parámetro para FIND_IN_SET().
     *
     * @return array
     */
    static public function consulta() {
        $entradas = get_transient( 'xml_video_sitemap_consulta' );
        if ( is_array( $entradas ) ) {
            //Las versiones anteriores guardaban aquí otra estructura: si no es la de ahora, se rehace.
            $primera = reset( $entradas );
            if ( false === $primera || isset( $primera->contenido ) ) {
                return $entradas;
            }
        }

        global $wpdb;

        $tipos_de_entradas = self::dame_tipos_de_entradas();
        if ( empty( $tipos_de_entradas ) ) {
            return [];
        }

        $tipos    = implode( ',', $tipos_de_entradas );
        $patrones = self::dame_patrones();

        /**
         * Permite desactivar el rastreo de campos personalizados, que recorre toda la
         * tabla de metadatos y puede resultar caro en sitios web muy grandes.
         *
         * @param bool $busca Si hay que buscar vídeos en los campos personalizados.
         */
        $busca_campos = (bool) apply_filters( 'apg_video_sitemap_busca_en_campos_personalizados', true );
        $campos       = self::dame_campos_incluidos();

        //Las consultas se pasan a $wpdb->prepare() con todos sus parámetros. La segunda rama
        //sólo mira las claves conocidas y la caché de oEmbed, que es lo que de verdad guarda
        //contenido vivo, en lugar de leer la tabla de metadatos entera.
        if ( $busca_campos && $campos ) {
            $campos_sql = implode( ', ', array_fill( 0, count( $campos ), '%s' ) );
            $argumentos = array_merge( [ $tipos ], $patrones, $patrones, $campos, [ $wpdb->esc_like( '_oembed_' ) . '%', $tipos ], $patrones );
            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- lo único interpolado es $campos_sql, una lista de marcadores %s generada a partir de dame_campos_incluidos(); se usa IN y no FIND_IN_SET porque IN sí aprovecha el índice de meta_key (2 ms frente a 413 ms medidos en producción). El resultado se guarda en el transitorio xml_video_sitemap_consulta unas líneas más abajo.
            $filas      = $wpdb->get_results( $wpdb->prepare( "(SELECT ID, post_title, post_excerpt, post_date, post_modified, post_content AS contenido
                                FROM $wpdb->posts
                                WHERE post_status = 'publish'
                                    AND FIND_IN_SET( post_type, %s )
                                    AND ( post_content LIKE %s OR post_content LIKE %s OR post_content LIKE %s OR post_content LIKE %s OR post_content LIKE %s OR post_content LIKE %s OR post_content LIKE %s
                                        OR post_excerpt LIKE %s OR post_excerpt LIKE %s OR post_excerpt LIKE %s OR post_excerpt LIKE %s OR post_excerpt LIKE %s OR post_excerpt LIKE %s OR post_excerpt LIKE %s ))
                            UNION ALL
                            (SELECT entradas.ID, entradas.post_title, entradas.post_excerpt, entradas.post_date, entradas.post_modified, campos.meta_value AS contenido
                                FROM $wpdb->posts AS entradas
                                INNER JOIN $wpdb->postmeta AS campos
                                    ON campos.post_id = entradas.ID
                                        AND ( campos.meta_key IN ( $campos_sql ) OR campos.meta_key LIKE %s )
                                WHERE entradas.post_status = 'publish'
                                    AND FIND_IN_SET( entradas.post_type, %s )
                                    AND ( campos.meta_value LIKE %s OR campos.meta_value LIKE %s OR campos.meta_value LIKE %s OR campos.meta_value LIKE %s OR campos.meta_value LIKE %s OR campos.meta_value LIKE %s OR campos.meta_value LIKE %s ))
                            ORDER BY post_date DESC", ...$argumentos ) );
            // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        } else {
            $argumentos = array_merge( [ $tipos ], $patrones, $patrones );
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber -- no hay API de WordPress que busque siete cadenas dentro del contenido y el resultado se guarda en el transitorio xml_video_sitemap_consulta unas líneas más abajo; $argumentos lleva 15 valores para los 15 marcadores de esta consulta.
            $filas      = $wpdb->get_results( $wpdb->prepare( "(SELECT ID, post_title, post_excerpt, post_date, post_modified, post_content AS contenido
                                FROM $wpdb->posts
                                WHERE post_status = 'publish'
                                    AND FIND_IN_SET( post_type, %s )
                                    AND ( post_content LIKE %s OR post_content LIKE %s OR post_content LIKE %s OR post_content LIKE %s OR post_content LIKE %s OR post_content LIKE %s OR post_content LIKE %s
                                        OR post_excerpt LIKE %s OR post_excerpt LIKE %s OR post_excerpt LIKE %s OR post_excerpt LIKE %s OR post_excerpt LIKE %s OR post_excerpt LIKE %s OR post_excerpt LIKE %s ))
                            ORDER BY post_date DESC", ...$argumentos ) );
        }

        $entradas = [];
        foreach ( (array) $filas as $fila ) {
            $id = (int) $fila->ID;
            //Una misma entrada puede llegar varias veces (contenido y cada campo): se agrupan.
            if ( ! isset( $entradas[ $id ] ) ) {
                $entradas[ $id ] = (object) [
                    'ID'         => $id,
                    'titulo'     => $fila->post_title,
                    'extracto'   => $fila->post_excerpt,
                    'fecha'      => $fila->post_date,
                    'modificada' => $fila->post_modified,
                    'contenido'  => (string) $fila->post_excerpt,
                ];
            }
            $entradas[ $id ]->contenido .= "\n" . (string) $fila->contenido;
        }

        set_transient( 'xml_video_sitemap_consulta', $entradas, DAY_IN_SECONDS );

        //Sólo se regeneran las reglas si ha cambiado el número de sitemaps parciales.
        $paginas = (int) ceil( count( $entradas ) / APG_VIDEO_SITEMAP_MAXIMO );
        if ( (int) get_option( 'apg_video_sitemap_paginas' ) !== $paginas ) {
            update_option( 'apg_video_sitemap_paginas', $paginas, false );
            $GLOBALS[ 'wp_rewrite' ]->flush_rules();
        }

        return $entradas;
    }

    //Envía un correo informando de que el vídeo ya no existe
    static public function envia_correo( $video ) {
        global $wpdb;

        $tipos_de_entradas = self::dame_tipos_de_entradas();
        if ( empty( $tipos_de_entradas ) ) {
            return;
        }

        $entrada = $wpdb->get_row( $wpdb->prepare( "SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish' AND FIND_IN_SET( post_type, %s ) AND post_content LIKE %s LIMIT 1", implode( ',', $tipos_de_entradas ), '%' . $wpdb->esc_like( $video ) . '%' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- búsqueda puntual del texto de un vídeo perdido; no hay API de WordPress equivalente y sólo ocurre al enviar el aviso.

        if ( empty( $entrada ) ) {
            return;
        }

        /* translators: 1: URL of the page, 2: title of the page, 3: name of the website, 4: identifier of the video. */
        $aviso = sprintf( __( 'Please check the page <a href="%1$s">%2$s</a> from your website %3$s and edit the deleted video with id %4$s.<br /><br />email sent by <a href="https://artprojectgroup.es/plugins-para-wordpress/apg-google-video-sitemap-feed">APG Google Video Sitemap Feed</a>.', 'google-video-sitemap-feed-with-multisite-support' ), esc_url( get_permalink( $entrada->ID ) ), esc_html( $entrada->post_title ), esc_html( get_bloginfo( 'name' ) ), esc_html( $video ) );
        wp_mail( get_option( 'admin_email' ), __( 'Video not found!', 'google-video-sitemap-feed-with-multisite-support' ), $aviso, [ 'Content-type: text/html' ] );
    }

    /**
     * Avisa por correo, una sola vez por vídeo, de que el vídeo ya no está disponible.
     *
     * @param string $video Identificador del vídeo.
     * @return void
     */
    static protected function avisa_de_video_perdido( $video ) {
        $configuracion = get_option( 'xml_video_sitemap' );
        if ( ! is_array( $configuracion ) || empty( $configuracion[ 'correo' ] ) ) {
            return;
        }

        //El aviso se marca con su propia clave: la clave del vídeo guarda su URL de consulta.
        $aviso = 'aviso_' . $video;
        if ( isset( $configuracion[ $aviso ] ) ) {
            return;
        }

        $configuracion[ $aviso ] = '1';
        update_option( 'xml_video_sitemap', $configuracion );
        self::envia_correo( $video );
    }

    //Obtiene información del vídeo ( función mejorada con ayuda de Ludo Bonnet [https://github.com/ludobonnet] )
    static public function procesa_url( $url, $video ) {
        self::$perdido = false;
        $clave         = 'apg_video_' . md5( $url );
        $respuesta     = get_transient( $clave );
        $consultado    = false;
        if ( $respuesta === false ) { //No hay información en la base de datos
            $respuesta  = wp_safe_remote_get( $url, [ 'timeout' => 10 ] );
            $consultado = true;
        }

        //Comprueba si hay error en la respuesta y si hay que enviar el correo de aviso
        $cuerpo   = wp_remote_retrieve_body( $respuesta );
        $codigo   = (int) wp_remote_retrieve_response_code( $respuesta );
        $datos    = ( '' !== $cuerpo ) ? json_decode( $cuerpo ) : null;
        $mensajes = [ 'Video not found', 'Invalid id', 'Private video' ];
        //Sólo se da por perdido cuando el proveedor dice que el vídeo ya no está: una caída
        //de red o un 500 son temporales y no deben generar un correo ni marcar el vídeo.
        $perdido       = ! is_wp_error( $respuesta ) && ( in_array( $codigo, [ 401, 403, 404, 410 ], true ) || in_array( trim( $cuerpo ), $mensajes, true ) || isset( $datos->error ) );
        $fallo         = $perdido || is_wp_error( $respuesta ) || 200 !== $codigo;
        self::$perdido = $perdido;

        if ( $consultado ) {
            //El fallo también se cachea, con su propio plazo. Borrar la caché al fallar, que
            //es lo que hacía antes, obligaba a repetir la petición en cada pasada.
            //El mismo plazo que decide si hay que revisar: si fueran distintos, se reencolaría
            //para refrescar un dato que la caché todavía da por bueno.
            if ( ! $fallo ) {
                $estado = 'ok';
            } elseif ( $perdido ) {
                $estado = 'perdido';
            } else {
                $estado = 'error';
            }
            set_transient( $clave, $respuesta, self::dame_plazo( $estado ) );
            self::registra_video( $video, $url );

            if ( $perdido ) {
                self::avisa_de_video_perdido( $video );
            }
        }

        return $fallo ? null : $cuerpo;
    }

    /**
     * Guarda la URL de consulta de un vídeo para poder limpiar su caché al desinstalar.
     *
     * @param string $video Identificador del vídeo.
     * @param string $url   URL consultada.
     * @return void
     */
    static protected function registra_video( $video, $url ) {
        $configuracion = get_option( 'xml_video_sitemap' );
        if ( ! is_array( $configuracion ) ) {
            $configuracion = [];
        }

        if ( isset( $configuracion[ $video ] ) && $configuracion[ $video ] === $url ) {
            return;
        }

        $configuracion[ $video ] = $url;
        update_option( 'xml_video_sitemap', $configuracion );
    }

    //Procesa los datos externos
    static public function obtiene_informacion( $identificador, $proveedor ) {
        $codificado = rawurlencode( $identificador );
        $api        = [ 
            //oEmbed propio de YouTube: no necesita clave y evita depender de un tercero.
            'youtube'		=> 'https://www.youtube.com/oembed?format=json&url=' . rawurlencode( 'https://www.youtube.com/watch?v=' . $identificador ), 
            'dailymotion'	=> 'https://api.dailymotion.com/video/' . $codificado . '?fields=' . rawurlencode( 'id,title,duration,thumbnail_url,created_time,owner.screenname,owner.url' ), 
            'vimeo'			=> 'https://vimeo.com/api/v2/video/' . $codificado . '.json'
        ];

        if ( ! isset( $api[ $proveedor ] ) ) {
            return false;
        }

        $cuerpo = self::procesa_url( $api[ $proveedor ], $identificador );
        if ( empty( $cuerpo ) ) {
            return false;
        }

        $datos = json_decode( $cuerpo );
        if ( $proveedor === 'vimeo' ) {
            return isset( $datos[ 0 ] ) ? $datos[ 0 ] : false;
        }

        return is_object( $datos ) ? $datos : false;
    }

    /**
     * Devuelve los datos de un vídeo ya normalizados, venga de donde venga.
     *
     * @param array  $video_buscado Vídeo localizado por busca_videos().
     * @param object $entrada       Entrada que lo contiene.
     * @return array|false
     */
    static public function dame_datos_del_video( $video_buscado, $entrada ) {
        if ( $video_buscado[ 'proveedor' ] === 'local' ) {
            return self::dame_datos_locales( $video_buscado, $entrada );
        }

        $datos = self::obtiene_informacion( $video_buscado[ 'identificador' ], $video_buscado[ 'proveedor' ] );
        if ( ! $datos ) {
            return false;
        }

        return self::normaliza( $datos, $video_buscado );
    }

    /**
     * Estructura común a todos los proveedores.
     *
     * @param array $video_buscado Vídeo localizado por busca_videos().
     * @return array
     */
    static protected function dame_plantilla( $video_buscado ) {
        return [
            'titulo'      => '',
            'descripcion' => '',
            'imagen'      => isset( $video_buscado[ 'imagen' ] ) ? $video_buscado[ 'imagen' ] : '',
            'reproductor' => isset( $video_buscado[ 'reproductor' ] ) ? $video_buscado[ 'reproductor' ] : '',
            'contenido'   => isset( $video_buscado[ 'contenido' ] ) ? $video_buscado[ 'contenido' ] : '',
            'duracion'    => 0,
            'autor'       => '',
            'autor_url'   => '',
            'publicado'   => '',
        ];
    }

    /**
     * Traduce la respuesta de cada API a la estructura común.
     *
     * @param object $datos         Respuesta de la API.
     * @param array  $video_buscado Vídeo localizado por busca_videos().
     * @return array
     */
    static protected function normaliza( $datos, $video_buscado ) {
        $video = self::dame_plantilla( $video_buscado );

        switch ( $video_buscado[ 'proveedor' ] ) {
            case 'youtube':
                $video[ 'titulo' ]    = isset( $datos->title ) ? $datos->title : '';
                $video[ 'autor' ]     = isset( $datos->author_name ) ? $datos->author_name : '';
                $video[ 'autor_url' ] = isset( $datos->author_url ) ? $datos->author_url : '';
                if ( ! empty( $datos->thumbnail_url ) ) {
                    $video[ 'imagen' ] = $datos->thumbnail_url;
                }
                break;

            case 'vimeo':
                $video[ 'titulo' ]      = isset( $datos->title ) ? $datos->title : '';
                $video[ 'descripcion' ] = isset( $datos->description ) ? $datos->description : '';
                $video[ 'autor' ]       = isset( $datos->user_name ) ? $datos->user_name : '';
                $video[ 'autor_url' ]   = isset( $datos->user_url ) ? $datos->user_url : '';
                $video[ 'duracion' ]    = isset( $datos->duration ) ? (int) $datos->duration : 0;
                if ( ! empty( $datos->thumbnail_large ) ) {
                    $video[ 'imagen' ] = $datos->thumbnail_large;
                }
                if ( ! empty( $datos->upload_date ) ) {
                    $video[ 'publicado' ] = self::fecha_w3c( $datos->upload_date );
                }
                break;

            case 'dailymotion':
                $video[ 'titulo' ]   = isset( $datos->title ) ? $datos->title : '';
                $video[ 'duracion' ] = isset( $datos->duration ) ? (int) $datos->duration : 0;
                //Dailymotion devuelve los campos anidados con el punto en el nombre.
                $video[ 'autor' ]     = isset( $datos->{ 'owner.screenname' } ) ? $datos->{ 'owner.screenname' } : '';
                $video[ 'autor_url' ] = isset( $datos->{ 'owner.url' } ) ? $datos->{ 'owner.url' } : '';
                if ( ! empty( $datos->thumbnail_url ) ) {
                    $video[ 'imagen' ] = $datos->thumbnail_url;
                }
                if ( ! empty( $datos->created_time ) ) {
                    $video[ 'publicado' ] = self::fecha_w3c( '@' . (int) $datos->created_time );
                }
                break;
        }

        return $video;
    }

    /**
     * Resuelve los datos de un vídeo alojado en el propio sitio web.
     *
     * @param array  $video_buscado Vídeo localizado por busca_videos().
     * @param object $entrada       Entrada que lo contiene.
     * @return array
     */
    static protected function dame_datos_locales( $video_buscado, $entrada ) {
        $video   = self::dame_plantilla( $video_buscado );
        $adjunto = attachment_url_to_postid( $video[ 'contenido' ] );

        if ( $adjunto ) {
            $video[ 'titulo' ]    = get_the_title( $adjunto );
            $video[ 'publicado' ] = self::fecha_w3c( get_post_time( 'c', true, $adjunto ) );
            $metadatos            = wp_get_attachment_metadata( $adjunto );
            if ( ! empty( $metadatos[ 'length' ] ) ) {
                $video[ 'duracion' ] = (int) $metadatos[ 'length' ];
            }
            $miniatura = get_post_thumbnail_id( $adjunto );
            if ( $miniatura ) {
                $video[ 'imagen' ] = (string) wp_get_attachment_image_url( $miniatura, 'full' );
            }
        }

        if ( '' === $video[ 'titulo' ] ) {
            $video[ 'titulo' ] = $entrada->titulo;
        }
        if ( '' === $video[ 'imagen' ] ) {
            //Google exige miniatura: sin una propia se recurre a la imagen destacada.
            $video[ 'imagen' ] = (string) get_the_post_thumbnail_url( $entrada->ID, 'full' );
        }

        return $video;
    }

    /**
     * Convierte una fecha a formato W3C, que es el que exige Google.
     *
     * @param string $fecha Fecha en cualquier formato que entienda strtotime().
     * @return string
     */
    static protected function fecha_w3c( $fecha ) {
        $marca = strtotime( (string) $fecha );

        return $marca ? gmdate( 'c', $marca ) : '';
    }

    //Busca el vídeo en el contenido
    static public function busca_videos( $contenido, $videos ) { //Mejorado con ayuda de Ludo Bonnet [https://github.com/ludobonnet]
        $contenido = (string) $contenido;
        //Los constructores visuales guardan el contenido como JSON, con las barras escapadas.
        $contenido = str_replace( '\/', '/', $contenido );

        if ( preg_match_all( '/youtube\.com\/(v\/|watch\?v=|embed\/)([^\$][a-zA-Z0-9\-_]*)/', $contenido, $busquedas, PREG_SET_ORDER ) || preg_match_all( '/youtube-nocookie\.com\/(v\/|watch\?v=|embed\/)([^\$][a-zA-Z0-9\-_]*)/', $contenido, $busquedas, PREG_SET_ORDER ) ) { //Youtube
            foreach ( $busquedas as $busqueda ) {
                $identificador               = $busqueda[ 2 ];
                $videos[ $identificador ]    = [ 
                    'proveedor'		=> 'youtube', 
                    'identificador'	=> $identificador, 
                    'reproductor'	=> "https://www.youtube.com/embed/$identificador", 
                    'imagen'		=> "https://i.ytimg.com/vi/$identificador/hqdefault.jpg" 
                ];
            }
        }
        if ( preg_match_all( '/youtu\.be\/([^\$][a-zA-Z0-9\-_]*)/', $contenido, $busquedas, PREG_SET_ORDER ) ) { //Acortador de Youtube
            foreach ( $busquedas as $busqueda ) {
                $identificador               = $busqueda[ 1 ];
                $videos[ $identificador ]    = [ 
                    'proveedor'		=> 'youtube', 
                    'identificador'	=> $identificador, 
                    'reproductor'	=> "https://www.youtube.com/embed/$identificador", 
                    'imagen'		=> "https://i.ytimg.com/vi/$identificador/hqdefault.jpg" 
                ];
            }
        }
        if ( preg_match_all( '/dailymotion\.com\/video\/([^\$][a-zA-Z0-9]*)/', $contenido, $busquedas, PREG_SET_ORDER ) ) { //Dailymotion. Añadido por Ludo Bonnet [https://github.com/ludobonnet]	
            foreach ( $busquedas as $busqueda ) {
                $identificador               = $busqueda[ 1 ];
                $videos[ $identificador ]    = [ 
                    'proveedor'		=> 'dailymotion', 
                    'identificador'	=> $identificador, 
                    'reproductor'	=> "https://www.dailymotion.com/embed/video/$identificador", 
                    'imagen'        => "https://www.dailymotion.com/thumbnail/video/$identificador" 
                ];
            }
        }
        if ( preg_match_all( '/vimeo\.com\/moogaloop.swf\?clip_id=([^\$][0-9]*)/', $contenido, $busquedas, PREG_SET_ORDER ) || preg_match_all( '/vimeo\.com\/video\/([^\$][0-9]*)/', $contenido, $busquedas, PREG_SET_ORDER ) || preg_match_all( '/vimeo\.com\/([^\$][0-9]*)/', $contenido, $busquedas, PREG_SET_ORDER ) ) { //Vimeo. Mejorado a partir del código aportado por Ludo Bonnet [https://github.com/ludobonnet]
            foreach ( $busquedas as $busqueda ) {
                $identificador               = $busqueda[ 1 ];
                if ( is_numeric( $identificador ) ) {
                    $videos[ $identificador ]    = [ 
                        'proveedor'		=> 'vimeo', 
                        'identificador'	=> $identificador, 
                        'reproductor'	=> "https://player.vimeo.com/video/$identificador" 
                    ];
                }
            }
        }
        //Vídeo autoalojado: bloque de vídeo, etiqueta <video> y shortcode [video]
        if ( preg_match_all( '/(?:src|mp4|m4v|webm|ogv|flv|wmv)\s*=\s*["\']([^"\']+)["\']/i', $contenido, $busquedas, PREG_SET_ORDER ) ) {
            foreach ( $busquedas as $busqueda ) {
                $url       = $busqueda[ 1 ];
                $ruta      = wp_parse_url( $url, PHP_URL_PATH );
                $extension = strtolower( (string) pathinfo( (string) $ruta, PATHINFO_EXTENSION ) );
                if ( ! in_array( $extension, self::EXTENSIONES, true ) ) {
                    continue;
                }
                $identificador            = 'local_' . md5( $url );
                $videos[ $identificador ] = [ 
                    'proveedor'		=> 'local', 
                    'identificador'	=> $identificador, 
                    'contenido'		=> $url 
                ];
            }
        }

        return $videos;
    }
    
    /**
     * Devuelve el registro duradero de cada vídeo.
     *
     * Va en una opción y no en un transitorio a propósito: un transitorio vive en la caché
     * de objetos cuando la hay, así que un vaciado de caché borra una marca «de un año» y
     * reabre la puerta del reencolado. Esto tiene que sobrevivir a eso.
     *
     * @return array
     */
    static public function dame_registros() {
        $registros = get_option( 'apg_video_sitemap_videos' );

        return is_array( $registros ) ? $registros : [];
    }

    /**
     * Guarda el registro duradero de los vídeos.
     *
     * @param array $registros Registros a guardar.
     * @return void
     */
    static protected function guarda_registros( $registros ) {
        update_option( 'apg_video_sitemap_videos', $registros, false );
    }

    /**
     * Devuelve el registro de un vídeo, con todos sus campos.
     *
     * @param array  $registros     Registros completos.
     * @param string $identificador Identificador del vídeo.
     * @return array
     */
    static protected function dame_registro( $registros, $identificador ) {
        $vacio = [ 'proveedor' => '', 'estado' => '', 'revisado' => 0, 'intentos' => 0, 'veces' => 0, 'dia' => '', 'aviso' => 0 ];

        return isset( $registros[ $identificador ] ) ? array_merge( $vacio, (array) $registros[ $identificador ] ) : $vacio;
    }

    /**
     * Plazo tras el cual conviene volver a consultar un vídeo, según cómo acabó la última vez.
     *
     * @param string $estado Estado del último intento.
     * @return int Segundos.
     */
    static public function dame_plazo( $estado ) {
        $plazos = apply_filters( 'apg_video_sitemap_plazos', [
            'ok'      => 30 * DAY_IN_SECONDS,
            'perdido' => 7 * DAY_IN_SECONDS,
            'error'   => DAY_IN_SECONDS,
        ] );

        return isset( $plazos[ $estado ] ) ? (int) $plazos[ $estado ] : 30 * DAY_IN_SECONDS;
    }

    /**
     * Número de intentos fallidos tras los cuales se abandona un vídeo.
     *
     * @return int
     */
    static public function dame_intentos() {
        return max( 1, (int) apply_filters( 'apg_video_sitemap_intentos', 3 ) );
    }

    /**
     * Decide si toca volver a consultar un vídeo.
     *
     * La decisión sale del plazo, no de que falte un dato: así un fallo puntual no convierte
     * el vídeo en carne de reencolado permanente.
     *
     * @param array  $registros     Registros completos.
     * @param string $identificador Identificador del vídeo.
     * @return bool
     */
    static public function hay_que_revisar( $registros, $identificador ) {
        if ( ! isset( $registros[ $identificador ] ) ) {
            return true; //Nunca se ha consultado
        }

        $registro = self::dame_registro( $registros, $identificador );
        if ( 'abandonado' === $registro[ 'estado' ] ) {
            return false; //Se agotaron los intentos: no se vuelve a intentar solo
        }

        return ( time() - (int) $registro[ 'revisado' ] ) >= self::dame_plazo( $registro[ 'estado' ] );
    }

    /**
     * Recuerda qué acción de la cola se está ejecutando, para poder escribir en su registro.
     *
     * @param int $accion_id Identificador de la acción.
     * @return void
     */
    static public function recuerda_accion( $accion_id ) {
        self::$accion_actual = (int) $accion_id;
    }

    /**
     * Deja constancia en el registro de Action Scheduler, que es donde se mira.
     *
     * @param string $mensaje Mensaje.
     * @return void
     */
    static protected function anota_en_el_registro( $mensaje ) {
        if ( self::$accion_actual && class_exists( 'ActionScheduler_Logger' ) ) {
            ActionScheduler_Logger::instance()->log( self::$accion_actual, $mensaje );
        }

        do_action( 'apg_video_sitemap_bucle_detectado', $mensaje );
    }

    /**
     * Procesa un vídeo y anota el resultado. Es el destino de la acción encolada.
     *
     * @param string $identificador Identificador del vídeo.
     * @param string $proveedor     Proveedor del vídeo.
     * @return void
     */
    static public function procesa_video( $identificador, $proveedor ) {
        $registros = self::dame_registros();
        $registro  = self::dame_registro( $registros, $identificador );

        //Contador diario: un bucle silencioso es el que dura tres años.
        $hoy = gmdate( 'Y-m-d' );
        if ( $registro[ 'dia' ] !== $hoy ) {
            $registro[ 'dia' ]   = $hoy;
            $registro[ 'veces' ] = 0;
            $registro[ 'aviso' ] = 0;
        }
        $registro[ 'veces' ]++;

        $umbral = max( 1, (int) apply_filters( 'apg_video_sitemap_umbral_repeticion', 5 ) );
        if ( $registro[ 'veces' ] > $umbral && empty( $registro[ 'aviso' ] ) ) {
            $registro[ 'aviso' ] = 1;
            /* translators: 1: video identifier, 2: video provider, 3: number of times. */
            self::anota_en_el_registro( sprintf( __( 'The video %1$s (%2$s) has been processed %3$d times in 24 hours. Something is queueing it again in a loop.', 'google-video-sitemap-feed-with-multisite-support' ), $identificador, $proveedor, $registro[ 'veces' ] ) );
        }

        $datos = self::obtiene_informacion( $identificador, $proveedor );

        $registro[ 'proveedor' ] = $proveedor;
        $registro[ 'revisado' ]  = time();
        if ( $datos ) {
            $registro[ 'intentos' ] = 0;
            $registro[ 'estado' ]   = 'ok';
        } else {
            $registro[ 'intentos' ] = (int) $registro[ 'intentos' ] + 1;
            //A la tercera se abandona, se deja constancia y no se reintenta.
            if ( $registro[ 'intentos' ] >= self::dame_intentos() ) {
                //Sólo se anota al abandonar: si no, un bucle llenaría el registro de repeticiones.
                if ( 'abandonado' !== $registro[ 'estado' ] ) {
                    /* translators: 1: video identifier, 2: number of attempts. */
                    self::anota_en_el_registro( sprintf( __( 'The video %1$s could not be read after %2$d attempts. It will not be tried again until its page changes.', 'google-video-sitemap-feed-with-multisite-support' ), $identificador, $registro[ 'intentos' ] ) );
                }
                $registro[ 'estado' ] = 'abandonado';
            } else {
                $registro[ 'estado' ] = self::$perdido ? 'perdido' : 'error';
            }
        }

        $registros[ $identificador ] = $registro;
        self::guarda_registros( $registros );
    }

    /**
     * Programa una revisión de los vídeos, sin duplicarla si ya hay una esperando.
     *
     * @return void
     */
    static public function programa_revision() {
        if ( ! function_exists( 'as_schedule_single_action' ) || ! function_exists( 'as_has_scheduled_action' ) ) {
            return;
        }

        //Lleva su propio motivo para no confundirse con la pasada diaria, que ya está encolada.
        $argumentos = [ 'motivo' => 'contenido' ];
        if ( as_has_scheduled_action( 'apg_video_sitemap_revision', $argumentos, 'apg_video_sitemap' ) ) {
            return;
        }

        as_schedule_single_action( time() + MINUTE_IN_SECONDS, 'apg_video_sitemap_revision', $argumentos, 'apg_video_sitemap' );
    }

    /**
     * Garantiza que exista la pasada diaria, sin consultar la cola en cada petición.
     *
     * @return void
     */
    static public function asegura_revision_diaria() {
        if ( get_option( 'apg_video_sitemap_revision_diaria' ) === APG_VIDEO_SITEMAP_VERSION ) {
            return;
        }

        if ( ! function_exists( 'as_schedule_recurring_action' ) || ! function_exists( 'as_has_scheduled_action' ) ) {
            return;
        }

        if ( ! as_has_scheduled_action( 'apg_video_sitemap_revision', [], 'apg_video_sitemap' ) ) {
            //La primera pasada, en un minuto; a partir de ahí, una al día.
            as_schedule_recurring_action( time() + MINUTE_IN_SECONDS, DAY_IN_SECONDS, 'apg_video_sitemap_revision', [], 'apg_video_sitemap' );
        }

        //Autocargada: se consulta en cada init como guarda barata.
        update_option( 'apg_video_sitemap_revision_diaria', APG_VIDEO_SITEMAP_VERSION, true );
    }

    /**
     * Recorre las entradas y encola sólo los vídeos que de verdad toca revisar.
     *
     * @return void
     */
    static public function procesamiento() {
        if ( ! function_exists( 'as_schedule_single_action' ) || ! function_exists( 'as_has_scheduled_action' ) ) {
            return;
        }

        $entradas = self::consulta();
        if ( empty( $entradas ) ) {
            return;
        }

        $registros = self::dame_registros();
        $vistos    = [];
        foreach ( $entradas as $entrada ) {
            foreach ( self::busca_videos( $entrada->contenido, [] ) as $video_buscado ) {
                $identificador = $video_buscado[ 'identificador' ];
                //El vídeo autoalojado no consulta ninguna API: no hay nada que precargar.
                if ( $video_buscado[ 'proveedor' ] === 'local' || isset( $vistos[ $identificador ] ) ) {
                    continue;
                }
                $vistos[ $identificador ] = true;

                if ( ! self::hay_que_revisar( $registros, $identificador ) ) {
                    continue; //Sus datos siguen vigentes, o está abandonado
                }

                $argumentos = [
                    'identificador' => $identificador,
                    'proveedor'     => $video_buscado[ 'proveedor' ],
                ];

                //Idempotencia: si ya hay una acción igual esperando, no se añade otra.
                if ( as_has_scheduled_action( 'apg_video_sitemap_procesamiento', $argumentos, 'apg_video_sitemap' ) ) {
                    continue;
                }

                as_schedule_single_action( time() + MINUTE_IN_SECONDS, 'apg_video_sitemap_procesamiento', $argumentos, 'apg_video_sitemap' );
            }
        }

        //Los vídeos que ya no están en ninguna entrada dejan de ocupar sitio.
        $sobrantes = array_diff_key( $registros, $vistos );
        if ( $sobrantes ) {
            self::guarda_registros( array_intersect_key( $registros, $vistos ) );
        }
    }
}
new APG_Video_Sitemap();
