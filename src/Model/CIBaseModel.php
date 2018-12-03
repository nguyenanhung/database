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
     * Hàm truncate bảng dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 14:07
     *
     * @return mixed
     */
    public function truncate()
    {
        return $this->db->truncate($this->table);
    }

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
     * Hàm kiểm tra sự tồn tại bản ghi theo tham số đầu vào
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:45
     *
     * @param string|array $whereValue Giá trị cần kiểm tra
     * @param string|null  $whereField Field tương ứng, ví dụ: ID
     *
     * @return int Số lượng bàn ghi tồn tại phù hợp với điều kiện đưa ra
     */
    public function checkExists($whereValue = '', $whereField = 'id')
    {
        $this->db->from($this->table);
        if (is_array($whereValue) && count($whereValue) > 0) {
            foreach ($whereValue as $column => $column_value) {
                if (is_array($column_value)) {
                    $this->db->where_in($column, $column_value);
                } else {
                    $this->db->where($column, $column_value);
                }
            }
        } else {
            $this->db->where($whereField, $whereValue);
        }

        return (int) $this->db->count_all_results();
    }

    /**
     * Hàm kiểm tra sự tồn tại bản ghi theo tham số đầu vào - Đa điều kiện
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:45
     *
     * @param string|array $whereValue Giá trị cần kiểm tra
     * @param string|null  $whereField Field tương ứng, ví dụ: ID
     *
     * @return int Số lượng bàn ghi tồn tại phù hợp với điều kiện đưa ra
     */
    public function checkExistsWithMultipleWhere($whereValue = '', $whereField = 'id')
    {
        $this->db->from($this->table);
        if (is_array($whereValue) && count($whereValue) > 0) {
            foreach ($whereValue as $key => $value) {
                if (is_array($value['value'])) {
                    $this->db->where_in($value['field'], $value['value']);
                } else {
                    $this->db->where($value['field'] . $value['operator'], $value['value']);
                }
            }
        } else {
            $this->db->where($whereField, $whereValue);
        }

        return (int) $this->db->count_all_results();
    }

    /**
     * Function getLatest
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 14:13
     *
     * @param string $selectField
     * @param string $byColumn
     *
     * @return mixed|object|null
     */
    public function getLatest($selectField = '*', $byColumn = 'id')
    {
        $this->db->select($selectField);
        $this->db->from($this->table);
        $this->db->order_by($byColumn, 'DESC');
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    /**
     * Function getOldest
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 14:13
     *
     * @param string $selectField
     * @param string $byColumn
     *
     * @return mixed|object|null
     */
    public function getOldest($selectField = '*', $byColumn = 'id')
    {
        $this->db->select($selectField);
        $this->db->from($this->table);
        $this->db->order_by($byColumn, 'ASC');
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    /**
     * Function getInfo
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 14:17
     *
     * @param string $value
     * @param string $field
     * @param null   $format
     * @param null   $selectField
     *
     * @return mixed|object|array|null
     */
    public function getInfo($value = '', $field = 'id', $format = NULL, $selectField = NULL)
    {
        if (empty($selectField)) {
            $selectField = '*';
        }
        $format = strtolower($format);
        $this->db->select($selectField);
        $this->db->from($this->table);
        if (is_array($value) && count($value) > 0) {
            foreach ($value as $f => $v) {
                if (is_array($v)) {
                    $this->db->where_in($f, $v);
                } else {
                    $this->db->where($f, $v);
                }
            }
        } else {
            $this->db->where($field, $value);
        }
        if ($format == 'array') {
            return $this->db->get()->row_array();
        } else {
            return $this->db->get()->row();
        }
    }

    /**
     * Function getInfoWithMultipleWhere
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 14:18
     *
     * @param string $wheres
     * @param string $field
     * @param null   $format
     * @param null   $selectField
     *
     * @return mixed|object|array|null
     */
    public function getInfoWithMultipleWhere($wheres = '', $field = 'id', $format = NULL, $selectField = NULL)
    {
        if (empty($selectField)) {
            $selectField = '*';
        }
        $format = strtolower($format);
        $this->db->select($selectField);
        $this->db->from($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $key => $value) {
                if (is_array($value['value'])) {
                    $this->db->where_in($value['field'], $value['value']);
                } else {
                    $this->db->where($value['field'] . $value['operator'], $value['value']);
                }
            }
        } else {
            $this->db->where($field, $wheres);
        }
        if ($format == 'array') {
            return $this->db->get()->row_array();
        } else {
            return $this->db->get()->row();
        }
    }

    /**
     * Function getValue
     *
     * @author : 713uk13m <dev@nguyenanhung.com>
     * @time   : 2018-12-03 14:19
     *
     * @param string $value
     * @param string $field
     * @param string $fieldOutput
     *
     * @return mixed|object|string|int|null
     */
    public function getValue($value = '', $field = 'id', $fieldOutput = '')
    {
        $this->db->select($fieldOutput);
        $this->db->from($this->table);
        if (is_array($value) && count($value) > 0) {
            foreach ($value as $column => $column_value) {
                if (is_array($column_value)) {
                    $this->db->where_in($column, $column_value);
                } else {
                    $this->db->where($column, $column_value);
                }
            }
        } else {
            $this->db->where($field, self::OPERATOR_EQUAL_TO, $value);
        }
        $result = $this->db->get()->row();
        if (isset($result->$fieldOutput)) {
            return $result->$fieldOutput;
        }

        return NULL;
    }

    /**
     * Function getValueWithMultipleWhere
     *
     * @author : 713uk13m <dev@nguyenanhung.com>
     * @time   : 2018-12-03 14:20
     *
     * @param string $wheres
     * @param string $field
     * @param string $fieldOutput
     *
     * @return mixed|object|string|int|null
     */
    public function getValueWithMultipleWhere($wheres = '', $field = 'id', $fieldOutput = '')
    {
        $this->db->select($fieldOutput);
        $this->db->from($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $key => $value) {
                if (is_array($value['value'])) {
                    $this->db->where_in($value['field'], $value['value']);
                } else {
                    $this->db->where($value['field'] . $value['operator'], $value['value']);
                }
            }
        } else {
            $this->db->where($field, $wheres);
        }
        $result = $this->db->get()->row();
        if (isset($result->$fieldOutput)) {
            return $result->$fieldOutput;
        }

        return NULL;
    }

    /**
     * Function getDistinctResult
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 14:22
     *
     * @param string $selectField
     *
     * @return mixed|object
     */
    public function getDistinctResult($selectField = '*')
    {
        $this->db->select($selectField);
        $this->db->distinct(TRUE);
        $this->db->from($this->table);
        $result = $this->db->get()->result();
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
     * @return \Illuminate\Support\Collection|object|array
     */
    public function getResultDistinct($selectField = '*')
    {
        return $this->getDistinctResult($selectField);
    }

    /**
     * Function getResult
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 14:28
     *
     * @param array  $wheres
     * @param string $selectField
     * @param null   $options
     *
     * @return mixed|object|array|null
     */
    public function getResult($wheres = [], $selectField = '*', $options = NULL)
    {
        $format = isset($options['format']) ? strtolower($options['format']) : NULL;
        $this->db->select($selectField);
        $this->db->from($this->table);
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
        if (isset($options['orderBy']) && is_array($options['orderBy'])) {
            foreach ($options['orderBy'] as $column => $direction) {
                $this->db->order_by($column, $direction);
            }
        }
        if (isset($options['orderBy']) && $options['orderBy'] == 'random') {
            $this->db->order_by($this->primaryKey, 'RANDOM');
        }
        if ($format == 'array') {
            return $this->db->get()->result_array();
        } else {
            return $this->db->get()->result();
        }
    }

    /**
     * Function getResultWithMultipleWhere
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 14:28
     *
     * @param array  $wheres
     * @param string $selectField
     * @param null   $options
     *
     * @return mixed|object|array|null
     */
    public function getResultWithMultipleWhere($wheres = [], $selectField = '*', $options = NULL)
    {
        $format = isset($options['format']) ? strtolower($options['format']) : NULL;
        $this->db->select($selectField);
        $this->db->from($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value['value'])) {
                    $this->db->where_in($value['field'], $value['value']);
                } else {
                    $this->db->where($value['field'] . $value['operator'], $value['value']);
                }
            }
        }
        if (isset($options['orderBy']) && is_array($options['orderBy'])) {
            foreach ($options['orderBy'] as $column => $direction) {
                $this->db->order_by($column, $direction);
            }
        }
        if (isset($options['orderBy']) && $options['orderBy'] == 'random') {
            $this->db->order_by($this->primaryKey, 'RANDOM');
        }
        if ($format == 'array') {
            return $this->db->get()->result_array();
        } else {
            return $this->db->get()->result();
        }
    }

    /**
     * Function countResult
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 14:29
     *
     * @param array  $wheres
     * @param string $selectField
     *
     * @return int
     */
    public function countResult($wheres = [], $selectField = '*')
    {
        $this->db->select($selectField);
        $this->db->from($this->table);
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

        return (int) $this->db->count_all_results();
    }

    /**
     * Function getResultWithSimpleJoin
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 14:32
     *
     * @param array  $joins
     * @param string $select
     * @param null   $options
     *
     * @return mixed|object|array|null
     */
    public function getResultWithSimpleJoin($joins = [], $select = '*', $options = NULL)
    {
        $format = isset($options['format']) ? strtolower($options['format']) : NULL;
        $this->db->select($select);
        $this->db->from($this->table);
        foreach ($joins as $key => $join) {
            $this->db->join($join['table'], $join['first'], $join['operator'], $join['second']);
        }
        if ($format == 'array') {
            return $this->db->get()->result_array();
        } else {
            return $this->db->get()->result();
        }
    }

    /**
     * Function getResultWithSimpleLeftJoin
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 14:33
     *
     * @param array  $joins
     * @param string $select
     * @param null   $options
     *
     * @return mixed|object|array|null
     */
    public function getResultWithSimpleLeftJoin($joins = [], $select = '*', $options = NULL)
    {
        $format = isset($options['format']) ? strtolower($options['format']) : NULL;
        $this->db->select($select);
        $this->db->from($this->table);
        foreach ($joins as $key => $join) {
            $this->db->join($join['table'], $join['first'], 'left', $join['second']);
        }
        if ($format == 'array') {
            return $this->db->get()->result_array();
        } else {
            return $this->db->get()->result();
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
     * @return int Insert ID của bản ghi
     */
    public function add($data = [])
    {
        $this->db->insert($this->table, $data);
        $insertId = $this->db->insert_id();
        $this->debug->info(__FUNCTION__, 'Result Insert ID: ' . $insertId);

        return (int) $insertId;
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

        return (int) $resultId;
    }

    /**
     * Hàm xóa dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:13
     *
     * @param array|string $wheres Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
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

        return (int) $resultId;
    }
}
