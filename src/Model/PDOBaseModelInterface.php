<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 2018-12-01
 * Time: 21:52
 */

namespace nguyenanhung\MyDatabase\Model;

/**
 * Interface PDOBaseModelInterface
 *
 * @package   nguyenanhung\MyDatabase\Model
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
interface PDOBaseModelInterface
{
    /**
     * Function setDatabase
     *
     * @param array  $database
     * @param string $name
     *
     * @return $this|\nguyenanhung\MyDatabase\Model\PDOBaseModel
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 44:12
     */
    public function setDatabase($database = [], $name = 'default');

    /**
     * Function getDatabase
     *
     * @return array|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 44:18
     */
    public function getDatabase();

    /**
     * Function setTable
     *
     * @param string $table
     *
     * @return $this|\nguyenanhung\MyDatabase\Model\PDOBaseModel
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 44:22
     */
    public function setTable($table = '');

    /**
     * Function getTable
     *
     * @return string|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 44:26
     */
    public function getTable();

    /**
     * Function connection
     *
     * @return $this|\nguyenanhung\MyDatabase\Model\PDOBaseModel
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 44:31
     */
    public function connection();

    /**
     * Function disconnect
     *
     * @return $this|\nguyenanhung\MyDatabase\Model\PDOBaseModel
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 44:37
     */
    public function disconnect();

    /**
     * Function getDb
     *
     * @return \FaaPz\PDO\Database|object
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 44:42
     */
    public function getDb();

    /**
     * Function countAll - Hàm đếm toàn bộ bản ghi tồn tại trong bảng
     *
     * @param string|array $select
     *
     * @return int
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 45:10
     */
    public function countAll($select = ['id']);

    /**
     * Function checkExists - Hàm kiểm tra sự tồn tại bản ghi theo tham số đầu vào
     *
     * @param string|array      $whereValue Giá trị cần kiểm tra
     * @param string|null       $whereField Field tương ứng, ví dụ: ID
     * @param string|array|null $select     Bản ghi cần chọn
     *
     * @return int Số lượng bàn ghi tồn tại phù hợp với điều kiện đưa ra
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/16/18 11:45
     */
    public function checkExists($whereValue = '', $whereField = 'id', $select = ['*']);

    /**
     * Hàm kiểm tra sự tồn tại bản ghi theo tham số đầu vào - Đa điều kiện
     *
     * @param string|array      $whereValue Giá trị cần kiểm tra
     * @param string|null       $whereField Field tương ứng, ví dụ: ID
     * @param string|array|null $select     Bản ghi cần chọn
     *
     * @return int Số lượng bàn ghi tồn tại phù hợp với điều kiện đưa ra
     * @author    : 713uk13m <dev@nguyenanhung.com>
     * @copyright : 713uk13m <dev@nguyenanhung.com>
     * @time      : 10/16/18 11:45
     */
    public function checkExistsWithMultipleWhere($whereValue = '', $whereField = 'id', $select = ['*']);

    /**
     * Function getLatest - Hàm lấy bản ghi mới nhất theo điều kiện
     *
     * Mặc định giá trị so sánh dựa trên column created_at
     *
     * @param array  $selectField Danh sách các column cần lấy
     * @param string $byColumn    Column cần so sánh dữ liệu, mặc định sẽ sử dụng column created_at
     *
     * @return mixed
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 53:20
     */
    public function getLatest($selectField = ['*'], $byColumn = 'created_at');

    /**
     * Function getOldest - Hàm lấy bản ghi cũ nhất nhất theo điều kiện
     *
     * Mặc định giá trị so sánh dựa trên column created_at
     *
     * @param array  $selectField Danh sách các column cần lấy
     * @param string $byColumn    Column cần so sánh dữ liệu, mặc định sẽ sử dụng column created_at
     *
     * @return mixed
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 54:15
     */
    public function getOldest($selectField = ['*'], $byColumn = 'created_at');

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
     * @return object|array|string|null Mảng|String|Object dữ liều phụ hợp với yêu cầu map theo biến format truyền vào
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/16/18 11:51
     */
    public function getInfo($value = '', $field = 'id', $format = NULL, $selectField = NULL);

    /**
     * Function getInfoWithMultipleWhere
     *
     * @param string $wheres
     * @param string $field
     * @param null   $format
     * @param null   $selectField
     *
     * @return array|false|mixed|string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 56:15
     */
    public function getInfoWithMultipleWhere($wheres = '', $field = 'id', $format = NULL, $selectField = NULL);

    /**
     * Function getValue
     *
     * @param string $value
     * @param string $field
     * @param string $fieldOutput
     *
     * @return   mixed|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 58:06
     */
    public function getValue($value = '', $field = 'id', $fieldOutput = '');

    /**
     * Function getValueWithMultipleWhere
     *
     * @param string $wheres
     * @param string $field
     * @param string $fieldOutput
     *
     * @return   mixed|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 58:54
     */
    public function getValueWithMultipleWhere($wheres = '', $field = 'id', $fieldOutput = '');

    /**
     * Hàm lấy danh sách Distinct toàn bộ bản ghi trong 1 bảng
     *
     * @param string $selectField Mảng dữ liệu danh sách các field cần so sánh
     *
     * @return array
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 59:24
     */
    public function getDistinctResult($selectField = '');

    /**
     * Function getResultDistinct - Hàm getResultDistinct là alias của hàm getDistinctResult
     *
     * Các tham số đầu ra và đầu vào theo quy chuẩn của hàm getDistinctResult
     *
     * @param string $selectField Mảng dữ liệu danh sách các field cần so sánh
     *
     * @return array
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 59:37
     */
    public function getResultDistinct($selectField = '');

    /**
     * Function getResult
     *
     * @param array  $wheres
     * @param string $selectField
     * @param null   $options
     *
     * @return array
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 59:54
     */
    public function getResult($wheres = array(), $selectField = '*', $options = NULL);

    /**
     * Function getResultWithMultipleWhere
     *
     * @param array  $wheres
     * @param string $selectField
     * @param null   $options
     *
     * @return array
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 47:38
     */
    public function getResultWithMultipleWhere($wheres = array(), $selectField = '*', $options = NULL);

    /**
     * Function countResult
     *
     * @param array  $wheres
     * @param string $selectField
     *
     * @return int
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 48:26
     */
    public function countResult($wheres = array(), $selectField = '*');

    /**
     * Function add
     *
     * @param array $data
     *
     * @return int|string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 49:03
     */
    public function add($data = array());

    /**
     * Function update
     *
     * @param array $data
     * @param array $wheres
     *
     * @return int
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 50:08
     */
    public function update($data = array(), $wheres = array());

    /**
     * Function delete
     *
     * @param array $wheres
     *
     * @return int
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 10/09/2020 50:03
     */
    public function delete($wheres = array());
}
