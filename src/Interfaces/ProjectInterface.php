<?php
/**
 * Project td-lottery.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 10/4/18
 * Time: 14:55
 */

namespace nguyenanhung\MyDatabase\Interfaces;

/**
 * Interface ProjectInterface
 *
 * @package   nguyenanhung\MyDatabase\Interfaces
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
interface ProjectInterface
{
    const VERSION       = '0.0.1';
    const USE_BENCHMARK = TRUE;

    /**
     * Function getVersion
     *
     * @author  : 713uk13m <dev@nguyenanhung.com>
     * @time    : 10/13/18 15:12
     *
     * @return mixed|string Current Project Version
     * @example 0.1.0
     */
    public function getVersion();
}
