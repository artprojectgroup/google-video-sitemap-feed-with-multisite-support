<?php
/*
Genera la plantilla XML
*/
//Igual no deberías poder abrirme
defined( 'ABSPATH' ) || exit;

/*
Todo va dentro de una función anónima que se ejecuta al vuelo: la plantilla se carga con
load_template() en el ámbito global y, si no, cada variable de trabajo quedaría suelta ahí.
*/
( static function () {
	global $wp_query, $wp;

	//Obtiene todas las entradas que contienen algún vídeo
	$entradas = APG_Video_Sitemap::consulta();
	$entradas = is_array( $entradas ) ? $entradas : [];

	//Añade la cabecera
	status_header( 200 );
	header( 'Content-Type: text/xml; charset=' . get_bloginfo( 'charset' ), true );

	$generado = gmdate( 'Y-m-d\TH:i:s+00:00' );
	$paginas  = (int) ceil( count( $entradas ) / APG_VIDEO_SITEMAP_MAXIMO );

	//Hay que dividir el sitemap en varios
	$numero_feed = (int) get_query_var( 'sitemap_video_feed', 0 );
	if ( ! $numero_feed && ! empty( $wp->request ) ) {
		//Compatibilidad con los enlaces permanentes generados antes de registrar la variable.
		preg_match( '#(?:^|/)sitemap-video-(\d+)\.xml$#', $wp->request, $coincidencia );
		$numero_feed = isset( $coincidencia[ 1 ] ) ? (int) $coincidencia[ 1 ] : 0;
	}
	if ( $numero_feed > $paginas ) {
		$numero_feed = 0;
	}

	if ( $paginas > 1 && ! $numero_feed ) {
		echo '<?xml version="1.0" encoding="' . esc_attr( get_bloginfo( 'charset' ) ) . '"?>
<!-- Created by APG Google Video Sitemap Feed by Art Project Group (https://artprojectgroup.es/plugins-para-wordpress/apg-google-video-sitemap-feed) -->
<!-- generated-on="' . esc_xml( $generado ) . '" -->
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
		for ( $i = 1; $i <= $paginas; $i++ ) {
			echo "\t" . '<sitemap>' . PHP_EOL;
			echo "\t\t" . '<loc>' . esc_url( home_url( "/sitemap-video-$i.xml" ) ) . '</loc>' . PHP_EOL;
			echo "\t" . '</sitemap>' . PHP_EOL;
		}
		echo '</sitemapindex>';

		return;
	}

	//Inicia la plantilla
	echo '<?xml version="1.0" encoding="' . esc_attr( get_bloginfo( 'charset' ) ) . '"?>
<!-- Created by APG Google Video Sitemap Feed by Art Project Group (https://artprojectgroup.es/plugins-para-wordpress/apg-google-video-sitemap-feed) -->
<!-- Generated-on="' . esc_xml( $generado ) . '" -->
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">' . PHP_EOL;

	$wp_query->is_404 = false;
	$wp_query->is_feed = true;

	if ( $numero_feed ) {
		$offset   = ( $numero_feed - 1 ) * APG_VIDEO_SITEMAP_MAXIMO;
		$entradas = array_slice( $entradas, $offset, APG_VIDEO_SITEMAP_MAXIMO, true );
	}

	//Se recorre por lotes para poder precargar de golpe entradas, campos y etiquetas.
	foreach ( array_chunk( $entradas, 200, true ) as $lote ) {
		if ( function_exists( '_prime_post_caches' ) ) {
			_prime_post_caches( array_keys( $lote ), true, true );
		}

		foreach ( $lote as $entrada ) {
			$videos_buscados = APG_Video_Sitemap::busca_videos( $entrada->contenido, [] );
			if ( empty( $videos_buscados ) ) {
				continue;
			}

			//Primero se resuelven todos los vídeos de la página: si ninguno vale, no se pinta <url>.
			$validos = [];
			foreach ( $videos_buscados as $video_buscado ) {
				$datos = APG_Video_Sitemap::dame_datos_del_video( $video_buscado, $entrada );
				if ( ! $datos ) {
					continue;
				}

				//Google exige miniatura, título y una de content_loc o player_loc.
				if ( empty( $datos[ 'imagen' ] ) || empty( $datos[ 'titulo' ] ) ) {
					continue;
				}
				if ( empty( $datos[ 'contenido' ] ) && empty( $datos[ 'reproductor' ] ) ) {
					continue;
				}

				$descripcion = trim( wp_strip_all_tags( (string) $datos[ 'descripcion' ] ) );
				if ( '' === $descripcion ) {
					$descripcion = trim( wp_strip_all_tags( (string) $entrada->extracto ) );
				}
				if ( '' === $descripcion ) {
					$descripcion = $datos[ 'titulo' ];
				}
				//Google descarta la descripción si pasa de 2048 caracteres.
				$datos[ 'descripcion' ] = wp_html_excerpt( $descripcion, 2048 );
				//La duración válida para Google va de 1 segundo a 8 horas.
				$datos[ 'duracion' ]    = ( $datos[ 'duracion' ] > 0 && $datos[ 'duracion' ] <= 28800 ) ? $datos[ 'duracion' ] : 0;
				if ( '' === $datos[ 'publicado' ] ) {
					$datos[ 'publicado' ] = get_gmt_from_date( $entrada->fecha, 'c' );
				}

				$validos[] = $datos;
			}

			if ( empty( $validos ) ) {
				continue;
			}

			$etiquetas = get_the_tags( $entrada->ID );
			$etiquetas = ( $etiquetas && ! is_wp_error( $etiquetas ) ) ? $etiquetas : [];

			echo "\t" . '<url>' . PHP_EOL;
			echo "\t\t" . '<loc>' . esc_url( get_permalink( $entrada->ID ) ) . '</loc>' . PHP_EOL;
			echo "\t\t" . '<lastmod>' . esc_xml( get_gmt_from_date( $entrada->modificada, 'c' ) ) . '</lastmod>' . PHP_EOL;

			//Todos los vídeos de una misma página van dentro de su único <url>.
			foreach ( $validos as $datos ) {
				echo "\t\t" . '<video:video>' . PHP_EOL;
				echo "\t\t\t" . '<video:thumbnail_loc>' . esc_url( $datos[ 'imagen' ] ) . '</video:thumbnail_loc>' . PHP_EOL;
				echo "\t\t\t" . '<video:title>' . esc_xml( $datos[ 'titulo' ] ) . '</video:title>' . PHP_EOL;
				echo "\t\t\t" . '<video:description>' . esc_xml( $datos[ 'descripcion' ] ) . '</video:description>' . PHP_EOL;
				//El orden de las etiquetas lo fija el esquema sitemap-video/1.1 de Google.
				if ( ! empty( $datos[ 'contenido' ] ) ) {
					echo "\t\t\t" . '<video:content_loc>' . esc_url( $datos[ 'contenido' ] ) . '</video:content_loc>' . PHP_EOL;
				}
				if ( ! empty( $datos[ 'reproductor' ] ) ) {
					echo "\t\t\t" . '<video:player_loc>' . esc_url( $datos[ 'reproductor' ] ) . '</video:player_loc>' . PHP_EOL;
				}
				if ( $datos[ 'duracion' ] ) {
					echo "\t\t\t" . '<video:duration>' . absint( $datos[ 'duracion' ] ) . '</video:duration>' . PHP_EOL;
				}
				if ( ! empty( $datos[ 'publicado' ] ) ) {
					echo "\t\t\t" . '<video:publication_date>' . esc_xml( $datos[ 'publicado' ] ) . '</video:publication_date>' . PHP_EOL;
				}

				$numero_de_etiquetas = 0;
				foreach ( $etiquetas as $etiqueta ) {
					//Google admite un máximo de 32 etiquetas por vídeo.
					if ( $numero_de_etiquetas++ >= 32 ) {
						break;
					}
					echo "\t\t\t" . '<video:tag>' . esc_xml( $etiqueta->name ) . '</video:tag>' . PHP_EOL;
				}

				if ( ! empty( $datos[ 'autor' ] ) ) {
					//Se separan las dos ramas para que el escapado quede a la vista en el echo.
					if ( ! empty( $datos[ 'autor_url' ] ) ) {
						echo "\t\t\t" . '<video:uploader info="' . esc_url( $datos[ 'autor_url' ] ) . '">' . esc_xml( wp_html_excerpt( $datos[ 'autor' ], 255 ) ) . '</video:uploader>' . PHP_EOL;
					} else {
						echo "\t\t\t" . '<video:uploader>' . esc_xml( wp_html_excerpt( $datos[ 'autor' ], 255 ) ) . '</video:uploader>' . PHP_EOL;
					}
				}

				echo "\t\t" . '</video:video>' . PHP_EOL;
			}

			echo "\t" . '</url>' . PHP_EOL;
		}
	}
	echo '</urlset>';
} )();
