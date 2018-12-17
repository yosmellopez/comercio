<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MensajeRepository extends EntityRepository {

    /**
     * ListarMensajesRangoFecha: Lista los mensajes en un rango de fecha
     *     
     *
     * @author Marcel
     */
    public function ListarMensajesRangoFecha($fecha_inicial, $fecha_final, $limit="") {
        $consulta = $this->createQueryBuilder('m');

        if ($fecha_inicial != "") {
            $consulta->andWhere('m.fecha >= :fecha_inicial')
                    ->setParameter('fecha_inicial', $fecha_inicial);
        }
        if ($fecha_final != "") {
            $consulta->andWhere('m.fecha <= :fecha_final')
                    ->setParameter('fecha_final', $fecha_final);
        }

        $consulta->orderBy('m.fecha', 'DESC');

        if($limit != ""){
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarMensajes: Lista los mensaje
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     * 
     * @author Marcel
     */
    public function ListarMensajes($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $fecha_inicial ="", $fecha_fin ="") {
        $consulta = $this->createQueryBuilder('m');

        if ($sSearch != "")
            $consulta->andWhere('m.email LIKE :email OR m.asunto LIKE :asunto OR m.nombre LIKE :nombre OR m.descripcion LIKE :descripcion')
                    ->setParameter('email', "%${sSearch}%")
                    ->setParameter('asunto', "%${sSearch}%")
                    ->setParameter('nombre', "%${sSearch}%")
                    ->setParameter('descripcion', "%${sSearch}%");


        if ($fecha_inicial != "") {

            $fecha_inicial = \DateTime::createFromFormat("d/m/Y H:i:s", $fecha_inicial . " 00:00:00");
            $fecha_inicial = $fecha_inicial->format("Y-m-d H:i:s");

            $consulta->andWhere('m.fecha >= :fecha_inicial')
                ->setParameter('fecha_inicial', $fecha_inicial);
        }
        if ($fecha_fin != "") {

            $fecha_fin = \DateTime::createFromFormat("d/m/Y H:i:s", $fecha_fin . " 23:59:59");
            $fecha_fin = $fecha_fin->format("Y-m-d H:i:s");

            $consulta->andWhere('m.fecha <= :fecha_final')
                ->setParameter('fecha_final', $fecha_fin);
        }

        $consulta->orderBy("m.$iSortCol_0", $sSortDir_0);

        if ($limit > 0) {
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->setFirstResult($start)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * TotalMensajes: Total de mensaje de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalMensajes($sSearch, $fecha_inicial ="", $fecha_fin ="") {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(m.mensajeId) FROM IcanBundle\Entity\Mensaje m ';
        $join = '';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1)
                $where .= 'WHERE m.email LIKE :email OR m.asunto LIKE :asunto OR m.nombre LIKE :nombre OR m.descripcion LIKE :descripcion ';
            else
                $where .= 'AND m.email LIKE :email OR m.asunto LIKE :asunto OR m.nombre LIKE :nombre OR m.descripcion LIKE :descripcion ';
        }

        if ($fecha_inicial != "") {

            $fecha_inicial = \DateTime::createFromFormat("d/m/Y H:i:s", $fecha_inicial . " 00:00:00");
            $fecha_inicial = $fecha_inicial->format("Y-m-d H:i:s");

            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1) {
                $where .= 'WHERE m.fecha >= :inicio ';
            } else {
                $where .= ' AND m.fecha >= :inicio ';
            }
        }

        if ($fecha_fin != "") {

            $fecha_fin = \DateTime::createFromFormat("d/m/Y H:i:s", $fecha_fin . " 23:59:59");
            $fecha_fin = $fecha_fin->format("Y-m-d H:i:s");

            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1) {
                $where .= 'WHERE m.fecha <= :fin ';
            } else {
                $where .= ' AND m.fecha <= :fin ';
            }
        }

        $consulta.=$join;
        $consulta.=$where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros        
        //$sSearch        
        $esta_query_email = substr_count($consulta, ':email');
        if ($esta_query_email == 1)
            $query->setParameter('email', "%${sSearch}%");

        $esta_query_asunto = substr_count($consulta, ':asunto');
        if ($esta_query_asunto == 1)
            $query->setParameter('asunto', "%${sSearch}%");

        $esta_query_nombre = substr_count($consulta, ':nombre');
        if ($esta_query_nombre == 1)
            $query->setParameter('nombre', "%${sSearch}%");

        $esta_query_descripcion = substr_count($consulta, ':descripcion');
        if ($esta_query_descripcion == 1)
            $query->setParameter('descripcion', "%${sSearch}%");

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
