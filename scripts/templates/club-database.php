<?php
namespace Config;
use CodeIgniter\Database\Config;

class Database extends Config
{
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;
    public string $defaultGroup = 'default';
    public array $default = [
        'DSN' => '', 'hostname' => '127.0.0.1', 'username' => '', 'password' => '',
        'database' => '', 'DBDriver' => 'MySQLi', 'DBPrefix' => '', 'pConnect' => false,
        'DBDebug' => false, 'charset' => 'utf8mb4', 'DBCollat' => 'utf8mb4_unicode_ci',
        'swapPre' => '', 'encrypt' => false, 'compress' => false, 'strictOn' => false,
        'failover' => [], 'port' => 33307,
    ];
    public function __construct()
    {
        parent::__construct();
        foreach (['hostname','username','password','database','DBDriver'] as $key) {
            $this->default[$key] = env('database.default.' . $key, $this->default[$key]);
        }
        $this->default['port'] = (int) env('database.default.port', 33307);
    }
}
