<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 2018-12-03
 * Time: 13:42
 */

namespace nguyenanhung\MyDatabase\Model;

/**
 * Interface CIBaseModelInterface
 *
 * @package   nguyenanhung\MyDatabase\Model
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
interface CIBaseModelInterface
{
    /**
     * Function connection
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 23:10
     *
     * @return $this
     */
    public function connection();

    /**
     * Function disconnect
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:55
     *
     * @return $this
     */
    public function disconnect();

    /**
     * Function reconnect
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 13:52
     *
     */
    public function reconnect();

    /**
     * Function getConnection
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 22:27
     *
     * @return object
     */
    public function getConnection();

    /**
     * Function getConnectionName
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 22:28
     *
     * @return string
     */
    public function getConnectionName();

    /**
     * Hàm set và kết nối cơ sở dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:43
     *
     * @param array  $database Mảng dữ liệu thông tin DB cần kết nối
     * @param string $name     Tên DB kết nối
     *
     * @return  $this
     */
    public function setDatabase($database = [], $name = 'default');

    /**
     * Function getDatabase
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 23:07
     *
     * @return array|null
     */
    public function getDatabase();

    /**
     * Hàm set và kết nối đến bảng dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:43
     *
     * @param string $table Bảng cần lấy dữ liệu
     *
     * @return  $this
     */
    public function setTable($table = '');

    /**
     * Function getTable
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 23:07
     *
     * @return string|null
     */
    public function getTable();

    /*************************** DATABASE METHOD ***************************/
    /**
     * Hàm truncate bảng dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 14:07
     *
     * @return mixed
     */
    public function truncate();

    /**
     * Function countAll
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 13:57
     *
     * @return int
     */
    public function countAll();

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
    public function checkExists($whereValue = '', $whereField = 'id');

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
    public function checkExistsWithMultipleWhere($whereValue = '', $whereField = 'id');

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
    public function getLatest($selectField = '*', $byColumn = 'id');

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
    public function getOldest($selectField = '*', $byColumn = 'id');

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
     * @return mixed|object|null
     */
    public function getInfo($value = '', $field = 'id', $format = NULL, $selectField = NULL);

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
     * @return mixed|object|null
     */
    public function getInfoWithMultipleWhere($wheres = '', $field = 'id', $format = NULL, $selectField = NULL);

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
     * @return mixed|null
     */
    public function getValue($value = '', $field = 'id', $fieldOutput = '');

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
     * @return mixed|null
     */
    public function getValueWithMultipleWhere($wheres = '', $field = 'id', $fieldOutput = '');

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
    public function getDistinctResult($selectField = '*');

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
    public function getResultDistinct($selectField = '*');

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
    public function getResult($wheres = [], $selectField = '*', $options = NULL);

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
    public function getResultWithMultipleWhere($wheres = [], $selectField = '*', $options = NULL);

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
    public function countResult($wheres = [], $selectField = '*');

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
    public function getResultWithSimpleJoin($joins = [], $select = '*', $options = NULL);

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
    public function getResultWithSimpleLeftJoin($joins = [], $select = '*', $options = NULL);

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
    public function add($data = []);

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
    public function update($data = [], $wheres = []);

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
    public function delete($wheres = []);
}
