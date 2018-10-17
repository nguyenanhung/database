<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 10/17/18
 * Time: 01:59
 */

namespace nguyenanhung\MyDatabase\Interfaces;

/**
 * Interface BackupInterface
 *
 * @package   nguyenanhung\MyDatabase\Interfaces
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
interface BackupInterface
{
    /**
     * Hàm set mảng dữ liệu Storage
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 02:00
     *
     * @param array $storage Mảng dữ liệu storage
     *
     * @see   https://github.com/backup-manager/backup-manager/blob/master/examples/standalone/config/storage.php
     */
    public function setStorage($storage = []);

    /**
     * Hàm set File dữ liệu Storage
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 02:00
     *
     * @param string $storageFile File dữ liệu storage
     *
     * @see   https://github.com/backup-manager/backup-manager/blob/master/examples/standalone/config/storage.php
     */
    public function setStorageFile($storageFile = '');

    /**
     * Hàm set mảng dữ liệu Database
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 02:00
     *
     * @param array $database Mảng dữ liệu database
     *
     * @see   https://github.com/backup-manager/backup-manager/blob/master/examples/standalone/config/database.php
     */
    public function setDatabase($database = []);

    /**
     * Hàm set File dữ liệu Database
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 02:00
     *
     * @param string $databaseFile File dữ liệu database
     *
     * @see   https://github.com/backup-manager/backup-manager/blob/master/examples/standalone/config/database.php
     */
    public function setDatabaseFile($databaseFile = '');

    /**
     * Hàm set Folder lưu trữ file Backup
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 09:07
     *
     * @param string $folderBackup Folder lưu trữ file Backup, VD: /your/to/path
     */
    public function setFolderBackup($folderBackup = '');

    /**
     * Hàm bootstrap cho tiến trình Backup và Restore Database
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 02:07
     *
     * @return \BackupManager\Manager
     * @throws \BackupManager\Config\ConfigFileNotFound
     */
    public function boot();

    /**
     * Hàm backup Database
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 09:28
     *
     * @param string $database Tên cấu hình Database
     *
     * @return bool|string TRUE nếu thành công, Message Error nếu có lỗi xảy ra
     */
    public function backup($database = '');

    /**
     * Hàm phục hồi CSDL
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 09:29
     *
     * @param string $database Tên cấu hình Database cần phục hồi
     * @param string $filename Đường dẫn đến file sao lưu
     *
     * @return bool|string TRUE nếu thành công, Message Error nếu có lỗi xảy ra
     */
    public function restore($database = '', $filename = '');
}
