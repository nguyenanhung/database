<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 2019-07-20
 * Time: 09:08
 */

namespace nguyenanhung\MyDatabase\Model;

/**
 * Interface PDOUtilsModelInterface
 *
 * @package   nguyenanhung\MyDatabase\Model
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
interface PDOUtilsModelInterface
{
    /**
     * Function setDatabase
     *
     * @param array  $database
     * @param string $name
     *
     * @return mixed
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-07-20 09:09
     *
     */
    public function setDatabase($database = [], $name = 'default');

    /**
     * Function getDatabase
     *
     * @return array|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:42
     *
     */
    public function getDatabase();

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
    public function setTable($table = '');

    /**
     * Function getTable
     *
     * @return string|null
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 21:54
     *
     */
    public function getTable();

    /**
     * Function connection
     *
     * @return $this
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 20:43
     *
     */
    public function connection();

    /**
     * Function disconnect
     *
     * @return $this
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 22:03
     *
     */
    public function disconnect();

    /**
     * Function getDb
     *
     * @return object
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 22:03
     *
     */
    public function getDb();

    /**
     * Function getPrimaryKey
     *
     * @return string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 41:53
     */
    public function getPrimaryKey();

    /*************************** DATABASE METHOD ***************************/
    /**
     * Function rawExecStatement
     *
     * @param string $statement
     *
     * @return bool
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2019-07-20 09:07
     *
     */
    public function rawExecStatement($statement = '');
}