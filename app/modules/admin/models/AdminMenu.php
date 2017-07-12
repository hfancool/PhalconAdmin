<?php
/**
 * Author: hfan
 * Contact:804667084@qq.com
 * Date: 2017/7/5
 * Time: 22:09
 */

namespace Phalcon\Modules\Admin\Models;

use Phalcon\Mvc\Model;

class AdminMenu extends Model{

    const nsTable = 'Phalcon\Modules\Admin\Models\AdminMenu';

    public function initialize()
    {
        $this->hasMany(
            'menu_id',
            "Phalcon\\Modules\\Admin\\Models\\AdminMenu",
            'pid',
            ['alias'=>'children']
        );

        $this->hasOne(
            'pid',
            "Phalcon\\Modules\\Admin\\Models\\AdminMenu",
            'menu_id',
            ['alias'=>'parents']
        );
    }


    /**
     * 通过父id找出孩子
     */
    public function getMenuList(){

        return $this->getRelated('children') ->toArray();

    }

    /**
     * 通过孩子id找出父父亲
     */
    public function getParent(){
        return $this->getRelated('parents') -> toArray();
    }

}