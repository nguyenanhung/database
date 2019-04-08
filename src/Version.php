<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 2019-04-08
 * Time: 10:08
 */

namespace nguyenanhung\MyDatabase;

/**
 * Trait Version
 *
 * @package   nguyenanhung\MyDatabase
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
trait Version
{
    /**
     * Function getVersion
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 9/28/18 14:47
     *
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }
}
