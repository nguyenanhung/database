<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 10/16/18
 * Time: 11:22
 */

namespace nguyenanhung\MyDatabase\Model;

use Exception;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use nguyenanhung\MyDebug\Debug;
use nguyenanhung\MyDatabase\Environment;

/**
 * Class BaseModel
 *
 * Class Base Model sử dụng Query Builder của Illuminate Database
 *
 * Class này chỉ khai báo các hàm cơ bản và thông dụng trong quá trính sử dụng
 * các cú pháp, function khác đều có thể sử dụng theo tài liệu chính thức của Illuminate Database
 *
 * @see       https://laravel.com/docs/5.8/database
 * @see       https://packagist.org/packages/illuminate/database#v5.8.36
 *
 * @package   nguyenanhung\MyDatabase\Model
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 * @since     2018-10-17
 * @version   0.1.2
 */
class BaseModel implements Environment, BaseModelInterface
{
    /** @var object Đối tượng khởi tạo dùng gọi đến Class Debug \nguyenanhung\MyDebug\Debug */
    protected $debug;
    /** @var array|null Mảng dữ liệu chứa thông tin database cần kết nối tới */
    protected $database = null;
    /** @var string|null Bảng cần lấy dữ liệu */
    protected $table = null;
    /** @var object|null Đối tượng khởi tạo dùng gọi đến Class Capsule Manager \Illuminate\Database\Capsule\Manager */
    protected $db = null;
    /** @var mixed $schema */
    protected $schema;
    /** @var string DB Name */
    protected $dbName = 'default';
    /** @var bool|null Cấu hình trạng thái select Raw */
    protected $selectRaw;
    /** @var bool Cấu hình trạng thái Debug, TRUE nếu bật, FALSE nếu tắt */
    public $debugStatus = false;
    /** @var null|string Cấu hình Level Debug */
    public $debugLevel = null;
    /** @var null|bool|string Cấu hình thư mục lưu trữ Log, VD: /your/to/path */
    public $debugLoggerPath = null;
    /** @var null|string Cấu hình File Log, VD: Log-2018-10-15.log | Log-date('Y-m-d').log */
    public $debugLoggerFilename = null;
    /** @var string Primary Key Default */
    public $primaryKey = 'id';

    /**
     * BaseModel constructor.
     *
     * @param array $database
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     */
    public function __construct($database = array())
    {
        $this->debug = new Debug();
        if ($this->debugStatus === true) {
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
        // Cấu trúc kết nối Database qua __construct
        if (!empty($database)) {
            $this->database = $database;
        }
        if (is_array($this->database) && !empty($this->database)) {
            $this->db = new DB;
            $this->db->addConnection($this->database);
            $this->db->setEventDispatcher(new Dispatcher(new Container));
            $this->db->setAsGlobal();
            $this->db->bootEloquent();
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
     * @return string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/29/2021 04:53
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * Function getPrimaryKey
     *
     * @return string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/29/2021 03:44
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * Function setPrimaryKey
     *
     * @param string $primaryKey
     *
     * @return $this
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/29/2021 04:01
     */
    public function setPrimaryKey(string $primaryKey = 'id')
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    /**
     * Function preparePaging
     *
     * @param int $pageIndex
     * @param int $pageSize
     *
     * @return array
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/21/2021 23:24
     */
    public function preparePaging($pageIndex = 1, $pageSize = 10)
    {
        if ($pageIndex != 0) {
            if (!$pageIndex || $pageIndex <= 0 || empty($pageIndex)) {
                $pageIndex = 1;
            }
            $offset = ($pageIndex - 1) * $pageSize;
        } else {
            $offset = $pageIndex;
        }

        return array('offset' => $offset, 'limit' => $pageSize);
    }

    /**
     * Function connection
     *
     * @return $this|\nguyenanhung\MyDatabase\Model\BaseModelInterface
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/07/2020 11:53
     */
    public function connection()
    {
        if (!is_object($this->db)) {
            try {
                $this->db = new DB;
                $this->db->addConnection($this->database);
                $this->db->setEventDispatcher(new Dispatcher(new Container));
                $this->db->setAsGlobal();
                $this->db->bootEloquent();
            } catch (Exception $e) {
                $this->debug->error(__FUNCTION__, 'Error Message: ' . $e->getMessage());
                $this->debug->error(__FUNCTION__, 'Error Trace As String: ' . $e->getTraceAsString());
            }
        }

        return $this;
    }

    /**
     * Function closeConnection
     *
     * @return void
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 46:46
     */
    public function closeConnection()
    {
        try {
            return $this->db->getDatabaseManager()->disconnect($this->dbName);
        } catch (Exception $e) {
            $this->debug->error(__FUNCTION__, 'Error Message: ' . $e->getMessage());
            $this->debug->error(__FUNCTION__, 'Error Trace As String: ' . $e->getTraceAsString());
        }
    }

    /**
     * Function disconnect
     *
     * @return void
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:55
     */
    public function disconnect()
    {
        try {
            return $this->db->getDatabaseManager()->disconnect($this->dbName);
        } catch (Exception $e) {
            $this->debug->error(__FUNCTION__, 'Error Message: ' . $e->getMessage());
            $this->debug->error(__FUNCTION__, 'Error Trace As String: ' . $e->getTraceAsString());
        }
    }

    /**
     * Function getConnection
     *
     * @return object
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 22:27
     */
    public function getConnection()
    {
        return $this->db;
    }

    /**
     * Function getConnectionName
     *
     * @return string
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 22:28
     */
    public function getConnectionName()
    {
        return $this->dbName;
    }

    /**
     * Hàm set và kết nối cơ sở dữ liệu
     *
     * @param array  $database Mảng dữ liệu thông tin DB cần kết nối
     * @param string $name     Tên DB kết nối
     *
     * @return  $this;
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:43
     *
     * @see   https://github.com/nguyenanhung/database/tree/master/src/Repository/config/example_db.php
     * @see   https://packagist.org/packages/illuminate/database#v5.8.36
     */
    public function setDatabase($database = array(), $name = 'default')
    {
        $this->database = $database;
        $this->dbName   = $name;

        return $this;
    }

    /**
     * Function getDatabase
     *
     * @return array|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 23:07
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Hàm set và kết nối đến bảng dữ liệu
     *
     * @param string $table Bảng cần lấy dữ liệu
     *
     * @return  $this;
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:43
     */
    public function setTable($table = '')
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Function getTable
     *
     * @return string|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 23:07
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Function getTableColumns
     *
     * @return array
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/31/2021 37:51
     */
    public function getTableColumns()
    {
        try {
            return Schema::getColumnListing($this->table);
        } catch (Exception $e) {
            $this->debug->error(__FUNCTION__, 'Error Message: ' . $e->getMessage());
            $this->debug->error(__FUNCTION__, 'Error Trace As String: ' . $e->getTraceAsString());
        }
    }

    /**
     * Function getSchema
     *
     * @return \Illuminate\Database\Schema\Builder
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/21/2021 17:43
     */
    public function getSchema()
    {
        try {
            return DB::schema();
        } catch (Exception $e) {
            $this->debug->error(__FUNCTION__, 'Error Message: ' . $e->getMessage());
            $this->debug->error(__FUNCTION__, 'Error Trace As String: ' . $e->getTraceAsString());
        }
    }

    /**
     * Function setSelectRaw
     *
     * @param bool $selectRaw
     *
     * @return $this|\nguyenanhung\MyDatabase\Model\BaseModelInterface
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/07/2020 12:59
     */
    public function setSelectRaw($selectRaw = false)
    {
        $this->selectRaw = $selectRaw;

        return $this;
    }

    /**
     * Function getSelectRaw
     *
     * @return bool|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/07/2020 13:05
     */
    public function getSelectRaw()
    {
        return $this->selectRaw;
    }

    /*************************** DATABASE METHOD ***************************/

    /**
     * Function checkExistsTable
     *
     * @return bool
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/07/2020 13:10
     */
    public function checkExistsTable()
    {
        $this->connection();

        return $this->getSchema()->hasTable($this->table);
    }

    /**
     * Function checkExistsColumn
     *
     * @param string $column
     *
     * @return bool
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-12 15:1
     */
    public function checkExistsColumn($column = '')
    {
        $this->connection();

        return $this->getSchema()->hasColumn($this->table, $column);
    }

    /**
     * Function checkExistsColumns
     *
     * @param array $columns
     *
     * @return bool
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-12 15:10
     */
    public function checkExistsColumns($columns = array())
    {
        $this->connection();

        return $this->getSchema()->hasColumns($this->table, $columns);
    }

    /**
     * Hàm truncate bảng dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:15
     *
     * @see   https://laravel.com/docs/5.8/queries#deletes
     *
     */
    public function truncate()
    {
        DB::table($this->table)->truncate();
    }

    /**
     * Hàm đếm toàn bộ bản ghi tồn tại trong bảng
     *
     * @return int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:43
     *
     */
    public function countAll()
    {
        $this->connection();
        $db = DB::table($this->table);
        $this->debug->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->count();
    }

    /**
     * Hàm kiểm tra sự tồn tại bản ghi theo tham số đầu vào
     *
     * @param string|array $whereValue Giá trị cần kiểm tra
     * @param string|null  $whereField Field tương ứng, ví dụ: ID
     *
     * @return int Số lượng bàn ghi tồn tại phù hợp với điều kiện đưa ra
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:45
     *
     */
    public function checkExists($whereValue = '', $whereField = 'id')
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($whereValue) && count($whereValue) > 0) {
            foreach ($whereValue as $field => $value) {
                if (is_array($value) && isset($value['field']) && isset($value['value'])) {
                    if (is_array($value['value'])) {
                        $db->whereIn($value['field'], $value['value']);
                    } else {
                        $db->where($value['field'], $value['operator'], $value['value']);
                    }
                } else {
                    if (is_array($value)) {
                        $db->whereIn($field, $value);
                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            }
        } else {
            if (is_array($whereValue)) {
                $db->whereIn($whereField, $whereValue);
            } else {
                $db->where($whereField, self::OPERATOR_EQUAL_TO, $whereValue);
            }
        }
        $this->debug->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->count();
    }

    /**
     * Hàm kiểm tra sự tồn tại bản ghi theo tham số đầu vào - Đa điều kiện
     *
     * @param string|array $whereValue Giá trị cần kiểm tra
     * @param string|null  $whereField Field tương ứng, ví dụ: ID
     *
     * @return int Số lượng bàn ghi tồn tại phù hợp với điều kiện đưa ra
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:45
     *
     */
    public function checkExistsWithMultipleWhere($whereValue = '', $whereField = 'id')
    {
        return $this->checkExists($whereValue, $whereField);
    }

    /**
     * Hàm lấy bản ghi mới nhất theo điều kiện
     *
     * Mặc định giá trị so sánh dựa trên column created_at
     *
     * @param array  $selectField Danh sách các column cần lấy
     * @param string $byColumn    Column cần so sánh dữ liệu, mặc định sẽ sử dụng column created_at
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null|object Object dữ liệu đầu ra
     *                                                                                            của bản ghi
     * @see   https://laravel.com/docs/5.8/queries#ordering-grouping-limit-and-offset
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 01:06
     *
     */
    public function getLatest($selectField = ['*'], $byColumn = 'created_at')
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $this->connection();
        $db = DB::table($this->table)->latest($byColumn);
        $this->debug->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->first($selectField);
    }

    /**
     * Hàm lấy bản ghi mới nhất theo điều kiện đầu vào
     *
     * @param array  $whereValue
     * @param array  $selectField
     * @param string $byColumn
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:16
     *
     */
    public function getLatestByColumn($whereValue = array(), $selectField = ['*'], $byColumn = 'created_at')
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($whereValue) && count($whereValue) > 0) {
            foreach ($whereValue as $column => $column_value) {
                if (is_array($column_value)) {
                    $db->whereIn($column, $column_value);
                } else {
                    $db->where($column, self::OPERATOR_EQUAL_TO, $column_value);
                }
            }
        } else {
            $db->where($selectField, self::OPERATOR_EQUAL_TO, $whereValue);
        }
        $db->latest($byColumn);
        $this->debug->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->first($selectField);
    }

    /**
     * Hàm lấy bản ghi cũ nhất nhất theo điều kiện
     *
     * Mặc định giá trị so sánh dựa trên column created_at
     *
     * @param array  $selectField Danh sách các column cần lấy
     * @param string $byColumn    Column cần so sánh dữ liệu, mặc định sẽ sử dụng column created_at
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null|object Object dữ liệu đầu ra
     *                                                                                            của bản ghi
     * @see   https://laravel.com/docs/5.8/queries#ordering-grouping-limit-and-offset
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 01:06
     *
     */
    public function getOldest($selectField = ['*'], $byColumn = 'created_at')
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $this->connection();
        $db = DB::table($this->table)->oldest($byColumn);
        $this->debug->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->first($selectField);
    }

    /**
     * Hàm lấy bản ghi cũ nhất nhất theo điều kiện đầu vào
     *
     * @param array  $whereValue
     * @param array  $selectField
     * @param string $byColumn
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:17
     *
     */
    public function getOldestByColumn($whereValue = array(), $selectField = ['*'], $byColumn = 'created_at')
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($whereValue) && count($whereValue) > 0) {
            foreach ($whereValue as $column => $column_value) {
                if (is_array($column_value)) {
                    $db->whereIn($column, $column_value);
                } else {
                    $db->where($column, self::OPERATOR_EQUAL_TO, $column_value);
                }
            }
        } else {
            $db->where($selectField, self::OPERATOR_EQUAL_TO, $whereValue);
        }
        $db->oldest($byColumn);
        $this->debug->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->first($selectField);
    }

    /**
     * Hàm lấy thông tin bản ghi theo tham số đầu vào
     *
     * Đây là hàm cơ bản, chỉ áp dụng check theo 1 field
     *
     * Lấy bản ghi đầu tiên phù hợp với điều kiện
     *
     * @param array|string      $value       Giá trị cần kiểm tra
     * @param null|string       $field       Field tương ứng, ví dụ: ID
     * @param null|string       $format      Format dữ liệu đầu ra: null, json, array, base, result
     * @param null|string|array $selectField Các field cần lấy
     *
     * @return object|array|\Illuminate\Support\Collection|string Mảng|String|Object dữ liều phụ hợp với yêu cầu
     *                                                     map theo biến format truyền vào
     * @see   https://laravel.com/docs/5.8/queries#selects
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:51
     *
     */
    public function getInfo($value = '', $field = 'id', $format = null, $selectField = null)
    {
        $this->connection();
        $format = strtolower($format);
        if (!empty($selectField)) {
            if (!is_array($selectField)) {
                $selectField = [$selectField];
            }
            if ($this->selectRaw === true) {
                $db = DB::table($this->table)->selectRaw($selectField['expression'], $selectField['bindingParam']);
            } else {
                $db = DB::table($this->table)->select($selectField);
            }
        } else {
            $db = DB::table($this->table)->select();
        }
        if (is_array($value) && count($value) > 0) {
            foreach ($value as $f => $v) {
                if (is_array($v) && isset($v['field']) && isset($v['value'])) {
                    if (is_array($v['value'])) {
                        $db->whereIn($v['field'], $v['value']);
                    } else {
                        $db->where($v['field'], $v['operator'], $v['value']);
                    }
                } else {
                    if (is_array($v)) {
                        $db->whereIn($f, $v);
                    } else {
                        $db->where($f, self::OPERATOR_EQUAL_TO, $v);
                    }
                }
            }
        } else {
            if (is_array($value)) {
                $db->whereIn($field, $value);
            } else {
                $db->where($field, self::OPERATOR_EQUAL_TO, $value);
            }
        }
        $this->debug->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        if ($format == 'result') {
            $result = $db->get();
            // $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        } else {
            $result = $db->first();
            // $this->debug->debug(__FUNCTION__, 'Format is get first Result => ' . json_encode($result));
        }
        if ($format == 'json') {
            // $this->debug->debug(__FUNCTION__, 'Output Result is Json');
            return $result->toJson();
        } elseif ($format == 'array') {
            // $this->debug->debug(__FUNCTION__, 'Output Result is Array');
            return $result->toArray();
        } elseif ($format == 'base') {
            // $this->debug->debug(__FUNCTION__, 'Output Result is Base');
            return $result->toBase();
        } else {
            if ($format == 'result') {
                if ($result->count() <= 0) {
                    return null;
                }
            }

            return $result;
        }
    }

    /**
     * Hàm lấy thông tin bản ghi theo tham số đầu vào - Đa điều kiện
     *
     * @param array|string      $wheres      Giá trị cần kiểm tra
     * @param null|string       $field       Field tương ứng, ví dụ: ID
     * @param null|string       $format      Format dữ liệu đầu ra: null, json, array, base, result
     * @param null|string|array $selectField Các field cần lấy
     *
     * @return array|\Illuminate\Support\Collection|object|string|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/26/18 16:40
     *
     */
    public function getInfoWithMultipleWhere($wheres = '', $field = 'id', $format = null, $selectField = null)
    {
        return $this->getInfo($wheres, $field, $format, $selectField);
    }

    /**
     * Hàm lấy giá trị 1 field của bản ghi dựa trên điều kiện 1 bản ghi đầu vào
     *
     * Đây là hàm cơ bản, chỉ áp dụng check theo 1 field
     *
     * Lấy bản ghi đầu tiên phù hợp với điều kiện
     *
     * @param string $value       Giá trị cần kiểm tra
     * @param string $field       Field tương ứng với giá tri kiểm tra, ví dụ: ID
     * @param string $fieldOutput field kết quả đầu ra
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|mixed|null|object
     * @see   https://laravel.com/docs/5.8/queries#selects
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:51
     *
     */
    public function getValue($value = '', $field = 'id', $fieldOutput = '')
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($value) && count($value) > 0) {
            foreach ($value as $f => $v) {
                if (is_array($v) && isset($v['field']) && isset($v['value'])) {
                    if (is_array($v['value'])) {
                        $db->whereIn($v['field'], $v['value']);
                    } else {
                        $db->where($v['field'], $v['operator'], $v['value']);
                    }
                } else {
                    if (is_array($v)) {
                        $db->whereIn($f, $v);
                    } else {
                        $db->where($f, self::OPERATOR_EQUAL_TO, $v);
                    }
                }
            }
        } else {
            if (is_array($value)) {
                $db->whereIn($field, $value);
            } else {
                $db->where($field, self::OPERATOR_EQUAL_TO, $value);
            }
        }
        $this->debug->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $result = $db->first();
        // $this->debug->debug(__FUNCTION__, 'Result from DB => ' . json_encode($result));
        if (!empty($fieldOutput) && isset($result->$fieldOutput)) {
            return $result->$fieldOutput;
        } else {
            $this->debug->error(__FUNCTION__, 'Không tìm thấy cột dữ liệu ' . $fieldOutput);

            return $result;
        }
    }

    /**
     * Hàm lấy giá trị 1 field của bản ghi dựa trên điều kiện 1 bản ghi đầu vào - Đa điều kiện
     *
     * Đây là hàm cơ bản, chỉ áp dụng check theo 1 field
     *
     * Lấy bản ghi đầu tiên phù hợp với điều kiện
     *
     * @param string $wheres      Giá trị cần kiểm tra
     * @param string $field       Field tương ứng với giá tri kiểm tra, ví dụ: ID
     * @param string $fieldOutput field kết quả đầu ra
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|mixed|null|object
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/26/18 16:41
     *
     */
    public function getValueWithMultipleWhere($wheres = '', $field = 'id', $fieldOutput = '')
    {
        return $this->getValue($wheres, $field, $fieldOutput);
    }

    /**
     * Hàm lấy danh sách Distinct toàn bộ bản ghi trong 1 bảng
     *
     * @param string $selectField Mảng dữ liệu danh sách các field cần so sánh
     *
     * @return \Illuminate\Support\Collection
     * @see   https://laravel.com/docs/5.8/queries#selects
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 13:59
     *
     */
    public function getDistinctResult($selectField = '')
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $this->connection();
        $db = DB::table($this->table);
        $db->distinct();
        $this->debug->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        // $this->debug->debug(__FUNCTION__, 'Result from DB => ' . json_encode($result));
        return $db->get($selectField);
    }

    /**
     * Hàm lấy danh sách Distinct toàn bộ bản ghi theo điều kiện
     *
     * @param string $selectField
     * @param array  $whereValue
     *
     * @return \Illuminate\Support\Collection
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:21
     *
     */
    public function getDistinctResultByColumn($selectField = '', $whereValue = array())
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($whereValue) && count($whereValue) > 0) {
            foreach ($whereValue as $column => $column_value) {
                if (is_array($column_value)) {
                    $db->whereIn($column, $column_value);
                } else {
                    $db->where($column, self::OPERATOR_EQUAL_TO, $column_value);
                }
            }
        } else {
            if (!empty($whereValue)) {
                if (is_array($whereValue)) {
                    $db->whereIn($selectField, $whereValue);
                } else {
                    $db->where($selectField, self::OPERATOR_EQUAL_TO, $whereValue);
                }
            }
        }
        $db->distinct();
        $this->debug->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        //$this->debug->debug(__FUNCTION__, 'Result from DB => ' . json_encode($result));
        return $db->get($selectField);
    }

    /**
     * Hàm getResultDistinct là alias của hàm getDistinctResult
     *
     * Các tham số đầu ra và đầu vào theo quy chuẩn của hàm getDistinctResult
     *
     * @param string $selectField Mảng dữ liệu danh sách các field cần so sánh
     *
     * @return \Illuminate\Support\Collection
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 23:49
     *
     */
    public function getResultDistinct($selectField = '')
    {
        return $this->getDistinctResult($selectField);
    }

    /**
     * Hàm getResultDistinctByColumn là alias của hàm getDistinctResultByColumn
     *
     * @param string $selectField
     * @param array  $whereValue
     *
     * @return \Illuminate\Support\Collection
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:22
     *
     */
    public function getResultDistinctByColumn($selectField = '', $whereValue = array())
    {
        return $this->getDistinctResultByColumn($selectField, $whereValue);
    }

    /**
     * Function getResult
     *
     * @param array|string $wheres              Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     * @param string|array $selectField         Mảng dữ liệu danh sách các field cần so sánh
     * @param null|string  $options             Mảng dữ liệu các cấu hình tùy chọn
     *                                          example $options = [
     *                                          'format' => null,
     *                                          'orderBy => [
     *                                          'id' => 'desc'
     *                                          ]
     *                                          ];
     *
     * @return object|array|\Illuminate\Support\Collection|string Mảng|String|Object dữ liều phụ hợp với yêu cầu
     *                                                     map theo biến format truyền vào
     * @see   https://laravel.com/docs/5.8/queries#selects
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 16:14
     *
     */
    public function getResult($wheres = array(), $selectField = '*', $options = null)
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        if (isset($options['format'])) {
            $format = strtolower($options['format']);
        } else {
            $format = null;
        }
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value) && isset($value['field']) && isset($value['value'])) {
                    if (is_array($value['value'])) {
                        $db->whereIn($value['field'], $value['value']);
                    } else {
                        $db->where($value['field'], $value['operator'], $value['value']);
                    }
                } else {
                    if (is_array($value)) {
                        $db->whereIn($field, $value);
                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            }
        } else {
            if (is_array($wheres)) {
                $db->whereIn($this->primaryKey, $wheres);
            } else {
                $db->where($this->primaryKey, self::OPERATOR_EQUAL_TO, $wheres);
            }
        }
        if ((isset($options['limit']) && $options['limit'] > 0) && isset($options['offset'])) {
            $page = $this->preparePaging($options['offset'], $options['limit']);
            $db->offset($page['offset'])->limit($page['limit']);
        }
        if (isset($options['orderBy']) && is_array($options['orderBy'])) {
            foreach ($options['orderBy'] as $column => $direction) {
                $db->orderBy($column, $direction);
            }
        }
        if (isset($options['orderBy']) && $options['orderBy'] == 'random') {
            $db->inRandomOrder();
        }
        $this->debug->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $result = $db->get($selectField);
        // $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        if ($format == 'json') {
            // $this->debug->debug(__FUNCTION__, 'Output Result is Json');
            return $result->toJson();
        } elseif ($format == 'array') {
            // $this->debug->debug(__FUNCTION__, 'Output Result is Array');
            return $result->toArray();
        } elseif ($format == 'base') {
            // $this->debug->debug(__FUNCTION__, 'Output Result is Base');
            return $result->toBase();
        } else {
            return $result;
        }
    }

    /**
     * Function getResult - Đa điều kiện
     *
     * @param array|string $wheres              Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     * @param string|array $selectField         Mảng dữ liệu danh sách các field cần so sánh
     * @param null|string  $options             Mảng dữ liệu các cấu hình tùy chọn
     *                                          example $options = [
     *                                          'format' => null,
     *                                          'orderBy => [
     *                                          'id' => 'desc'
     *                                          ]
     *                                          ];
     *
     * @return object|array|\Illuminate\Support\Collection|string Mảng|String|Object dữ liều phụ hợp với yêu cầu
     *                                                     map theo biến format truyền vào
     * @see   https://laravel.com/docs/5.8/queries#selects
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 16:14
     *
     */
    public function getResultWithMultipleWhere($wheres = array(), $selectField = '*', $options = null)
    {
        return $this->getResult($wheres, $selectField, $options);
    }

    /**
     * Function countResult
     *
     * @param array  $wheres
     * @param string $selectField
     *
     * @return int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/25/18 14:10
     *
     */
    public function countResult($wheres = array(), $selectField = '*')
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value) && isset($value['field']) && isset($value['value'])) {
                    if (is_array($value['value'])) {
                        $db->whereIn($value['field'], $value['value']);
                    } else {
                        $db->where($value['field'], $value['operator'], $value['value']);
                    }
                } else {
                    if (is_array($value)) {
                        $db->whereIn($field, $value);
                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            }
        } else {
            if (is_array($wheres)) {
                $db->whereIn($this->primaryKey, $wheres);
            } else {
                $db->where($this->primaryKey, self::OPERATOR_EQUAL_TO, $wheres);
            }
        }
        $this->debug->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $result = $db->get($selectField);
        // $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        // $this->debug->debug(__FUNCTION__, 'Total Item Result => ' . json_encode($totalItem));
        return $result->count();
    }

    /**
     * Function countResultWithMultipleWhere
     *
     * @param array  $wheres
     * @param string $selectField
     *
     * @return int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:29
     *
     */
    public function countResultWithMultipleWhere($wheres = array(), $selectField = '*')
    {
        return $this->countResult($wheres, $selectField);
    }

    /**
     * Function getResultWithSimpleJoin
     *
     * @param array  $joins
     * @param string $select
     * @param null   $options
     *
     * @return object|array|\Illuminate\Support\Collection|string
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 02:03
     *
     */
    public function getResultWithSimpleJoin($joins = array(), $select = '*', $options = null)
    {
        $format = isset($options['format']) ? strtolower($options['format']) : null;
        $db     = DB::table($this->table);
        foreach ($joins as $key => $join) {
            $db->join($join['table'], $join['first'], $join['operator'], $join['second']);
        }
        $result = $db->select($select)->get();
        // $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        if ($format == 'json') {
            // $this->debug->debug(__FUNCTION__, 'Output Result is Json');
            return $result->toJson();
        } elseif ($format == 'array') {
            // $this->debug->debug(__FUNCTION__, 'Output Result is Array');
            return $result->toArray();
        } elseif ($format == 'base') {
            // $this->debug->debug(__FUNCTION__, 'Output Result is Base');
            return $result->toBase();
        } else {
            return $result;
        }
    }

    /**
     * Function getResultWithSimpleLeftJoin
     *
     * @param array  $joins
     * @param string $select
     * @param null   $options
     *
     * @return object|array|\Illuminate\Support\Collection|string
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 02:05
     *
     */
    public function getResultWithSimpleLeftJoin($joins = array(), $select = '*', $options = null)
    {
        $format = isset($options['format']) ? strtolower($options['format']) : null;
        $db     = DB::table($this->table);
        foreach ($joins as $key => $join) {
            $db->leftJoin($join['table'], $join['first'], $join['operator'], $join['second']);
        }
        $result = $db->select($select)->get();
        // $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        if ($format == 'json') {
            // $this->debug->debug(__FUNCTION__, 'Output Result is Json');
            return $result->toJson();
        } elseif ($format == 'array') {
            // $this->debug->debug(__FUNCTION__, 'Output Result is Array');
            return $result->toArray();
        } elseif ($format == 'base') {
            // $this->debug->debug(__FUNCTION__, 'Output Result is Base');
            return $result->toBase();
        } else {
            return $result;
        }
    }

    /**
     * Function getResultWithSimpleCrossJoin
     *
     * @param array  $joins
     * @param string $select
     * @param null   $options
     *
     * @return object|array|\Illuminate\Support\Collection|string
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 02:06
     *
     */
    public function getResultWithSimpleCrossJoin($joins = array(), $select = '*', $options = null)
    {
        $format = isset($options['format']) ? strtolower($options['format']) : null;
        $db     = DB::table($this->table);
        foreach ($joins as $key => $join) {
            $db->crossJoin($join['table'], $join['first'], $join['operator'], $join['second']);
        }
        $result = $db->select($select)->get();
        // $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        if ($format == 'json') {
            // $this->debug->debug(__FUNCTION__, 'Output Result is Json');
            return $result->toJson();
        } elseif ($format == 'array') {
            // $this->debug->debug(__FUNCTION__, 'Output Result is Array');
            return $result->toArray();
        } elseif ($format == 'base') {
            // $this->debug->debug(__FUNCTION__, 'Output Result is Base');
            return $result->toBase();
        } else {
            return $result;
        }
    }

    /**
     * Hàm thêm mới bản ghi vào bảng
     *
     * @param array $data Mảng chứa dữ liệu cần insert
     *
     * @return int Insert ID của bản ghi
     * @see   https://laravel.com/docs/5.8/queries#inserts
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:04
     *
     */
    public function add($data = array())
    {
        $this->connection();
        $db = DB::table($this->table);
        $id = $db->insertGetId($data);
        $this->debug->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        // $this->debug->debug(__FUNCTION__, 'Result Insert ID: ' . $id);

        return $id;
    }

    /**
     * Hàm update dữ liệu
     *
     * @param array        $data   Mảng dữ liệu cần Update
     * @param array|string $wheres Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     *
     * @return int Số bản ghi được update thỏa mãn với điều kiện đầu vào
     * @see   https://laravel.com/docs/5.8/queries#updates
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:10
     *
     */
    public function update($data = array(), $wheres = array())
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value) && isset($value['field']) && isset($value['value'])) {
                    if (is_array($value['value'])) {
                        $db->whereIn($value['field'], $value['value']);
                    } else {
                        $db->where($value['field'], $value['operator'], $value['value']);
                    }
                } else {
                    if (is_array($value)) {
                        $db->whereIn($field, $value);
                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            }
        } else {
            if (is_array($wheres)) {
                $db->whereIn($this->primaryKey, $wheres);
            } else {
                $db->where($this->primaryKey, self::OPERATOR_EQUAL_TO, $wheres);
            }
        }
        $this->debug->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        // $this->debug->debug(__FUNCTION__, 'Result Update Rows: ' . $resultId);
        return $db->update($data);
    }

    /**
     * Hàm update dữ liệu - Đa điều kiện
     *
     * @param array        $data   Mảng dữ liệu cần Update
     * @param array|string $wheres Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     *
     * @return int Số bản ghi được update thỏa mãn với điều kiện đầu vào
     * @see   https://laravel.com/docs/5.8/queries#updates
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:10
     *
     */
    public function updateWithMultipleWhere($data = array(), $wheres = array())
    {
        return $this->update($data, $wheres);
    }

    /**
     * Hàm xóa dữ liệu
     *
     * @param array|string $wheres Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     *
     * @return int Số bản ghi đã xóa
     * @see   https://laravel.com/docs/5.8/queries#deletes
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:13
     *
     */
    public function delete($wheres = array())
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value) && isset($value['field']) && isset($value['value'])) {
                    if (is_array($value['value'])) {
                        $db->whereIn($value['field'], $value['value']);
                    } else {
                        $db->where($value['field'], $value['operator'], $value['value']);
                    }
                } else {
                    if (is_array($value)) {
                        $db->whereIn($field, $value);
                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            }
        } else {
            if (is_array($wheres)) {
                $db->whereIn($this->primaryKey, $wheres);
            } else {
                $db->where($this->primaryKey, self::OPERATOR_EQUAL_TO, $wheres);
            }
        }
        $this->debug->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        // $this->debug->debug(__FUNCTION__, 'Result Delete Rows: ' . $resultId);
        return $db->delete();
    }

    /**
     * Hàm xóa dữ liệu - Đa điều kiện
     *
     * @param array|string $wheres Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     *
     * @return int Số bản ghi đã xóa
     * @see   https://laravel.com/docs/5.8/queries#deletes
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:13
     *
     */
    public function deleteWithMultipleWhere($wheres = array())
    {
        return $this->delete($wheres);
    }

    /**
     * Hàm kiểm tra dữ liệu đã tồn tại hay chưa, nếu chưa sẽ ghi mới
     *
     * @param array $data
     * @param array $wheres
     *
     * @return bool|int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 03:58
     *
     */
    public function checkExistsAndInsertData($data = array(), $wheres = array())
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value) && isset($value['field']) && isset($value['value'])) {
                    if (is_array($value['value'])) {
                        $db->whereIn($value['field'], $value['value']);
                    } else {
                        $db->where($value['field'], $value['operator'], $value['value']);
                    }
                } else {
                    if (is_array($value)) {
                        $db->whereIn($field, $value);
                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            }
        } else {
            $db->where($this->primaryKey, self::OPERATOR_EQUAL_TO, $wheres);
        }
        $checkExists = $db->count();
        // $this->debug->debug(__FUNCTION__, 'Check Exists Data: ' . $checkExists);
        if (!$checkExists) {
            $id = $db->insertGetId($data);
            $this->debug->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
            $this->debug->debug(__FUNCTION__, 'Result Insert ID: ' . $id);

            return $id;
        } else {
            $this->debug->debug(__FUNCTION__, 'Đã tồn tại bản ghi, bỏ qua không ghi nữa');

            return false;
        }
    }

    /**
     * Hàm kiểm tra dữ liệu đã tồn tại hay chưa, nếu chưa sẽ ghi mới - Đa điều kiện
     *
     * @param array $data
     * @param array $wheres
     *
     * @return bool|int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 03:58
     *
     */
    public function checkExistsAndInsertDataWithMultipleWhere($data = array(), $wheres = array())
    {
        return $this->checkExistsAndInsertData($data, $wheres);
    }

    /**
     * Hàm kiểm tra dữ liệu đã tồn tại hay chưa, nếu chưa sẽ ghi mới, nếu tồn tại sẵn sẽ update
     *
     * @param array $dataInsert
     * @param array $dataUpdate
     * @param array $wheres
     *
     * @return int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:01
     *
     */
    public function checkExistsAndInsertOrUpdateData($dataInsert = array(), $dataUpdate = array(), $wheres = array())
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value) && isset($value['field']) && isset($value['value'])) {
                    if (is_array($value['value'])) {
                        $db->whereIn($value['field'], $value['value']);
                    } else {
                        $db->where($value['field'], $value['operator'], $value['value']);
                    }
                } else {
                    if (is_array($value)) {
                        $db->whereIn($field, $value);
                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            }
        } else {
            $db->where($this->primaryKey, self::OPERATOR_EQUAL_TO, $wheres);
        }
        $checkExists = $db->count();
        // $this->debug->debug(__FUNCTION__, 'Check Exists Data: ' . $checkExists);
        if (!$checkExists) {
            $id = $db->insertGetId($dataInsert);
            $this->debug->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
            $this->debug->debug(__FUNCTION__, 'Result Insert ID: ' . $id);

            return $id;
        } else {
            $resultId = $db->update($dataUpdate);
            $this->debug->debug(__FUNCTION__, 'Result Update Rows: ' . $resultId);

            return $resultId;
        }
    }

    /**
     * Hàm kiểm tra dữ liệu đã tồn tại hay chưa, nếu chưa sẽ ghi mới, nếu tồn tại sẵn sẽ update - Đa điều kiện
     *
     * @param array $dataInsert
     * @param array $dataUpdate
     * @param array $wheres
     *
     * @return int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:01
     *
     */
    public function checkExistsAndInsertOrUpdateDataWithMultipleWhere($dataInsert = array(), $dataUpdate = array(), $wheres = array())
    {
        return $this->checkExistsAndInsertOrUpdateData($dataInsert, $dataUpdate, $wheres);
    }
}
