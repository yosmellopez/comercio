<?php

namespace IcanBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Transbank\Webpay\Configuration;
use Transbank\Webpay\Webpay;

class BaseController extends Controller
{

    //Is mobile
    public function isMobile() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }

    public function writelog($txt, $filename = "weblog.txt") {
        global $path_logs;

        $datetime = date("Y-m-d H:i:s", time());
        $txt = $datetime . "\t---\t" . $txt . "\n";

        if ($path_logs != "") {
            $filename = $path_logs . $filename;
        }

        $fp = fopen($filename, "a");
        if ($fp) {
            fputs($fp, $txt, strlen($txt));
            fclose($fp);
        } else {
            die("Error IO: writing file '{$filename}'");
        }
    }

    // Original PHP code by Chirp Internet: www.chirp.com.au
    // Please acknowledge use of this code by including this header.
    // http://www.the-art-of-web.com/php/truncate/
    function truncate($string, $limit, $break = ".", $pad = "...") {
        $string = strip_tags($string);
        // return with no change if string is shorter than $limit
        if (strlen($string) <= $limit)
            return $string;

        // is $break present between $limit and the end of the string?
        $string = substr($string, 0, $limit) . $pad;

        return $string;
    }

    /**
     * HacerUrl
     */
    public function HacerUrl($tex) {
        // Código copiado de http://cubiq.org/the-perfect-php-clean-url-generator
        setlocale(LC_ALL, 'en_US.UTF8');
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $tex);
        $slug = preg_replace_callback("/[^a-zA-Z0-9\/_|+ -]/", function ($c) {
            return '';
        }, $slug);
        $slug = strtolower(trim($slug, '-'));
        $slug = preg_replace_callback("/[\/_|+ -]+/", function ($c) {
            return '-';
        }, $slug);

        return $slug;

        /*$eliminar = array("!", "¡", "?", "¿", "‘", "\"", "$", "(", ")", ".", ":", ";", "_", "/", "\\", "\$", "%", "@", "#", ",", "«", "»");
        $buscados = array(" ", "á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "Ñ", "ü", "à", "è", "ì", "ò", "ù", "À", "È", "Ì", "Ò", "Ù");
        $sustitut = array("-", "a", "e", "i", "o", "u", "a", "e", "i", "o", "u", "n", "n", "u", "a", "e", "i", "o", "u", "A", "E", "I", "O", "U");
        $final = strtolower(str_replace($buscados, $sustitut, str_replace($eliminar, "", $cadena)));
        $final = str_replace("–", "-", $final);
        $final = str_replace("–", "-", $final);
        return $final;*/
    }

    //Obterner bien la url
    public function ObtenerURL() {
        $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
        $protocol = $this->strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), " / ") . $s;

        $ruta = $this->generateUrl('homefrontend');
        if (substr_count($ruta, 'app_dev.php') > 0 || substr_count($ruta, 'app.php') > 0) {
            $ruta = $this->generateUrl('homefrontend') . '../';
        } else {
            $ruta = $this->generateUrl('homefrontend');
        }

        $direccion_url = "http" . "://" . $_SERVER['HTTP_HOST'] . $ruta;

        return $direccion_url;
    }

    public function strleft($s1, $s2) {
        return substr($s1, 0, strpos($s1, $s2));
    }

    /*Devuelve el ip*/
    public function getIP() {

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        }

        return $ip;
    }

    /*
     * LimpiarRut: Remover puntos del rut
     *
     * */
    public function LimpiarRut($rut) {
        $patron = "[.]";
        $rut = mberegi_replace($patron, "", $rut);
        return $rut;
    }

    /*
     * FormatearRut: Poner puntos del rut
     *
     * */
    function FormatearRut($rut) {
        if ($rut != "") {
            $rut = $this->LimpiarRut($rut);
            $rutTmp = explode("-", $rut);
            return number_format($rutTmp[0], 0, "", ".") . '-' . $rutTmp[1];
        } else {
            return $rut;
        }

    }


    //Cambiar time zone
    function setTimeZone($zone = 'America/Santiago') {
        date_default_timezone_set($zone);
    }

    //Devolver fecha actual
    function ObtenerFechaActual($format = 'Y-m-d') {
        $this->setTimeZone();
        $fecha = date($format);

        return $fecha;
    }

    //Generar hexadecimal aleatorio
    function generarCadenaAleatoria() {
        $codigo = "";
        //Dos letras
        $codigo .= substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
        //Tres numeros
        $codigo .= "-" . substr(str_shuffle("0123456789"), 0, 6);
        //Tres numeros
        //$codigo .= "-".substr(str_shuffle("0123456789"), 0, 6);

        return $codigo;
    }

    //Generar nombre de paramatero aleatorio
    function generarParamAleatorio() {
        $codigo = "";
        //Dos letras
        $codigo .= substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);

        return $codigo;
    }

    /*
     * DevolverFechaFormatoMensajes: Devuelve la fecha del mensaje en el formato Agosto, 25 H:i
     */

    public function DevolverFechaFormatoBarras($fecha) {
        $resultado = "";

        $mes = $fecha->format('m');
        switch ($mes) {
            case "01":
                $mes = "Ene";
                break;
            case "02":
                $mes = "Feb";
                break;
            case "03":
                $mes = "Mar";
                break;
            case "04":
                $mes = "Abr";
                break;
            case "05":
                $mes = "May";
                break;
            case "06":
                $mes = "Jun";
                break;
            case "07":
                $mes = "Jul";
                break;
            case "08":
                $mes = "Ago";
                break;
            case "09":
                $mes = "Sept";
                break;
            case "10":
                $mes = "Oct";
                break;
            case "11":
                $mes = "Nov";
                break;
            case "12":
                $mes = "Dic";
                break;
            default:
                break;
        }

        $resultado = $mes . ", " . $fecha->format('j');

        return $resultado;
    }

    /**
     * ListarMensajesUltimosDias: Lista los mensajes ultimos 30 dias
     *
     *
     * @author Marcel
     */
    public function ListarMensajesUltimosDias() {
        $arreglo_resultado = array();
        $cont = 0;

        //$fecha_inicial = date('Y-m-d H:i:s', strtotime("-30 day"));
        //$fecha_final = date('Y-m-d H:i:s');

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Mensaje')
            ->ListarMensajesRangoFecha("", "", 10);

        foreach ($lista as $value) {
            $arreglo_resultado[$cont]['mensaje_id'] = $value->getMensajeId();
            $arreglo_resultado[$cont]['nombre'] = $value->getNombre();
            $arreglo_resultado[$cont]['telefono'] = $value->getTelefono();
            $arreglo_resultado[$cont]['email'] = $value->getEmail();
            $arreglo_resultado[$cont]['asunto'] = $value->getAsunto();
            $arreglo_resultado[$cont]['descripcion'] = $value->getDescripcion();

            $arreglo_resultado[$cont]['fecha'] = $this->DevolverFechaFormatoBarras($value->getFecha());

            $cont++;
        }

        return $arreglo_resultado;
    }

    //get size
    public function getSize($archivo) {
        $size = 0;
        if (is_file($archivo)) {
            $size = filesize($archivo);
        }

        return $size;

    }

    /*
      * DevolverFechaFormatoMensajes: Devuelve la fecha del mensaje en el formato Agosto, 25 H:i
      */

    public function DevolverFechaFormatoMensajes($fecha) {
        $mes = $fecha->format('m');
        switch ($mes) {
            case "01":
                $mes = "Ene";
                break;
            case "02":
                $mes = "Feb";
                break;
            case "03":
                $mes = "Mar";
                break;
            case "04":
                $mes = "Abr";
                break;
            case "05":
                $mes = "May";
                break;
            case "06":
                $mes = "Jun";
                break;
            case "07":
                $mes = "Jul";
                break;
            case "08":
                $mes = "Ago";
                break;
            case "09":
                $mes = "Sept";
                break;
            case "10":
                $mes = "Oct";
                break;
            case "11":
                $mes = "Nov";
                break;
            case "12":
                $mes = "Dic";
                break;
            default:
                break;
        }

        $resultado = $mes . ", " . $fecha->format('j');

        return $resultado;
    }

    function generarCadenaAleatoriaMix($length = 4) {
        if ($length > 36) {
            $length = 36;
        }

        $str1 = str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");
        $str2 = str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");
        $codigo = substr($str1, 0, $length) . "-" . substr($str2, 0, $length);

        return $codigo;
    }

    /**
     * ConfigurarWebpay: Configurar la variable webpay
     *
     * @author Marcel
     */
    public function ConfigurarWebpay() {
        /*
        $webpay_config = $this->getDoctrine()->getRepository('AdminBundle:WebpayConfig')
            ->DevolverConfig();

        $configuration = new Configuration();
        $configuration->setEnvironment("PRODUCCION");
        $configuration->setCommerceCode($webpay_config->getComercioCode());
        $configuration->setPrivateKey($webpay_config->getPrivateKey());
        $configuration->setPublicCert($webpay_config->getPublicCert());
        $configuration->setWebpayCert($webpay_config->getPrivateCert());
        */


        $configuration = Configuration::forTestingWebpayPlusNormal();

        /** Creacion Objeto Webpay */
        $webpay = new Webpay($configuration);
        $webpay = $webpay->getNormalTransaction(); // Crea Transaccion Normal

        return $webpay;
    }

}
