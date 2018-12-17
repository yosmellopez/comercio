<?php
namespace FrontendBundle;

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Util
 *
 * @author Marcel
 */
class Util
{ 
   
   private static $instance = null;

    /**
    * Constructor de la clase Util vac�o
    *
    *@return Util
    * @author Marcel
    */
   public function  __construct()
   {
        
   }

   /**
    * Instance: Devuelve la instancia única de la clase para el singleton
    *
    * @return Util
    * @author Marcel
    */

   public static function Instance()
   {
       if(self::$instance == null)
       {
           self::$instance = new Util();
       }
       return self::$instance;
   }     
   
   /**
    * EstaElemento: Para saber si hay un elemento en el arreglo
    *
    * @return bool
    * @author Marcel
    */
    public function EstaElemento($arreglo_resultado, $id)
    {
        $resultado = false;
        foreach ($arreglo_resultado as $value) {
            if($value['id'] == $id)
            {
                $resultado = true;
                break;
            }                
        }
        return $resultado;
    }  
}
?>
