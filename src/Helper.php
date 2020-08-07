<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 2019-07-06
 * Time: 10:21
 */

namespace nguyenanhung\MyDatabase;

/**
 * Trait Helper
 *
 * @package   nguyenanhung\MyDatabase
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
trait Helper
{
    /**
     * Function preparePaging
     *
     * @param int $pageIndex
     * @param int $pageSize
     *
     * @return array
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/02/2020 23:39
     */
    public function preparePaging($pageIndex = 1, $pageSize = 10)
    {
        if ($pageIndex != 0) {
            if (!$pageIndex || $pageIndex <= 0 || empty($pageIndex)) {
                $pageIndex = 1;
            }
            $offset = ($pageIndex - 1) * $pageSize;
        } else {
            $offset = $pageIndex;
        }
        $response = array(
            'offset' => $offset,
            'limit'  => $pageSize
        );

        return $response;
    }
}
