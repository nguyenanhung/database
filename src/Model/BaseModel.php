<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 10/16/18
 * Time: 11:22
 */

namespace nguyenanhung\MyDatabase\Model;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use nguyenanhung\MyDebug\Debug;
use nguyenanhung\MyDatabase\Interfaces\ProjectInterface;
use nguyenanhung\MyDatabase\Interfaces\ModelInterface;
use nguyenanhung\MyDatabase\Interfaces\BaseModelInterface;

/**
 * Class BaseModel
 *
 * Class Base Model sử dụng Query Builder của Illuminate Database
 *
 * Class này chỉ khai báo các hàm cơ bản và thông dụng trong quá trính sử dụng
 * các cú pháp, function khác đều có thể sử dụng theo tài liệu chính thức của Illuminate Database
 *
 * @see       https://laravel.com/docs/5.4/database
 * @see       https://packagist.org/packages/illuminate/database#v5.4.36
 *
 * @package   nguyenanhung\MyDatabase\Model
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 * @since     2018-10-16
 * @version   0.1.2
 */
class BaseModel implements ProjectInterface, ModelInterface, BaseModelInterface
{
    /** @var object Đối tượng khởi tạo dùng gọi đến Class Debug \nguyenanhung\MyDebug\Debug */
    protected $debug;
    /** @var array|null Mảng dữ liệu chứa thông tin database cần kết nối tới */
    protected $database;
    /** @var string|null Bảng cần lấy dữ liệu */
    protected $table;
    /** @var object Đối tượng khởi tạo dùng gọi đến Class Capsule Manager \Illuminate\Database\Capsule\Manager */
    protected $db;
    /** @var bool Cấu hình trạng thái Debug, TRUE nếu bật, FALSE nếu tắt */
    public $debugStatus = FALSE;
    /**
     * @var null|string Cấu hình Level Debug
     * @see https://github.com/nguyenanhung/my-debug/blob/master/src/Interfaces/DebugInterface.php
     */
    public $debugLevel = NULL;
    /** @var null|bool|string Cấu hình thư mục lưu trữ Log, VD: /your/to/path */
    public $debugLoggerPath = NULL;
    /** @var null|string Cấu hình File Log, VD: Log-2018-10-15.log | Log-date('Y-m-d').log */
    public $debugLoggerFilename = NULL;
    /** @var string Primary Key Default */
    public $primaryKey = 'id';

    /**
     * BaseModel constructor.
     */
    public function __construct()
    {
        $this->debug = new Debug();
        if ($this->debugStatus === TRUE) {
            $this->debug->setDebugStatus($this->debugStatus);
            if ($this->debugLevel) {
                $this->debug->setGlobalLoggerLevel($this->debugLevel);
            }
            if ($this->debugLoggerPath) {
                $this->debug->setLoggerPath($this->debugLoggerPath);
            }
            if (empty($this->debugLoggerFilename)) {
                $this->debugLoggerFilename = 'Log-' . date('Y-m-d') . '.log';
            }
            $this->debug->setLoggerSubPath(__CLASS__);
            $this->debug->setLoggerFilename($this->debugLoggerFilename);
        }
    }

    /**
     * BaseModel destructor.
     */
    public function __destruct()
    {
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
     * Hàm khởi tạo kết nối đến Cơ sở dữ liệu
     *
     * Sử dụng đối tượng DB được truyền từ bên ngoài vào
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 15:47
     *
     */
    public function connection()
    {
        $this->db = new DB;
        $this->db->addConnection($this->database);
        $this->db->setEventDispatcher(new Dispatcher(new Container));
        $this->db->setAsGlobal();
        $this->db->bootEloquent();
    }

    /**
     * Hàm set và kết nối cơ sở dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:43
     *
     * @param array $database Mảng dữ liệu thông tin DB cần kết nối
     *
     * @see   https://github.com/nguyenanhung/database/tree/master/src/Repository/config/example_db.php
     * @see   https://packagist.org/packages/illuminate/database#v5.4.36
     */
    public function setDatabase($database = [])
    {
        $this->database = $database;
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
     * @see   https://laravel.com/docs/5.4/queries#deletes
     *
     */
    public function truncate()
    {
        DB::table($this->table)->truncate();
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
        $this->connection();
        $db = DB::table($this->table);
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->count();
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
        $this->connection();
        $db = DB::table($this->table)->where($field, '=', $value);
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->count();
    }

    /**
     * Hàm lấy bản ghi mới nhất theo điều kiện
     *
     * Mặc định giá trị so sánh dựa trên column created_at
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 01:06
     *
     * @param array  $selectField Danh sách các column cần lấy
     * @param string $byColumn    Column cần so sánh dữ liệu, mặc định sẽ sử dụng column created_at
     *
     * @see   https://laravel.com/docs/5.4/queries#ordering-grouping-limit-and-offset
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null|object Object dữ liệu đầu ra
     *                                                                                            của bản ghi
     */
    public function getLatest($selectField = ['*'], $byColumn = 'created_at')
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $this->connection();
        $db = DB::table($this->table)->latest($byColumn);
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->first($selectField);
    }

    /**
     * Hàm lấy bản ghi cũ nhất nhất theo điều kiện
     *
     * Mặc định giá trị so sánh dựa trên column created_at
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 01:06
     *
     * @param array  $selectField Danh sách các column cần lấy
     * @param string $byColumn    Column cần so sánh dữ liệu, mặc định sẽ sử dụng column created_at
     *
     * @see   https://laravel.com/docs/5.4/queries#ordering-grouping-limit-and-offset
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null|object Object dữ liệu đầu ra
     *                                                                                            của bản ghi
     */
    public function getOldest($selectField = ['*'], $byColumn = 'created_at')
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $this->connection();
        $db = DB::table($this->table)->oldest($byColumn);
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->first($selectField);
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
     * @see   https://laravel.com/docs/5.4/queries#selects
     *
     * @return array|\Illuminate\Support\Collection|string Mảng|String|Object dữ liều phụ hợp với yêu cầu
     *                                                     map theo biến format truyền vào
     */
    public function getInfo($value = '', $field = 'id', $format = NULL)
    {
        $this->connection();
        $format = strtolower($format);
        $db     = DB::table($this->table);
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
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        if ($format == 'result') {
            $result = $db->get();
            $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        } else {
            $result = $db->first();
            $this->debug->debug(__FUNCTION__, 'Format is get first Result => ' . json_encode($result));
        }
        if ($format == 'json') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Json');

            return $result->toJson();
        } elseif ($format == 'array') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Array');

            return $result->toArray();
        } elseif ($format == 'base') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Base');

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
     * @see   https://laravel.com/docs/5.4/queries#selects
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|mixed|null|object
     */
    public function getValue($value = '', $field = 'id', $fieldOutput = '')
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($value) && count($value) > 0) {
            foreach ($value as $column => $column_value) {
                if (is_array($column_value)) {
                    $db->whereIn($column, $column_value);
                } else {
                    $db->where($column, self::OPERATOR_EQUAL_TO, $column_value);
                }
            }
        } else {
            $db->where($field, self::OPERATOR_EQUAL_TO, $value);
        }
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $result = $db->first();
        $this->debug->debug(__FUNCTION__, 'Result from DB => ' . json_encode($result));
        if (!empty($fieldOutput) && isset($result->$fieldOutput)) {
            $this->debug->debug(__FUNCTION__, 'Tìm thấy thông tin cột dữ liệu ' . $fieldOutput . ' -> ' . $result->$fieldOutput);

            return $result->$fieldOutput;
        } else {
            $this->debug->error(__FUNCTION__, 'Không tìm thấy cột dữ liệu ' . $fieldOutput);

            return $result;
        }
    }

    /**
     * Hàm lấy danh sách Distinct toàn bộ bản ghi trong 1 bảng
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 13:59
     *
     * @param string $selectField Mảng dữ liệu danh sách các field cần so sánh
     *
     * @see   https://laravel.com/docs/5.4/queries#selects
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDistinctResult($selectField = '')
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $this->connection();
        $db = DB::table($this->table);
        $db->distinct();
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $result = $db->get($selectField);
        $this->debug->debug(__FUNCTION__, 'Result from DB => ' . json_encode($result));

        return $result;
    }

    /**
     * Hàm getResultDistinct là alias của hàm getDistinctResult
     *
     * Các tham số đầu ra và đầu vào theo quy chuẩn của hàm getDistinctResult
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 23:49
     *
     * @param string $selectField Mảng dữ liệu danh sách các field cần so sánh
     *
     * @return \Illuminate\Support\Collection
     */
    public function getResultDistinct($selectField = '')
    {
        return $this->getDistinctResult($selectField);
    }

    /**
     * Function getResult
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 16:14
     *
     * @param array|string $wheres              Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     * @param string       $selectField         Mảng dữ liệu danh sách các field cần so sánh
     * @param null|string  $options             Mảng dữ liệu các cấu hình tùy chọn
     *                                          example $options = [
     *                                          'format' => null,
     *                                          'orderBy => [
     *                                          'id' => 'desc'
     *                                          ]
     *                                          ];
     *
     * @see   https://laravel.com/docs/5.4/queries#selects
     *
     * @return array|\Illuminate\Support\Collection|string Mảng|String|Object dữ liều phụ hợp với yêu cầu
     *                                                     map theo biến format truyền vào
     */
    public function getResult($wheres = [], $selectField = '*', $options = NULL)
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        if (isset($options['format'])) {
            $format = strtolower($options['format']);
        } else {
            $format = NULL;
        }
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value)) {
                    $db->whereIn($field, $value);
                } else {
                    $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                }
            }
        } else {
            $db->where($this->primaryKey, self::OPERATOR_EQUAL_TO, $wheres);
        }
        if (isset($options['orderBy']) && is_array($options['orderBy'])) {
            foreach ($options['orderBy'] as $column => $direction) {
                $db->orderBy($column, $direction);
            }
        }
        if (isset($options['orderBy']) && $options['orderBy'] == 'random') {
            $db->inRandomOrder();
        }
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $result = $db->get($selectField);
        $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        if ($format == 'json') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Json');

            return $result->toJson();
        } elseif ($format == 'array') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Array');

            return $result->toArray();
        } elseif ($format == 'base') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Base');

            return $result->toBase();
        } else {
            return $result;
        }
    }

    /**
     * Hàm thêm mới bản ghi vào bảng
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:04
     *
     * @param array $data Mảng chứa dữ liệu cần insert
     *
     * @see   https://laravel.com/docs/5.4/queries#inserts
     *
     * @return int Insert ID của bản ghi
     */
    public function add($data = [])
    {
        $this->connection();
        $db = DB::table($this->table);
        $id = $db->insertGetId($data);
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $this->debug->info(__FUNCTION__, 'Result Insert ID: ' . $id);

        return $id;
    }

    /**
     * Hàm update dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:10
     *
     * @param array        $data   Mảng dữ liệu cần Update
     * @param array|string $wheres Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     *
     * @see   https://laravel.com/docs/5.4/queries#updates
     *
     * @return int Số bản ghi được update thỏa mãn với điều kiện đầu vào
     */
    public function update($data = [], $wheres = [])
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value)) {
                    $db->whereIn($field, $value);
                } else {
                    $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                }
            }
        } else {
            $db->where($this->primaryKey, self::OPERATOR_EQUAL_TO, $wheres);
        }
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $resultId = $db->update($data);
        $this->debug->info(__FUNCTION__, 'Result Update Rows: ' . $resultId);

        return $resultId;
    }

    /**
     * Hàm xóa dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:13
     *
     * @param array|string $wheres Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     *
     * @see   https://laravel.com/docs/5.4/queries#deletes
     *
     * @return int Số bản ghi đã xóa
     */
    public function delete($wheres = [])
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value)) {
                    $db->whereIn($field, $value);
                } else {
                    $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                }
            }
        } else {
            $db->where($this->primaryKey, self::OPERATOR_EQUAL_TO, $wheres);
        }
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $resultId = $db->delete();
        $this->debug->info(__FUNCTION__, 'Result Delete Rows: ' . $resultId);

        return $resultId;
    }
}
