<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 2018-12-03
 * Time: 13:42
 */

namespace nguyenanhung\MyDatabase\Model;

use nguyenanhung\MyDebug\Debug;
use nguyenanhung\CodeIgniterDB as CI_DB;
use nguyenanhung\MyDatabase\Interfaces\ProjectInterface;
use nguyenanhung\MyDatabase\Interfaces\ModelInterface;

/**
 * Class CIBaseModel
 *
 * @package   nguyenanhung\MyDatabase\Model
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class CIBaseModel implements ProjectInterface, ModelInterface, CIBaseModelInterface
{
    /** @var object Đối tượng khởi tạo dùng gọi đến Class Debug \nguyenanhung\MyDebug\Debug */
    protected $debug;
    /** @var array|null Mảng dữ liệu chứa thông tin database cần kết nối tới */
    protected $database = NULL;
    /** @var string|null Bảng cần lấy dữ liệu */
    protected $table = NULL;
    /** @var object|null Đối tượng khởi tạo dùng gọi đến Class Database */
    protected $db = NULL;
    /** @var string DB Name */
    protected $dbName = 'default';
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
     * CIBaseModel constructor.
     *
     * @param array $database
     */
    public function __construct($database = [])
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
        // Cấu trúc kết nối Database qua __construct
        if (!empty($database)) {
            $this->database = $database;
        }
        if (is_array($this->database) && !empty($this->database)) {
            $this->db =& CI_DB\DB($this->database);
        }
    }

    /**
     * CIBaseModel destructor.
     */
    public function __destruct()
    {
    }

    /**
     * Hàm lấy thông tin phiên bản Package
     *
     * @author  : 713uk13m <dev@nguyenanhung.com>
     * @time    : 10/13/18 15:12
     *
     * @return mixed|string Current Project Version, VD: 0.1.0
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * Function connection
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 23:10
     *
     * @return $this
     */
    public function connection()
    {
        if (!is_object($this->db)) {
            $this->db =& CI_DB\DB($this->database);
        }

        return $this;
    }

    /**
     * Function disconnect
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:55
     *
     * @return $this
     */
    public function disconnect()
    {
        $this->db->close();

        return $this;
    }

    /**
     * Function reconnect
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 13:52
     *
     */
    public function reconnect()
    {
        $this->db->reconnect();
    }

    /**
     * Function getConnection
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 22:27
     *
     * @return object
     */
    public function getConnection()
    {
        return $this->db;
    }

    /**
     * Function getConnectionName
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 22:28
     *
     * @return string
     */
    public function getConnectionName()
    {
        return $this->dbName;
    }

    /**
     * Hàm set và kết nối cơ sở dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:43
     *
     * @param array  $database Mảng dữ liệu thông tin DB cần kết nối
     * @param string $name     Tên DB kết nối
     *
     * @return  $this;
     *
     * @see   https://github.com/nguyenanhung/database/tree/master/src/Repository/config/example_db.php
     * @see   https://packagist.org/packages/illuminate/database#v5.4.36
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
     * @time  : 2018-12-01 23:07
     *
     * @return array|null
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Hàm set và kết nối đến bảng dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:43
     *
     * @param string $table Bảng cần lấy dữ liệu
     *
     * @return  $this;
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
     * @time  : 2018-12-01 23:07
     *
     * @return string|null
     */
    public function getTable()
    {
        return $this->table;
    }

    /*************************** DATABASE METHOD ***************************/
    /**
     * Function countAll
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 13:57
     *
     * @return int
     */
    public function countAll()
    {
        return (int) $this->db->count_all($this->table);
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
        $this->db->insert($this->table, $data);
        $id = $this->db->insert_id();
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
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value)) {
                    $this->db->where_in($field, $value);
                } else {
                    $this->db->where($field, $value);
                }
            }
        } else {
            $this->db->where($this->primaryKey, $wheres);
        }
        $this->db->update($this->table, $data);
        $resultId = $this->db->affected_rows();
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
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value)) {
                    $this->db->where_in($field, $value);
                } else {
                    $this->db->where($field, $value);
                }
            }
        } else {
            $this->db->where($this->primaryKey, $wheres);
        }
        $this->db->delete($this->table);
        $resultId = $this->db->affected_rows();
        $this->debug->info(__FUNCTION__, 'Result Delete Rows: ' . $resultId);

        return $resultId;
    }
}
