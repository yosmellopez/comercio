<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class NewsletterRepository extends EntityRepository {

    /**
     * ListarNewslettersRangoFecha: Lista los newsletters en un rango de fecha
     *     
     *
     * @author Marcel
     */
    public function ListarNewslettersRangoFecha($fecha_inicial, $fecha_final, $limit="") {
        $consulta = $this->createQueryBuilder('n');

        if ($fecha_inicial != "") {
            $consulta->andWhere('n.fecha >= :fecha_inicial')
                    ->setParameter('fecha_inicial', $fecha_inicial);
        }
        if ($fecha_final != "") {
            $consulta->andWhere('n.fecha <= :fecha_final')
                    ->setParameter('fecha_final', $fecha_final);
        }

        $consulta->orderBy('n.fecha', 'DESC');

        if($limit != ""){
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarNewsletters: Lista los newsletter
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     * 
     * @author Marcel
     */
    public function ListarNewsletters($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $fecha_inicial ="", $fecha_fin ="") {
        $consulta = $this->createQueryBuilder('n');

        if ($sSearch != "")
            $consulta->andWhere('n.email LIKE :email')
                    ->setParameter('email', "%${sSearch}%");


        if ($fecha_inicial != "") {

            $fecha_inicial = \DateTime::createFromFormat("d/m/Y H:i:s", $fecha_inicial . " 00:00:00");
            $fecha_inicial = $fecha_inicial->format("Y-m-d H:i:s");

            $consulta->andWhere('n.fecha >= :fecha_inicial')
                ->setParameter('fecha_inicial', $fecha_inicial);
        }
        if ($fecha_fin != "") {

            $fecha_fin = \DateTime::createFromFormat("d/m/Y H:i:s", $fecha_fin . " 23:59:59");
            $fecha_fin = $fecha_fin->format("Y-m-d H:i:s");

            $consulta->andWhere('n.fecha <= :fecha_final')
                ->setParameter('fecha_final', $fecha_fin);
        }

        $consulta->orderBy("n.$iSortCol_0", $sSortDir_0);

        if ($limit > 0) {
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->setFirstResult($start)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * TotalNewsletters: Total de newsletter de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalNewsletters($sSearch, $fecha_inicial ="", $fecha_fin ="") {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(n.newsletterId) FROM IcanBundle\Entity\Newsletter n ';
        $join = '';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1)
                $where .= 'WHERE n.email LIKE :email ';
            else
                $where .= 'AND n.email LIKE :email ';
        }

        if ($fecha_inicial != "") {

            $fecha_inicial = \DateTime::createFromFormat("d/m/Y H:i:s", $fecha_inicial . " 00:00:00");
            $fecha_inicial = $fecha_inicial->format("Y-m-d H:i:s");

            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1) {
                $where .= 'WHERE n.fecha >= :inicio ';
            } else {
                $where .= ' AND n.fecha >= :inicio ';
            }
        }

        if ($fecha_fin != "") {

            $fecha_fin = \DateTime::createFromFormat("d/m/Y H:i:s", $fecha_fin . " 23:59:59");
            $fecha_fin = $fecha_fin->format("Y-m-d H:i:s");

            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1) {
                $where .= 'WHERE n.fecha <= :fin ';
            } else {
                $where .= ' AND n.fecha <= :fin ';
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
