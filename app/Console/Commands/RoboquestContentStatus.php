<?php
namespace App\Console\Commands;
use App\Models\Course;
use Illuminate\Console\Command;
class RoboquestContentStatus extends Command {
    protected $signature='roboquest:content-status';
    protected $description='List mapped courses and their active chapter and lesson counts';
    public function handle(): int {
        $rows=[];
        foreach(Course::whereNotNull('api_course_id')->with('chapters.chapterItems.lesson')->orderBy('api_course_id')->get() as $course){
            $active=$course->chapters->where('status','active');$lessons=$active->flatMap(fn($chapter)=>$chapter->chapterItems->map(fn($item)=>$item->lesson)->filter());
            $rows[]=[$course->id,$course->api_course_id,$course->title,$active->count(),$course->chapters->count()-$active->count(),$lessons->count(),$course->instructor_id?'Yes':'Missing'];
        }
        $this->table(['Local ID','API ID','Title','Active chapters','Draft chapters','Active lessons','Instructor'],$rows);
        $this->line('Existing explicit mappings only; review authored content before activation. This does not grant course access.');return self::SUCCESS;
    }
}
