<?php

namespace FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller
{

    /**
     * ListarSliders: Devuleve los sliders del sitio
     *
     * @return array
     * @author Marcel
     */
    public function ListarSliders() {
        $arreglo_resultado = array();
        $cont = 0;

        $fecha_actual = date('Y-m-d H:i', strtotime("+1 day"));

        $sliders = $this->getDoctrine()->getRepository('IcanBundle:Slider')->ListarSliderPortada($fecha_actual);
        foreach ($sliders as $value) {

            $arreglo_resultado[$cont]['slider_id'] = $value->getSliderId();
            $arreglo_resultado[$cont]['nombre'] = $value->getNombre();
            $arreglo_resultado[$cont]['titulo'] = $value->getTitulo();
            $arreglo_resultado[$cont]['descripcion'] = $value->getDescripcion();
            $arreglo_resultado[$cont]['imagen'] = $value->getImagen();
            $arreglo_resultado[$cont]['link'] = $value->getUrl();
            $arreglo_resultado[$cont]['target'] = ($value->getFormadeabrir() == "Self") ? "_parent" : '_blank';

            $cont++;
        }
        return $arreglo_resultado;
    }

    /**
     * ListarRedesSociales: Lista las redes sociales activas
     *
     * @return array $redessociales
     * @author Marcel
     */
    public function ListarRedesSociales() {
        $arreglo_resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Redsocial')->DevolverRedSocial();
        $redsocial_id = "";
        $facebook = "";
        $activarfacebook = false;
        $youtube = "";
        $activaryoutube = false;
        $twitter = "";
        $activartwitter = false;
        $flickr = "";
        $activarflickr = false;
        $instagram = "";
        $activarinstagram = false;
        $linkedin = "";
        $activarlinkedin = false;
        $soundcloud = "";
        $activarsoundcloud = false;
        $tumblr = "";
        $activartumblr = false;
        $vimeo = "";
        $activarvimeo = false;
        $google = "";
        $activargoogle = false;

        if ($entity != null) {
            $redsocial_id = $entity->getRedsocialId();

            $facebook = $entity->getFacebook();
            $activarfacebook = ($entity->getActivarfacebook() == 1) ? true : false;
            $youtube = $entity->getYoutube();
            $activaryoutube = ($entity->getActivaryoutube() == 1) ? true : false;
            $twitter = $entity->getTwitter();
            $activartwitter = ($entity->getActivartwitter() == 1) ? true : false;
            $flickr = $entity->getFlickr();
            $activarflickr = ($entity->getActivarflickr() == 1) ? true : false;
            $instagram = $entity->getInstagram();
            $activarinstagram = ($entity->getActivarinstagram() == 1) ? true : false;
            $linkedin = $entity->getLinkedin();
            $activarlinkedin = ($entity->getActivarlinkedin() == 1) ? true : false;
            $soundcloud = $entity->getSoundcloud();
            $activarsoundcloud = ($entity->getActivarsoundcloud() == 1) ? true : false;
            $tumblr = $entity->getTumblr();
            $activartumblr = ($entity->getActivartumblr() == 1) ? true : false;
            $vimeo = $entity->getVimeo();
            $activarvimeo = ($entity->getActivarvimeo() == 1) ? true : false;
            $google = $entity->getGoogle();
            $activargoogle = ($entity->getActivargoogle() == 1) ? true : false;
        }

        $arreglo_resultado['redsocial_id'] = $redsocial_id;
        $arreglo_resultado['facebook'] = $facebook;
        $arreglo_resultado['activarfacebook'] = $activarfacebook;
        $arreglo_resultado['youtube'] = $youtube;
        $arreglo_resultado['activaryoutube'] = $activaryoutube;
        $arreglo_resultado['twitter'] = $twitter;
        $arreglo_resultado['activartwitter'] = $activartwitter;
        $arreglo_resultado['flickr'] = $flickr;
        $arreglo_resultado['activarflickr'] = $activarflickr;
        $arreglo_resultado['instagram'] = $instagram;
        $arreglo_resultado['activarinstagram'] = $activarinstagram;
        $arreglo_resultado['linkedin'] = $linkedin;
        $arreglo_resultado['activarlinkedin'] = $activarlinkedin;
        $arreglo_resultado['soundcloud'] = $soundcloud;
        $arreglo_resultado['activarsoundcloud'] = $activarsoundcloud;
        $arreglo_resultado['tumblr'] = $tumblr;
        $arreglo_resultado['activartumblr'] = $activartumblr;
        $arreglo_resultado['vimeo'] = $vimeo;
        $arreglo_resultado['activarvimeo'] = $activarvimeo;
        $arreglo_resultado['google'] = $google;
        $arreglo_resultado['activargoogle'] = $activargoogle;

        return $arreglo_resultado;
    }

    /**
     * ListarUltimosProductos: Lista los uultimos productos
     *
     * @return array $productos
     * @author Marcel
     */
    public function ListarUltimosProductos() {
        $arreglo_resultado = array();
        $cont = 0;
        $fecha_actual = date('Y-m-d H:i', strtotime("+1 day"));
        $limit = 8;
        $lista = $this->getDoctrine()->getRepository('IcanBundle:Producto')->ListarUltimosProductos($fecha_actual, $limit);

        foreach ($lista as $value) {
            $producto_id = $value->getProductoId();

            $arreglo_resultado[$cont]['producto_id'] = $producto_id;

            $arreglo_resultado[$cont]['nombre'] = $value->getNombre();
            $arreglo_resultado[$cont]['descripcion'] = $value->getDescripcion();
            $arreglo_resultado[$cont]['stock'] = $value->getStock();
            $arreglo_resultado[$cont]['mostrarPrecio'] = true;
            $arreglo_resultado[$cont]['precioOferta'] = 100;
            $arreglo_resultado[$cont]['precio'] = number_format($value->getPrecio(), 0, ',', '.');
            $arreglo_resultado[$cont]['imagen'] = $value->getImagen();
            $arreglo_resultado[$cont]['url'] = $value->getUrl();

            $arreglo_resultado[$cont]['categoria'] = $value->getCategoria();
            $arreglo_resultado[$cont]['marca'] = $value->getMarca();

            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * ListarCategorias: Lista las categorias de la BD
     * @author Marcel
     */
    public function ListarCategorias() {
        $arreglo_resultado = array();
        $cont = 0;

        //Categorias Padres
        $categorias_padres = $this->getDoctrine()->getRepository('IcanBundle:Categoria')->ListarPadresActivos();
        foreach ($categorias_padres as $categoria) {
            $categoria_id = $categoria->getCategoriaId();

            $arreglo_resultado[$cont]['categoria_id'] = $categoria_id;
            $arreglo_resultado[$cont]['nombre'] = $categoria->getNombre();
            $arreglo_resultado[$cont]['url'] = $categoria->getUrl();
            //Subcategorias
            $subcategorias = array();
            $cont_sub = 0;
            $lista_sub = $this->getDoctrine()->getRepository('IcanBundle:Categoria')->ListarCategoriasDelPadreOrdenadas($categoria_id);
            foreach ($lista_sub as $subcategoria) {
                $subcategorias[$cont_sub]['categoria_id'] = $subcategoria->getCategoriaId();
                $subcategorias[$cont_sub]['nombre'] = $subcategoria->getNombre();
                $subcategorias[$cont_sub]['url'] = $subcategoria->getUrl();
                $cont_sub++;
            }
            $arreglo_resultado[$cont]['subcategorias'] = $subcategorias;
            $cont++;
        }
        return $arreglo_resultado;
    }

    /**
     * ListarMarcasRandom: Lista 3 marcas randoms
     *
     * @return array marcas
     */
    public function ListarMarcasRandom() {

        $ramdoms = $this->getDoctrine()->getRepository("IcanBundle:Marca")->ListarOrdenadas();

        $total_ramdom = count($ramdoms);
        if ($total_ramdom >= 10) {
            $posiciones = array_rand($ramdoms, 10);
            $resultado = array();
            $resultado[0] = $ramdoms[$posiciones[0]];
            $resultado[1] = $ramdoms[$posiciones[1]];
            $resultado[2] = $ramdoms[$posiciones[2]];
            $resultado[3] = $ramdoms[$posiciones[3]];
            $resultado[4] = $ramdoms[$posiciones[4]];
            $resultado[5] = $ramdoms[$posiciones[5]];
            $resultado[6] = $ramdoms[$posiciones[6]];
            $resultado[7] = $ramdoms[$posiciones[7]];
            $resultado[8] = $ramdoms[$posiciones[8]];
            $resultado[9] = $ramdoms[$posiciones[9]];
            $ramdoms = $resultado;
        }


        return $ramdoms;
    }

    /**
     * DevolverMes: Devolver mes
     *
     * @author Marcel
     */
    public function DevolverMes($mes) {
        $nombre = "";
        switch ($mes) {
            case "1":
                $nombre = "Enero";
                break;
            case "2":
                $nombre = "Febrero";
                break;
            case "3":
                $nombre = "Marzo";
                break;
            case "4":
                $nombre = "Abril";
                break;
            case "5":
                $nombre = "Mayo";
                break;
            case "6":
                $nombre = "Junio";
                break;
            case "7":
                $nombre = "Julio";
                break;
            case "8":
                $nombre = "Agosto";
                break;
            case "9":
                $nombre = "Septiembre";
                break;
            case "10":
                $nombre = "Octubre";
                break;
            case "11":
                $nombre = "Noviembre";
                break;
            case "12":
                $nombre = "Diciembre";
                break;
            default:
                break;
        }
        return $nombre;
    }

    //Obterner bien la url
    public function ObtenerURL() {
        $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
        $protocol = $this->strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/") . $s;
        $ruta = $this->generateUrl('homefrontend');
        if (substr_count($ruta, 'app_dev.php') > 0 || substr_count($ruta, 'app.php') > 0) {
            $ruta = $this->generateUrl('home') . '../';
        } else {
            $ruta = $this->generateUrl('homefrontend');
        }
        $direccion_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . $ruta;

        return $direccion_url;
    }

    public function strleft($s1, $s2) {
        return substr($s1, 0, strpos($s1, $s2));
    }

}
