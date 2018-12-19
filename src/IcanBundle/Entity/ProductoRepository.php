<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ProductoRepository extends EntityRepository
{

    /**
     * ListarOrdenados: Lista los productos ordenados por fecha
     *
     * @author Marcel
     */
    public function ListarOrdenados() {
        $consulta = $this->createQueryBuilder('p')
            ->where('p.estado = 1')
            ->orderBy('p.fechapublicacion', 'DESC');

        $lista = $consulta->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarProductosDestacados: Lista los productos destacados ordenados por fecha
     *
     * @author Marcel
     */
    public function ListarProductosDestacados($fecha_actual) {
        $consulta = $this->createQueryBuilder('p')
            ->andWhere('p.estado = 1 AND p.destacado = 1');

        if ($fecha_actual != "") {
            $consulta->andWhere("p.fechapublicacion <= :fecha")
                ->setParameter('fecha', $fecha_actual);
        }

        $lista = $consulta->orderBy('p.fechapublicacion', 'DESC')->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarUltimosProductos: Lista los ultimos 6 productos ordenados por fecha
     *
     * @author Marcel
     */
    public function ListarUltimosProductos($fecha_actual, $limit) {
        $consulta = $this->createQueryBuilder('p')
            ->andWhere('p.estado = 1');

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
     * BuscarPorUrl: Devuelve el producto de la url
     * @param string $url url
     *
     * @author Marcel
     */
    public function BuscarPorUrl($url) {
        $criteria = array('url' => $url);
        return $this->findOneBy($criteria);
    }

    /**
     * ListarProductosDeCategoria: Lista los productos de una categoria
     * @param int $categoria_id
     *
     * @author Marcel
     */
    public function ListarProductosDeCategoria($categoria_id) {
        $consulta = $this->createQueryBuilder('p')
            ->leftJoin('p.categoria', 'c')
            ->where('c.categoriaId = :categoria_id')
            ->setParameter('categoria_id', $categoria_id)
            ->getQuery();

        $lista = $consulta->getResult();
        return $lista;
    }

    /**
     * ListarProductosDeMarca: Lista los productos de una marca
     * @param int $marca_id
     *
     * @author Marcel
     */
    public function ListarProductosDeMarca($marca_id) {
        $consulta = $this->createQueryBuilder('p')
            ->leftJoin('p.marca', 'm')
            ->where('m.marcaId = :marca_id')
            ->setParameter('marca_id', $marca_id)
            ->getQuery();

        $lista = $consulta->getResult();
        return $lista;
    }

    /**
     * ListarProductosDeMarcaYCategoria: Lista los productos de una marca
     * @param int $marca_id
     *
     * @author Marcel
     */
    public function ListarProductosDeMarcaYCategoria($marca_id, $categoria_id) {
        $consulta = $this->createQueryBuilder('p')
            ->leftJoin('p.marca', 'm')
            ->leftJoin('p.categoria', 'c')
            ->where('c.categoriaId = :categoria_id')
            ->andWhere('m.marcaId = :marca_id')
            ->setParameter('categoria_id', $categoria_id)
            ->setParameter('marca_id', $marca_id)
            ->getQuery();

        $lista = $consulta->getResult();
        return $lista;
    }

    /**
     * ListarProductosDePromocion: Lista los productos de una promocion
     * @param int $promocion_id
     *
     * @author Marcel
     */
    public function ListarProductosDePromocion($promocion_id) {
        $consulta = $this->createQueryBuilder('p')
            ->leftJoin('p.promocion', 'pr')
            ->where('pr.promocionId = :promocion_id')
            ->setParameter('promocion_id', $promocion_id)
            ->getQuery();

        $lista = $consulta->getResult();
        return $lista;
    }

    /**
     * ListarProductosDePorciento: Lista los productos de un porciento
     * @param int $porciento_id
     *
     * @author Marcel
     */
    public function ListarProductosDePorciento($porciento_id) {
        $consulta = $this->createQueryBuilder('p')
            ->leftJoin('p.porciento', 'pr')
            ->where('pr.porcientoId = :porciento_id')
            ->setParameter('porciento_id', $porciento_id)
            ->getQuery();

        $lista = $consulta->getResult();
        return $lista;
    }

    /**
     * ListarProductosOrdenados: Lista los productos ordenados por fecha
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarProductosOrdenados($start, $limit) {
        $consulta = $this->createQueryBuilder('p')->orderBy('p.fechapublicacion', 'DESC');

        $lista = $consulta->setFirstResult($start)
            ->setMaxResults($limit)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarProductos: Lista los producto
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarProductos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id = "", $marca_id = "") {
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
     * TotalProductos: Total de producto de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalProductos($sSearch, $categoria_id = "", $marca_id = "") {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(p.productoId) FROM IcanBundle\Entity\Producto p ';
        $join = ' LEFT JOIN p.categoria c LEFT JOIN p.marca m ';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1)
                $where .= ' WHERE p.nombre LIKE :nombre OR p.descripcion LIKE :descripcion ';
            else
                $where .= ' AND p.nombre LIKE :nombre OR p.descripcion LIKE :descripcion  ';
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
        if ($esta_query_nombre == 1)
            $query->setParameter('nombre', "%${sSearch}%");

        $esta_query_descripcion = substr_count($consulta, ':descripcion');
        if ($esta_query_descripcion == 1)
            $query->setParameter('descripcion', "%${sSearch}%");

        $total = $query->getSingleScalarResult();
        return $total;
    }

    /**
     * ListarProductosParaRelacionados: Lista los productos para seleccionar relacionados
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarProductosParaRelacionados($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id = "", $marca_id = "", $productos_id = array()) {
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

        if (count($productos_id) > 0) {
            foreach ($productos_id as $key => $producto_id) {
                $consulta->andWhere("p.productoId <> :producto$key")
                    ->setParameter("producto$key", $producto_id);
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
     * TotalProductosParaRelacionados: Total de productos para seleccionar relacionados de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalProductosParaRelacionados($sSearch, $categoria_id = "", $marca_id = "", $productos_id = array()) {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(p.productoId) FROM IcanBundle\Entity\Producto p ';
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

        $params_productos = array();
        if (count($productos_id) > 0) {
            foreach ($productos_id as $key => $producto_id) {

                $param = $this->generarParamAleatorio();
                array_push($params_productos, $param);

                $esta_query = explode("WHERE", $where);
                if (count($esta_query) == 1) {
                    $where .= " WHERE (p.productoId <> :$param)";
                } else {
                    $where .= "AND (p.productoId <> :$param) ";
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

        if (count($productos_id) > 0) {
            foreach ($productos_id as $key => $producto_id) {
                $param = $params_productos[$key];
                $esta_query_pregunta = substr_count($consulta, ":$param");
                if ($esta_query_pregunta == 1) {
                    $query->setParameter(":$param", $producto_id);
                }
            }
        }

        $total = $query->getSingleScalarResult();
        return $total;
    }


    /**
     * ListarProductosParaCotizacion: Lista los productos para cotizar
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarProductosParaCotizacion($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id = "", $marca_id = "", $productos_id = array()) {
        $consulta = $this->createQueryBuilder('p')
            ->leftJoin('p.categoria', 'c')
            ->leftJoin('p.marca', 'm')
            ->where('p.estado = 1');

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

        if (count($productos_id) > 0) {
            foreach ($productos_id as $key => $producto_id) {
                $consulta->andWhere("p.productoId <> :producto$key")
                    ->setParameter("producto$key", $producto_id);
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
     * TotalProductosParaCotizacion: Total de productos para cotizar de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalProductosParaCotizacion($sSearch, $categoria_id = "", $marca_id = "", $productos_id = array()) {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(p.productoId) FROM IcanBundle\Entity\Producto p ';
        $join = ' LEFT JOIN p.categoria c LEFT JOIN p.marca m ';
        $where = ' WHERE p.estado = 1 ';

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

        $params_productos = array();
        if (count($productos_id) > 0) {
            foreach ($productos_id as $key => $producto_id) {

                $param = $this->generarParamAleatorio();
                array_push($params_productos, $param);

                $esta_query = explode("WHERE", $where);
                if (count($esta_query) == 1) {
                    $where .= " WHERE (p.productoId <> :$param)";
                } else {
                    $where .= "AND (p.productoId <> :$param) ";
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

        if (count($productos_id) > 0) {
            foreach ($productos_id as $key => $producto_id) {
                $param = $params_productos[$key];
                $esta_query_pregunta = substr_count($consulta, ":$param");
                if ($esta_query_pregunta == 1) {
                    $query->setParameter(":$param", $producto_id);
                }
            }
        }

        $total = $query->getSingleScalarResult();
        return $total;
    }


    /**
     * ListarProductosParaPromocion: Lista los productos para generar una promocion
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarProductosParaPromocion($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id = "", $marca_id = "", $productos_id = array()) {
        $consulta = $this->createQueryBuilder('p')
            ->leftJoin('p.categoria', 'c')
            ->leftJoin('p.marca', 'm')
            ->where('p.promocion is null')
            ->andWhere('p.porciento is null');

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

        if (count($productos_id) > 0) {
            foreach ($productos_id as $key => $producto_id) {
                $consulta->andWhere("p.productoId <> :producto$key")
                    ->setParameter("producto$key", $producto_id);
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
     * TotalProductosParaPromocion: Total de productos para generar una promocion de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalProductosParaPromocion($sSearch, $categoria_id = "", $marca_id = "", $productos_id = array()) {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(p.productoId) FROM IcanBundle\Entity\Producto p ';
        $join = ' LEFT JOIN p.categoria c LEFT JOIN p.marca m ';
        $where = ' WHERE p.promocion is null AND p.porciento is null ';

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

        $params_productos = array();
        if (count($productos_id) > 0) {
            foreach ($productos_id as $key => $producto_id) {

                $param = $this->generarParamAleatorio();
                array_push($params_productos, $param);

                $esta_query = explode("WHERE", $where);
                if (count($esta_query) == 1) {
                    $where .= " WHERE (p.productoId <> :$param)";
                } else {
                    $where .= "AND (p.productoId <> :$param) ";
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

        if (count($productos_id) > 0) {
            foreach ($productos_id as $key => $producto_id) {
                $param = $params_productos[$key];
                $esta_query_pregunta = substr_count($consulta, ":$param");
                if ($esta_query_pregunta == 1) {
                    $query->setParameter(":$param", $producto_id);
                }
            }
        }

        $total = $query->getSingleScalarResult();
        return $total;
    }

    /**
     * ListarProductosMasVistas: Lista los productos mas vistas
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarProductosMasVistas() {
        $consulta = $this->createQueryBuilder('p')
            ->where('p.views > 0')
            ->orderBy('p.views', 'DESC');

        $lista = $consulta->setMaxResults(3)
            ->getQuery()->getResult();
        return $lista;
    }


    /**
     * ListarProductosPortada: Lista los productos para la portada
     *
     * @author Marcel
     */
    public function ListarProductosPortada($sort, $fecha_actual) {
        $consulta = $this->createQueryBuilder('p')
            ->where('p.estado = 1');

        if ($fecha_actual != "") {
            $consulta->andWhere("p.fechapublicacion <= :fecha")
                ->setParameter('fecha', $fecha_actual);
        }

        if ($sort == "") {
            $consulta->orderBy('p.precio', 'DESC');
        }
        if ($sort == "sort_name_asc") {
            $consulta->orderBy('p.nombre', 'ASC');
        }
        if ($sort == "sort_name_desc") {
            $consulta->orderBy('p.nombre', 'DESC');
        }
        if ($sort == "sort_price_asc") {
            $consulta->orderBy('p.precio', 'ASC');
        }
        if ($sort == "sort_price_desc") {
            $consulta->orderBy('p.precio', 'DESC');
        }

        $lista = $consulta->getQuery()
            ->getResult();

        return $lista;
    }

    /**
     * ListarProductosCategoriaPortada: Lista los productos de una categoria para el frontend
     *
     * @author Marcel
     */
    public function ListarProductosCategoriaPortada($categoria, $fecha_actual, $sort) {
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

        if ($sort == "") {
            $consulta->orderBy('p.precio', 'DESC');
        }

        if ($sort == "sort_name_asc") {
            $consulta->orderBy('p.nombre', 'ASC');
        }
        if ($sort == "sort_name_desc") {
            $consulta->orderBy('p.nombre', 'DESC');
        }
        if ($sort == "sort_price_asc") {
            $consulta->orderBy('p.precio', 'ASC');
        }
        if ($sort == "sort_price_desc") {
            $consulta->orderBy('p.precio', 'DESC');
        }

        $lista = $consulta->getQuery()
            ->getResult();

        return $lista;
    }

    /**
     * ListarProductosMarcaPortada: Lista los productos de una marca para el frontend
     *
     * @author Marcel
     */
    public function ListarProductosMarcaPortada($marca, $fecha_actual, $sort) {
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

        if ($sort == "") {
            $consulta->orderBy('p.precio', 'DESC');
        }
        if ($sort == "sort_name_asc") {
            $consulta->orderBy('p.nombre', 'ASC');
        }
        if ($sort == "sort_name_desc") {
            $consulta->orderBy('p.nombre', 'DESC');
        }
        if ($sort == "sort_price_asc") {
            $consulta->orderBy('p.precio', 'ASC');
        }
        if ($sort == "sort_price_desc") {
            $consulta->orderBy('p.precio', 'DESC');
        }

        $lista = $consulta->getQuery()
            ->getResult();

        return $lista;
    }

    /**
     * ListarBusquedaProductosPortada: Realiza la busqueda del frontend
     *
     * @author Marcel
     */
    public function ListarBusquedaProductosPortada($sSearch, $fecha_actual, $sort) {
        $consulta = $this->createQueryBuilder('p')
            ->where('p.estado = 1');

        if ($sSearch != "") {
            $consulta->leftJoin('p.categoria', 'c')
                ->leftJoin('c.categoriaPadre', 'c_p')
                ->leftJoin('p.marca', 'm')
                ->andWhere("m.nombre LIKE :marca OR c.nombre LIKE :categoria OR c_p.nombre LIKE :categoria_padre OR p.nombre LIKE :nombre OR p.descripcion LIKE :descripcion")
                ->setParameter('marca', "%${sSearch}%")
                ->setParameter('categoria', "%${sSearch}%")
                ->setParameter('categoria_padre', "%${sSearch}%")
                ->setParameter('nombre', "%${sSearch}%")
                ->setParameter('descripcion', "%${sSearch}%");
        }

        if ($fecha_actual != "") {
            $consulta->andWhere("p.fechapublicacion <= :fecha")
                ->setParameter('fecha', $fecha_actual);
        }

        if ($sort == "") {
            $consulta->orderBy('p.precio', 'DESC');
        }

        if ($sort == "sort_name_asc") {
            $consulta->orderBy('p.nombre', 'ASC');
        }
        if ($sort == "sort_name_desc") {
            $consulta->orderBy('p.nombre', 'DESC');
        }
        if ($sort == "sort_price_asc") {
            $consulta->orderBy('p.precio', 'ASC');
        }
        if ($sort == "sort_price_desc") {
            $consulta->orderBy('p.precio', 'DESC');
        }

        $lista = $consulta->getQuery()
            ->getResult();

        return $lista;
    }

    /**
     * ListarProductosRangoPrecios: Lista los productos de un rango de precios
     *
     *
     * @author Marcel
     */
    public function ListarProductosRangoPrecios($precio1, $precio2, $sort = "", $categoria_id = 0) {
        $consulta = $this->createQueryBuilder('p');

        if ($categoria_id > 0) {
            $consulta->leftJoin('p.categoria', 'c')
                ->andWhere('c.categoriaId = :categoria_id')
                ->setParameter('categoria_id', $categoria_id);
        }

        if ($precio1 != "") {
            $consulta->andWhere('p.precio >= :precio1')
                ->setParameter('precio1', $precio1);
        }
        if ($precio2 != "") {
            $consulta->andWhere('p.precio <= :precio2')
                ->setParameter('precio2', $precio2);
        }

        if ($sort == "") {
            $consulta->orderBy('p.precio', 'DESC');
        }

        if ($sort == "sort_name_asc") {
            $consulta->orderBy('p.nombre', 'ASC');
        }
        if ($sort == "sort_name_desc") {
            $consulta->orderBy('p.nombre', 'DESC');
        }
        if ($sort == "sort_price_asc") {
            $consulta->orderBy('p.precio', 'ASC');
        }
        if ($sort == "sort_price_desc") {
            $consulta->orderBy('p.precio', 'DESC');
        }

        $lista = $consulta->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarProductosPromocionPortada: Lista los productos para la portada
     *
     * @author Marcel
     */
    public function ListarProductosPromocionPortada($promocion_id, $sort, $fecha_actual) {
        $consulta = $this->createQueryBuilder('p')
            ->where('p.estado = 1');

        if ($promocion_id != "") {
            $consulta->leftJoin('p.promocion', 'pr')
                ->andWhere("pr.promocionId = :promocion_id")
                ->setParameter('promocion_id', $promocion_id);
        }

        if ($fecha_actual != "") {
            $consulta->andWhere("p.fechapublicacion <= :fecha")
                ->setParameter('fecha', $fecha_actual);
        }

        if ($sort == "") {
            $consulta->orderBy('p.precio', 'DESC');
        }
        if ($sort == "sort_name_asc") {
            $consulta->orderBy('p.nombre', 'ASC');
        }
        if ($sort == "sort_name_desc") {
            $consulta->orderBy('p.nombre', 'DESC');
        }
        if ($sort == "sort_price_asc") {
            $consulta->orderBy('p.precio', 'ASC');
        }
        if ($sort == "sort_price_desc") {
            $consulta->orderBy('p.precio', 'DESC');
        }

        $lista = $consulta->getQuery()
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
