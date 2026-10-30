<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAttachmentPathToEmailQueue extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('email_queue') && !$this->db->fieldExists('attachment_path', 'email_queue')) {
            $this->forge->addColumn('email_queue', [
                'attachment_path' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'template_path',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('email_queue') && $this->db->fieldExists('attachment_path', 'email_queue')) {
            $this->forge->dropColumn('email_queue', 'attachment_path');
        }
    }
}
