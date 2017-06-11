<?php
namespace Phalcon\Modules\Admin\Models;

use Phalcon\Mvc\Model;

class RobotsParts extends Model{

    public $id;

    public $robots_id;

    public $parts_id;


    public function initialize()
    {
        $this->hasMany(
            "robots_id",
            "Phalcon\\Modules\\Admin\\Models\\Robots",
            "id",
            ['alias'=>'aaaa']
        );

        $this->belongsTo(
            "parts_id",
            "Phalcon\\Modules\\Admin\\Models\\Parts",
            "id"
        );
    }

}