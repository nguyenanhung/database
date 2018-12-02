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
}

