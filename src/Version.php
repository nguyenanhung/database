<?php
/**
 * Project database
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 09/22/2021
 * Time: 02:59
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
     * @return string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 08/29/2021 04:53
     */
    public function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * Function getSDKPropertiesInfo
     *
     * @param  bool  $json
     *
     * @return array|false|string
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     * @time     : 09/22/2021 04:23
     */
    public function getSDKPropertiesInfo(bool $json = false)
    {
        $properties = array(
            'name' => self::PROJECT_NAME,
            'version' => self::VERSION,
            'last_modified' => self::LAST_MODIFIED,
            'author_name' => self::AUTHOR_NAME,
            'author_email' => self::AUTHOR_EMAIL,
            'author_url' => self::AUTHOR_URL,
            'github_url' => self::GITHUB_URL,
            'packages_url' => self::PACKAGES_URL
        );
        if ($json === true) {
            return json_encode($properties);
        }

        return $properties;
    }
}
