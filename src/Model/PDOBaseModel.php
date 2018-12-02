<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 2018-12-01
 * Time: 21:50
 */

namespace nguyenanhung\MyDatabase\Model;

use nguyenanhung\MyDatabase\Interfaces\ModelInterface;
use nguyenanhung\MyDatabase\Interfaces\ProjectInterface;
use nguyenanhung\MyDebug\Debug;

/**
 * Class PDOBaseModel
 *
 * @package   nguyenanhung\MyDatabase\Model
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class PDOBaseModel implements ProjectInterface, ModelInterface, PDOBaseModelInterface
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
     * PDOBaseModel constructor.
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
        if (!empty($database)) {
            $this->database = $database;
        }
        if (is_array($this->database) && !empty($this->database)) {
            $this->db = new \Slim\PDO\Database(
                $this->database['driver'] . ':host=' . $this->database['host'] . ';port=' . $this->database['port'] . ';dbname=' . $this->database['database'] . ';charset=' . $this->database['charset'] . ';collation=' . $this->database['collation'] . ';prefix=' . $this->database['prefix'],
                $this->database['username'],
                $this->database['password'],
                $this->database['options']
            );
            $this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        }
    }

    /**
     * PDOBaseModel destructor.
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
     * @time  : 2018-12-02 20:42
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
     * @time  : 2018-12-02 20:42
     *
     * @return array|null
     */
    public function getDatabase()
    {
        return $this->database;
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
            $this->db = new \Slim\PDO\Database(
                $this->database['driver'] . ':host=' . $this->database['host'] . ';port=' . $this->database['port'] . ';dbname=' . $this->database['database'] . ';charset=' . $this->database['charset'] . ';collation=' . $this->database['collation'] . ';prefix=' . $this->database['prefix'],
                $this->database['username'],
                $this->database['password'],
                $this->database['options']
            );
            $this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        }

        return $this;
    }

    /**
     * Function disconnect
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 22:03
     *
     * @return $this
     */
    public function disconnect()
    {
        $this->db = NULL;

        return $this;
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
     * Hàm đếm toàn bộ bản ghi tồn tại trong bảng
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 22:19
     *
     * @param array $select
     *
     * @return int
     */
    public function countAll($select = ['id'])
    {
        $total = $this->db->select($select)->from($this->table)->execute()->rowCount();
        $this->debug->debug(__FUNCTION__, 'Total Result: ' . $total);

        return $total;
    }

    /**
     * Hàm kiểm tra sự tồn tại bản ghi theo tham số đầu vào
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:45
     *
     * @param string|array      $whereValue Giá trị cần kiểm tra
     * @param string|null       $whereField Field tương ứng, ví dụ: ID
     * @param string|array|null $select     Bản ghi cần chọn
     *
     * @return int Số lượng bàn ghi tồn tại phù hợp với điều kiện đưa ra
     */
    public function checkExists($whereValue = '', $whereField = 'id', $select = ['*'])
    {
        $db = $this->db->select($select)->from($this->table);
        if (is_array($whereValue) && count($whereValue) > 0) {
            foreach ($whereValue as $column => $column_value) {
                if (is_array($column_value)) {
                    $db->whereIn($column, $column_value);
                } else {
                    $db->where($column, self::OPERATOR_EQUAL_TO, $column_value);
                }
            }
        } else {
            $db->where($whereField, self::OPERATOR_EQUAL_TO, $whereValue);
        }
        $total = $db->execute()->rowCount();
        $this->debug->debug(__FUNCTION__, 'Total Result: ' . $total);

        return $total;
    }

    /**
     * Hàm kiểm tra sự tồn tại bản ghi theo tham số đầu vào - Đa điều kiện
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:45
     *
     * @param string|array      $whereValue Giá trị cần kiểm tra
     * @param string|null       $whereField Field tương ứng, ví dụ: ID
     * @param string|array|null $select     Bản ghi cần chọn
     *
     * @return int Số lượng bàn ghi tồn tại phù hợp với điều kiện đưa ra
     */
    public function checkExistsWithMultipleWhere($whereValue = '', $whereField = 'id', $select = ['*'])
    {
        $db = $this->db->select($select)->from($this->table);
        if (is_array($whereValue) && count($whereValue) > 0) {
            foreach ($whereValue as $key => $value) {
                if (is_array($value['value'])) {
                    $db->whereIn($value['field'], $value['value']);
                } else {
                    $db->where($value['field'], $value['operator'], $value['value']);
                }
            }
        } else {
            $db->where($whereField, self::OPERATOR_EQUAL_TO, $whereValue);
        }

        return $db->execute()->rowCount();
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
        $db = $this->db->select($selectField)->from($this->table);
        $db->orderBy($byColumn, 'DESC')->limit(1);
        $result = $db->execute()->fetch();
        $this->debug->debug(__FUNCTION__, 'GET Result => ' . json_encode($result));

        return $result;
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
        $db = $this->db->select($selectField)->from($this->table);
        $db->orderBy($byColumn, 'ASC')->limit(1);
        $result = $db->execute()->fetch();
        $this->debug->debug(__FUNCTION__, 'GET Result => ' . json_encode($result));

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
     * @param array|string      $value       Giá trị cần kiểm tra
     * @param null|string       $field       Field tương ứng, ví dụ: ID
     * @param null|string       $format      Format dữ liệu đầu ra: null, json, array, base, result
     * @param null|string|array $selectField Các field cần lấy
     *
     * @return object|array|string|null Mảng|String|Object dữ liều phụ hợp với yêu cầu
     *                                                     map theo biến format truyền vào
     */
    public function getInfo($value = '', $field = 'id', $format = NULL, $selectField = NULL)
    {
        $format = strtolower($format);
        if (!empty($selectField)) {
            if (!is_array($selectField)) {
                $selectField = [$selectField];
            }
        } else {
            $selectField = ['*'];
        }
        $db = $this->db->select($selectField)->from($this->table);
        if (is_array($value) && count($value) > 0) {
            foreach ($value as $f => $v) {
                if (is_array($v)) {
                    $db->whereIn($f, $v);
                } else {
                    $db->where($f, self::OPERATOR_EQUAL_TO, $v);
                }
            }
        } else {
            $db->where($field, self::OPERATOR_EQUAL_TO, $value);
        }
        if ($format == 'result') {
            $result = $db->execute()->fetchAll();
            $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        } else {
            $result = $db->execute()->fetch();
            $this->debug->debug(__FUNCTION__, 'Format is get first Result => ' . json_encode($result));
        }
        $this->debug->debug(__FUNCTION__, 'GET Result => ' . json_encode($result));
        if ($format == 'json') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Json');

            return json_encode($result);
        } else {
            return $result;
        }
    }

    /**
     * Function getInfoWithMultipleWhere
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 22:42
     *
     * @param string $wheres
     * @param string $field
     * @param null   $format
     * @param null   $selectField
     *
     * @return array|false|mixed|string
     */
    public function getInfoWithMultipleWhere($wheres = '', $field = 'id', $format = NULL, $selectField = NULL)
    {
        $format = strtolower($format);
        if (!empty($selectField)) {
            if (!is_array($selectField)) {
                $selectField = [$selectField];
            }
        } else {
            $selectField = ['*'];
        }
        $db = $this->db->select($selectField)->from($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $key => $value) {
                if (is_array($value['value'])) {
                    $db->whereIn($value['field'], $value['value']);
                } else {
                    $db->where($value['field'], $value['operator'], $value['value']);
                }
            }
        } else {
            $db->where($field, self::OPERATOR_EQUAL_TO, $wheres);
        }
        if ($format == 'result') {
            $result = $db->execute()->fetchAll();
            $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        } else {
            $result = $db->execute()->fetch();
            $this->debug->debug(__FUNCTION__, 'Format is get first Result => ' . json_encode($result));
        }
        $this->debug->debug(__FUNCTION__, 'GET Result => ' . json_encode($result));
        if ($format == 'json') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Json');

            return json_encode($result);
        } else {
            return $result;
        }
    }

    /**
     * Function getValue
     *
     * @author : 713uk13m <dev@nguyenanhung.com>
     * @time   : 2018-12-01 22:45
     *
     * @param string $value
     * @param string $field
     * @param string $fieldOutput
     *
     * @return mixed|null
     */
    public function getValue($value = '', $field = 'id', $fieldOutput = '')
    {
        if (!is_array($fieldOutput)) {
            $fieldOutput = [$fieldOutput];
        }
        $db = $this->db->select($fieldOutput)->from($this->table);
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
        $result = $db->execute()->fetch();

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
     * @time   : 2018-12-01 22:46
     *
     * @param string $wheres
     * @param string $field
     * @param string $fieldOutput
     *
     * @return mixed|null
     */
    public function getValueWithMultipleWhere($wheres = '', $field = 'id', $fieldOutput = '')
    {
        if (!is_array($fieldOutput)) {
            $fieldOutput = [$fieldOutput];
        }
        $db = $this->db->select($fieldOutput)->from($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $key => $value) {
                if (is_array($value['value'])) {
                    $db->whereIn($value['field'], $value['value']);
                } else {
                    $db->where($value['field'], $value['operator'], $value['value']);
                }
            }
        } else {
            $db->where($field, self::OPERATOR_EQUAL_TO, $wheres);
        }
        $result = $db->execute()->fetch();

        $this->debug->debug(__FUNCTION__, 'GET Result => ' . json_encode($result));
        if (isset($result->$fieldOutput)) {
            return $result->$fieldOutput;
        } else {
            return NULL;
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
     * @return \Illuminate\Support\Collection|object|array
     */
    public function getDistinctResult($selectField = '')
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $db     = $this->db->select($selectField)->from($this->table)->distinct();
        $result = $db->execute()->fetchAll();
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
     * @param string|array $selectField         Mảng dữ liệu danh sách các field cần so sánh
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
     * @return object|array|\Illuminate\Support\Collection|string Mảng|String|Object dữ liều phụ hợp với yêu cầu
     *                                                     map theo biến format truyền vào
     */
    public function getResult($wheres = [], $selectField = '*', $options = NULL)
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $db = $this->db->select($selectField)->from($this->table);
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
        $result = $db->execute()->fetchAll();
        $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));

        return $result;
    }

    /**
     * Function getResult - Đa điều kiện
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 16:14
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
     * @see   https://laravel.com/docs/5.4/queries#selects
     *
     * @return object|array|\Illuminate\Support\Collection|string Mảng|String|Object dữ liều phụ hợp với yêu cầu
     *                                                     map theo biến format truyền vào
     */
    public function getResultWithMultipleWhere($wheres = [], $selectField = '*', $options = NULL)
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $db = $this->db->select($selectField)->from($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value['value'])) {
                    $db->whereIn($value['field'], $value['value']);
                } else {
                    $db->where($value['field'], $value['operator'], $value['value']);
                }
            }
        }
        if (isset($options['orderBy']) && is_array($options['orderBy'])) {
            foreach ($options['orderBy'] as $column => $direction) {
                $db->orderBy($column, $direction);
            }
        }
        $result = $db->execute()->fetchAll();
        $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));

        return $result;
    }

    /**
     * Function countResult
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/25/18 14:10
     *
     * @param array  $wheres
     * @param string $selectField
     *
     * @return int
     */
    public function countResult($wheres = [], $selectField = '*')
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $db = $this->db->select($selectField)->from($this->table);
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
        $totalItem = $db->execute()->rowCount();
        $this->debug->debug(__FUNCTION__, 'Total Item Result => ' . json_encode($totalItem));

        return $totalItem;
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
        $insertId = $this->db->insert($data)->into($this->table)->execute(FALSE);

        return $insertId;
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
        $db = $this->db->update($data);
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
        $resultId = $db->execute();
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
        $db = $this->db->delete($this->table);
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
        $resultId = $db->execute();
        $this->debug->info(__FUNCTION__, 'Result Delete Rows: ' . $resultId);

        return $resultId;
    }
}
