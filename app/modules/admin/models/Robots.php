<?php
namespace Phalcon\Modules\Admin\Models;
use Phalcon\Mvc\Model;

class Robots extends Model
{

    public $id;

    public $name;

    public function initialize()
    {
        $this->hasManyToMany(
            "id",
            "Phalcon\\Modules\\Admin\\Models\\RobotsParts",
            "robots_id", "parts_id",
            "Phalcon\\Modules\\Admin\\Models\\Parts",
            "id",
            ['alias'=>'sssss']
        );

        $this->hasMany(
            'id',
            "Phalcon\\Modules\\Admin\\Models\\RobotsParts",
            'robots_id',
            ['alias'=>'eeeeee']
        );

        $this->belongsTo(
            "id",
            "Phalcon\\Modules\\Admin\\Models\\RobotsParts",
            'robots_id',
            ['alias'=>'dddd']
        );
    }
}