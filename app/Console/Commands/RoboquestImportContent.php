<?php
namespace App\Console\Commands;
use App\Services\RoboquestContentImport;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;
class RoboquestImportContent extends Command {
    protected $signature='roboquest:import-content {file : Path to a UTF-8 JSON bundle} {--apply : Save the validated bundle} {--receipt= : Write the bundle with stable chapter and lesson IDs}';
    protected $description='Preview or import mapped RoboQuest course lessons without changing orders or assignments';
    public function handle(RoboquestContentImport $service): int {
        try {
            $file=$this->argument('file');
            if(!is_file($file)||filesize($file)>10*1024*1024)throw new \RuntimeException('Use a local JSON file under 10 MB.');
            $bundle=json_decode(file_get_contents($file),true,512,JSON_THROW_ON_ERROR);
            if(!is_array($bundle))throw new \RuntimeException('Content must be a JSON object.');
            $receipt=$service->run($bundle,(bool)$this->option('apply'));
            $encoded=json_encode($receipt,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR);
            if($path=$this->option('receipt')) {
                // Exclusive creation avoids accidentally replacing a source or previous receipt.
                $handle=@fopen($path,'x');if(!$handle)throw new \RuntimeException('Receipt path already exists or is not writable. Content validation/import has completed; receipt is printed below.');
                fwrite($handle,$encoded."\n");fclose($handle);
            }
            $this->info($this->option('apply')?'Content saved. Sync RoboQuest purchases to deliver active chapters.':'Preview passed. No lessons, purchases or assignments were changed.');
            $this->line($encoded);return self::SUCCESS;
        }catch(ValidationException $e){foreach($e->errors() as $key=>$messages)$this->error($key.': '.implode(' ',$messages));return self::FAILURE;}
        catch(\Throwable $e){$this->error($e->getMessage());if(isset($encoded))$this->line($encoded);return self::FAILURE;}
    }
}
