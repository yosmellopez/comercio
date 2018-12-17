<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CotizacionRepository extends EntityRepository
{

    /**
     * ListarCotizacionesRangoFecha: Lista los cotizaciones en un rango de fecha
     *
     *
     * @author Marcel
     */
    public function ListarCotizacionesRangoFecha($fecha_inicial, $fecha_final, $limit = "")
    {
        $consulta = $this->createQueryBuilder('c');

        if ($fecha_inicial != "") {
            $consulta->andWhere('c.fecha >= :fecha_inicial')
                ->setParameter('fecha_inicial', $fecha_inicial);
        }
        if ($fecha_final != "") {
            $consulta->andWhere('c.fecha <= :fecha_final')
                ->setParameter('fecha_final', $fecha_final);
        }

        $consulta->orderBy('c.fecha', 'DESC');

        if ($limit != "") {
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarCotizaciones: Lista los cotizacion
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarCotizaciones($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $fecha_inicial = "", $fecha_fin = "")
    {
        $consulta = $this->createQueryBuilder('c');

        if ($sSearch != "")
            $consulta->andWhere('c.estado LIKE :estado OR c.email LIKE :email OR c.nombre LIKE :nombre OR c.rut LIKE :rut
             OR c.apellidos LIKE :apellidos OR c.calle LIKE :calle')
                ->setParameter('estado', "%${sSearch}%")
                ->setParameter('email', "%${sSearch}%")
                ->setParameter('nombre', "%${sSearch}%")
                ->setParameter('apellidos', "%${sSearch}%")
                ->setParameter('rut', "%${sSearch}%")
                ->setParameter('calle', "%${sSearch}%");


        if ($fecha_inicial != "") {

            $fecha_inicial = \DateTime::createFromFormat("d/m/Y H:i:s", $fecha_inicial . " 00:00:00");
            $fecha_inicial = $fecha_inicial->format("Y-m-d H:i:s");

            $consulta->andWhere('c.fecha >= :fecha_inicial')
                ->setParameter('fecha_inicial', $fecha_inicial);
        }
        if ($fecha_fin != "") {

            $fecha_fin = \DateTime::createFromFormat("d/m/Y H:i:s", $fecha_fin . " 23:59:59");
            $fecha_fin = $fecha_fin->format("Y-m-d H:i:s");

            $consulta->andWhere('c.fecha <= :fecha_final')
                ->setParameter('fecha_final', $fecha_fin);
        }

        $consulta->orderBy("c.$iSortCol_0", $sSortDir_0);

        if ($limit > 0) {
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->setFirstResult($start)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * TotalCotizaciones: Total de cotizacion de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalCotizaciones($sSearch, $fecha_inicial = "", $fecha_fin = "")
    {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(c.cotizacionId) FROM IcanBundle\Entity\Cotizacion c ';
        $join = '';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1)
                $where .= 'WHERE (c.estado LIKE :estado OR c.email LIKE :email OR c.nombre LIKE :nombre OR c.rut LIKE :rut
             OR c.apellidos LIKE :apellidos OR c.calle LIKE :calle) ';
            else
                $where .= 'AND (c.estado LIKE :estado OR c.email LIKE :email OR c.nombre LIKE :nombre OR c.rut LIKE :rut
             OR c.apellidos LIKE :apellidos OR c.calle LIKE :calle) ';
        }

        if ($fecha_inicial != "") {

            $fecha_inicial = \DateTime::createFromFormat("d/m/Y H:i:s", $fecha_inicial . " 00:00:00");
            $fecha_inicial = $fecha_inicial->format("Y-m-d H:i:s");

            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1) {
                $where .= 'WHERE c.fecha >= :inicio ';
            } else {
                $where .= ' AND c.fecha >= :inicio ';
            }
        }

        if ($fecha_fin != "") {

            $fecha_fin = \DateTime::createFromFormat("d/m/Y H:i:s", $fecha_fin . " 23:59:59");
            $fecha_fin = $fecha_fin->format("Y-m-d H:i:s");

            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1) {
                $where .= 'WHERE c.fecha <= :fin ';
            } else {
                $where .= ' AND c.fecha <= :fin ';
            }
        }

        $consulta .= $join;
        $consulta .= $where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros        
        //$sSearch
        $esta_query_rut = substr_count($consulta, ':rut');
        if ($esta_query_rut == 1)
            $query->setParameter('rut', "%${sSearch}%");

        $esta_query_email = substr_count($consulta, ':email');
        if ($esta_query_email == 1)
            $query->setParameter('email', "%${sSearch}%");

        $esta_query_apellidos = substr_count($consulta, ':apellidos');
        if ($esta_query_apellidos == 1)
            $query->setParameter('apellidos', "%${sSearch}%");

        $esta_query_nombre = substr_count($consulta, ':nombre');
        if ($esta_query_nombre == 1)
            $query->setParameter('nombre', "%${sSearch}%");

        $esta_query_estado = substr_count($consulta, ':estado');
        if ($esta_query_estado == 1)
            $query->setParameter('estado', "%${sSearch}%");

        $esta_query_calle = substr_count($consulta, ':calle');
        if ($esta_query_calle == 1)
            $query->setParameter('calle', "%${sSearch}%");

        $esta_query_inicio = substr_count($consulta, ':inicio');
        if ($esta_query_inicio == 1) {
            $query->setParameter('inicio', $fecha_inicial);
        }

        $esta_query_fin = substr_count($consulta, ':fin');
        if ($esta_query_fin == 1) {
            $query->setParameter('fin', $fecha_fin);
        }

        $total = $query->getSingleScalarResult();
        return $total;
    }

}
