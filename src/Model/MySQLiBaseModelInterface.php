<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 2018-12-02
 * Time: 20:35
 */

namespace nguyenanhung\MyDatabase\Model;

/**
 * Interface MySQLiBaseModelInterface
 *
 * @package   nguyenanhung\MyDatabase\Model
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
interface MySQLiBaseModelInterface
{
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
    public function setDatabase($database = [], $name = 'default');

    /**
     * Function getDatabase
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:53
     *
     * @return array|null
     */
    public function getDatabase();

    /**
     * Function getDbName
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:59
     *
     * @return string
     */
    public function getDbName();

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
    public function setTable($table = '');

    /**
     * Function getTable
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 21:54
     *
     * @return string|null
     */
    public function getTable();

    /**
     * Function connection
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:43
     *
     * @return $this
     */
    public function connection();

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
    public function disconnect($name = '');

    /**
     * Function disconnectAll
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:01
     *
     */
    public function disconnectAll();

    /**
     * Function getDb
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 22:03
     *
     * @return object
     */
    public function getDb();

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
    public function countAll($column = '*');

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
    public function checkExists($whereValue = '', $whereField = 'id', $select = '*');

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
    public function checkExistsWithMultipleWhere($whereValue = '', $whereField = 'id', $select = '*');

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
    public function getLatest($selectField = '*', $byColumn = 'id');

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
    public function getOldest($selectField = '*', $byColumn = 'id');

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
    public function getInfo($value = '', $field = 'id', $selectField = '*');

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
    public function getInfoWithMultipleWhere($wheres = '', $field = 'id', $selectField = NULL);

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
    public function getValue($value = '', $field = 'id', $fieldOutput = '');

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
    public function getValueWithMultipleWhere($wheres = '', $field = 'id', $fieldOutput = '');

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
    public function getDistinctResult($selectField = '*');

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
    public function getResultDistinct($selectField = '');

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
    public function getResult($wheres = [], $selectField = '*', $options = NULL);

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
    public function getResultWithMultipleWhere($wheres = [], $selectField = '*', $options = NULL);

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
    public function countResult($wheres = [], $selectField = '*');

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
    public function add($data = []);

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
    public function update($data = [], $wheres = []);

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
    public function delete($wheres = []);
}
