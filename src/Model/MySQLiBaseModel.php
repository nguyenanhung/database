<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 2018-12-02
 * Time: 20:35
 */

namespace nguyenanhung\MyDatabase\Model;

use Exception;
use MysqliDb;
use nguyenanhung\MyDebug\Debug;
use nguyenanhung\MyDatabase\ProjectInterface;
use nguyenanhung\MyDatabase\ModelInterface;
use nguyenanhung\MyDatabase\Version;
use nguyenanhung\MyDatabase\Helper;

/**
 * Class MySQLiBaseModel
 *
 * @package   nguyenanhung\MyDatabase\Model
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class MySQLiBaseModel implements ProjectInterface, ModelInterface, MySQLiBaseModelInterface
{
    use Version, Helper;
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
                $this->db = new MysqliDb();
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
     * Function setDatabase
     *
     * @param array  $database
     * @param string $name
     *
     * @return $this
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:53
     *
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
     * @return array|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:53
     *
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Function getDbName
     *
     * @return string
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:59
     *
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * Function setTable
     *
     * @param string $table
     *
     * @return $this
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 21:54
     *
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
     * @time  : 2018-12-01 21:54
     *
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Function connection
     *
     * @return $this
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:43
     *
     */
    public function connection()
    {
        if (!is_object($this->db)) {
            $this->db = new MysqliDb();
            $this->db->addConnection($this->dbName, $this->database);
        }

        return $this;
    }

    /**
     * Function disconnect
     *
     * @param string $name
     *
     * @return void|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:01
     *
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
        catch (Exception $e) {
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
     * @return object
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 22:03
     *
     */
    public function getDb()
    {
        return $this->db;
    }

    /*************************** DATABASE METHOD ***************************/
    /**
     * Function countAll
     *
     * @param string $column
     *
     * @return int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:13
     *
     */
    public function countAll($column = '*')
    {
        $results = $this->db->get($this->table, NULL, $column);

        return (int) count($results);
    }

    /**
     * Function checkExists
     *
     * @param string $whereValue
     * @param string $whereField
     * @param string $select
     *
     * @return int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:30
     *
     */
    public function checkExists($whereValue = '', $whereField = 'id', $select = '*')
    {
        if (is_array($whereValue) && count($whereValue) > 0) {
            foreach ($whereValue as $column => $column_value) {
                if (is_array($column_value)) {
                    $this->db->where($column, $column_value, self::OPERATOR_IS_IN);
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
     * @param string $whereValue
     * @param string $whereField
     * @param string $select
     *
     * @return int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:29
     *
     */
    public function checkExistsWithMultipleWhere($whereValue = '', $whereField = 'id', $select = '*')
    {
        if (is_array($whereValue) && count($whereValue) > 0) {
            foreach ($whereValue as $key => $value) {
                if (is_array($value['value'])) {
                    $this->db->where($value['field'], $value['value'], self::OPERATOR_IS_IN);
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
     * @param string $selectField
     * @param string $byColumn
     *
     * @return array|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:34
     *
     */
    public function getLatest($selectField = '*', $byColumn = 'id')
    {
        try {
            $this->db->orderBy($byColumn, self::ORDER_DESCENDING);

            return $this->db->getOne($this->table, $selectField);
        }
        catch (Exception $e) {
            $this->debug->error(__FUNCTION__, $e->getFile() . ' - Line: ' . $e->getLine() . ' - Message: ' . $e->getMessage());

            return NULL;
        }

    }

    /**
     * Function getOldest
     *
     * @param string $selectField
     * @param string $byColumn
     *
     * @return array|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:36
     *
     */
    public function getOldest($selectField = '*', $byColumn = 'id')
    {
        try {
            $this->db->orderBy($byColumn, self::ORDER_ASCENDING);

            return $this->db->getOne($this->table, $selectField);
        }
        catch (Exception $e) {
            $this->debug->error(__FUNCTION__, $e->getFile() . ' - Line: ' . $e->getLine() . ' - Message: ' . $e->getMessage());

            return NULL;
        }
    }

    /**
     * Function getInfo
     *
     * @param string $value
     * @param string $field
     * @param string $selectField
     *
     * @return array|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:38
     *
     */
    public function getInfo($value = '', $field = 'id', $selectField = '*')
    {
        if (is_array($value) && count($value) > 0) {
            foreach ($value as $f => $v) {
                if (is_array($v)) {
                    $this->db->where($f, $v, self::OPERATOR_IS_IN);
                } else {
                    $this->db->where($f, $v, self::OPERATOR_EQUAL_TO);
                }
            }
        } else {
            $this->db->where($field, $value, self::OPERATOR_EQUAL_TO);
        }
        $result = $this->db->getOne($this->table, $selectField);
        if ($this->db->count > 0) {
            return $result;
        }

        return NULL;
    }

    /**
     * Function getInfoWithMultipleWhere
     *
     * @param string $wheres
     * @param string $field
     * @param null   $selectField
     *
     * @return array|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:39
     *
     */
    public function getInfoWithMultipleWhere($wheres = '', $field = 'id', $selectField = NULL)
    {
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $key => $value) {
                if (is_array($value['value'])) {
                    $this->db->where($value['field'], $value['value'], self::OPERATOR_IS_IN);
                } else {
                    $this->db->where($value['field'], $value['value'], $value['operator']);
                }
            }
        } else {
            $this->db->where($field, $wheres, self::OPERATOR_EQUAL_TO);
        }
        $result = $this->db->getOne($this->table, $selectField);
        if ($this->db->count > 0) {
            return $result;
        }

        return NULL;
    }

    /**
     * Function getValue
     *
     * @param string $value
     * @param string $field
     * @param string $fieldOutput
     *
     * @return mixed|null
     * @author : 713uk13m <dev@nguyenanhung.com>
     * @time   : 2018-12-02 21:41
     *
     */
    public function getValue($value = '', $field = 'id', $fieldOutput = '')
    {
        if (is_array($value) && count($value) > 0) {
            foreach ($value as $f => $v) {
                if (is_array($v)) {
                    $this->db->where($f, $v, self::OPERATOR_IS_IN);
                } else {
                    $this->db->where($f, $v, self::OPERATOR_EQUAL_TO);
                }
            }
        } else {
            $this->db->where($field, $value, self::OPERATOR_EQUAL_TO);
        }
        $result = $this->db->getOne($this->table, $fieldOutput);
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
     * @param string $wheres
     * @param string $field
     * @param string $fieldOutput
     *
     * @return mixed|null
     * @author : 713uk13m <dev@nguyenanhung.com>
     * @time   : 2018-12-02 21:42
     *
     */
    public function getValueWithMultipleWhere($wheres = '', $field = 'id', $fieldOutput = '')
    {
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $key => $value) {
                if (is_array($value['value'])) {
                    $this->db->where($value['field'], $value['value'], self::OPERATOR_IS_IN);
                } else {
                    $this->db->where($value['field'], $value['value'], $value['operator']);
                }
            }
        } else {
            $this->db->where($field, $wheres, self::OPERATOR_EQUAL_TO);
        }
        $result = $this->db->getOne($this->table, $fieldOutput);
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
     * @param string $selectField
     *
     * @return array|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:45
     *
     */
    public function getDistinctResult($selectField = '*')
    {
        try {
            $result = $this->db->setQueryOption(['DISTINCT'])->get($this->table, NULL, $selectField);
            $this->debug->debug(__FUNCTION__, 'Result from DB => ' . json_encode($result));

            return $result;
        }
        catch (Exception $e) {
            $this->debug->error(__FUNCTION__, $e->getFile() . ' - Line: ' . $e->getLine() . ' - Message: ' . $e->getMessage());

            return NULL;
        }
    }

    /**
     * Function getResultDistinct
     *
     * @param string $selectField
     *
     * @return array
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:43
     *
     */
    public function getResultDistinct($selectField = '')
    {
        return $this->getDistinctResult($selectField);
    }

    /**
     * Function getResult
     *
     * @param array  $wheres
     * @param string $selectField
     * @param null   $options
     *
     * @return array|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:47
     *
     */
    public function getResult($wheres = [], $selectField = '*', $options = NULL)
    {
        try {
            if (is_array($wheres) && count($wheres) > 0) {
                foreach ($wheres as $field => $value) {
                    if (is_array($value)) {
                        $this->db->where($field, $value, self::OPERATOR_IS_IN);
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
            if ((isset($options['limit']) && $options['limit'] > 0) && isset($options['offset'])) {
                $page    = $this->preparePaging($options['offset'], $options['limit']);
                $numRows = array($page['offset'], $options['limit']);
                $result  = $this->db->get($this->table, $numRows, $selectField);
            } else {
                $result = $this->db->get($this->table, NULL, $selectField);
            }
            $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));

            return $result;
        }
        catch (Exception $e) {
            $this->debug->error(__FUNCTION__, $e->getFile() . ' - Line: ' . $e->getLine() . ' - Message: ' . $e->getMessage());

            return NULL;
        }
    }

    /**
     * Function getResultWithMultipleWhere
     *
     * @param array  $wheres
     * @param string $selectField
     * @param null   $options
     *
     * @return array|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:48
     *
     */
    public function getResultWithMultipleWhere($wheres = [], $selectField = '*', $options = NULL)
    {
        try {
            if (is_array($wheres) && count($wheres) > 0) {
                foreach ($wheres as $field => $value) {
                    if (is_array($value['value'])) {
                        $this->db->where($value['field'], $value['value'], self::OPERATOR_IS_IN);
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
            if ((isset($options['limit']) && $options['limit'] > 0) && isset($options['offset'])) {
                $page    = $this->preparePaging($options['offset'], $options['limit']);
                $numRows = array($page['offset'], $options['limit']);
                $result  = $this->db->get($this->table, $numRows, $selectField);
            } else {
                $result = $this->db->get($this->table, NULL, $selectField);
            }
            $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));

            return $result;
        }
        catch (Exception $e) {
            $this->debug->error(__FUNCTION__, $e->getFile() . ' - Line: ' . $e->getLine() . ' - Message: ' . $e->getMessage());

            return NULL;
        }
    }

    /**
     * Function countResult
     *
     * @param array  $wheres
     * @param string $selectField
     *
     * @return int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:50
     *
     */
    public function countResult($wheres = [], $selectField = '*')
    {
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value)) {
                    $this->db->where($field, $value, self::OPERATOR_IS_IN);
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
     * @param array $data
     *
     * @return int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:22
     *
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
     * @param array $data
     * @param array $wheres
     *
     * @return int
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:21
     *
     */
    public function update($data = [], $wheres = [])
    {
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $column => $column_value) {
                if (is_array($column_value)) {
                    $this->db->where($column, $column_value, self::OPERATOR_IS_IN);
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
     * @param array $wheres
     *
     * @return string
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:17
     *
     */
    public function delete($wheres = [])
    {
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $column => $column_value) {
                if (is_array($column_value)) {
                    $this->db->where($column, $column_value, self::OPERATOR_IS_IN);
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
