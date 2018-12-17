<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ProductoViewRepository extends EntityRepository {

    /**
     * BuscarProductoView: Busca un view por fecha e ip
     * @param datetime $fecha Fecha
     * @param string $ip IP
     * @author Marcel
     */
    public function BuscarProductoView($fecha, $ip, $producto_id) {
        $consulta = $this->createQueryBuilder('p_v')
                ->leftJoin('p_v.producto', 'n')
                ->where('p_v.fecha = :fecha AND p_v.ip = :ip AND n.productoId = :producto_id')
                ->setParameter('producto_id', $producto_id)
                ->setParameter('fecha', $fecha)
                ->setParameter('ip', $ip)
                ->getQuery();

        $view = $consulta->getOneOrNullResult();
        return $view;
    }

    /**
     * ListarViewsDeProducto: Lista las visitas de una producto
     * @param int $producto_id Id
     *
     * @author Marcel
     */
    public function ListarViewsDeProducto($producto_id) {
        $consulta = $this->createQueryBuilder('p_v')
                ->leftJoin('p_v.producto', 'n')
                ->where('n.productoId = :producto_id')
                ->setParameter('producto_id', $producto_id)
                ->getQuery();

        $usuarios = $consulta->getResult();
        return $usuarios;
    }

    /**
     * TotalViews: Total de visitas de una producto de la BD
     * @param int $producto_id Id
     *
     * @author Marcel
     */
    public function TotalViews($producto_id) {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(p_v.productoviewId) FROM IcanBundle\Entity\ProductoView p_v ';
        $join = '';
        $where = '';

        if ($producto_id != "") {
            $join .= 'JOIN p_v.producto n ';
            $where .= 'WHERE n.productoId = :producto_id';
        }

        $consulta.=$join;
        $consulta.=$where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros        
        //$sSearch        
        $esta_query_producto_id = substr_count($consulta, ':producto_id');
        if ($esta_query_producto_id == 1) {
            $query->setParameter('producto_id', $producto_id);
        }

        $total = $query->getSingleScalarResult();
        return $total;
    }

}
