<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;
use IcanBundle\Util;

class CategoriaController extends BaseController
{

    public function indexAction()
    {

        $categorias = $this->ListarCategoriasArbol();
        return $this->render('IcanBundle:Categoria:index.html.twig', array(
            'categorias' => $categorias
        ));
    }

    /**
     * listarAction Acción que lista las categorias
     *
     */
    public function listarAction(Request $request)
    {
        $parent = $request->get('parent');

        try {
            $data = $this->ListarCategorias($parent);

            return new Response(json_encode($data));

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
    public function salvarAction(Request $request)
    {
        $categoria_id = $request->get('categoria_id');

        $nombre = $request->get('nombre');
        $titulo = $request->get('titulo');
        $descripcion = $request->get('descripcion');
        $tags = $request->get('tags');
        $categoria = $request->get('categoria_padre_id');
        $estado = $request->get('estado');

        $resultadoJson = array();
        if ($categoria_id == "")
            $resultado = $this->SalvarCategoria($categoria, $nombre, $titulo, $descripcion, $tags, $estado);
        else
            $resultado = $this->ActualizarCategoria($categoria_id, $categoria, $nombre, $titulo, $descripcion, $tags, $estado);

        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['message'] = "La operación se realizó correctamente";

            $categorias = $this->ListarCategoriasArbol();
            $resultadoJson['categorias'] = $categorias;

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
    public function eliminarAction(Request $request)
    {
        $categoria_id = $request->get('categoria_id');

        $resultado = $this->EliminarCategoria($categoria_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['message'] = "La operación se realizó correctamente";

            $categorias = $this->ListarCategoriasArbol();
            $resultadoJson['categorias'] = $categorias;

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
    public function cargarDatosAction(Request $request)
    {
        $categoria_id = $request->get('categoria_id');

        $resultado = $this->CargarDatosCategoria($categoria_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['categoria'] = $resultado['categoria'];
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * CargarDatosCategoria: Carga los datos de un usuario
     *
     * @param int $categoria_id Id
     *
     * @author Marcel
     */
    public function CargarDatosCategoria($categoria_id)
    {
        $resultado = array();
        $arreglo_resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
            ->find($categoria_id);
        if ($entity != null) {
            $arreglo_resultado['nombre'] = $entity->getNombre();
            $arreglo_resultado['titulo'] = $entity->getTitulo();
            $arreglo_resultado['tags'] = $entity->getTags();
            $arreglo_resultado['descripcion'] = $entity->getDescripcion();
            $arreglo_resultado['estado'] = ($entity->getEstado() == 1) ? true : false;
            $arreglo_resultado['categoria_padre_id'] = ($entity->getCategoriaPadre() != null) ? $entity->getCategoriaPadre()->getCategoriaId() : "";

            $resultado['success'] = true;
            $resultado['categoria'] = $arreglo_resultado;
        }
        return $resultado;
    }

    /**
     * EliminarCategoria: Elimina un categoria en la BD
     * @param int $categoria_id Id
     * @author Marcel
     */
    public function EliminarCategoria($categoria_id)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
            ->find($categoria_id);

        if ($entity != null) {
            //Productos
            $productos = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                ->ListarProductosDeCategoria($categoria_id);
            if (count($productos) > 0) {
                $resultado['success'] = false;
                $resultado['error'] = "No se pudo eliminar la categoria, porque tiene productos asociados";
                return $resultado;
            }

            //Subcategorias
            $subcategorias = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
                ->ListarCategoriasDelPadre($categoria_id);
            foreach ($subcategorias as $subcategoria) {
                //Productos
                $productos = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                    ->ListarProductosDeCategoria($subcategoria->getCategoriaId());
                if (count($productos) == 0) {
                    $em->remove($subcategoria);
                }
            }
            $em->flush();
            //Subcategorias
            $subcategorias = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
                ->ListarCategoriasDelPadre($categoria_id);
            if (count($subcategorias) != 0) {
                $resultado['success'] = false;
                $resultado['error'] = "No se pudo eliminar la categoria, porque tiene subcategorías con productos asociados";
                return $resultado;
            }

            $em->remove($entity);
            $em->flush();

            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe el registro solicitado";
        }

        return $resultado;
    }

    /**
     * ActualizarCategoria: Actualiza los datos del categoria en la BD
     *
     * @param string $categoria_id Id
     *
     * @author Marcel
     */
    public function ActualizarCategoria($categoria_id, $categoria, $nombre, $titulo, $descripcion, $tags, $estado)
    {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Categoria')->find($categoria_id);
        if ($entity != null) {
            if ($categoria_id == $categoria) {
                $resultado['success'] = false;
                $resultado['error'] = "No puede asociar la categoría sobre si misma";
                return $resultado;
            }
            $entity->setNombre($nombre);
            $entity->setTitulo($titulo);
            $entity->setDescripcion($descripcion);
            $entity->setEstado($estado);
            $entity->setTags($tags);

            $categoria_entity = $em->find('IcanBundle:Categoria', $categoria);
            if ($categoria_entity != null) {
                $entity->setCategoriaPadre($categoria_entity);
            }

            //Hacer Url           
            $url = $this->HacerUrl($nombre);
            $i = 1;
            $paux = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
                ->BuscarPorUrl($url);
            while (!empty($paux) && $paux->getCategoriaId() != $categoria_id) {
                $url = $this->HacerUrl($nombre) . "-" . $i;
                $paux = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
                    ->BuscarPorUrl($url);
                $i++;
            }

            $entity->setUrl($url);

            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe el registro solicitado";
        }
        return $resultado;
    }

    /**
     * SalvarCategoria: Guarda los datos del usuario en la BD
     *
     *
     * @author Marcel
     */
    public function SalvarCategoria($categoria, $nombre, $titulo, $descripcion, $tags, $estado)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $entity = new Entity\Categoria();

        $entity->setNombre($nombre);
        $entity->setTitulo($titulo);
        $entity->setDescripcion($descripcion);
        $entity->setEstado($estado);
        $entity->setTags($tags);

        $categoria_entity = $em->find('IcanBundle:Categoria', $categoria);
        if ($categoria_entity != null)
            $entity->setCategoriaPadre($categoria_entity);

        //Hacer Url        
        $url = $this->HacerUrl($nombre);
        $i = 1;
        $paux = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
            ->BuscarPorUrl($url);
        while (!empty($paux)) {
            $url = $this->HacerUrl($nombre) . "-" . $i;
            $paux = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
                ->BuscarPorUrl($url);
            $i++;
        }

        $entity->setUrl($url);

        $em->persist($entity);

        $em->flush();
        $resultado['success'] = true;

        return $resultado;
    }

    /**
     * ListarCategoria: Listar las categorias
     *
     * @author Marcel
     */
    public function ListarCategorias($parent)
    {
        $arreglo_resultado = array();
        $cont = 0;

        if ($parent == "#") {
            $lista = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
                ->ListarPadres();
            foreach ($lista as $value) {
                $categoria_id = $value->getCategoriaId();


                $arreglo_resultado[$cont]['id'] = $categoria_id;
                //$arreglo_resultado[$cont]['parent'] = null;
                $arreglo_resultado[$cont]['text'] = $value->getNombre();

                $icon = ($value->getEstado() == 1) ? 'fa fa-check-circle ic-color-ok' : 'fa fa-minus-circle ic-color-error';
                $arreglo_resultado[$cont]['icon'] = $icon;

                //Hijos
                $cant_hijos = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
                    ->ListarCategoriasDelPadre($categoria_id);
                $hijos = (count($cant_hijos) > 0) ? true : false;
                $arreglo_resultado[$cont]['children'] = $hijos;

                $arreglo_resultado[$cont]['state'] = array("opened" => true);
                $arreglo_resultado[$cont]['type'] = "root";
                $cont++;
            }
        } else {
            $lista = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
                ->ListarCategoriasDelPadre($parent);
            foreach ($lista as $value) {
                $categoria_id = $value->getCategoriaId();


                $arreglo_resultado[$cont]['id'] = $categoria_id;
                $arreglo_resultado[$cont]['parent'] = $parent;
                $arreglo_resultado[$cont]['text'] = $value->getNombre();

                $icon = ($value->getEstado() == 1) ? 'fa fa-check-circle ic-color-ok' : 'fa fa-minus-circle ic-color-error';
                $arreglo_resultado[$cont]['icon'] = $icon;

                //Hijos
                $cant_hijos = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
                    ->ListarCategoriasDelPadre($categoria_id);
                $hijos = (count($cant_hijos) > 0) ? true : false;
                $arreglo_resultado[$cont]['children'] = $hijos;

                $cont++;
            }
        }

        return $arreglo_resultado;
    }

    /**
     * ListarCategoriasArbol: Lista las categoria para el select en forma de arbol de la BD
     * @author Marcel
     */
    public function ListarCategoriasArbol($estado = "")
    {
        $tree = array();
        //Categoria Padres
        $categoria_padres = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
            ->ListarPadres($estado);
        //Resto de categoria
        $categoria_hijos = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
            ->ListarHijos($estado);

        for ($i = 0; $i < count($categoria_padres); $i++) {

            $value = $categoria_padres[$i];
            $categoria_id = $value->getCategoriaId();

            $subcategoria = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
                ->ListarCategoriasDelPadre($categoria_id, $estado);
            $class = (count($subcategoria) > 0) ? "optionGroup" : "";

            $element = array(
                'categoria_id' => $categoria_id,
                'categoria_padre_id' => "",
                'descripcion' => $value->getNombre(),
                'class' => $class
            );

            array_push($tree, $element);

            $tree = $this->getChilds($tree, $categoria_hijos, $categoria_id, 0, $categoria_id);

        }

        return $tree;
    }

    public function getChilds($tree, $categoria_hijos, $master_id, $class_count = 0, $categoria_padre_id)
    {
        $class_count = $class_count + 1;

        for ($i = 0; $i < count($categoria_hijos); $i++) {

            $value = $categoria_hijos[$i];

            if ($value->getCategoriaPadre()->getCategoriaId() == $master_id) {

                $hijos = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
                    ->ListarCategoriasDelPadre($value->getCategoriaId(), "");
                $class_padre_hijo = (count($hijos) > 0) ? "optionGroup" : "";

                $element = array(
                    'categoria_id' => $value->getCategoriaId(),
                    'categoria_padre_id' => $categoria_padre_id,
                    'descripcion' => $value->getNombre(),
                    'class' => "optionChild$class_count $class_padre_hijo"
                );

                array_push($tree, $element);

                $tree = $this->getChilds($tree, $categoria_hijos, $value->getCategoriaId(), $class_count, $categoria_padre_id);
            }
        }

        return $tree;
    }

}
