<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CompraRepository extends EntityRepository
{

    /**
     * ListarOrdenados: Lista los compras ordenados por fecha
     *
     * @author Yosmel
     */
    public function ListarOrdenados() {
        $consulta = $this->createQueryBuilder('p')
            ->where('p.estado = 1')
            ->orderBy('p.fechapublicacion', 'DESC');

        $lista = $consulta->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarUltimosCompras: Lista los ultimos 6 compras ordenados por fecha
     *
     * @author Yosmel
     */
    public function ListarUltimosCompras($fecha_actual, $limit) {
        $consulta = $this->createQueryBuilder('p')->andWhere('p.estado = 1');

        if ($fecha_actual != "") {
            $consulta->andWhere("p.fechapublicacion <= :fecha")
                ->setParameter('fecha', $fecha_actual);
        }

        $lista = $consulta->orderBy('p.fechapublicacion', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
        return $lista;
    }

    /**
     * BuscarPorUrl: Devuelve el compra de la url
     * @param string $url url
     *
     * @author Yosmel
     */
    public function BuscarPorUrl($url) {
        $criteria = array('url' => $url);
        return $this->findOneBy($criteria);
    }

    /**
     * ListarComprasDeCategoria: Lista los compras de una categoria
     * @param int $categoria_id
     *
     * @author Yosmel
     */
    public function ListarComprasDeCategoria($categoria_id) {
        $consulta = $this->createQueryBuilder('p')
            ->leftJoin('p.categoria', 'c')
            ->where('c.categoriaId = :categoria_id')
            ->setParameter('categoria_id', $categoria_id)
            ->getQuery();
        $lista = $consulta->getResult();
        return $lista;
    }

    /**
     * ListarComprasDeMarca: Lista los compras de una marca
     * @param int $marca_id
     *
     * @author Yosmel
     */
    public function ListarComprasDeMarca($marca_id) {
        $consulta = $this->createQueryBuilder('p')
            ->leftJoin('p.marca', 'm')
            ->where('m.marcaId = :marca_id')
            ->setParameter('marca_id', $marca_id)
            ->getQuery();
        $lista = $consulta->getResult();
        return $lista;
    }

    /**
     * ListarComprasOrdenados: Lista los compras ordenados por fecha
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Yosmel
     */
    public function ListarComprasOrdenados($start, $limit) {
        $consulta = $this->createQueryBuilder('p')->orderBy('p.fechapublicacion', 'DESC');
        $lista = $consulta->setFirstResult($start)
            ->setMaxResults($limit)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarCompras: Lista los compra
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Yosmel
     */
    public function ListarCompras($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id = "", $marca_id = "") {
        $consulta = $this->createQueryBuilder('p');

        if ($sSearch != "") {
            $consulta
                ->andWhere('p.nombre LIKE :nombre OR p.descripcion LIKE :descripcion')
                ->setParameter('nombre', "%${sSearch}%")
                ->setParameter('descripcion', "%${sSearch}%");
        }

        if ($limit > 0) {
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->setFirstResult($start)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * TotalCompras: Total de compra de la BD
     * @param string $sSearch Para buscar
     *
     * @author Yosmel
     */
    public function TotalCompras($sSearch, $categoria_id = "", $marca_id = "") {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(p.compraId) FROM IcanBundle\Entity\Compra p ';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1)
                $where .= ' WHERE p.nombre LIKE :nombre OR p.descripcion LIKE :descripcion ';
            else
                $where .= ' AND p.nombre LIKE :nombre OR p.descripcion LIKE :descripcion  ';
        }

        $consulta .= $where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros        
        //$sSearch    

        $esta_query_nombre = substr_count($consulta, ':nombre');
        if ($esta_query_nombre == 1)
            $query->setParameter('nombre', "%${sSearch}%");

        $esta_query_descripcion = substr_count($consulta, ':descripcion');
        if ($esta_query_descripcion == 1)
            $query->setParameter('descripcion', "%${sSearch}%");

        $total = $query->getSingleScalarResult();
        return $total;
    }

    /**
     * ListarComprasParaRelacionados: Lista los compras para seleccionar relacionados
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Yosmel
     */
    public function ListarComprasParaRelacionados($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id = "", $marca_id = "", $compras_id = array()) {
        $consulta = $this->createQueryBuilder('p')
            ->leftJoin('p.categoria', 'c')
            ->leftJoin('p.marca', 'm');

        if ($sSearch != "") {
            $consulta
                ->andWhere('p.nombre LIKE :nombre OR p.descripcion LIKE :descripcion')
                ->setParameter('nombre', "%${sSearch}%")
                ->setParameter('descripcion', "%${sSearch}%");
        }

        if ($categoria_id != "") {
            $consulta->andWhere('c.categoriaId = :categoria_id')
                ->setParameter('categoria_id', $categoria_id);
        }
        if ($marca_id != "") {
            $consulta->andWhere('m.marcaId = :marca_id')
                ->setParameter('marca_id', $marca_id);
        }

        if (count($compras_id) > 0) {
            foreach ($compras_id as $key => $compra_id) {
                $consulta->andWhere("p.compraId <> :compra$key")
                    ->setParameter("compra$key", $compra_id);
            }
        }

        if ($iSortCol_0 == "categoria") {
            $consulta->orderBy("c.nombre", $sSortDir_0);
        } else {
            if ($iSortCol_0 == "marca") {
                $consulta->orderBy("m.descripcion", $sSortDir_0);
            } else {
                $consulta->orderBy("p.$iSortCol_0", $sSortDir_0);
            }
        }

        if ($limit > 0) {
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->setFirstResult($start)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * TotalComprasParaRelacionados: Total de compras para seleccionar relacionados de la BD
     * @param string $sSearch Para buscar
     *
     * @author Yosmel
     */
    public function TotalComprasParaRelacionados($sSearch, $categoria_id = "", $marca_id = "", $compras_id = array()) {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(p.compraId) FROM IcanBundle\Entity\Compra p ';
        $join = ' LEFT JOIN p.categoria c LEFT JOIN p.marca m ';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1) {
                $where .= ' WHERE p.nombre LIKE :nombre OR p.descripcion LIKE :descripcion ';
            } else {
                $where .= ' AND p.nombre LIKE :nombre OR p.descripcion LIKE :descripcion  ';
            }
        }

        if ($categoria_id != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1) {
                $where .= 'WHERE c.categoriaId = :categoria_id ';
            } else {
                $where .= 'AND c.categoriaId = :categoria_id ';
            }
        }
        if ($marca_id != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1) {
                $where .= 'WHERE m.marcaId = :marca_id ';
            } else {
                $where .= 'AND m.marcaId = :marca_id ';
            }
        }

        $params_compras = array();
        if (count($compras_id) > 0) {
            foreach ($compras_id as $key => $compra_id) {

                $param = $this->generarParamAleatorio();
                array_push($params_compras, $param);

                $esta_query = explode("WHERE", $where);
                if (count($esta_query) == 1) {
                    $where .= " WHERE (p.compraId <> :$param)";
                } else {
                    $where .= "AND (p.compraId <> :$param) ";
                }
            }
        }

        $consulta .= $join;
        $consulta .= $where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros        
        //$sSearch
        $esta_query_categoria_id = substr_count($consulta, ':categoria_id');
        if ($esta_query_categoria_id == 1)
            $query->setParameter('categoria_id', $categoria_id);

        $esta_query_marca_id = substr_count($consulta, ':marca_id');
        if ($esta_query_marca_id == 1)
            $query->setParameter('marca_id', $marca_id);

        $esta_query_nombre = substr_count($consulta, ':nombre');
        if ($esta_query_nombre == 1) {
            $query->setParameter('nombre', "%${sSearch}%");
        }

        $esta_query_descripcion = substr_count($consulta, ':descripcion');
        if ($esta_query_descripcion == 1) {
            $query->setParameter('descripcion', "%${sSearch}%");
        }

        if (count($compras_id) > 0) {
            foreach ($compras_id as $key => $compra_id) {
                $param = $params_compras[$key];
                $esta_query_pregunta = substr_count($consulta, ":$param");
                if ($esta_query_pregunta == 1) {
                    $query->setParameter(":$param", $compra_id);
                }
            }
        }

        $total = $query->getSingleScalarResult();
        return $total;
    }

    /**
     * ListarComprasMasVistas: Lista los compras mas vistas
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Yosmel
     */
    public function ListarComprasMasVistas() {
        $consulta = $this->createQueryBuilder('p')
            ->where('p.views > 0')
            ->orderBy('p.views', 'DESC');

        $lista = $consulta->setMaxResults(3)
            ->getQuery()->getResult();
        return $lista;
    }


    /**
     * ListarComprasPortada: Lista los compras para la portada
     *
     * @author Yosmel
     */
    public function ListarComprasPortada($fecha_actual) {
        $consulta = $this->createQueryBuilder('p')
            ->where('p.estado = 1');

        if ($fecha_actual != "") {
            $consulta->andWhere("p.fechapublicacion <= :fecha")
                ->setParameter('fecha', $fecha_actual);
        }

        $lista = $consulta->orderBy('p.fechapublicacion', 'DESC')
            ->getQuery()
            ->getResult();

        return $lista;
    }

    /**
     * ListarComprasCategoriaPortada: Lista los compras de una categoria para el frontend
     *
     * @author Yosmel
     */
    public function ListarComprasCategoriaPortada($categoria, $fecha_actual) {
        $consulta = $this->createQueryBuilder('p')
            ->where('p.estado = 1');

        if ($categoria != "") {
            $consulta->leftJoin('p.categoria', 'c')
                ->leftJoin('c.categoriaPadre', 'c_p')
                ->andWhere("c.url = :url OR c_p.url = :url_padre")
                ->setParameter('url', $categoria)
                ->setParameter('url_padre', $categoria);
        }

        if ($fecha_actual != "") {
            $consulta->andWhere("p.fechapublicacion <= :fecha")
                ->setParameter('fecha', $fecha_actual);
        }

        $lista = $consulta->orderBy('p.fechapublicacion', 'DESC')
            ->getQuery()
            ->getResult();
        return $lista;
    }

    /**
     * ListarComprasMarcaPortada: Lista los compras de una marca para el frontend
     *
     * @author Yosmel
     */
    public function ListarComprasMarcaPortada($marca, $fecha_actual) {
        $consulta = $this->createQueryBuilder('p')
            ->where('p.estado = 1');

        if ($marca != "") {
            $consulta->leftJoin('p.marca', 'm')
                ->andWhere("m.url = :url")
                ->setParameter('url', $marca);
        }

        if ($fecha_actual != "") {
            $consulta->andWhere("p.fechapublicacion <= :fecha")
                ->setParameter('fecha', $fecha_actual);
        }

        $lista = $consulta->orderBy('p.fechapublicacion', 'DESC')
            ->getQuery()
            ->getResult();
        return $lista;
    }

    /**
     * ListarBusquedaComprasPortada: Realiza la busqueda del frontend
     *
     * @author Yosmel
     */
    public function ListarBusquedaComprasPortada($sSearch, $fecha_actual) {
        $consulta = $this->createQueryBuilder('p')
            ->where('p.estado = 1');

        if ($sSearch != "") {
            $consulta->leftJoin('p.categoria', 'c')
                ->leftJoin('c.categoriaPadre', 'c_p')
                ->andWhere("c.nombre LIKE :categoria OR c_p.nombre LIKE :categoria_padre OR p.nombre LIKE :nombre OR p.descripcion LIKE :descripcion")
                ->setParameter('categoria', "%${sSearch}%")
                ->setParameter('categoria_padre', "%${sSearch}%")
                ->setParameter('nombre', "%${sSearch}%")
                ->setParameter('descripcion', "%${sSearch}%");
        }

        if ($fecha_actual != "") {
            $consulta->andWhere("p.fechapublicacion <= :fecha")
                ->setParameter('fecha', $fecha_actual);
        }

        $lista = $consulta->orderBy('p.fechapublicacion', 'DESC')
            ->getQuery()
            ->getResult();
        return $lista;
    }

    function generarParamAleatorio() {
        $codigo = "";
        //Dos letras
        $codigo .= substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"), 0, 10);

        return $codigo;
    }
}
