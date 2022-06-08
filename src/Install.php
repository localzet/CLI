<?php

namespace localzet\CLI;

class Install
{
    const FRAMEX_PLUGIN = true;

    /**
     * @var array
     */
    protected static $pathRelation = array(
        'config/plugin/framex/console' => 'config/plugin/framex/console',
    );

    /**
     * Install
     * @return void
     */
    public static function install()
    {
        copy(__DIR__ . "/framex", base_path() . "/framex");
        chmod(base_path() . "/framex", 0755);

        static::installByRelation();
    }

    /**
     * Uninstall
     * @return void
     */
    public static function uninstall()
    {
        if (is_file(base_path() . "/framex")) {
            unlink(base_path() . "/framex");
        }

        self::uninstallByRelation();
    }

    /**
     * installByRelation
     * @return void
     */
    public static function installByRelation()
    {
        foreach (static::$pathRelation as $source => $dest) {
            if ($pos = strrpos($dest, '/')) {
                $parent_dir = base_path() . '/' . substr($dest, 0, $pos);
                if (!is_dir($parent_dir)) {
                    mkdir($parent_dir, 0777, true);
                }
            }
            //symlink(__DIR__ . "/$source", base_path()."/$dest");
            copy_dir(__DIR__ . "/$source", base_path() . "/$dest");
        }
    }

    /**
     * uninstallByRelation
     * @return void
     */
    public static function uninstallByRelation()
    {
        foreach (static::$pathRelation as $source => $dest) {
            $path = base_path() . "/$dest";
            if (!is_dir($path) && !is_file($path)) {
                continue;
            }
            /*if (is_link($path) {
                unlink($path);
            }*/
            remove_dir($path);
        }
    }
}
