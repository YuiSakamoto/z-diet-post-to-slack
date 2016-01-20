<?php
/**
 * Created by IntelliJ IDEA.
 * User: yui_tang
 * Date: 1/20/16
 * Time: 19:47
 */

namespace ZDiet\Service;

use ZDiet\Entity\User;

class UserService
{

    public function __construct()
    {
        $this->users_dir = dirname(dirname(dirname(__FILE__))) . '/tmp/users/';
    }

    public function getUsers()
    {
        $users = [];
        $paths = $this->getUsersFilePath();
        foreach ($paths as $path) {
            $raw = file_get_contents($path);
            $values = explode("\t", $raw);
            $users[] = new User($values);
        }
        return $users;
    }

    private function getUsersFilePath()
    {
        $files = scandir($this->users_dir);
        $files = array_filter($files, function ($file) {
            return !in_array($file, array('.', '..'));
        });
        $list = [];

        foreach ($files as $file) {
            $fullpath = rtrim($this->users_dir, '/') . '/' . $file;
            if (is_file($fullpath)) {
                $list[] = $fullpath;
            }
            if (is_dir($fullpath)) {
                $list = array_merge($list, getFileList($fullpath));
            }
        }

        return $list;
    }
}