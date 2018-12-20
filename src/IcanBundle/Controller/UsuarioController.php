<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;

class UsuarioController extends BaseController
{
    /**
     * loginAction Acción para mostrar el formulario de login
     *
     */
    public function loginAction(Request $request) {
        $target_path = $request->get("backTo");
        return $this->render('IcanBundle:Usuario:login.html.twig', array("target_path" => $target_path));
    }

    /**
     * autenticarAction Acción para el chequear el login
     *
     */
    public function autenticarAction(Request $request) {
        $email = $request->get('email');
        $pass = $request->get('passwordcodificada');
        $remember_me = $request->get('remember');
        $target_path = $request->get("backTo");
        try {
            $resultado = $this->AutenticarLogin($email, $pass);
            if ($resultado['success']) {
                $entity = $resultado['usuario'];

                if ($remember_me == "on") {
                    $token = new RememberMeToken($entity, 'secured_area', "tretrtsdasfd3gfhfghfffghfghfghtyy");
                    $this->get('security.token_storage')->setToken($token);
                } else {
                    $token = new UsernamePasswordToken($entity, null, 'secured_area', $entity->getRoles());
                    $this->get('security.token_storage')->setToken($token);
                }
                if (is_null($target_path) && empty($target_path))
                    $target_path = "home";
                $resultadoJson['success'] = $resultado['success'];
                $resultadoJson['url'] = $this->generateUrl($target_path);
                return new Response(json_encode($resultadoJson));
            } else {
                $resultadoJson['success'] = $resultado['success'];
                $resultadoJson['error'] = $resultado['error'];
                return new Response(json_encode($resultadoJson));
            }
        } catch (\Exception $e) {
            $resultadoJson['success'] = false;
            $resultadoJson['error'] = $e->getMessage();
            $resultadoJson['target'] = $target_path;
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * denegadoAction Perfil de usuario
     *
     */
    public function denegadoAction() {
        return $this->render('IcanBundle:Usuario:denegado.html.twig', array());
    }

    /**
     * perfilAction Perfil de usuario
     *
     */
    public function perfilAction() {
        $usuario = $this->getUser();

        return $this->render('IcanBundle:Usuario:perfil.html.twig', array(
            'usuario' => $usuario
        ));
    }

    /**
     * indexAction Perfil de usuario
     *
     */
    public function indexAction() {

        $perfiles = $this->getDoctrine()->getRepository('IcanBundle:Rol')
            ->ListarOrdenados();


        return $this->render('IcanBundle:Usuario:index.html.twig', array(
            'perfiles' => $perfiles
        ));
    }

    /**
     * listarAction Acción que lista los usuarios
     *
     */
    public function listarAction(Request $request) {
        // search filter by keywords
        $query = !empty($request->get('query')) ? $request->get('query') : array();
        $sSearch = isset($query['generalSearch']) && is_string($query['generalSearch']) ? $query['generalSearch'] : '';
        //Sort
        $sort = !empty($request->get('sort')) ? $request->get('sort') : array();
        $sSortDir_0 = !empty($sort['sort']) ? $sort['sort'] : 'asc';
        $iSortCol_0 = !empty($sort['field']) ? $sort['field'] : 'nombre';
        //$start and $limit
        $pagination = !empty($request->get('pagination')) ? $request->get('pagination') : array();
        $page = !empty($pagination['page']) ? (int)$pagination['page'] : 1;
        $limit = !empty($pagination['perpage']) ? (int)$pagination['perpage'] : -1;
        $start = 0;

        try {
            $pages = 1;
            $total = $this->TotalUsuarios($sSearch);
            if ($limit > 0) {
                $pages = ceil($total / $limit); // calculate total pages
                $page = max($page, 1); // get 1 page when $_REQUEST['page'] <= 0
                $page = min($page, $pages); // get last page when $_REQUEST['page'] > $totalPages
                $start = ($page - 1) * $limit;
                if ($start < 0) {
                    $start = 0;
                }
            }

            $meta = array(
                'page' => $page,
                'pages' => $pages,
                'perpage' => $limit,
                'total' => $total,
                'field' => $iSortCol_0,
                'sort' => $sSortDir_0
            );

            $data = $this->ListarUsuarios($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

            $resultadoJson = array(
                'meta' => $meta,
                'data' => $data
            );

            return new Response(json_encode($resultadoJson));

        } catch (Exception $e) {
            $resultadoJson['success'] = false;
            $resultadoJson['error'] = $e->getMessage();

            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * salvarAction Acción que inserta un usuario en la BD
     *
     */
    public function salvarAction(Request $request) {
        $usuario_id = $request->get('usuario_id');

        $rol_id = $request->get('rol');
        $habilitado = $request->get('habilitado');
        $contrasenna = $request->get('passwordcodificada');
        $nombre = $request->get('nombre');
        $apellidos = $request->get('apellidos');
        $email = $request->get('email');

        //Info
        $rut = $request->request->get('rut');
        $telefono = $request->request->get('telefono');
        $calle = $request->request->get('calle');
        $numero = $request->request->get('numero');

        $resultadoJson = array();

        if ($usuario_id == "") {
            $resultado = $this->SalvarUsuario($rol_id, $habilitado, $contrasenna, $nombre, $apellidos,
                $email, $rut, $calle, $numero, $telefono);
        } else {
            $resultado = $this->ActualizarUsuario($usuario_id, $rol_id, $habilitado, $contrasenna, $nombre,
                $apellidos, $email, $rut, $calle, $numero, $telefono);
        }

        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['message'] = "La operación se realizó correctamente";

            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * eliminarAction Acción que elimina un rol en la BD
     *
     */
    public function eliminarAction(Request $request) {
        $usuario_id = $request->get('usuario_id');

        $resultado = $this->EliminarUsuario($usuario_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['message'] = "La operación se realizó correctamente";
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * eliminarUsuariosAction Acción que elimina varios usuarios en la BD
     *
     */
    public function eliminarUsuariosAction(Request $request) {
        $ids = $request->get('ids');

        $resultado = $this->EliminarUsuarios($ids);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['message'] = "La operación se realizó correctamente";
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * olvidoContrasennaAction Acción para recuperar la contraseña de un usuario
     *
     */
    public function olvidoContrasennaAction(Request $request) {
        $email = $request->get('email');
        try {
            $resultado = $this->RecuperarContrasenna($email);
            if ($resultado['success']) {

                $resultadoJson['success'] = $resultado['success'];
                $resultadoJson['message'] = "El proceso de recuperación de contraseña se ha iniciado con éxito, en unos momentos recibira un correo a la dirección ingresada";
                return new Response(json_encode($resultadoJson));
            } else {
                $resultadoJson['success'] = $resultado['success'];
                $resultadoJson['error'] = $resultado['error'];
                return new Response(json_encode($resultadoJson));
            }
        } catch (\Exception $e) {
            $resultadoJson['success'] = false;
            $resultadoJson['error'] = $e->getMessage();
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * activarUsuarioAction Acción que activa o desactiva un usuario
     *
     */
    public function activarUsuarioAction(Request $request) {
        $usuario_id = $request->get('usuario_id');

        $resultado = $this->ActivarUsuario($usuario_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * cargarDatosAction Acción que carga los datos del usuario en la BD
     *
     */
    public function cargarDatosAction(Request $request) {
        $usuario_id = $request->get('usuario_id');

        $resultado = $this->CargarDatosUsuario($usuario_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['usuario'] = $resultado['usuario'];
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * actualizarMisDatosAction Acción que actualiza el perfil del usuario en la BD
     *
     */
    public function actualizarMisDatosAction(Request $request) {

        $usuario_id = $request->get('usuario_id');
        $contrasenna = $request->get('password');
        $nombre = $request->get('nombre');
        $apellidos = $request->get('apellidos');
        $email = $request->get('email');

        $resultado = $this->ActualizarMisDatos($usuario_id, $contrasenna, $nombre, $apellidos, $email);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['message'] = "La operación se realizó correctamente";

            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * AutenticarLogin: Chequear el login
     *
     * @param string $email Email
     * @param string $pass Pass
     * @author Marcel
     */
    public function AutenticarLogin($email, $pass) {
        $resultado = array();

        $usuario = $this->getDoctrine()->getRepository('IcanBundle:Usuario')
            ->AutenticarLogin($email, $pass);
        if ($usuario != null) {
            if ($usuario->getHabilitado() == 1) {
                $resultado['success'] = true;
                $resultado['usuario'] = $usuario;

                $em = $this->getDoctrine()->getManager();

                $this->setTimeZone();
                $usuario->setFechaultimologin(new \DateTime());
                $em->flush();
            } else {
                $resultado['success'] = false;
                $resultado['error'] = "Su usuario ha sido bloqueado, por favor contacte con su administrador";
            }
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "Los datos de acceso son incorrectos";
        }
        return $resultado;
    }

    /**
     * ActualizarMisDatos: Actualiza los datos del usuario en la BD
     *
     * @author Marcel
     */
    public function ActualizarMisDatos($usuario_id, $contrasenna, $nombre, $apellidos, $email) {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Usuario')->find($usuario_id);
        if ($entity != null) {
            //Verificar email
            $usuario = $this->getDoctrine()->getRepository('IcanBundle:Usuario')
                ->BuscarUsuarioPorEmail($email);
            if ($usuario != null) {
                if ($entity->getUsuarioId() != $usuario->getUsuarioId()) {
                    $resultado['success'] = false;
                    $resultado['error'] = "La dirección electrónica ya está asignada a otro usuario.";
                    return $resultado;
                }
            }
            $entity->setNombre($nombre);
            $entity->setApellidos($apellidos);
            $entity->setEmail($email);

            if ($contrasenna != "") {
                $entity->setContrasenna($contrasenna);
            }


            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe el registro solicitado";
        }
        return $resultado;
    }

    /**
     * CargarDatosUsuario: Carga los datos de un usuario
     *
     * @param int $usuario_id Id
     *
     * @author Marcel
     */
    public function CargarDatosUsuario($usuario_id) {
        $resultado = array();
        $arreglo_resultado = array();

        $usuario = $this->getDoctrine()->getRepository('IcanBundle:Usuario')
            ->find($usuario_id);
        if ($usuario != null) {

            $arreglo_resultado['rol'] = $usuario->getRol()->getRolId();
            $arreglo_resultado['nombre'] = $usuario->getNombre();
            $arreglo_resultado['apellidos'] = $usuario->getApellidos();
            $arreglo_resultado['email'] = $usuario->getEmail();
            $arreglo_resultado['habilitado'] = ($usuario->getHabilitado() == 1) ? true : false;

            $usuario_info = $this->getDoctrine()->getRepository('IcanBundle:UsuarioInfo')->BuscarInfoDeUsuario($usuario_id);
            $rut = "";
            $telefono = "";
            $calle = "";
            $numero = "";
            if ($usuario_info != null) {

                $rut = $usuario_info->getRut();
                $rut = $this->FormatearRut($rut);

                $telefono = $usuario_info->getTelefono();
                $calle = $usuario_info->getCalle();
                $numero = $usuario_info->getNumero();
            }
            $arreglo_resultado['rut'] = $rut;
            $arreglo_resultado['telefono'] = $telefono;
            $arreglo_resultado['calle'] = $calle;
            $arreglo_resultado['numero'] = $numero;

            $resultado['success'] = true;
            $resultado['usuario'] = $arreglo_resultado;
        }
        return $resultado;
    }

    /**
     * ActivarUsuario: Activa/Desactiva un usuario
     * @param int $usuario_id Id del usuario
     * @author Marcel
     */
    public function ActivarUsuario($usuario_id) {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $usuario = $this->getDoctrine()->getRepository('IcanBundle:Usuario')
            ->find($usuario_id);

        if (!is_null($usuario)) {
            if ($usuario->getHabilitado() == 1) {
                $usuario->setHabilitado(0);
            } else {
                $usuario->setHabilitado(1);
            }
            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe el registro solicitado";
        }
        return $resultado;
    }

    /**
     * RecuperarContrasenna: Recupera la contrasenna de un usuario
     *
     * @param string $email Email del usuario
     * @author Marcel
     */
    public function RecuperarContrasenna($email) {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $usuario = $this->getDoctrine()->getRepository('IcanBundle:Usuario')
            ->BuscarUsuarioPorEmail($email);

        if (!is_null($usuario)) {
            $pass = strval(rand(99, 9999999999));

            //Enviar email
            $direccion_url = $this->ObtenerURL();
            $direccion_from = $this->getParameter('mailer_sender_address');

            $asunto = "Notificación de Recuperación de Contraseña";
            $contenido = "Estimado usuario, se ha generado una nueva contraseña de acceso a Ican.";
            $contenido .= "Una vez dentro del sistema podrá modificarla ingresando a la sección \"Mi Perfil\".<br>";
            $contenido .= "Su nueva contraseña es: " . $pass . ".<br>";
            $contenido .= "Gracias por preferir nuestro servicio.";

            $mensaje = new \Swift_Message();
            $mensaje->setSubject($asunto)
                ->setFrom($direccion_from)
                ->setTo($email)
                ->setBody(
                    $this->renderView(
                        'IcanBundle:Mailing:mail.html.twig',
                        array(
                            'direccion_url' => $direccion_url,
                            'asunto' => $asunto,
                            'receptor' => $usuario->getNombreCompleto(),
                            'contenido' => $contenido,
                        )
                    ),
                    'text/html'
                );

            $this->get('mailer')->send($mensaje);

            $contrasenna = sha1($pass);
            $usuario->setContrasenna($contrasenna);
            $em->flush();

            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe un usuario en nuestro sistema para el correo electrónico ingresado";
        }

        return $resultado;
    }

    /**
     * EliminarUsuario: Elimina un usuario en la BD
     * @param int $usuario_id Id del usuario
     * @author Marcel
     */
    public function EliminarUsuario($usuario_id) {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $usuario = $this->getDoctrine()->getRepository('IcanBundle:Usuario')
            ->find($usuario_id);

        if ($usuario != null) {

            //Cotizaciones
            $cotizaciones = $this->getDoctrine()->getRepository('IcanBundle:Cotizacion')
                ->ListarCotizacionesDeUsuario($usuario_id);
            if (count($cotizaciones) > 0) {
                $resultado['success'] = false;
                $resultado['error'] = "No se pudo eliminar el usuario, porque tiene cotizaciones asociadas";
                return $resultado;
            }

            //Comprar el usuario actual
            $user_logued = $this->getUser();
            if ($usuario->getUsuarioId() == $user_logued->getUsuarioId()) {
                $resultado['success'] = false;
                $resultado['error'] = "No se puede eliminar el usuario actual logueado en el sistema";
                return $resultado;
            }

            //Info
            $info = $em->getRepository('IcanBundle:UsuarioInfo')->BuscarInfoDeUsuario($usuario_id);
            if ($info != null) {
                $em->remove($info);
            }

            $em->remove($usuario);

            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe el registro solicitado";
        }

        return $resultado;
    }

    /**
     * EliminarUsuarios: Elimina varios usuarios en la BD
     * @param array $$ids Ids
     * @author Marcel
     */
    public function EliminarUsuarios($ids) {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        if ($ids != "") {
            $ids = explode(',', $ids);
            $cant_eliminada = 0;
            $cant_total = 0;
            foreach ($ids as $usuario_id) {
                if ($usuario_id != "") {
                    $cant_total++;
                    $usuario = $this->getDoctrine()->getRepository('IcanBundle:Usuario')
                        ->find($usuario_id);

                    if ($usuario != null) {
                        //Comprar el usuario actual
                        $user_logued = $this->getUser();

                        //Cotizaciones
                        $cotizaciones = $this->getDoctrine()->getRepository('IcanBundle:Cotizacion')
                            ->ListarCotizacionesDeUsuario($usuario_id);

                        if ($usuario->getUsuarioId() != $user_logued->getUsuarioId() && count($cotizaciones) == 0) {

                            //Info
                            $info = $em->getRepository('IcanBundle:UsuarioInfo')->BuscarInfoDeUsuario($usuario_id);
                            if ($info != null) {
                                $em->remove($info);
                            }

                            $em->remove($usuario);
                            $cant_eliminada++;
                        }
                    }
                }
            }
        }
        $em->flush();

        if ($cant_eliminada == 0) {
            $resultado['success'] = false;
            $resultado['error'] = "No se puede eliminar los usuarios seleccionados porque tienen cotizaciones asociadas";
        } else {
            $resultado['success'] = true;

            $mensaje = ($cant_eliminada == $cant_total) ? "La operación se ha realizado correctamente" : "La operación se ha realizado correctamente. Pero atención no se pudo eliminar todos los usuarios seleccionados porque tienen cotizaciones asociadas";
            $resultado['message'] = $mensaje;
        }

        return $resultado;
    }

    /**
     * ActualizarUsuario: Actualiza los datos del usuario en la BD
     *
     *
     * @author Marcel
     */
    public function ActualizarUsuario($usuario_id, $rol_id, $habilitado, $contrasenna,
                                      $nombre, $apellidos, $email, $rut, $calle, $numero, $telefono) {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Usuario')->find($usuario_id);
        if ($entity != null) {
            //Verificar email
            $usuario = $this->getDoctrine()->getRepository('IcanBundle:Usuario')
                ->BuscarUsuarioPorEmail($email);
            if ($usuario != null) {
                if ($usuario_id != $usuario->getUsuarioId()) {
                    $resultado['success'] = false;
                    $resultado['error'] = "La dirección electrónica ya está asignada a otro usuario.";
                    return $resultado;
                }
            }

            $entity->setNombre($nombre);
            $entity->setApellidos($apellidos);
            $entity->setEmail($email);
            $entity->setHabilitado($habilitado);

            if ($contrasenna != "") {
                $entity->setContrasenna($contrasenna);
            }

            $rol = $this->getDoctrine()->getRepository('IcanBundle:Rol')->find($rol_id);
            if ($rol != null) {
                $entity->setRol($rol);
            }

            //Info
            if ($rol_id != 1) {

                $usuario_info = $em->getRepository('IcanBundle:UsuarioInfo')->BuscarInfoDeUsuario($usuario_id);
                $is_new = false;
                if ($usuario_info == null) {
                    $usuario_info = new Entity\UsuarioInfo();
                    $is_new = true;
                }

                $rut = $this->LimpiarRut($rut);
                $usuario_info->setRut($rut);

                $usuario_info->setTelefono($telefono);
                $usuario_info->setCalle($calle);
                $usuario_info->setNumero($numero);

                if ($is_new) {
                    $usuario_info->setUsuario($entity);
                    $em->persist($usuario_info);
                }
            }


            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe el registro solicitado";
        }
        return $resultado;
    }

    /**
     * SalvarUsuario: Guarda los datos del usuario en la BD
     *
     *
     * @author Marcel
     */
    public function SalvarUsuario($rol_id, $habilitado, $contrasenna, $nombre, $apellidos,
                                  $email, $rut, $calle, $numero, $telefono) {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        //Verificar email
        $usuario = $this->getDoctrine()->getRepository('IcanBundle:Usuario')
            ->BuscarUsuarioPorEmail($email);
        if ($usuario != null) {
            $resultado['success'] = false;
            $resultado['error'] = "La dirección electrónica ya está asignada a otro usuario.";
            return $resultado;
        }
        $entity = new Entity\Usuario();

        $entity->setContrasenna($contrasenna);
        $entity->setNombre($nombre);
        $entity->setApellidos($apellidos);
        $entity->setEmail($email);

        $rol = $this->getDoctrine()->getRepository('IcanBundle:Rol')->find($rol_id);
        if ($rol != null)
            $entity->setRol($rol);

        $entity->setHabilitado($habilitado);

        $this->setTimeZone();
        $entity->setFecharegistro(new \DateTime());

        $em->persist($entity);

        //Info
        if ($rol_id != 1) {
            $usuario_info = new Entity\UsuarioInfo();

            $rut = $this->LimpiarRut($rut);
            $usuario_info->setRut($rut);

            $usuario_info->setTelefono($telefono);
            $usuario_info->setCalle($calle);
            $usuario_info->setNumero($numero);

            $usuario_info->setUsuario($entity);
            $em->persist($usuario_info);
        }

        $em->flush();

        $resultado['success'] = true;

        return $resultado;
    }

    /**
     * ListarUsuarios: Listar los usuarios
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarUsuarios($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0) {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Usuario')
            ->ListarUsuarios($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

        foreach ($lista as $value) {
            $usuario_id = $value->getUsuarioId();

            $acciones = $this->ListarAcciones($usuario_id);

            $arreglo_resultado[$cont] = array(
                "id" => $usuario_id,
                'email' => $value->getEmail(),
                'nombre' => $value->getNombre(),
                'apellidos' => $value->getApellidos(),
                'habilitado' => ($value->getHabilitado()) ? 1 : 0,
                'perfil' => $value->getRol()->getNombre(),
                "acciones" => $acciones
            );

            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * TotalUsuarios: Total de usuarios
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalUsuarios($sSearch) {
        $total = $this->getDoctrine()->getRepository('IcanBundle:Usuario')
            ->TotalUsuarios($sSearch);

        return $total;
    }

    /**
     * ListarAcciones: Lista las acciones de un usuario en la tabla
     * @param string $nick Usuario
     * @author Marcel
     */
    public function ListarAcciones($id) {
        $acciones = "";

        $acciones .= '<a href="javascript:;" class="edit m-portlet__nav-link btn m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill" title="Editar registro" data-id="' . $id . '"> <i class="la la-edit"></i> </a> ';
        $acciones .= '<a href="javascript:;" class="block m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Activar/Inactivar registro" data-id="' . $id . '"> <i class="la la-lock"></i> </a> ';
        $acciones .= ' <a href="javascript:;" class="delete m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Eliminar registro" data-id="' . $id . '"><i class="la la-trash"></i></a>';

        return $acciones;
    }

}
