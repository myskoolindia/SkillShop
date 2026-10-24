<?php

namespace App\Commands;

use App\Libraries\RoboQuestUnlocks;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/** Links a buyer email to a RoboQuest school when it is not a School Admin/Teacher email there. */
class RoboQuestLink extends BaseCommand
{
    protected $group = 'RoboQuest';
    protected $name = 'roboquest:link';
    protected $description = 'Link a shop buyer email to a RoboQuest school ID, then resend that buyer\'s orders.';
    protected $usage = 'roboquest:link <buyer-email> <school-id>';

    public function run(array $params)
    {
        [$email, $schoolId] = $params + [null, null];
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$schoolId || !preg_match('/^[A-Za-z0-9_-]{1,80}$/', $schoolId)) {
            CLI::error('Usage: php spark ' . $this->usage);
            return EXIT_USER_INPUT;
        }
        $unlocks = new RoboQuestUnlocks();
        $unlocks->link($email, $schoolId);
        CLI::write("Linked {$email} to {$schoolId}.");
        foreach ($unlocks->syncAll() as $state => $count) {
            CLI::write(str_pad($state, 18) . $count);
        }
        return EXIT_SUCCESS;
    }
}
