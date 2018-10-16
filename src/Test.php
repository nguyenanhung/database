<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 10/16/18
 * Time: 11:22
 */

namespace nguyenanhung\MyDatabase;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class Test
{
    protected $db;
    protected $capsule;

    public function __construct()
    {
        $this->db      = [
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'database'  => 'vas_content',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ];
        $this->capsule = new Capsule;
        $this->capsule->addConnection($this->db);
        $this->capsule->setEventDispatcher(new Dispatcher(new Container));
        // Make this Capsule instance available globally via static methods... (optional)
        $this->capsule->setAsGlobal();
        // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $this->capsule->bootEloquent();
    }

    public function test()
    {
        $test = Capsule::table('data_news_version_2_category')
                       ->where('status', '=', 1)
                       ->get();
        d($test);
    }
}
