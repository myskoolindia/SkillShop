<?php
namespace App\Services;

use App\Models\{Course,CourseChapter,CourseChapterItem,CourseChapterLesson};
use Illuminate\Support\Facades\{DB,Validator};
use Illuminate\Validation\ValidationException;

class RoboquestContentImport {
    // IDs in the returned receipt make subsequent edits update the same learning records.
    public function run(array $bundle, bool $apply = false): array {
        Validator::make($bundle, [
            'version'=>'required|integer|in:1','courses'=>'required|array|min:1|max:100',
            'courses.*.course_id'=>'required|integer|min:1|distinct',
            'courses.*.external_course_id'=>'required|integer|min:1|distinct',
            'courses.*.chapters'=>'required|array|min:1|max:100',
            'courses.*.chapters.*.id'=>'nullable|integer|min:1',
            'courses.*.chapters.*.title'=>'required|string|max:255',
            'courses.*.chapters.*.status'=>'required|in:active,inactive',
            'courses.*.chapters.*.lessons'=>'required|array|min:1|max:100',
            'courses.*.chapters.*.lessons.*.id'=>'nullable|integer|min:1',
            'courses.*.chapters.*.lessons.*.title'=>'required|string|max:255',
            'courses.*.chapters.*.lessons.*.text'=>'present|nullable|string|max:12000',
            'courses.*.chapters.*.lessons.*.materials'=>'present|nullable|string|max:12000',
            'courses.*.chapters.*.lessons.*.video_url'=>'present|nullable|string|max:2000',
        ])->validate();
        return DB::transaction(function() use ($bundle,$apply) {
            $receipt=['version'=>1,'courses'=>[]];
            foreach($bundle['courses'] as $ci=>$input) {
                $course=Course::whereKey($input['course_id'])->lockForUpdate()->first();
                $this->require($course && $course->instructor_id,"courses.$ci",'Use an existing course with an instructor.');
                $this->require((int)$course->api_course_id===$input['external_course_id'],"courses.$ci.external_course_id",'The API ID must match the explicit mapping on this local course. No title or ID guessing is allowed.');
                $saved=['course_id'=>$course->id,'external_course_id'=>$input['external_course_id'],'chapters'=>[]];$seenChapters=[];
                foreach($input['chapters'] as $index=>$chapterInput) {
                    $key="courses.$ci.chapters.$index";
                    $this->require(trim($chapterInput['title'])!=='',$key,'Chapter title cannot be blank.');
                    $chapter=isset($chapterInput['id'])?CourseChapter::where('course_id',$course->id)->find($chapterInput['id']):null;
                    $this->require(!isset($chapterInput['id'])||$chapter,"$key.id",'Chapter ID does not belong to this course.');
                    $identity=$chapter?'id:'.$chapter->id:'title:'.mb_strtolower(trim($chapterInput['title']));
                    $this->require(!isset($seenChapters[$identity]),$key,'Duplicate chapter in bundle.');$seenChapters[$identity]=true;
                    $this->require($chapter||!CourseChapter::where('course_id',$course->id)->where('title',trim($chapterInput['title']))->exists(),$key,'A chapter with this title exists. Use its ID to update it.');
                    if($apply){$chapter=$chapter??new CourseChapter();$chapter->fill(['course_id'=>$course->id,'instructor_id'=>$course->instructor_id,'title'=>trim($chapterInput['title']),'status'=>$chapterInput['status'],'order'=>$index+1])->save();}
                    $savedChapter=['id'=>$chapter?->id,'title'=>trim($chapterInput['title']),'status'=>$chapterInput['status'],'lessons'=>[]];$seenLessons=[];
                    foreach($chapterInput['lessons'] as $li=>$lessonInput) {
                        $lk="$key.lessons.$li";$this->require(trim($lessonInput['title'])!=='',$lk,'Lesson title cannot be blank.');$video=$lessonInput['video_url']?:null;
                        $this->require(!$video||($this->safeVideo($video)),"$lk.video_url",'Use an HTTPS video URL without embedded credentials.');
                        $text=trim(strip_tags($lessonInput['text']??''));$materials=trim(strip_tags($lessonInput['materials']??''));
                        $this->require($text!==''||$video,$lk,'A lesson needs readable text or a video. Titles and materials alone are not lesson content.');
                        $lesson=isset($lessonInput['id'])?CourseChapterLesson::where('course_id',$course->id)->where('chapter_id',$chapter?->id)->find($lessonInput['id']):null;
                        $this->require(!isset($lessonInput['id'])||$lesson,"$lk.id",'Lesson ID does not belong to this chapter and course.');
                        $identity=$lesson?'id:'.$lesson->id:'title:'.mb_strtolower(trim($lessonInput['title']));
                        $this->require(!isset($seenLessons[$identity]),$lk,'Duplicate lesson in bundle.');$seenLessons[$identity]=true;
                        $this->require($lesson||!$chapter||!CourseChapterLesson::where('chapter_id',$chapter->id)->where('title',trim($lessonInput['title']))->exists(),$lk,'A lesson with this title exists. Use its ID to update it.');
                        if($apply){
                            $item=$lesson?->chapterItem??new CourseChapterItem();
                            $item->fill(['chapter_id'=>$chapter->id,'instructor_id'=>$course->instructor_id,'type'=>'lesson','order'=>$li+1])->save();
                            $lesson=$lesson??new CourseChapterLesson();
                            $lesson->fill(['course_id'=>$course->id,'chapter_id'=>$chapter->id,'chapter_item_id'=>$item->id,'instructor_id'=>$course->instructor_id,'title'=>trim($lessonInput['title']),'description'=>$text,'material_required'=>$materials,'video_url'=>$video,'storage'=>'external_link','file_type'=>$video?'video':'txt','file_path'=>$video,'is_free'=>false])->save();
                        }
                        $savedChapter['lessons'][]=['id'=>$lesson?->id,'title'=>trim($lessonInput['title']),'text'=>$text,'materials'=>$materials,'video_url'=>$video];
                    }
                    $saved['chapters'][]=$savedChapter;
                }
                $receipt['courses'][]=$saved;
            }
            return $receipt;
        });
    }
    private function require($condition,string $key,string $message): void {if(!$condition)throw ValidationException::withMessages([$key=>$message]);}
    private function safeVideo(string $value): bool {$url=parse_url($value);return filter_var($value,FILTER_VALIDATE_URL)&&($url['scheme']??'')==='https'&&!isset($url['user'])&&!isset($url['pass']);}
}
