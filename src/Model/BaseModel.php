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
    public    $primaryKey = 'id';

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
     * Hàm truncate bảng dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:15
     *
     */
    public function truncate()
    {
        Capsule::table($this->table)->truncate();
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
     * @return int Số lượng bàn ghi tồn tại phù hợp với điều kiện đưa ra
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
     * @return array|\Illuminate\Support\Collection|string Mảng|String|Object dữ liều phụ hợp với yêu cầu
     *                                                     map theo biến format truyền vào
     */
    public function getInfo($value = '', $field = 'id', $format = NULL)
    {
        $format = strtolower($format);
        $db     = Capsule::table($this->table);
        if (is_array($value) && count($value) > 0) {
            foreach ($value as $f => $v) {
                if (is_array($v)) {
                    $db->whereIn($f, $v);
                } else {
                    $db->where($f, '=', $v);
                }
            }
        } else {
            $db->where($field, '=', $value);
        }
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

    /**
     * Hàm lấy giá trị 1 field của bản ghi dựa trên điều kiện 1 bản ghi đầu vào
     *
     * Đây là hàm cơ bản, chỉ áp dụng check theo 1 field
     *
     * Lấy bản ghi đầu tiên phù hợp với điều kiện
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:51
     *
     * @param string $value       Giá trị cần kiểm tra
     * @param string $field       Field tương ứng với giá tri kiểm tra, ví dụ: ID
     * @param string $fieldOutput field kết quả đầu ra
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|mixed|null|object
     */
    public function getValue($value = '', $field = 'id', $fieldOutput = '')
    {
        $db = Capsule::table($this->table);
        if (is_array($value) && count($value) > 0) {
            foreach ($value as $f => $v) {
                if (is_array($v)) {
                    $db->whereIn($f, $v);
                } else {
                    $db->where($f, '=', $v);
                }
            }
        } else {
            $db->where($field, '=', $value);
        }
        $result = $db->first();
        if (!empty($fieldOutput) && isset($result->$fieldOutput)) {
            return $result->$fieldOutput;
        } else {
            return $result;
        }
    }

    /**
     * Hàm lấy danh sách Distinct toàn bộ bản ghi trong 1 bảng
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 13:59
     *
     * @param string $field Mảng dữ liệu danh sách các field cần so sánh
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDistinctResult($field = '')
    {
        if (!is_array($field)) {
            $field = [$field];
        }
        $db     = Capsule::table($this->table);
        $result = $db->distinct()->get($field);

        return $result;
    }

    /**
     * Hàm thêm mới bản ghi vào bảng
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:04
     *
     * @param array $data Mảng chứa dữ liệu cần insert
     *
     * @return int Insert ID của bản ghi
     */
    public function add($data = [])
    {
        $id = Capsule::table($this->table)->insertGetId($data);

        return $id;
    }

    /**
     * Hàm update dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:10
     *
     * @param array        $data   Mảng dữ liệu cần Update
     * @param array|string $wheres Mãng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     *
     * @return int Số bản ghi được update thỏa mãn với điều kiện đầu vào
     */
    public function update($data = [], $wheres = [])
    {
        $db = Capsule::table($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value)) {
                    $db->whereIn($field, $value);
                } else {
                    $db->where($field, '=', $value);
                }
            }
        } else {
            $db->where($this->primaryKey, '=', $wheres);
        }
        $result = $db->update($data);

        return $result;
    }

    /**
     * Hàm xóa dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:13
     *
     * @param array|string $wheres Mãng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     *
     * @return int Số bản ghi đã xóa
     */
    public function delete($wheres = [])
    {
        $db = Capsule::table($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value)) {
                    $db->whereIn($field, $value);
                } else {
                    $db->where($field, '=', $value);
                }
            }
        } else {
            $db->where($this->primaryKey, '=', $wheres);
        }
        $result = $db->delete();

        return $result;
    }

    public function __destruct()
    {
    }
}
