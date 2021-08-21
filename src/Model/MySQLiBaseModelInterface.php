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
     * @param array  $database
     * @param string $name
     *
     * @return $this
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:53
     */
    public function setDatabase($database = array(), $name = 'default');

    /**
     * Function getDatabase
     *
     * @return array|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:53
     */
    public function getDatabase();

    /**
     * Function getDbName
     *
     * @return string
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:59
     */
    public function getDbName();

    /**
     * Function setTable
     *
     * @param string $table
     *
     * @return $this
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 21:54
     */
    public function setTable($table = '');

    /**
     * Function getTable
     *
     * @return string|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 21:54
     */
    public function getTable();

    /**
     * Function connection
     *
     * @return $this
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:43
     */
    public function connection();

    /**
     * Function disconnect
     *
     * @param string $name
     *
     * @return void|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:01
     */
    public function disconnect($name = '');

    /**
     * Function disconnectAll
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 29:15
     */
    public function disconnectAll();

    /**
     * Function getDb
     *
     * @return object
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 22:03
     *
     */
    public function getDb();

    /*************************** DATABASE METHOD ***************************/
    /**
     * Function countAll
     *
     * @param string $column
     *
     * @return int|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 33:24
     */
    public function countAll($column = '*');

    /**
     * Function checkExists
     *
     * @param string $whereValue
     * @param string $whereField
     * @param string $select
     *
     * @return int|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 33:50
     */
    public function checkExists($whereValue = '', $whereField = 'id', $select = '*');

    /**
     * Function checkExistsWithMultipleWhere
     *
     * @param string $whereValue
     * @param string $whereField
     * @param string $select
     *
     * @return int|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 34:04
     */
    public function checkExistsWithMultipleWhere($whereValue = '', $whereField = 'id', $select = '*');

    /**
     * Function getLatest
     *
     * @param string $selectField
     * @param string $byColumn
     *
     * @return array|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 34:21
     */
    public function getLatest($selectField = '*', $byColumn = 'id');

    /**
     * Function getOldest
     *
     * @param string $selectField
     * @param string $byColumn
     *
     * @return array|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 34:30
     */
    public function getOldest($selectField = '*', $byColumn = 'id');

    /**
     * Function getInfo
     *
     * @param string $value
     * @param string $field
     * @param string $selectField
     *
     * @return array|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 34:56
     */
    public function getInfo($value = '', $field = 'id', $selectField = '*');

    /**
     * Function getInfoWithMultipleWhere
     *
     * @param string $wheres
     * @param string $field
     * @param null   $selectField
     *
     * @return array|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 35:18
     */
    public function getInfoWithMultipleWhere($wheres = '', $field = 'id', $selectField = NULL);

    /**
     * Function getValue
     *
     * @param string $value
     * @param string $field
     * @param string $fieldOutput
     *
     * @return mixed|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 35:39
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
     * @time     : 08/02/2020 36:20
     */
    public function getValueWithMultipleWhere($wheres = '', $field = 'id', $fieldOutput = '');

    /**
     * Function getDistinctResult
     *
     * @param string $selectField
     *
     * @return array|\nguyenanhung\MySQLi\MysqliDb|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 36:54
     */
    public function getDistinctResult($selectField = '*');

    /**
     * Function getResultDistinct
     *
     * @param string $selectField
     *
     * @return array|\nguyenanhung\MySQLi\MysqliDb|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 36:58
     */
    public function getResultDistinct($selectField = '');

    /**
     * Function getResult
     *
     * @param array  $wheres
     * @param string $selectField
     * @param null   $options
     *
     * @return array|\nguyenanhung\MySQLi\MysqliDb|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 37:11
     */
    public function getResult($wheres = array(), $selectField = '*', $options = NULL);

    /**
     * Function getResultWithMultipleWhere
     *
     * @param array  $wheres
     * @param string $selectField
     * @param null   $options
     *
     * @return array|\nguyenanhung\MySQLi\MysqliDb|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 37:18
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
     * @time     : 08/02/2020 37:51
     */
    public function countResult($wheres = array(), $selectField = '*');

    /**
     * Function add
     *
     * @param array $data
     *
     * @return int|null
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 38:06
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
     * @time     : 08/02/2020 38:31
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
     * @time     : 08/02/2020 38:47
     */
    public function delete($wheres = array());
}
