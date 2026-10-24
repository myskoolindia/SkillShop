<?php

namespace App\Commands;

use App\Libraries\RoboQuestUnlocks;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/** Reports paid, cancelled and refunded orders to RoboQuest; run from cron every few minutes. */
class RoboQuestSync extends BaseCommand
{
    protected $group = 'RoboQuest';
    protected $name = 'roboquest:sync';
    protected $description = 'Send pending RoboQuest content unlocks for shop orders (safe to repeat).';
    protected $usage = 'roboquest:sync';

    public function run(array $params)
    {
        if (!RoboQuestUnlocks::configured()) {
            CLI::error('Set roboquest.webhookURL and roboquest.webhookSecret (32+ characters) in .env.');
            return EXIT_ERROR;
        }
        foreach ((new RoboQuestUnlocks())->syncAll() as $state => $count) {
            CLI::write(str_pad($state, 18) . $count);
        }
        return EXIT_SUCCESS;
    }
}
