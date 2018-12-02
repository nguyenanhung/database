<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 2018-12-02
 * Time: 20:35
 */

namespace nguyenanhung\MyDatabase\Model;

use nguyenanhung\MyDebug\Debug;
use nguyenanhung\MyDatabase\Interfaces\ModelInterface;
use nguyenanhung\MyDatabase\Interfaces\ProjectInterface;

/**
 * Class MySQLiBaseModel
 *
 * @package   nguyenanhung\MyDatabase\Model
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class MySQLiBaseModel implements ProjectInterface, ModelInterface, MySQLiBaseModelInterface
{
    /** @var object Đối tượng khởi tạo dùng gọi đến Class Debug \nguyenanhung\MyDebug\Debug */
    protected $debug;
    /** @var array|null Mảng dữ liệu chứa thông tin database cần kết nối tới */
    protected $database;
    /** @var string DB Name */
    protected $dbName = 'default';
    /** @var string|null Bảng cần lấy dữ liệu */
    protected $table;
    /** @var object Database */
    protected $db;
    /** @var bool Cấu hình trạng thái Debug, TRUE nếu bật, FALSE nếu tắt */
    public $debugStatus = FALSE;
    /** @var null|string Cấu hình Level Debug */
    public $debugLevel = NULL;
    /** @var null|bool|string Cấu hình thư mục lưu trữ Log, VD: /your/to/path */
    public $debugLoggerPath = NULL;
    /** @var null|string Cấu hình File Log, VD: Log-2018-10-15.log | Log-date('Y-m-d').log */
    public $debugLoggerFilename = NULL;
    /** @var string Primary Key Default */
    public $primaryKey = 'id';

    /**
     * MySQLiBaseModel constructor.
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
            if (isset($this->database) && is_array($this->database) && !empty($this->database)) {
                $this->db = new \MysqliDb();
                $this->db->addConnection($this->dbName, $this->database);
            }
        }
    }

    /**
     * MySQLiBaseModel destructor.
     */
    public function __destruct()
    {
    }

    /**
     * Function getVersion
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 21:51
     *
     * @return mixed|string
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * Function setDatabase
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:53
     *
     * @param array  $database
     * @param string $name
     *
     * @return $this
     */
    public function setDatabase($database = [], $name = 'default')
    {
        $this->database = $database;
        $this->dbName   = $name;

        return $this;
    }

    /**
     * Function getDatabase
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:53
     *
     * @return array|null
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Function getDbName
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:59
     *
     * @return string
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * Function setTable
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 21:54
     *
     * @param string $table
     *
     * @return $this
     */
    public function setTable($table = '')
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Function getTable
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 21:54
     *
     * @return string|null
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Function connection
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:43
     *
     * @return $this
     */
    public function connection()
    {
        if (!is_object($this->db)) {
            $this->db = new \MysqliDb();
            $this->db->addConnection($this->dbName, $this->database);
        }

        return $this;
    }

    /**
     * Function disconnect
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:01
     *
     * @param string $name
     *
     * @return void|null
     */
    public function disconnect($name = '')
    {
        if (empty($name)) {
            $name = $this->dbName;
        }
        try {
            $this->db->disconnect($name);
        }
        catch (\Exception $e) {
            return NULL;
        }
    }

    /**
     * Function disconnectAll
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:01
     *
     */
    public function disconnectAll()
    {
        $this->db->disconnectAll();
    }

    /**
     * Function getDb
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 22:03
     *
     * @return object
     */
    public function getDb()
    {
        return $this->db;
    }

    /*************************** DATABASE METHOD ***************************/
    /**
     * Function countAll
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:13
     *
     * @param string $column
     *
     * @return int
     */
    public function countAll($column = '*')
    {
        $results = $this->db->get($this->table, NULL, $column);

        return (int) count($results);
    }

    /**
     * Function add
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:22
     *
     * @param array $data
     *
     * @return int
     */
    public function add($data = [])
    {
        $insertId = $this->db->insert($this->table, $data);
        if ($insertId) {
            return (int) $insertId;
        }

        return 0;
    }

    /**
     * Function update
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:21
     *
     * @param array $data
     * @param array $wheres
     *
     * @return int
     */
    public function update($data = [], $wheres = [])
    {
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $column => $column_value) {
                if (is_array($column_value)) {
                    $this->db->where($column, $column_value, 'IN');
                } else {
                    $this->db->where($column, $column_value, self::OPERATOR_EQUAL_TO);
                }
            }
        }
        if ($this->db->update($this->table, $data)) {
            return (int) $this->db->count;
        }

        return 0;
    }

    /**
     * Function delete
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:17
     *
     * @param array $wheres
     *
     * @return string
     */
    public function delete($wheres = [])
    {
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $column => $column_value) {
                if (is_array($column_value)) {
                    $this->db->where($column, $column_value, 'IN');
                } else {
                    $this->db->where($column, $column_value, self::OPERATOR_EQUAL_TO);
                }
            }
        }
        if ($this->db->delete($this->table)) {
            return (int) $this->db->count;
        }

        return 0;
    }
}
