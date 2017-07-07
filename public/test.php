<?php
/**
 * Author: hfan
 * Contact:804667084@qq.com
 * Date: 2017/7/6
 * Time: 11:07
 */

interface Add {
    public function code($string);
}

interface Bdd {
    public function code($string);
}

class C implements Add,Bdd{
    /**
     * @param $string
     */
    public function code($string){

    }
}