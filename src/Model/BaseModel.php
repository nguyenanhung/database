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
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use nguyenanhung\MyDatabase\Version;
use nguyenanhung\MyDebug\Logger;
use nguyenanhung\MyDatabase\Environment;

/**
 * Class BaseModel
 *
 * Class Base Model sử dụng Query Builder của Illuminate Database
 *
 * Class này chỉ khai báo các hàm cơ bản và thông dụng trong quá trình sử dụng.
 *
 * Các cú pháp, function khác đều có thể sử dụng theo tài liệu chính thức của Illuminate Database
 *
 * @see               https://laravel.com/docs/6.x/database
 * @see               https://laravel.com/docs/6.x/queries
 * @see               https://packagist.org/packages/illuminate/database#v8.61
 *
 * @package           nguyenanhung\MyDatabase\Model
 * @author            713uk13m <dev@nguyenanhung.com>
 * @copyright         713uk13m <dev@nguyenanhung.com>
 * @since             2018-10-17
 * @last_updated      2021-09-22
 * @version           2.1.0
 */
class BaseModel implements Environment
{
    use Version, Helper;

    /** @var \nguyenanhung\MyDebug\Logger $logger */
    protected $logger;

    /** @var \nguyenanhung\MyDebug\Logger $debug */
    protected $debug;

    /** @var array|null Mảng dữ liệu chứa thông tin database cần kết nối tới */
    protected $database = array();

    /** @var string|null Bảng cần lấy dữ liệu */
    protected $table = '';

    /** @var \Illuminate\Database\Capsule\Manager|null $db Đối tượng khởi tạo dùng gọi đến Class Capsule Manager */
    protected $db;

    /** @var bool Cấu hình trạng thái Debug, TRUE nếu bật, FALSE nếu tắt */
    public $debugStatus = false;

    /** @var null|string Cấu hình Level Debug */
    public $debugLevel = 'error';

    /** @var null|bool|string Cấu hình thư mục lưu trữ Log, VD: /your/to/path */
    public $debugLoggerPath = '';

    /** @var null|string Cấu hình File Log, VD: Log-2018-10-15.log | Log-date('Y-m-d').log */
    public $debugLoggerFilename = '';

    /** @var mixed $schema */
    protected $schema;

    /** @var array $joins */
    protected $joins;

    /** @var string DB Name */
    protected $dbName = 'default';

    /** @var bool|null Cấu hình trạng thái select Raw */
    protected $selectRaw;

    /** @var string Primary Key Default */
    public $primaryKey = 'id';

    /** @var null|string Table Prefix */
    protected $prefixTbl;

    /** @var int $chunkCount */
    protected $chunkCount;

    /** @var string|array|null List Field Order for Query Results */
    protected $orderColumn;

    /**
     * BaseModel constructor.
     *
     * @param array $database
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     */
    public function __construct(array $database = array())
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
        $this->debug = $this->logger;
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
     * Function setJoinStatement
     *
     * @param $joins
     *
     * @return $this
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 26:41
     */
    public function setJoinStatement($joins = array())
    {
        $this->joins = $joins;

        return $this;
    }

    /**
     * Function getJoinStatement
     *
     * @return array
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 07/02/2023 26:36
     */
    public function getJoinStatement()
    {
        return $this->joins;
    }

    /**
     * Function setChunkCount
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 10:59
     *
     * @param int $chunkCount
     *
     * @return $this
     */
    public function setChunkCount($chunkCount = 100)
    {
        $this->chunkCount = $chunkCount;

        return $this;
    }

    /**
     * Function getChunkCount
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 10:59
     *
     * @return int
     */
    public function getChunkCount()
    {
        return $this->chunkCount;
    }

    /**
     * Function setPrefixTbl
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-07 15:21
     *
     * @param string $prefixTbl
     *
     * @return $this
     */
    public function setPrefixTbl($prefixTbl = '')
    {
        $this->prefixTbl = $prefixTbl;

        return $this;
    }

    /**
     * Function getPrefixTbl
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-07 15:21
     *
     * @return string|null
     */
    public function getPrefixTbl()
    {
        return $this->prefixTbl;
    }

    /**
     * Function getOrderColumn
     *
     * @return array|string|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 14/12/2022 34:06
     */
    public function getOrderColumn()
    {
        return $this->orderColumn;
    }

    /**
     * Function setOrderColumn
     *
     * @param $orderColumn
     *
     * @return $this
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 14/12/2022 34:01
     */
    public function setOrderColumn($orderColumn)
    {
        $this->orderColumn = $orderColumn;

        return $this;
    }

    /**
     * Function getPrimaryKey
     *
     * @return string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/29/2021 03:44
     */
    public function getPrimaryKey()
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
    public function setPrimaryKey($primaryKey = 'id')
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    /**
     * Function connection
     *
     * @return $this
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/20/2021 15:50
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
    public function closeConnection()
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
    public function disconnect()
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
        $this->dbName = $name;

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
            $schema = $this->getSchema();
            if ($schema === null) {
                return array();
            }

            return $schema->getColumnListing($this->table);
        } catch (Exception $e) {
            $this->logger->error(__FUNCTION__, 'Error Message: ' . $e->getMessage());
            $this->logger->error(__FUNCTION__, 'Error Trace As String: ' . $e->getTraceAsString());

            return array();
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
    public function getSchema()
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
     * Function checkExistsTable - Hàm kiểm tra sự tồn tại của 1 bảng
     *
     * @return bool
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/07/2020 13:10
     */
    public function checkExistsTable()
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
    public function checkExistsColumn($column = '')
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
    public function checkExistsColumns($columns = array())
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
     * @see   https://laravel.com/docs/6.x/queries#deletes
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
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->count();
    }

    /**
     * Hàm kiểm tra sự tồn tại bản ghi theo tham số đầu vào
     *
     * @param string|array $wheres Giá trị cần kiểm tra, có thể là 1 string hoặc 1 array chứa nhiều cột
     * @param string       $fields Field tương ứng cần kiểm tra đối chiếu
     *
     * @return int Số lượng bàn ghi tồn tại phù hợp với điều kiện đưa ra
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:45
     *
     */
    public function checkExists($wheres = '', $fields = 'id')
    {
        $this->connection();
        $db = DB::table($this->table);
        $query = $this->prepareWhereAndFieldStatement($db, $wheres, $fields);
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $query->toSql());

        return $query->count();
    }

    /**
     * Hàm kiểm tra sự tồn tại bản ghi theo tham số đầu vào - Đa điều kiện
     *
     * @param string|array $wheres Giá trị cần kiểm tra
     * @param string       $fields Field tương ứng, ví dụ: ID
     *
     * @return int Số lượng bàn ghi tồn tại phù hợp với điều kiện đưa ra
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:45
     *
     */
    public function checkExistsWithMultipleWhere($wheres = '', $fields = 'id')
    {
        return $this->checkExists($wheres, $fields);
    }

    /**
     * Hàm lấy bản ghi mới nhất theo điều kiện
     *
     * Mặc định giá trị so sánh dựa trên column created_at
     *
     * @param array|string $select Danh sách các column cần lấy
     * @param string       $column Column cần so sánh dữ liệu, mặc định sẽ sử dụng column created_at
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null|object Object dữ liệu đầu ra
     *                                                                                            của bản ghi
     * @see   https://laravel.com/docs/6.x/queries#ordering-grouping-limit-and-offset
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 01:06
     *
     */
    public function getLatest($select = array('*'), $column = 'created_at')
    {
        $select = $this->prepareFormatSelectField($select);
        $this->connection();
        $db = DB::table($this->table)->latest($column);
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->first($select);
    }

    /**
     * Hàm lấy bản ghi mới nhất theo điều kiện đầu vào
     *
     * @param string|array $wheres Giá trị cần kiểm tra, có thể là 1 string hoặc 1 array chứa nhiều cột
     * @param string|array $select Danh sách cột cần lấy dữ liệu ra
     * @param string       $column Tên cột cần order theo điều kiện
     * @param string       $fields Field tương ứng cần kiểm tra đối chiếu
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:16
     */
    public function getLatestByColumn($wheres = array(), $select = array('*'), $column = 'created_at', $fields = 'id')
    {
        $select = $this->prepareFormatSelectField($select);
        $this->connection();
        $db = DB::table($this->table);
        $db = $this->prepareJoinStatement($db);
        $query = $this->prepareWhereAndFieldStatement($db, $wheres, $fields);
        $query->latest($column);
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $query->toSql());

        return $query->first($select);
    }

    /**
     * Hàm lấy bản ghi cũ nhất nhất theo điều kiện
     *
     * Mặc định giá trị so sánh dựa trên column created_at
     *
     * @param array|string $select Danh sách các column cần lấy
     * @param string       $column Column cần so sánh dữ liệu, mặc định sẽ sử dụng column created_at
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null|object Object dữ liệu đầu ra
     *                                                                                            của bản ghi
     * @see   https://laravel.com/docs/6.x/queries#ordering-grouping-limit-and-offset
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 01:06
     *
     */
    public function getOldest($select = array('*'), $column = 'created_at')
    {
        $select = $this->prepareFormatSelectField($select);

        $this->connection();
        $db = DB::table($this->table)->oldest($column);
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->first($select);
    }

    /**
     * Hàm lấy bản ghi cũ nhất nhất theo điều kiện đầu vào
     *
     * @param string|array $wheres Giá trị cần kiểm tra, có thể là 1 string hoặc 1 array chứa nhiều cột
     * @param string|array $select Danh sách cột cần lấy dữ liệu ra
     * @param string       $column Tên cột cần order theo điều kiện
     * @param string       $fields Field tương ứng cần kiểm tra đối chiếu
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:17
     *
     */
    public function getOldestByColumn($wheres = array(), $select = array('*'), $column = 'created_at', $fields = 'id')
    {
        $select = $this->prepareFormatSelectField($select);
        $this->connection();
        $db = DB::table($this->table);
        $db = $this->prepareJoinStatement($db);
        $query = $this->prepareWhereAndFieldStatement($db, $wheres, $fields);
        $query->oldest($column);
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $query->toSql());

        return $query->first($select);
    }

    /**
     * Hàm lấy thông tin bản ghi theo tham số đầu vào
     *
     * Đây là hàm cơ bản, chỉ áp dụng check theo 1 field
     *
     * Lấy bản ghi đầu tiên phù hợp với điều kiện
     *
     * @param array|string      $wheres Giá trị cần kiểm tra
     * @param string|null       $fields Field tương ứng, ví dụ: ID
     * @param string|null       $format Format dữ liệu đầu ra: null, json, array, base, result
     * @param null|string|array $select Các field cần lấy
     *
     * @return object|array|\Illuminate\Support\Collection|string Mảng|String|Object dữ liều phụ hợp với yêu cầu
     *                                                     map theo biến format truyền vào
     * @see   https://laravel.com/docs/6.x/queries#selects
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:51
     *
     */
    public function getInfo($wheres = '', $fields = 'id', $format = null, $select = null)
    {
        $format = $this->prepareOptionFormat($format);
        $this->connection();
        if (!empty($select)) {
            $select = $this->prepareFormatSelectField($select);
            if (isset($select['expression'], $select['bindingParam']) && is_array($select) && $this->selectRaw === true) {
                $db = DB::table($this->table);
                $db = $this->prepareJoinStatement($db);
                $db = $db->selectRaw($select['expression'], $select['bindingParam']);
            } else {
                $db = DB::table($this->table);
                $db = $this->prepareJoinStatement($db);
                $db = $db->select($select);
            }
        } else {
            $db = DB::table($this->table);
            $db = $this->prepareJoinStatement($db);
            $db = $db->select();
        }
        $query = $this->prepareWhereAndFieldStatement($db, $wheres, $fields);
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $query->toSql());

        return $this->formatReturnRowsResult($query, $format);
    }

    /**
     * Hàm lấy thông tin bản ghi theo tham số đầu vào - Đa điều kiện
     *
     * @param array|string      $wheres Giá trị cần kiểm tra
     * @param string|null       $fields Field tương ứng, ví dụ: ID
     * @param string|null       $format Format dữ liệu đầu ra: null, json, array, base, result
     * @param null|string|array $select Các field cần lấy
     *
     * @return array|\Illuminate\Support\Collection|object|string|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/26/18 16:40
     *
     */
    public function getInfoWithMultipleWhere($wheres = '', $fields = 'id', $format = null, $select = null)
    {
        return $this->getInfo($wheres, $fields, $format, $select);
    }

    /**
     * Hàm lấy giá trị 1 field của bản ghi dựa trên điều kiện 1 bản ghi đầu vào
     *
     * Đây là hàm cơ bản, chỉ áp dụng check theo 1 field
     *
     * Lấy bản ghi đầu tiên phù hợp với điều kiện
     *
     * @param string|array $wheres      Giá trị cần kiểm tra
     * @param string       $fields      Field tương ứng với giá tri kiểm tra, ví dụ: ID
     * @param string       $fieldOutput field kết quả đầu ra
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|mixed|null|object
     * @see   https://laravel.com/docs/6.x/queries#selects
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:51
     *
     */
    public function getValue($wheres = '', $fields = 'id', $fieldOutput = '')
    {
        $this->connection();
        $db = DB::table($this->table);
        $db = $this->prepareJoinStatement($db);
        $query = $this->prepareWhereAndFieldStatement($db, $wheres, $fields);
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $query->toSql());
        $result = $query->first();
        // $this->logger->debug(__FUNCTION__, 'Result from DB => ' . json_encode($result));
        if (!empty($fieldOutput) && ($result !== null) && isset($result->$fieldOutput)) {
            return $result->$fieldOutput;
        }

        $this->logger->error(__FUNCTION__, 'Không tìm thấy cột dữ liệu ' . $fieldOutput);

        return $result;
    }

    /**
     * Hàm lấy giá trị 1 field của bản ghi dựa trên điều kiện 1 bản ghi đầu vào
     *
     * Đây là hàm cơ bản, chỉ áp dụng check theo 1 field
     *
     * Lấy bản ghi đầu tiên phù hợp với điều kiện
     *
     * Nếu không có sẽ trả về giá trị rỗng
     *
     * @param string|array $wheres      Giá trị cần kiểm tra
     * @param string       $fields      Field tương ứng với giá tri kiểm tra, ví dụ: ID
     * @param string       $fieldOutput field kết quả đầu ra
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|mixed|null|object
     * @see   https://laravel.com/docs/6.x/queries#selects
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:51
     *
     */
    public function getValueOrEmpty($wheres = '', $fields = 'id', $fieldOutput = '')
    {
        $this->connection();
        $db = DB::table($this->table);
        $db = $this->prepareJoinStatement($db);
        $query = $this->prepareWhereAndFieldStatement($db, $wheres, $fields);
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $query->toSql());
        $result = $query->first();
        // $this->logger->debug(__FUNCTION__, 'Result from DB => ' . json_encode($result));
        if (!empty($fieldOutput) && ($result !== null) && isset($result->$fieldOutput)) {
            return $result->$fieldOutput;
        }

        $this->logger->error(__FUNCTION__, 'Không tìm thấy cột dữ liệu ' . $fieldOutput);

        return null;
    }

    /**
     * Hàm lấy giá trị 1 field của bản ghi dựa trên điều kiện 1 bản ghi đầu vào - Đa điều kiện
     *
     * Đây là hàm cơ bản, chỉ áp dụng check theo 1 field
     *
     * Lấy bản ghi đầu tiên phù hợp với điều kiện
     *
     * @param string|array $wheres      Giá trị cần kiểm tra
     * @param string       $fields      Field tương ứng với giá tri kiểm tra, ví dụ: ID
     * @param string       $fieldOutput field kết quả đầu ra
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|mixed|null|object
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/26/18 16:41
     *
     */
    public function getValueWithMultipleWhere($wheres = '', $fields = 'id', $fieldOutput = '')
    {
        return $this->getValue($wheres, $fields, $fieldOutput);
    }

    /**
     * Hàm lấy danh sách Distinct toàn bộ bản ghi trong 1 bảng
     *
     * @param string|array $select Mảng dữ liệu danh sách các field cần so sánh
     *
     * @return \Illuminate\Support\Collection
     * @see   https://laravel.com/docs/6.x/queries#selects
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 13:59
     *
     */
    public function getDistinctResult($select = array('*'))
    {
        $select = $this->prepareFormatSelectField($select);
        $this->connection();
        $db = DB::table($this->table);
        $db = $this->prepareJoinStatement($db);
        $db->distinct();
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        // $this->logger->debug(__FUNCTION__, 'Result from DB => ' . json_encode($result));
        return $db->get($select);
    }

    /**
     * Hàm lấy danh sách Distinct toàn bộ bản ghi theo điều kiện
     *
     * @param string|array $select Danh sách các cột dữ liệu cần lấy ra
     * @param array|string $wheres Điều kiện kiểm tra đầu vào của dữ liệu
     *
     * @return \Illuminate\Support\Collection
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:21
     *
     */
    public function getDistinctResultByColumn($select = '*', $wheres = array())
    {
        $this->connection();
        $db = DB::table($this->table);
        $db = $this->prepareJoinStatement($db);
        $query = $this->prepareWhereAndFieldStatement($db, $wheres, $select);
        $query->distinct();
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $query->toSql());

        //$this->logger->debug(__FUNCTION__, 'Result from DB => ' . json_encode($result));
        return $query->get($select);
    }

    /**
     * Hàm getResultDistinct là alias của hàm getDistinctResult
     *
     * Các tham số đầu ra và đầu vào theo quy chuẩn của hàm getDistinctResult
     *
     * @param string|array $select Mảng dữ liệu danh sách các field cần so sánh
     *
     * @return \Illuminate\Support\Collection
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 23:49
     *
     */
    public function getResultDistinct($select = '')
    {
        return $this->getDistinctResult($select);
    }

    /**
     * Hàm getResultDistinctByColumn là alias của hàm getDistinctResultByColumn
     *
     * @param string|array $select Danh sách các cột dữ liệu cần lấy ra
     * @param array|string $wheres Điều kiện kiểm tra đầu vào của dữ liệu
     *
     * @return \Illuminate\Support\Collection
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:22
     *
     */
    public function getResultDistinctByColumn($select = array('*'), $wheres = array())
    {
        return $this->getDistinctResultByColumn($select, $wheres);
    }

    /**
     * Function getResult
     *
     * @param array|string      $wheres         Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     * @param string|array      $select         Mảng dữ liệu danh sách các field cần so sánh
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
     * @see   https://laravel.com/docs/6.x/queries#selects
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 16:14
     *
     */
    public function getResult($wheres = array(), $select = array('*'), $options = null)
    {
        $select = $this->prepareFormatSelectField($select);
        $format = $this->prepareOptionFormat($options);
        $this->connection();
        $db = DB::table($this->table);
        $db = $this->prepareJoinStatement($db);
        $query = $this->prepareWhereAndFieldStatement($db, $wheres, $this->table . '.' . $this->primaryKey, $options);
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $query->toSql());
        $result = $query->get($select);

        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        return $this->formatReturnResult($result, $format, false);
    }

    /**
     * Function getResult - Đa điều kiện
     *
     * @param array|string      $wheres         Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     * @param string|array      $select         Mảng dữ liệu danh sách các field cần so sánh
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
     * @see   https://laravel.com/docs/6.x/queries#selects
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 16:14
     *
     */
    public function getResultWithMultipleWhere($wheres = array(), $select = array('*'), $options = null)
    {
        return $this->getResult($wheres, $select, $options);
    }

    /**
     * Function countResult
     *
     * @param string|array $wheres Điều kiện cần thực thi đối với các cột (queries)
     * @param string|array $select Danh sách các cột cần lấy ra. Mặc định sẽ là select *
     *
     * @return int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/25/18 14:10
     *
     */
    public function countResult($wheres = array(), $select = array('*'))
    {
        $select = $this->prepareFormatSelectField($select);
        $this->connection();
        $db = DB::table($this->table);
        $db = $this->prepareJoinStatement($db);
        $query = $this->prepareWhereAndFieldStatement($db, $wheres, $this->table . '.' . $this->primaryKey);
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $query->toSql());
        // $result = $query->get($select);
        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        // $this->logger->debug(__FUNCTION__, 'Total Item Result => ' . json_encode($totalItem));
        return $query->get($select)->count();
    }

    /**
     * Function countResultWithMultipleWhere
     *
     * @param string|array $wheres Điều kiện cần thực thi đối với các cột (queries)
     * @param string|array $select Danh sách các cột cần lấy ra. Mặc định sẽ là select *
     *
     * @return int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:29
     *
     */
    public function countResultWithMultipleWhere($wheres = array(), $select = array('*'))
    {
        return $this->countResult($wheres, $select);
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
    public function getResultWithSimpleJoin($joins = array(), $select = array('*'), $options = null)
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
    public function getResultWithSimpleInnerJoin($joins = array(), $select = array('*'), $options = null)
    {
        $format = $this->prepareOptionFormat($options);
        $db = DB::table($this->table);
        foreach ($joins as $join) {
            $db->join($join['table'], $join['first'], $join['operator'], $join['second']);
        }

        $select = $this->prepareFormatSelectField($select);
        $result = $db->select($select)->get();

        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        return $this->formatReturnResult($result, $format, false);
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
    public function getResultWithInnerJoinAndWheres($joins = array(), $wheres = array(), $select = array('*'), $options = null)
    {
        $select = $this->prepareFormatSelectField($select);
        $format = $this->prepareOptionFormat($options);
        $db = DB::table($this->table);
        foreach ($joins as $join) {
            $db->joinWhere($join['table'], $join['first'], $join['operator'], $join['second']);
        }
        $query = $this->prepareWhereAndFieldStatement($db, $wheres, $this->table . '.' . $this->primaryKey, $options);
        $result = $query->select($select)->get();

        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        return $this->formatReturnResult($result, $format, false);
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
    public function getResultWithSimpleCrossJoin($joins = array(), $select = array('*'), $options = null)
    {
        $select = $this->prepareFormatSelectField($select);
        $format = $this->prepareOptionFormat($options);
        $db = DB::table($this->table);
        foreach ($joins as $join) {
            $db->crossJoin($join['table'], $join['first'], $join['operator'], $join['second']);
        }

        $result = $db->select($select)->get();

        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        return $this->formatReturnResult($result, $format, false);
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
        $select = $this->prepareFormatSelectField($select);
        $format = $this->prepareOptionFormat($options);
        $db = DB::table($this->table);
        foreach ($joins as $join) {
            $db->crossJoin($join['table'], $join['first'], $join['operator'], $join['second']);
        }
        $query = $this->prepareWhereAndFieldStatement($db, $wheres, $this->table . '.' . $this->primaryKey, $options);
        $result = $query->select($select)->get();

        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        return $this->formatReturnResult($result, $format, false);
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
    public function getResultWithSimpleLeftJoin($joins = array(), $select = array('*'), $options = null)
    {
        $select = $this->prepareFormatSelectField($select);
        $format = $this->prepareOptionFormat($options);
        $db = DB::table($this->table);
        foreach ($joins as $join) {
            $db->leftJoin($join['table'], $join['first'], $join['operator'], $join['second']);
        }
        $result = $db->select($select)->get();

        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        return $this->formatReturnResult($result, $format, false);
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
    public function getResultWithLeftJoinAndWheres($joins = array(), $wheres = array(), $select = array('*'), $options = null)
    {
        $select = $this->prepareFormatSelectField($select);
        $format = $this->prepareOptionFormat($options);
        $db = DB::table($this->table);
        foreach ($joins as $join) {
            $db->leftJoinWhere($join['table'], $join['first'], $join['operator'], $join['second']);
        }
        $query = $this->prepareWhereAndFieldStatement($db, $wheres, $this->table . '.' . $this->primaryKey, $options);
        $result = $query->select($select)->get();

        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        return $this->formatReturnResult($result, $format, false);
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
    public function getResultWithSimpleRightJoin($joins = array(), $select = array('*'), $options = null)
    {
        $select = $this->prepareFormatSelectField($select);
        $format = $this->prepareOptionFormat($options);
        $db = DB::table($this->table);
        foreach ($joins as $join) {
            $db->rightJoin($join['table'], $join['first'], $join['operator'], $join['second']);
        }
        $result = $db->select($select)->get();

        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        return $this->formatReturnResult($result, $format, false);
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
    public function getResultWithRightJoinAndWheres($joins = array(), $wheres = array(), $select = array('*'), $options = null)
    {
        $select = $this->prepareFormatSelectField($select);
        $format = $this->prepareOptionFormat($options);
        $db = DB::table($this->table);
        foreach ($joins as $join) {
            $db->rightJoinWhere($join['table'], $join['first'], $join['operator'], $join['second']);
        }
        $query = $this->prepareWhereAndFieldStatement($db, $wheres, $this->table . '.' . $this->primaryKey, $options);
        $result = $query->select($select)->get();

        // $this->logger->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        return $this->formatReturnResult($result, $format, false);
    }

    /**
     * Hàm thêm mới bản ghi vào bảng
     *
     * @param array $data Mảng chứa dữ liệu cần insert
     *
     * @return int Insert ID của bản ghi
     * @see   https://laravel.com/docs/6.x/queries#inserts
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
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        // $this->logger->debug(__FUNCTION__, 'Result Insert ID: ' . $id);

        return $id;
    }

    /**
     * Hàm update dữ liệu
     *
     * @param array        $data   Mảng dữ liệu cần Update
     * @param array|string $wheres Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     *
     * @return int Số bản ghi được update thỏa mãn với điều kiện đầu vào
     * @see   https://laravel.com/docs/6.x/queries#updates
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:10
     *
     */
    public function update($data = array(), $wheres = array())
    {
        $this->connection();
        $db = DB::table($this->table);
        $query = $this->prepareWhereAndFieldStatement($db, $wheres, $this->table . '.' . $this->primaryKey);
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $query->toSql());

        // $this->logger->debug(__FUNCTION__, 'Result Update Rows: ' . $resultId);
        return $query->update($data);
    }

    /**
     * Hàm update dữ liệu - Đa điều kiện
     *
     * @param array        $data   Mảng dữ liệu cần Update
     * @param array|string $wheres Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     *
     * @return int Số bản ghi được update thỏa mãn với điều kiện đầu vào
     * @see   https://laravel.com/docs/6.x/queries#updates
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
     * @param array|string $whereValue Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     *
     * @return int Số bản ghi đã xóa
     * @see   https://laravel.com/docs/6.x/queries#deletes
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:13
     *
     */
    public function delete($whereValue = array())
    {
        $this->connection();
        $db = DB::table($this->table);
        $query = $this->prepareWhereAndFieldStatement($db, $whereValue, $this->table . '.' . $this->primaryKey);
        $this->logger->debug(__FUNCTION__, 'SQL Queries: ' . $query->toSql());

        // $this->logger->debug(__FUNCTION__, 'Result Delete Rows: ' . $resultId);
        return $query->delete();
    }

    /**
     * Hàm xóa dữ liệu - Đa điều kiện
     *
     * @param array|string $wheres Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     *
     * @return int Số bản ghi đã xóa
     * @see   https://laravel.com/docs/6.x/queries#deletes
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
     * @param array        $data   Mảng dữ liệu cần ghi mới hoặc update
     * @param array|string $wheres Điều kiện để kiểm tra và xác định bản ghi là ghi mới hay update
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
        $query = $this->prepareWhereAndFieldStatement($db, $wheres, $this->table . '.' . $this->primaryKey);
        $checkExists = $query->count();
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
     * @param array        $insert Mảng dữ liệu cần ghi mới
     * @param array        $update Mảng dữ liệu cần update
     * @param array|string $wheres Mảng / điều kiện để xác định query là update hay insert
     *
     * @return int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:01
     *
     */
    public function checkExistsAndInsertOrUpdateData($insert = array(), $update = array(), $wheres = array())
    {
        $this->connection();
        $db = DB::table($this->table);
        $query = $this->prepareWhereAndFieldStatement($db, $wheres, $this->table . '.' . $this->primaryKey);
        $checkExists = $query->count();
        // $this->logger->debug(__FUNCTION__, 'Check Exists Data: ' . $checkExists);

        if (!$checkExists) {
            $id = $db->insertGetId($insert);
            $this->logger->debug(__FUNCTION__, 'SQL Queries Insert Data: ' . $db->toSql());
            $this->logger->debug(__FUNCTION__, 'Result Insert ID: ' . $id);

            return $id;
        }

        $resultId = $query->update($update);
        $this->logger->debug(__FUNCTION__, 'SQL Queries Update Data: ' . $query->toSql());
        $this->logger->debug(__FUNCTION__, 'Result Update Rows: ' . $resultId);

        return $resultId;
    }

    /**
     * Hàm kiểm tra dữ liệu đã tồn tại hay chưa, nếu chưa sẽ ghi mới, nếu tồn tại sẵn sẽ update - Đa điều kiện
     *
     * @param array        $insert Mảng dữ liệu cần ghi mới
     * @param array        $update Mảng dữ liệu cần update
     * @param array|string $wheres Mảng / điều kiện để xác định query là update hay insert
     *
     * @return int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-04-07 04:01
     *
     */
    public function checkExistsAndInsertOrUpdateDataWithMultipleWhere($insert = array(), $update = array(), $wheres = array())
    {
        return $this->checkExistsAndInsertOrUpdateData($insert, $update, $wheres);
    }
}
