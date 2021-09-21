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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use nguyenanhung\MyDebug\Logger;
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
class BaseModel implements Environment
{
    /** @var object Đối tượng khởi tạo dùng gọi đến Class Debug \nguyenanhung\MyDebug\Debug */
    protected $logger;

    /** @var array|null Mảng dữ liệu chứa thông tin database cần kết nối tới */
    protected $database = [];

    /** @var string|null Bảng cần lấy dữ liệu */
    protected $table = '';

    /** @var \Illuminate\Database\Capsule\Manager|null $db Đối tượng khởi tạo dùng gọi đến Class Capsule Manager */
    protected $db;

    /** @var mixed $schema */
    protected $schema;

    /** @var string DB Name */
    protected $dbName = 'default';

    /** @var bool|null Cấu hình trạng thái select Raw */
    protected $selectRaw;

    /** @var bool Cấu hình trạng thái Debug, TRUE nếu bật, FALSE nếu tắt */
    public $debugStatus = false;

    /** @var null|string Cấu hình Level Debug */
    public $debugLevel = 'error';

    /** @var null|bool|string Cấu hình thư mục lưu trữ Log, VD: /your/to/path */
    public $debugLoggerPath = '';

    /** @var null|string Cấu hình File Log, VD: Log-2018-10-15.log | Log-date('Y-m-d').log */
    public $debugLoggerFilename = '';

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
        $this->logger = new Logger();
        if ($this->debugStatus === true) {
            $this->logger->setDebugStatus($this->debugStatus);
            if ($this->debugLevel) {
                $this->logger->setGlobalLoggerLevel($this->debugLevel);
            }
            if ($this->debugLoggerPath) {
                $this->logger->setLoggerPath($this->debugLoggerPath);
            }
            if (empty($this->debugLoggerFilename)) {
                $this->debugLoggerFilename = 'Log-' . date('Y-m-d') . '.log';
            }
            $this->logger->setLoggerSubPath(__CLASS__);
            $this->logger->setLoggerFilename($this->debugLoggerFilename);
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
    public function getVersion(): string
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
    public function setPrimaryKey(string $primaryKey = 'id'): self
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
    public function preparePaging($pageIndex = 1, $pageSize = 10): array
    {
        if ($pageIndex !== 0) {
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
     * @return $this
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/20/2021 15:50
     */
    public function connection(): self
    {
        if (!is_object($this->db)) {
            try {
                $this->db = new DB;
                $this->db->addConnection($this->database);
                $this->db->setEventDispatcher(new Dispatcher(new Container));
                $this->db->setAsGlobal();
                $this->db->bootEloquent();
            } catch (Exception $e) {
                $this->logger->error(__FUNCTION__, 'Error Message: ' . $e->getMessage());
                $this->logger->error(__FUNCTION__, 'Error Trace As String: ' . $e->getTraceAsString());
            }
        }

        return $this;
    }

    /**
     * Function closeConnection
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/07/2021 21:40
     */
    public function closeConnection(): void
    {
        try {
            $this->db->getDatabaseManager()->disconnect($this->dbName);
        } catch (Exception $e) {
            $this->logger->error(__FUNCTION__, 'Error Message: ' . $e->getMessage());
            $this->logger->error(__FUNCTION__, 'Error Trace As String: ' . $e->getTraceAsString());
        }
    }

    /**
     * Function disconnect
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/07/2021 21:44
     */
    public function disconnect(): void
    {
        try {
            $this->db->getDatabaseManager()->disconnect($this->dbName);
        } catch (Exception $e) {
            $this->logger->error(__FUNCTION__, 'Error Message: ' . $e->getMessage());
            $this->logger->error(__FUNCTION__, 'Error Trace As String: ' . $e->getTraceAsString());
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
    public function getConnectionName(): string
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
    public function setDatabase($database = array(), $name = 'default'): self
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
    public function getDatabase(): ?array
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
    public function setTable($table = ''): self
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
    public function getTable(): ?string
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
    public function getTableColumns(): ?array
    {
        try {
            return Schema::getColumnListing($this->table);
        } catch (Exception $e) {
            $this->logger->error(__FUNCTION__, 'Error Message: ' . $e->getMessage());
            $this->logger->error(__FUNCTION__, 'Error Trace As String: ' . $e->getTraceAsString());
        }
    }

    /**
     * Function getSchema
     *
     * @return \Illuminate\Database\Schema\Builder|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/20/2021 40:36
     */
    public function getSchema(): ?Builder
    {
        try {
            return DB::schema();
        } catch (Exception $e) {
            $this->logger->error(__FUNCTION__, 'Error Message: ' . $e->getMessage());
            $this->logger->error(__FUNCTION__, 'Error Trace As String: ' . $e->getTraceAsString());

            return null;
        }
    }

    /**
     * Function setSelectRaw
     *
     * @param bool $selectRaw TRUE nếu lấy Select Raw Queries
     *
     * @return $this
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/07/2020 12:59
     */
    public function setSelectRaw($selectRaw = false): self
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
    public function getSelectRaw(): ?bool
    {
        return $this->selectRaw;
    }

    /*************************** DATABASE METHOD ***************************/

    /**
     * Function checkExistsTable - Hàm kiểm tra sự tồn tại của 1 bảng
     *
     * @return bool
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/07/2020 13:10
     */
    public function checkExistsTable(): bool
    {
        $this->connection();
        if ($this->getSchema() !== null) {
            return $this->getSchema()->hasTable($this->table);
        }

        return false;
    }

    /**
     * Function checkExistsColumn - Hàm kiểm tra 1 column có tồn tại trong bảng hay không?
     *
     * @param string $column
     *
     * @return bool
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-12 15:1
     */
    public function checkExistsColumn($column = ''): bool
    {
        $this->connection();
        if ($this->getSchema() !== null) {
            return $this->getSchema()->hasColumn($this->table, $column);
        }

        return false;

    }

    /**
     * Function checkExistsColumns - Hàm kiểm tra 1 mảng nhiều cột có tồn tại trong bảng hay không?
     *
     * @param array $columns
     *
     * @return bool
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-12 15:10
     */
    public function checkExistsColumns($columns = array()): bool
    {
        $this->connection();

        if ($this->getSchema() !== null) {
            return $this->getSchema()->hasColumns($this->table, $columns);
        }

        return false;

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
    public function truncate(): void
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
    public function countAll(): int
    {
        $this->connection();
        $db = DB::table($this->table);
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->count();
    }

    /**
     * Hàm kiểm tra sự tồn tại bản ghi theo tham số đầu vào
     *
     * @param string|array $whereValue Giá trị cần kiểm tra, có thể là 1 string hoặc 1 array chứa nhiều cột
     * @param string|null  $whereField Field tương ứng cần kiểm tra đối chiếu
     *
     * @return int Số lượng bàn ghi tồn tại phù hợp với điều kiện đưa ra
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:45
     *
     */
    public function checkExists($whereValue = '', $whereField = 'id'): int
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($whereValue)) {
            if (count($whereValue) > 0) {
                foreach ($whereValue as $field => $value) {
                    if (is_array($value)) {
                        if (isset($value['field'], $value['value'])) {
                            if (is_array($value['value'])) {
                                $db->whereIn($value['field'], $value['value']);
                            } else {
                                $db->where($value['field'], $value['operator'], $value['value']);
                            }
                        } else {
                            $db->whereIn($field, $value);
                        }

                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            } else {
                $db->whereIn($whereField, $whereValue);
            }
        } else {
            $db->where($whereField, self::OPERATOR_EQUAL_TO, $whereValue);
        }
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

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
    public function checkExistsWithMultipleWhere($whereValue = '', $whereField = 'id'): int
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
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->first($selectField);
    }

    /**
     * Hàm lấy bản ghi mới nhất theo điều kiện đầu vào
     *
     * @param string|array $whereValue  Giá trị cần kiểm tra, có thể là 1 string hoặc 1 array chứa nhiều cột
     * @param string|array $selectField Danh sách cột cần lấy dữ liệu ra
     * @param string       $byColumn    Tên cột cần order theo điều kiện
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:16
     */
    public function getLatestByColumn($whereValue = array(), $selectField = array('*'), $byColumn = 'created_at')
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
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

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
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->first($selectField);
    }

    /**
     * Hàm lấy bản ghi cũ nhất nhất theo điều kiện đầu vào
     *
     * @param string|array $whereValue  Giá trị cần kiểm tra, có thể là 1 string hoặc 1 array chứa nhiều cột
     * @param string|array $selectField Danh sách cột cần lấy dữ liệu ra
     * @param string       $byColumn    Tên cột cần order theo điều kiện
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
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->first($selectField);
    }

    /**
     * Hàm lấy thông tin bản ghi theo tham số đầu vào
     *
     * Đây là hàm cơ bản, chỉ áp dụng check theo 1 field
     *
     * Lấy bản ghi đầu tiên phù hợp với điều kiện
     *
     * @param array|string      $whereValue  Giá trị cần kiểm tra
     * @param null|string       $whereField  Field tương ứng, ví dụ: ID
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
    public function getInfo($whereValue = '', $whereField = 'id', $format = null, $selectField = null)
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
        if (is_array($whereValue)) {
            if (count($whereValue) > 0) {
                foreach ($whereValue as $field => $value) {
                    if (is_array($value)) {
                        if (isset($value['field'], $value['value'])) {
                            if (is_array($value['value'])) {
                                $db->whereIn($value['field'], $value['value']);
                            } else {
                                $db->where($value['field'], $value['operator'], $value['value']);
                            }
                        } else {
                            $db->whereIn($field, $value);
                        }

                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            } else {
                $db->whereIn($whereField, $whereValue);
            }
        } else {
            $db->where($whereField, self::OPERATOR_EQUAL_TO, $whereValue);
        }

        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        if ($format === 'result') {
            $result = $db->get();
            // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        } else {
            $result = $db->first();
            // $this->logger->debug(__FUNCTION__, 'Format is get first Result => ' . json_encode($result));
        }

        if ($format === 'json') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Json');
            return $result->toJson();
        }

        if ($format === 'array') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Array');
            return $result->toArray();
        }

        if ($format === 'base') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Base');
            return $result->toBase();
        }

        if (($format === 'result') && $result->count() <= 0) {
            return null;
        }

        return $result;
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
     * @param string|array $whereValue  Giá trị cần kiểm tra
     * @param string       $whereField  Field tương ứng với giá tri kiểm tra, ví dụ: ID
     * @param string       $fieldOutput field kết quả đầu ra
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|mixed|null|object
     * @see   https://laravel.com/docs/5.8/queries#selects
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:51
     *
     */
    public function getValue($whereValue = '', $whereField = 'id', $fieldOutput = '')
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($whereValue)) {
            if (count($whereValue) > 0) {
                foreach ($whereValue as $field => $value) {
                    if (is_array($value)) {
                        if (isset($value['field'], $value['value'])) {
                            if (is_array($value['value'])) {
                                $db->whereIn($value['field'], $value['value']);
                            } else {
                                $db->where($value['field'], $value['operator'], $value['value']);
                            }
                        } else {
                            $db->whereIn($field, $value);
                        }

                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            } else {
                $db->whereIn($whereField, $whereValue);
            }
        } else {
            $db->where($whereField, self::OPERATOR_EQUAL_TO, $whereValue);
        }
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $result = $db->first();
        // $this->logger->debug(__FUNCTION__, 'Result from DB => ' . json_encode($result));
        if (!empty($fieldOutput) && $result !== null && isset($result->$fieldOutput)) {
            return $result->$fieldOutput;
        }

        $this->logger->error(__FUNCTION__, 'Không tìm thấy cột dữ liệu ' . $fieldOutput);

        return $result;
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
     * @param string|array $selectField Mảng dữ liệu danh sách các field cần so sánh
     *
     * @return \Illuminate\Support\Collection
     * @see   https://laravel.com/docs/5.8/queries#selects
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 13:59
     *
     */
    public function getDistinctResult($selectField = ''): Collection
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $this->connection();
        $db = DB::table($this->table);
        $db->distinct();
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        // $this->logger->debug(__FUNCTION__, 'Result from DB => ' . json_encode($result));
        return $db->get($selectField);
    }

    /**
     * Hàm lấy danh sách Distinct toàn bộ bản ghi theo điều kiện
     *
     * @param string|array $selectField Danh sách các cột dữ liệu cần lấy ra
     * @param array|string $whereValue  Điều kiện kiểm tra đầu vào của dữ liệu
     *
     * @return \Illuminate\Support\Collection
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:21
     *
     */
    public function getDistinctResultByColumn($selectField = '', $whereValue = array()): Collection
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($whereValue)) {
            if (count($whereValue) > 0) {
                foreach ($whereValue as $column => $column_value) {
                    if (is_array($column_value)) {
                        $db->whereIn($column, $column_value);
                    } else {
                        $db->where($column, self::OPERATOR_EQUAL_TO, $column_value);
                    }
                }
            } else {
                $db->whereIn($selectField, $whereValue);
            }
        } else {
            $db->where($selectField, self::OPERATOR_EQUAL_TO, $whereValue);
        }
        $db->distinct();
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        //$this->logger->debug(__FUNCTION__, 'Result from DB => ' . json_encode($result));
        return $db->get($selectField);
    }

    /**
     * Hàm getResultDistinct là alias của hàm getDistinctResult
     *
     * Các tham số đầu ra và đầu vào theo quy chuẩn của hàm getDistinctResult
     *
     * @param string|array $selectField Mảng dữ liệu danh sách các field cần so sánh
     *
     * @return \Illuminate\Support\Collection
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 23:49
     *
     */
    public function getResultDistinct($selectField = ''): Collection
    {
        return $this->getDistinctResult($selectField);
    }

    /**
     * Hàm getResultDistinctByColumn là alias của hàm getDistinctResultByColumn
     *
     * @param string|array $selectField
     * @param array        $whereValue
     *
     * @return \Illuminate\Support\Collection
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:22
     *
     */
    public function getResultDistinctByColumn($selectField = '', $whereValue = array()): Collection
    {
        return $this->getDistinctResultByColumn($selectField, $whereValue);
    }

    /**
     * Function getResult
     *
     * @param array|string      $whereValue     Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     * @param string|array      $selectField    Mảng dữ liệu danh sách các field cần so sánh
     * @param null|string|array $options        Mảng dữ liệu các cấu hình tùy chọn
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
    public function getResult($whereValue = array(), $selectField = '*', $options = null)
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

        if (is_array($whereValue)) {
            if (count($whereValue) > 0) {
                foreach ($whereValue as $field => $value) {
                    if (is_array($value)) {
                        if (isset($value['field'], $value['value'])) {
                            if (is_array($value['value'])) {
                                $db->whereIn($value['field'], $value['value']);
                            } else {
                                $db->where($value['field'], $value['operator'], $value['value']);
                            }
                        } else {
                            $db->whereIn($field, $value);
                        }

                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            } else {
                $db->whereIn($this->primaryKey, $whereValue);
            }
        } else {
            $db->where($this->primaryKey, self::OPERATOR_EQUAL_TO, $whereValue);
        }

        // Case có cả Limit và Offset -> active phân trang
        if (
            isset($options['limit'], $options['offset']) && $options['limit'] > 0
        ) {
            $page = $this->preparePaging($options['offset'], $options['limit']);
            $db->offset($page['offset'])->limit($page['limit']);
        }
        // Case chỉ có Limit
        if (
            (isset($options['limit']) && $options['limit'] > 0) &&
            !isset($options['offset'])
        ) {
            $db->limit($options['limit']);
        }
        if (isset($options['orderBy']) && is_array($options['orderBy'])) {
            foreach ($options['orderBy'] as $column => $direction) {
                $db->orderBy($column, $direction);
            }
        }

        if (isset($options['orderBy']) && $options['orderBy'] === 'random') {
            $db->inRandomOrder();
        }
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $result = $db->get($selectField);
        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        if ($format === 'json') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Json');
            return $result->toJson();
        }

        if ($format === 'array') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Array');
            return $result->toArray();
        }

        if ($format === 'base') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Base');
            return $result->toBase();
        }

        return $result;
    }

    /**
     * Function getResult - Đa điều kiện
     *
     * @param array|string      $wheres         Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     * @param string|array      $selectField    Mảng dữ liệu danh sách các field cần so sánh
     * @param null|string|array $options        Mảng dữ liệu các cấu hình tùy chọn
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
     * @param string|array $whereValue  Điều kiện cần thực thi đối với các cột (queries)
     * @param string|array $selectField Danh sách các cột cần lấy ra. Mặc định sẽ là select *
     *
     * @return int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/25/18 14:10
     *
     */
    public function countResult($whereValue = array(), $selectField = '*'): int
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($whereValue)) {
            if (count($whereValue) > 0) {
                foreach ($whereValue as $field => $value) {
                    if (is_array($value)) {
                        if (isset($value['field'], $value['value'])) {
                            if (is_array($value['value'])) {
                                $db->whereIn($value['field'], $value['value']);
                            } else {
                                $db->where($value['field'], $value['operator'], $value['value']);
                            }
                        } else {
                            $db->whereIn($field, $value);
                        }

                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            } else {
                $db->whereIn($this->primaryKey, $whereValue);
            }
        } else {
            $db->where($this->primaryKey, self::OPERATOR_EQUAL_TO, $whereValue);
        }
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $result = $db->get($selectField);
        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        // $this->logger->debug(__FUNCTION__, 'Total Item Result => ' . json_encode($totalItem));
        return $result->count();
    }

    /**
     * Function countResultWithMultipleWhere
     *
     * @param string|array $wheres      Điều kiện cần thực thi đối với các cột (queries)
     * @param string|array $selectField Danh sách các cột cần lấy ra. Mặc định sẽ là select *
     *
     * @return int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:29
     *
     */
    public function countResultWithMultipleWhere($wheres = array(), $selectField = '*'): int
    {
        return $this->countResult($wheres, $selectField);
    }

    /**
     * Function getResultWithSimpleJoin
     *
     * @param array             $joins          Danh sách các bảng dữ liệu cần join vào để lấy dữ liệu. Cấu trúc dạng $joins = [
     *                                          'tableA' => [
     *                                          'table' => 'tableA',
     *                                          'first' => 'tableAID',
     *                                          'operator' => '=',
     *                                          'second' => 'CurrentTableNameID'
     *                                          ]
     *                                          ]
     * @param string|array      $select         Danh sách các cột cần lấy dữ liệu ra
     * @param null|string|array $options        Mảng dữ liệu các cấu hình tùy chọn
     *                                          example $options = [
     *                                          'format' => null,
     *                                          'orderBy => [
     *                                          'id' => 'desc'
     *                                          ]
     *                                          ];
     *
     * @return object|array|\Illuminate\Support\Collection|string
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 02:03
     *
     */
    public function getResultWithSimpleJoin($joins = array(), $select = '*', $options = null)
    {
        return $this->getResultWithSimpleInnerJoin($joins, $select, $options);
    }

    /**
     * Function getResultWithSimpleInnerJoin
     *
     * @param array             $joins          Danh sách các bảng dữ liệu cần join vào để lấy dữ liệu. Cấu trúc dạng $joins = [
     *                                          'tableA' => [
     *                                          'table' => 'tableA',
     *                                          'first' => 'tableAID',
     *                                          'operator' => '=',
     *                                          'second' => 'CurrentTableNameID'
     *                                          ]
     *                                          ]
     * @param string|array      $select         Danh sách các cột cần lấy dữ liệu ra
     * @param null|string|array $options        Mảng dữ liệu các cấu hình tùy chọn
     *                                          example $options = [
     *                                          'format' => null,
     *                                          'orderBy => [
     *                                          'id' => 'desc'
     *                                          ]
     *                                          ];
     *
     * @return object|array|\Illuminate\Support\Collection|string
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 02:03
     *
     */
    public function getResultWithSimpleInnerJoin($joins = array(), $select = '*', $options = null)
    {
        $format = isset($options['format']) ? strtolower($options['format']) : null;
        $db     = DB::table($this->table);
        foreach ($joins as $key => $join) {
            $db->join($join['table'], $join['first'], $join['operator'], $join['second']);
        }
        if (!is_array($select)) {
            $select = [$select];
        }
        $result = $db->select($select)->get();
        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        if ($format === 'json') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Json');
            return $result->toJson();
        }

        if ($format === 'array') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Array');
            return $result->toArray();
        }

        if ($format === 'base') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Base');
            return $result->toBase();
        }

        return $result;
    }

    /**
     * Function getResultWithInnerJoinAndWheres
     *
     * @param array             $joins          Danh sách các bảng dữ liệu cần join vào để lấy dữ liệu. Cấu trúc dạng $joins = [
     *                                          'tableA' => [
     *                                          'table' => 'tableA',
     *                                          'first' => 'tableAID',
     *                                          'operator' => '=',
     *                                          'second' => 'CurrentTableNameID'
     *                                          ]
     *                                          ]
     * @param string|array      $wheres         Mảng chứa danh sách các điều kiện để lấy ra dữ liệu, cần map với tất cả các bảng joins
     * @param string|array      $select         Danh sách các cột cần lấy dữ liệu ra
     * @param null|string|array $options        Mảng dữ liệu các cấu hình tùy chọn
     *                                          example $options = [
     *                                          'format' => null,
     *                                          'orderBy => [
     *                                          'id' => 'desc'
     *                                          ]
     *                                          ];
     *
     * @return object|array|\Illuminate\Support\Collection|string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/21/2021 51:55
     *
     */
    public function getResultWithInnerJoinAndWheres($joins = array(), $wheres = array(), $select = '*', $options = null)
    {
        $format = isset($options['format']) ? strtolower($options['format']) : null;
        $db     = DB::table($this->table);
        foreach ($joins as $key => $join) {
            $db->joinWhere($join['table'], $join['first'], $join['operator'], $join['second']);
        }
        if (is_array($wheres)) {
            if (count($wheres) > 0) {
                foreach ($wheres as $field => $value) {
                    if (is_array($value)) {
                        if (isset($value['field'], $value['value'])) {
                            if (is_array($value['value'])) {
                                $db->whereIn($value['field'], $value['value']);
                            } else {
                                $db->where($value['field'], $value['operator'], $value['value']);
                            }
                        } else {
                            $db->whereIn($field, $value);
                        }

                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            } else {
                $db->whereIn($this->table . '.' . $this->primaryKey, $wheres);
            }
        } else {
            $db->where($this->table . '.' . $this->primaryKey, self::OPERATOR_EQUAL_TO, $wheres);
        }
        if (!is_array($select)) {
            $select = [$select];
        }
        $result = $db->select($select)->get();
        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        if ($format === 'json') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Json');
            return $result->toJson();
        }

        if ($format === 'array') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Array');
            return $result->toArray();
        }

        if ($format === 'base') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Base');
            return $result->toBase();
        }

        return $result;
    }

    /**
     * Function getResultWithSimpleCrossJoin
     *
     * @param array             $joins          Danh sách các bảng dữ liệu cần join vào để lấy dữ liệu. Cấu trúc dạng $joins = [
     *                                          'tableA' => [
     *                                          'table' => 'tableA',
     *                                          'first' => 'tableAID',
     *                                          'operator' => '=',
     *                                          'second' => 'CurrentTableNameID'
     *                                          ]
     *                                          ]
     * @param string|array      $select         Danh sách các cột cần lấy dữ liệu ra
     * @param null|string|array $options        Mảng dữ liệu các cấu hình tùy chọn
     *                                          example $options = [
     *                                          'format' => null,
     *                                          'orderBy => [
     *                                          'id' => 'desc'
     *                                          ]
     *                                          ];
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
        if (!is_array($select)) {
            $select = [$select];
        }
        $result = $db->select($select)->get();
        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        if ($format === 'json') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Json');
            return $result->toJson();
        }

        if ($format === 'array') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Array');
            return $result->toArray();
        }

        if ($format === 'base') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Base');
            return $result->toBase();
        }

        return $result;
    }

    /**
     * Function getResultWithCrossJoinAndWheres
     *
     * @param array             $joins          Danh sách các bảng dữ liệu cần join vào để lấy dữ liệu. Cấu trúc dạng $joins = [
     *                                          'tableA' => [
     *                                          'table' => 'tableA',
     *                                          'first' => 'tableAID',
     *                                          'operator' => '=',
     *                                          'second' => 'CurrentTableNameID'
     *                                          ]
     *                                          ]
     * @param string|array      $wheres         Mảng chứa danh sách các điều kiện để lấy ra dữ liệu, cần map với tất cả các bảng joins
     * @param string|array      $select         Danh sách các cột cần lấy dữ liệu ra
     * @param null|string|array $options        Mảng dữ liệu các cấu hình tùy chọn
     *                                          example $options = [
     *                                          'format' => null,
     *                                          'orderBy => [
     *                                          'id' => 'desc'
     *                                          ]
     *                                          ];
     *
     * @return object|array|\Illuminate\Support\Collection|string
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 02:06
     *
     */
    public function getResultWithCrossJoinAndWheres($joins = array(), $wheres = array(), $select = '*', $options = null)
    {
        $format = isset($options['format']) ? strtolower($options['format']) : null;
        $db     = DB::table($this->table);
        foreach ($joins as $key => $join) {
            $db->crossJoin($join['table'], $join['first'], $join['operator'], $join['second']);
        }
        if (is_array($wheres)) {
            if (count($wheres) > 0) {
                foreach ($wheres as $field => $value) {
                    if (is_array($value)) {
                        if (isset($value['field'], $value['value'])) {
                            if (is_array($value['value'])) {
                                $db->whereIn($value['field'], $value['value']);
                            } else {
                                $db->where($value['field'], $value['operator'], $value['value']);
                            }
                        } else {
                            $db->whereIn($field, $value);
                        }

                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            } else {
                $db->whereIn($this->table . '.' . $this->primaryKey, $wheres);
            }
        } else {
            $db->where($this->table . '.' . $this->primaryKey, self::OPERATOR_EQUAL_TO, $wheres);
        }
        if (!is_array($select)) {
            $select = [$select];
        }
        $result = $db->select($select)->get();
        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        if ($format === 'json') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Json');
            return $result->toJson();
        }

        if ($format === 'array') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Array');
            return $result->toArray();
        }

        if ($format === 'base') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Base');
            return $result->toBase();
        }

        return $result;
    }

    /**
     * Function getResultWithSimpleLeftJoin
     *
     * @param array             $joins          Danh sách các bảng dữ liệu cần join vào để lấy dữ liệu. Cấu trúc dạng $joins = [
     *                                          'tableA' => [
     *                                          'table' => 'tableA',
     *                                          'first' => 'tableAID',
     *                                          'operator' => '=',
     *                                          'second' => 'CurrentTableNameID'
     *                                          ]
     *                                          ]
     * @param string|array      $select         Danh sách các cột cần lấy dữ liệu ra
     * @param null|string|array $options        Mảng dữ liệu các cấu hình tùy chọn
     *                                          example $options = [
     *                                          'format' => null,
     *                                          'orderBy => [
     *                                          'id' => 'desc'
     *                                          ]
     *                                          ];
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
        if (!is_array($select)) {
            $select = [$select];
        }
        $result = $db->select($select)->get();
        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        if ($format === 'json') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Json');
            return $result->toJson();
        }

        if ($format === 'array') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Array');
            return $result->toArray();
        }

        if ($format === 'base') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Base');
            return $result->toBase();
        }

        return $result;
    }

    /**
     * Function getResultWithLeftJoinAndWheres
     *
     * @param array             $joins          Danh sách các bảng dữ liệu cần join vào để lấy dữ liệu. Cấu trúc dạng $joins = [
     *                                          'tableA' => [
     *                                          'table' => 'tableA',
     *                                          'first' => 'tableAID',
     *                                          'operator' => '=',
     *                                          'second' => 'CurrentTableNameID'
     *                                          ]
     *                                          ]
     * @param string|array      $wheres         Mảng chứa danh sách các điều kiện để lấy ra dữ liệu, cần map với tất cả các bảng joins
     * @param string|array      $select         Danh sách các cột cần lấy dữ liệu ra
     * @param null|string|array $options        Mảng dữ liệu các cấu hình tùy chọn
     *                                          example $options = [
     *                                          'format' => null,
     *                                          'orderBy => [
     *                                          'id' => 'desc'
     *                                          ]
     *                                          ];
     *
     * @return object|array|\Illuminate\Support\Collection|string
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 02:05
     *
     */
    public function getResultWithLeftJoinAndWheres($joins = array(), $wheres = array(), $select = '*', $options = null)
    {
        $format = isset($options['format']) ? strtolower($options['format']) : null;
        $db     = DB::table($this->table);
        foreach ($joins as $key => $join) {
            $db->leftJoinWhere($join['table'], $join['first'], $join['operator'], $join['second']);
        }
        if (is_array($wheres)) {
            if (count($wheres) > 0) {
                foreach ($wheres as $field => $value) {
                    if (is_array($value)) {
                        if (isset($value['field'], $value['value'])) {
                            if (is_array($value['value'])) {
                                $db->whereIn($value['field'], $value['value']);
                            } else {
                                $db->where($value['field'], $value['operator'], $value['value']);
                            }
                        } else {
                            $db->whereIn($field, $value);
                        }

                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            } else {
                $db->whereIn($this->table . '.' . $this->primaryKey, $wheres);
            }
        } else {
            $db->where($this->table . '.' . $this->primaryKey, self::OPERATOR_EQUAL_TO, $wheres);
        }
        if (!is_array($select)) {
            $select = [$select];
        }
        $result = $db->select($select)->get();
        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        if ($format === 'json') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Json');
            return $result->toJson();
        }

        if ($format === 'array') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Array');
            return $result->toArray();
        }

        if ($format === 'base') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Base');
            return $result->toBase();
        }

        return $result;
    }

    /**
     * Function getResultWithSimpleRightJoin
     *
     * @param array             $joins          Danh sách các bảng dữ liệu cần join vào để lấy dữ liệu. Cấu trúc dạng $joins = [
     *                                          'tableA' => [
     *                                          'table' => 'tableA',
     *                                          'first' => 'tableAID',
     *                                          'operator' => '=',
     *                                          'second' => 'CurrentTableNameID'
     *                                          ]
     *                                          ]
     * @param string|array      $select         Danh sách các cột cần lấy dữ liệu ra
     * @param null|string|array $options        Mảng dữ liệu các cấu hình tùy chọn
     *                                          example $options = [
     *                                          'format' => null,
     *                                          'orderBy => [
     *                                          'id' => 'desc'
     *                                          ]
     *                                          ];
     *
     * @return object|array|\Illuminate\Support\Collection|string
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 02:05
     *
     */
    public function getResultWithSimpleRightJoin($joins = array(), $select = '*', $options = null)
    {
        $format = isset($options['format']) ? strtolower($options['format']) : null;
        $db     = DB::table($this->table);
        foreach ($joins as $key => $join) {
            $db->rightJoin($join['table'], $join['first'], $join['operator'], $join['second']);
        }
        if (!is_array($select)) {
            $select = [$select];
        }
        $result = $db->select($select)->get();
        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        if ($format === 'json') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Json');
            return $result->toJson();
        }

        if ($format === 'array') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Array');
            return $result->toArray();
        }

        if ($format === 'base') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Base');
            return $result->toBase();
        }

        return $result;
    }

    /**
     * Function getResultWithRightJoinAndWheres
     *
     * @param array             $joins          Danh sách các bảng dữ liệu cần join vào để lấy dữ liệu. Cấu trúc dạng $joins = [
     *                                          'tableA' => [
     *                                          'table' => 'tableA',
     *                                          'first' => 'tableAID',
     *                                          'operator' => '=',
     *                                          'second' => 'CurrentTableNameID'
     *                                          ]
     *                                          ]
     * @param string|array      $wheres         Mảng chứa danh sách các điều kiện để lấy ra dữ liệu, cần map với tất cả các bảng joins
     * @param string|array      $select         Danh sách các cột cần lấy dữ liệu ra
     * @param null|string|array $options        Mảng dữ liệu các cấu hình tùy chọn
     *                                          example $options = [
     *                                          'format' => null,
     *                                          'orderBy => [
     *                                          'id' => 'desc'
     *                                          ]
     *                                          ];
     *
     * @return object|array|\Illuminate\Support\Collection|string
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 02:05
     *
     */
    public function getResultWithRightJoinAndWheres($joins = array(), $wheres = array(), $select = '*', $options = null)
    {
        $format = isset($options['format']) ? strtolower($options['format']) : null;
        $db     = DB::table($this->table);
        foreach ($joins as $key => $join) {
            $db->rightJoinWhere($join['table'], $join['first'], $join['operator'], $join['second']);
        }
        if (is_array($wheres)) {
            if (count($wheres) > 0) {
                foreach ($wheres as $field => $value) {
                    if (is_array($value)) {
                        if (isset($value['field'], $value['value'])) {
                            if (is_array($value['value'])) {
                                $db->whereIn($value['field'], $value['value']);
                            } else {
                                $db->where($value['field'], $value['operator'], $value['value']);
                            }
                        } else {
                            $db->whereIn($field, $value);
                        }

                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            } else {
                $db->whereIn($this->table . '.' . $this->primaryKey, $wheres);
            }
        } else {
            $db->where($this->table . '.' . $this->primaryKey, self::OPERATOR_EQUAL_TO, $wheres);
        }
        if (!is_array($select)) {
            $select = [$select];
        }
        $result = $db->select($select)->get();
        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        if ($format === 'json') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Json');
            return $result->toJson();
        }

        if ($format === 'array') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Array');
            return $result->toArray();
        }

        if ($format === 'base') {
            // $this->logger->debug(__FUNCTION__, 'Output Result is Base');
            return $result->toBase();
        }

        return $result;
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
    public function add($data = array()): int
    {
        $this->connection();
        $db = DB::table($this->table);
        $id = $db->insertGetId($data);
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        // $this->logger->debug(__FUNCTION__, 'Result Insert ID: ' . $id);

        return $id;
    }

    /**
     * Hàm update dữ liệu
     *
     * @param array        $data       Mảng dữ liệu cần Update
     * @param array|string $whereValue Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     *
     * @return int Số bản ghi được update thỏa mãn với điều kiện đầu vào
     * @see   https://laravel.com/docs/5.8/queries#updates
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:10
     *
     */
    public function update($data = array(), $whereValue = array()): int
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($whereValue)) {
            if (count($whereValue) > 0) {
                foreach ($whereValue as $field => $value) {
                    if (is_array($value)) {
                        if (isset($value['field'], $value['value'])) {
                            if (is_array($value['value'])) {
                                $db->whereIn($value['field'], $value['value']);
                            } else {
                                $db->where($value['field'], $value['operator'], $value['value']);
                            }
                        } else {
                            $db->whereIn($field, $value);
                        }

                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            } else {
                $db->whereIn($this->primaryKey, $whereValue);
            }
        } else {
            $db->where($this->primaryKey, self::OPERATOR_EQUAL_TO, $whereValue);
        }
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        // $this->logger->debug(__FUNCTION__, 'Result Update Rows: ' . $resultId);
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
    public function updateWithMultipleWhere($data = array(), $wheres = array()): int
    {
        return $this->update($data, $wheres);
    }

    /**
     * Hàm xóa dữ liệu
     *
     * @param array|string $whereValue Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     *
     * @return int Số bản ghi đã xóa
     * @see   https://laravel.com/docs/5.8/queries#deletes
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:13
     *
     */
    public function delete($whereValue = array()): int
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($whereValue)) {
            if (count($whereValue) > 0) {
                foreach ($whereValue as $field => $value) {
                    if (is_array($value)) {
                        if (isset($value['field'], $value['value'])) {
                            if (is_array($value['value'])) {
                                $db->whereIn($value['field'], $value['value']);
                            } else {
                                $db->where($value['field'], $value['operator'], $value['value']);
                            }
                        } else {
                            $db->whereIn($field, $value);
                        }

                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            } else {
                $db->whereIn($this->primaryKey, $whereValue);
            }
        } else {
            $db->where($this->primaryKey, self::OPERATOR_EQUAL_TO, $whereValue);
        }
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        // $this->logger->debug(__FUNCTION__, 'Result Delete Rows: ' . $resultId);
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
    public function deleteWithMultipleWhere($wheres = array()): int
    {
        return $this->delete($wheres);
    }

    /**
     * Hàm kiểm tra dữ liệu đã tồn tại hay chưa, nếu chưa sẽ ghi mới
     *
     * @param array        $data       Mảng dữ liệu cần ghi mới hoặc update
     * @param array|string $whereValue Điều kiện để kiểm tra và xác định bản ghi là ghi mới hay update
     *
     * @return bool|int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 03:58
     *
     */
    public function checkExistsAndInsertData($data = array(), $whereValue = array())
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($whereValue)) {
            if (count($whereValue) > 0) {
                foreach ($whereValue as $field => $value) {
                    if (is_array($value)) {
                        if (isset($value['field'], $value['value'])) {
                            if (is_array($value['value'])) {
                                $db->whereIn($value['field'], $value['value']);
                            } else {
                                $db->where($value['field'], $value['operator'], $value['value']);
                            }
                        } else {
                            $db->whereIn($field, $value);
                        }

                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            } else {
                $db->whereIn($this->primaryKey, $whereValue);
            }
        } else {
            $db->where($this->primaryKey, self::OPERATOR_EQUAL_TO, $whereValue);
        }
        $checkExists = $db->count();
        // $this->logger->debug(__FUNCTION__, 'Check Exists Data: ' . $checkExists);
        if (!$checkExists) {
            $id = $db->insertGetId($data);
            $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
            $this->logger->debug(__FUNCTION__, 'Result Insert ID: ' . $id);

            return $id;
        }

        $this->logger->debug(__FUNCTION__, 'Đã tồn tại bản ghi, bỏ qua không ghi nữa');

        return false;
    }

    /**
     * Hàm kiểm tra dữ liệu đã tồn tại hay chưa, nếu chưa sẽ ghi mới - Đa điều kiện
     *
     * @param array        $data   Mảng dữ liệu cần ghi mới hoặc update
     * @param array|string $wheres Điều kiện để kiểm tra và xác định bản ghi là ghi mới hay update
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
     * @param array        $dataInsert Mảng dữ liệu cần ghi mới
     * @param array        $dataUpdate Mảng dữ liệu cần update
     * @param array|string $whereValue Mảng / điều kiện để xác định query là update hay insert
     *
     * @return int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:01
     *
     */
    public function checkExistsAndInsertOrUpdateData($dataInsert = array(), $dataUpdate = array(), $whereValue = array()): int
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($whereValue)) {
            if (count($whereValue) > 0) {
                foreach ($whereValue as $field => $value) {
                    if (is_array($value)) {
                        if (isset($value['field'], $value['value'])) {
                            if (is_array($value['value'])) {
                                $db->whereIn($value['field'], $value['value']);
                            } else {
                                $db->where($value['field'], $value['operator'], $value['value']);
                            }
                        } else {
                            $db->whereIn($field, $value);
                        }

                    } else {
                        $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                    }
                }
            } else {
                $db->whereIn($this->primaryKey, $whereValue);
            }
        } else {
            $db->where($this->primaryKey, self::OPERATOR_EQUAL_TO, $whereValue);
        }
        $checkExists = $db->count();
        // $this->logger->debug(__FUNCTION__, 'Check Exists Data: ' . $checkExists);
        if (!$checkExists) {
            $id = $db->insertGetId($dataInsert);
            $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
            $this->logger->debug(__FUNCTION__, 'Result Insert ID: ' . $id);

            return $id;
        }

        $resultId = $db->update($dataUpdate);
        $this->logger->debug(__FUNCTION__, 'Result Update Rows: ' . $resultId);

        return $resultId;
    }

    /**
     * Hàm kiểm tra dữ liệu đã tồn tại hay chưa, nếu chưa sẽ ghi mới, nếu tồn tại sẵn sẽ update - Đa điều kiện
     *
     * @param array        $dataInsert Mảng dữ liệu cần ghi mới
     * @param array        $dataUpdate Mảng dữ liệu cần update
     * @param array|string $wheres     Mảng / điều kiện để xác định query là update hay insert
     *
     * @return int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:01
     *
     */
    public function checkExistsAndInsertOrUpdateDataWithMultipleWhere($dataInsert = array(), $dataUpdate = array(), $wheres = array()): int
    {
        return $this->checkExistsAndInsertOrUpdateData($dataInsert, $dataUpdate, $wheres);
    }
}
