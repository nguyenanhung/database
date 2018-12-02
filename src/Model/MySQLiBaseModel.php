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
            unset($this->db);
        }
        catch (\Exception $e) {
            $this->debug->error(__FUNCTION__, $e->getFile() . ' - Line: ' . $e->getLine() . ' - Message: ' . $e->getMessage());

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
     * Function checkExists
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:30
     *
     * @param string $whereValue
     * @param string $whereField
     * @param string $select
     *
     * @return int
     */
    public function checkExists($whereValue = '', $whereField = 'id', $select = '*')
    {
        if (is_array($whereValue) && count($whereValue) > 0) {
            foreach ($whereValue as $column => $column_value) {
                if (is_array($column_value)) {
                    $this->db->where($column, $column_value, 'IN');
                } else {
                    $this->db->where($column, $column_value, self::OPERATOR_EQUAL_TO);
                }
            }
        } else {
            $this->db->where($whereField, $whereValue, self::OPERATOR_EQUAL_TO);
        }
        $this->db->get($this->table, NULL, $select);

        return (int) $this->db->count;
    }

    /**
     * Function checkExistsWithMultipleWhere
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:29
     *
     * @param string $whereValue
     * @param string $whereField
     * @param string $select
     *
     * @return int
     */
    public function checkExistsWithMultipleWhere($whereValue = '', $whereField = 'id', $select = '*')
    {
        if (is_array($whereValue) && count($whereValue) > 0) {
            foreach ($whereValue as $key => $value) {
                if (is_array($value['value'])) {
                    $this->db->where($value['field'], $value['value'], 'IN');
                } else {
                    $this->db->where($value['field'], $value['value'], $value['operator']);
                }
            }
        } else {
            $this->db->where($whereField, $whereValue, self::OPERATOR_EQUAL_TO);
        }
        $this->db->get($this->table, NULL, $select);

        return (int) $this->db->count;
    }

    /**
     * Function getLatest
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:34
     *
     * @param string $selectField
     * @param string $byColumn
     *
     * @return array|null
     */
    public function getLatest($selectField = '*', $byColumn = 'id')
    {
        try {
            $this->db->orderBy($byColumn, "desc");

            return $this->db->get($this->table, 1, $selectField);
        }
        catch (\Exception $e) {
            $this->debug->error(__FUNCTION__, $e->getFile() . ' - Line: ' . $e->getLine() . ' - Message: ' . $e->getMessage());

            return NULL;
        }

    }

    /**
     * Function getOldest
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:36
     *
     * @param string $selectField
     * @param string $byColumn
     *
     * @return array|null
     */
    public function getOldest($selectField = '*', $byColumn = 'id')
    {
        try {
            $this->db->orderBy($byColumn, "asc");

            return $this->db->get($this->table, 1, $selectField);
        }
        catch (\Exception $e) {
            $this->debug->error(__FUNCTION__, $e->getFile() . ' - Line: ' . $e->getLine() . ' - Message: ' . $e->getMessage());

            return NULL;
        }
    }

    /**
     * Function getInfo
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:38
     *
     * @param string $value
     * @param string $field
     * @param string $selectField
     *
     * @return array|null
     */
    public function getInfo($value = '', $field = 'id', $selectField = '*')
    {
        if (is_array($value) && count($value) > 0) {
            foreach ($value as $f => $v) {
                if (is_array($v)) {
                    $this->db->where($f, $v, 'IN');
                } else {
                    $this->db->where($f, $v, self::OPERATOR_EQUAL_TO);
                }
            }
        } else {
            $this->db->where($field, $value, self::OPERATOR_EQUAL_TO);
        }
        $result = $this->db->get($this->table, NULL, $selectField);
        if ($this->db->count > 0) {
            return $result;
        }

        return NULL;
    }

    /**
     * Function getInfoWithMultipleWhere
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:39
     *
     * @param string $wheres
     * @param string $field
     * @param null   $selectField
     *
     * @return array|null
     */
    public function getInfoWithMultipleWhere($wheres = '', $field = 'id', $selectField = NULL)
    {
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $key => $value) {
                if (is_array($value['value'])) {
                    $this->db->where($value['field'], $value['value'], 'IN');
                } else {
                    $this->db->where($value['field'], $value['value'], $value['operator']);
                }
            }
        } else {
            $this->db->where($field, $wheres, self::OPERATOR_EQUAL_TO);
        }
        $result = $this->db->get($this->table, NULL, $selectField);
        if ($this->db->count > 0) {
            return $result;
        }

        return NULL;
    }

    /**
     * Function getValue
     *
     * @author : 713uk13m <dev@nguyenanhung.com>
     * @time   : 2018-12-02 21:41
     *
     * @param string $value
     * @param string $field
     * @param string $fieldOutput
     *
     * @return mixed|null
     */
    public function getValue($value = '', $field = 'id', $fieldOutput = '')
    {
        if (is_array($value) && count($value) > 0) {
            foreach ($value as $f => $v) {
                if (is_array($v)) {
                    $this->db->where($f, $v, 'IN');
                } else {
                    $this->db->where($f, $v, self::OPERATOR_EQUAL_TO);
                }
            }
        } else {
            $this->db->where($field, $value, self::OPERATOR_EQUAL_TO);
        }
        $result = $this->db->get($this->table, NULL, $fieldOutput);
        $this->debug->debug(__FUNCTION__, 'GET Result => ' . json_encode($result));
        if (isset($result->$fieldOutput)) {
            return $result->$fieldOutput;
        } else {
            return NULL;
        }
    }

    /**
     * Function getValueWithMultipleWhere
     *
     * @author : 713uk13m <dev@nguyenanhung.com>
     * @time   : 2018-12-02 21:42
     *
     * @param string $wheres
     * @param string $field
     * @param string $fieldOutput
     *
     * @return mixed|null
     */
    public function getValueWithMultipleWhere($wheres = '', $field = 'id', $fieldOutput = '')
    {
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $key => $value) {
                if (is_array($value['value'])) {
                    $this->db->where($value['field'], $value['value'], 'IN');
                } else {
                    $this->db->where($value['field'], $value['value'], $value['operator']);
                }
            }
        } else {
            $this->db->where($field, $wheres, self::OPERATOR_EQUAL_TO);
        }
        $result = $this->db->get($this->table, NULL, $fieldOutput);
        $this->debug->debug(__FUNCTION__, 'GET Result => ' . json_encode($result));
        if (isset($result->$fieldOutput)) {
            return $result->$fieldOutput;
        } else {
            return NULL;
        }
    }

    /**
     * Function getDistinctResult
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:45
     *
     * @param string $selectField
     *
     * @return array|null
     */
    public function getDistinctResult($selectField = '*')
    {
        try {
            $result = $this->db->setQueryOption(['DISTINCT'])->get($this->table, NULL, $selectField);
            $this->debug->debug(__FUNCTION__, 'Result from DB => ' . json_encode($result));

            return $result;
        }
        catch (\Exception $e) {
            $this->debug->error(__FUNCTION__, $e->getFile() . ' - Line: ' . $e->getLine() . ' - Message: ' . $e->getMessage());

            return NULL;
        }
    }

    /**
     * Function getResultDistinct
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:43
     *
     * @param string $selectField
     *
     * @return array
     */
    public function getResultDistinct($selectField = '')
    {
        return $this->getDistinctResult($selectField);
    }

    /**
     * Function getResult
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:47
     *
     * @param array  $wheres
     * @param string $selectField
     * @param null   $options
     *
     * @return array|null
     */
    public function getResult($wheres = [], $selectField = '*', $options = NULL)
    {
        try {
            if (is_array($wheres) && count($wheres) > 0) {
                foreach ($wheres as $field => $value) {
                    if (is_array($value)) {
                        $this->db->where($field, $value, 'IN');
                    } else {
                        $this->db->where($field, $value, self::OPERATOR_EQUAL_TO);
                    }
                }
            } else {
                $this->db->where($this->primaryKey, $wheres, self::OPERATOR_EQUAL_TO);
            }
            if (isset($options['orderBy']) && is_array($options['orderBy'])) {
                foreach ($options['orderBy'] as $column => $direction) {
                    $this->db->orderBy($column, $direction);
                }
            }
            $result = $this->db->get($this->table, NULL, $selectField);
            $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));

            return $result;
        }
        catch (\Exception $e) {
            $this->debug->error(__FUNCTION__, $e->getFile() . ' - Line: ' . $e->getLine() . ' - Message: ' . $e->getMessage());

            return NULL;
        }
    }

    /**
     * Function getResultWithMultipleWhere
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:48
     *
     * @param array  $wheres
     * @param string $selectField
     * @param null   $options
     *
     * @return array|null
     */
    public function getResultWithMultipleWhere($wheres = [], $selectField = '*', $options = NULL)
    {
        try {
            if (is_array($wheres) && count($wheres) > 0) {
                foreach ($wheres as $field => $value) {
                    if (is_array($value['value'])) {
                        $this->db->where($value['field'], $value['value'], 'IN');
                    } else {
                        $this->db->where($value['field'], $value['value'], $value['operator']);
                    }
                }
            }
            if (isset($options['orderBy']) && is_array($options['orderBy'])) {
                foreach ($options['orderBy'] as $column => $direction) {
                    $this->db->orderBy($column, $direction);
                }
            }
            $result = $this->db->get($this->table, NULL, $selectField);
            $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));

            return $result;
        }
        catch (\Exception $e) {
            $this->debug->error(__FUNCTION__, $e->getFile() . ' - Line: ' . $e->getLine() . ' - Message: ' . $e->getMessage());

            return NULL;
        }
    }

    /**
     * Function countResult
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:50
     *
     * @param array  $wheres
     * @param string $selectField
     *
     * @return int
     */
    public function countResult($wheres = [], $selectField = '*')
    {
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value)) {
                    $this->db->where($field, $value, 'IN');
                } else {
                    $this->db->where($field, $value, self::OPERATOR_EQUAL_TO);
                }
            }
        } else {
            $this->db->where($this->primaryKey, $wheres, self::OPERATOR_EQUAL_TO);
        }
        $result = $this->db->get($this->table, NULL, $selectField);
        $this->debug->debug(__FUNCTION__, 'Total Item Result => ' . json_encode($result));
        if ($this->db->count > 0) {
            return (int) $this->db->count;
        }

        return 0;
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
