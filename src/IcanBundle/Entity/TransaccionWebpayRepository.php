<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TransaccionWebpayRepository extends EntityRepository
{

    /**
     * BuscarTransaccionPorToken: Busca la transaccion dado el token
     * @param int $token
     *
     * @author Marcel
     */
    public function BuscarTransaccionPorToken($token)
    {
        $consulta = $this->createQueryBuilder('t')
            ->where('t.token = :token')
            ->setParameter('token', $token)
            ->getQuery();

        $lista = $consulta->getOneOrNullResult();
        return $lista;
    }

    /**
     * BuscarTransaccion: Busca la transaccion de una cotizacion
     * @param int $cotizacion_id
     *
     * @author Marcel
     */
    public function BuscarTransaccion($cotizacion_id)
    {
        $consulta = $this->createQueryBuilder('t')
            ->leftJoin('t.cotizacion', 'c')
            ->where('c.cotizacionId = :cotizacion_id')
            ->setParameter('cotizacion_id', $cotizacion_id)
            ->getQuery();

        $lista = $consulta->getOneOrNullResult();
        return $lista;
    }

    /**
     * ListarTransacciones: Lista los transaccion
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarTransacciones($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $fecha_inicial = "", $fecha_fin = "")
    {
        $consulta = $this->createQueryBuilder('t')
            ->leftJoin('t.cotizacion', 'c');

        if ($sSearch != "")
            $consulta->andWhere('t.cardNumber LIKE :cardNumber OR t.responseCode LIKE :responseCode')
                ->setParameter('cardNumber', "%${sSearch}%")
                ->setParameter('responseCode', "%${sSearch}%");

        if ($fecha_inicial != "") {

            $fecha_inicial = \DateTime::createFromFormat("d/m/Y H:i:s", $fecha_inicial . " 00:00:00");
            $fecha_inicial = $fecha_inicial->format("Y-m-d H:i:s");

            $consulta->andWhere('t.transactionDate >= :fecha_inicial')
                ->setParameter('fecha_inicial', $fecha_inicial);
        }
        if ($fecha_fin != "") {

            $fecha_fin = \DateTime::createFromFormat("d/m/Y H:i:s", $fecha_fin . " 23:59:59");
            $fecha_fin = $fecha_fin->format("Y-m-d H:i:s");

            $consulta->andWhere('t.transactionDate <= :fecha_final')
                ->setParameter('fecha_final', $fecha_fin);
        }

        if ($iSortCol_0 == "cotizacionId") {
            $consulta->orderBy("c.cotizacionId", $sSortDir_0);
        } else {
            $consulta->orderBy("t.$iSortCol_0", $sSortDir_0);
        }

        /*
         if ($iSortCol_0 == 1) {
            $consulta->orderBy('c.cotizacionId', $sSortDir_0);
        }
        if ($iSortCol_0 == 2) {
            $consulta->orderBy('t.transactionDate', $sSortDir_0);
        }
        if ($iSortCol_0 == 3) {
            $consulta->orderBy('t.cardNumber', $sSortDir_0);
        }
        if ($iSortCol_0 == 4) {
            $consulta->orderBy('t.cardExpirationDate', $sSortDir_0);
        }
        if ($iSortCol_0 == 5) {
            $consulta->orderBy('t.authorizationCode', $sSortDir_0);
        }
        if ($iSortCol_0 == 6) {
            $consulta->orderBy('t.paymentTypeCode', $sSortDir_0);
        }
        if ($iSortCol_0 == 7) {
            $consulta->orderBy('t.responseCode', $sSortDir_0);
        }
        if ($iSortCol_0 == 8) {
            $consulta->orderBy('t.amount', $sSortDir_0);
        }
        if ($iSortCol_0 == 9) {
            $consulta->orderBy('t.sharesAmount', $sSortDir_0);
        }
        if ($iSortCol_0 == 10) {
            $consulta->orderBy('t.sharesNumber', $sSortDir_0);
        }
         */

        if ($limit > 0) {
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->setFirstResult($start)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * TotalTransacciones: Total de transaccion de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalTransacciones($sSearch, $fecha_inicial = "", $fecha_fin = "")
    {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(t.transaccionId) FROM IcanBundle\Entity\TransaccionWebpay t ';
        $join = '';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1) {
                $where .= 'WHERE t.cardNumber LIKE :cardNumber OR t.responseCode LIKE :responseCode ';
            } else {
                $where .= 'AND t.cardNumber LIKE :cardNumber OR t.responseCode LIKE :responseCode ';
            }
        }

        if ($fecha_inicial != "") {

            $fecha_inicial = \DateTime::createFromFormat("d/m/Y H:i:s", $fecha_inicial . " 00:00:00");
            $fecha_inicial = $fecha_inicial->format("Y-m-d H:i:s");

            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1) {
                $where .= 'WHERE t.transactionDate >= :inicio ';
            } else {
                $where .= ' AND t.transactionDate >= :inicio ';
            }
        }

        if ($fecha_fin != "") {

            $fecha_fin = \DateTime::createFromFormat("d/m/Y H:i:s", $fecha_fin . " 23:59:59");
            $fecha_fin = $fecha_fin->format("Y-m-d H:i:s");

            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1) {
                $where .= 'WHERE t.transactionDate <= :fin ';
            } else {
                $where .= ' AND t.transactionDate <= :fin ';
            }
        }

        $consulta .= $join;
        $consulta .= $where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros        
        //$sSearch        
        $esta_query_cardNumber = substr_count($consulta, ':cardNumber');
        if ($esta_query_cardNumber == 1) {
            $query->setParameter('cardNumber', "%${sSearch}%");
        }

        $esta_query_responseCode = substr_count($consulta, ':responseCode');
        if ($esta_query_responseCode == 1) {
            $query->setParameter('responseCode', "%${sSearch}%");
        }

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
