<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace IcanBundle\Extension;

class IcanTwigExtension extends \Twig_Extension {

    public function getOperators() {
        return array(
            array(
            '!' => array('precedence' => 50, 'class' => 'Twig_Node_Expression_Unary_Not'),
            ),
            array(
            '||' => array('precedence' => 10, 'class' => 'Twig_Node_Expression_Binary_Or', 'associativity' =>  \Twig_ExpressionParser::OPERATOR_LEFT),
            '&&' => array('precedence' => 15, 'class' => 'Twig_Node_Expression_Binary_And', 'associativity' =>\Twig_ExpressionParser::OPERATOR_LEFT)
                )
        );
    }
    
    public function getFilters() {
        return array(
            'auto_link_text' => new \Twig_Filter_Method($this, 'auto_link_text', array('safe' => array('html')))
        );
    }

    static public function auto_link_text($texto) {
        $texto = preg_replace('/a/', 'b', $texto);

        return $texto;
    }

    public function getName() {
        return 'ican_twig_extension';
    }

}

