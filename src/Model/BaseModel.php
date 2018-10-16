<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 10/16/18
 * Time: 11:22
 */

namespace nguyenanhung\MyDatabase\Model;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use nguyenanhung\MyDatabase\Interfaces\ProjectInterface;

class BaseModel implements ProjectInterface
{
    protected $db;
    protected $table;
    protected $capsule;

    /**
     * BaseModel constructor.
     */
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

    /**
     * Function getVersion
     *
     * @author  : 713uk13m <dev@nguyenanhung.com>
     * @time    : 10/16/18 11:42
     *
     * @return mixed|string Current Project Version
     * @example 0.1.0
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * Hàm set và kết nối cơ sở dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:43
     *
     * @param array $db Mảng dữ liệu thông tin DB cần kết nối
     */
    public function setDatabase($db = [])
    {
        $this->db = $db;
    }

    /**
     * Hàm set và kết nối đến bảng dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:43
     *
     * @param string $table Bảng cần lấy dữ liệu
     */
    public function setTable($table = '')
    {
        $this->table = $table;
    }

    /**
     * Hàm đếm toàn bộ bản ghi tồn tại trong bảng
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:43
     *
     * @return int
     */
    public function countAll()
    {
        $result = Capsule::table($this->table)->count();

        return $result;
    }

    /**
     * Hàm kiểm tra sự tồn tại bản ghi theo tham số đầu vào
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:45
     *
     * @param string $value Giá trị cần kiểm tra
     * @param string $field Field tương ứng, ví dụ: ID
     *
     * @return int
     */
    public function checkExists($value = '', $field = 'id')
    {
        $result = Capsule::table($this->table)
                         ->where($field, '=', $value)
                         ->count();

        return $result;
    }

    /**
     * Hàm lấy thông tin bản ghi theo tham số đầu vào
     *
     * Đây là hàm cơ bản, chỉ áp dụng check theo 1 field
     *
     * Lấy bản ghi đầu tiên phù hợp với điều kiện
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:51
     *
     * @param string      $value  Giá trị cần kiểm tra
     * @param string      $field  Field tương ứng, ví dụ: ID
     * @param null|string $format Format dữ liệu đầu ra: null, json, array, base, result
     *
     * @return array|\Illuminate\Support\Collection|string
     */
    public function getInfo($value = '', $field = 'id', $format = NULL)
    {
        $format = strtolower($format);
        $db     = Capsule::table($this->table);
        $db->where($field, '=', $value);
        if ($format == 'result') {
            $result = $db->get();
        } else {
            $result = $db->first();
        }
        if ($format == 'json') {
            return $result->toJson();
        } elseif ($format == 'array') {
            return $result->toArray();
        } elseif ($format == 'base') {
            return $result->toBase();
        } else {
            return $result;
        }
    }
}
