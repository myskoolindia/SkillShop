# Evening content handover

The application is prepared for text lessons and HTTPS video links in existing SkillShop courses. SkillShop remains the curriculum source; RoboQuest receives content only for confirmed school purchases. Importing content never creates a payment, enrollment or learner assignment.

## What to collect

For each course, supply its program (Skill2School / TTT / Upskill), provider API course ID, local SkillShop course ID, ordered chapters, ordered lessons, lesson text, required materials and optional HTTPS video URL. Use one lesson per learning unit. Preserve paragraph breaks. Provide the intended student grade or teacher audience for review. Include rights to use supplied images/video and any account requirements for media links.

Use `course-content.template.json` as a starting structure. Its zero IDs and empty lesson deliberately fail validation: replace them with real mappings and content. Raw PDF, DOCX, slides, worksheets, audio, executable exercises and quiz files should accompany the handover separately; this course-text importer does not parse, host, download or grade those files. Existing Skill Labs upload supports its own published resources, but is separate from the purchased-course reader. Video links open externally and need internet and any provider permissions.

## Prepare and preview

Run `.local/bin/php artisan roboquest:content-status` to list existing local/API mappings and active/draft chapter counts before handover. Missing catalog courses require an explicitly mapped local course; the content importer does not create or sell products.

1. Map the provider course ID explicitly to the existing local course's `api_course_id`. The importer refuses an absent or mismatched mapping; it never guesses by title or local ID. Courses must have an instructor. Review the audience before assigning: TTT/Upskill are teacher programs.
2. Save the filled UTF-8 JSON bundle outside public web folders. Keep `status: "inactive"` while reviewing a chapter. Use `"active"` only for approved content: active chapters are delivered on the school's next successful purchase sync (normally within 60 seconds).
3. From SkillShop, preview with:

   ```sh
   .local/bin/php artisan roboquest:import-content /path/to/evening-content.json
   ```

   The default validates mappings, IDs, URL safety, duplicate titles and required lesson content without writing. Text and video are optional individually; at least one must contain actual learning material. Materials alone do not qualify.
4. Save when ready:

   ```sh
   .local/bin/php artisan roboquest:import-content /path/to/evening-content.json --apply --receipt=/tmp/evening-content-with-ids.json
   ```

   The bundle is transactional: an invalid chapter/lesson rolls the whole import back. This writes only supplied curriculum records. Existing chapters/lessons omitted from the file remain unchanged. Do not use omission to unpublish: explicitly set a chapter to inactive. Reordering a partial chapter can conflict with items omitted from that chapter; send complete chapter lesson order when reordering.
5. Keep the receipt with the real content. It includes stable chapter/lesson IDs. Make subsequent edits in that receipt and preview/apply it. Stable IDs preserve learner completion keys and assignments; repeated new rows with existing titles are rejected rather than duplicated. Never reuse an ID from another course/chapter. Treat substantially changed lesson objectives as new lessons so learners complete them afresh.

The command prints the full validated content receipt; keep terminal logs private. `--receipt` uses a new filename so it cannot replace the source file. A receipt write failure is reported after the transaction; use the printed receipt if saving the file fails.

## Release check

- Preview passes for every mapped course; read through inactive chapters in SkillShop before activation.
- Confirm text, paragraph breaks, materials and the video destination. Open media as a learner, not just a content author.
- Activate approved chapters. In school RoboQuest, use Purchased Courses → Sync purchases or wait for the scheduled sync.
- Confirm course cards say Lessons ready / More lessons coming / Lesson material coming soon. An empty lesson has no completion action.
- School Admin assigns the purchased course to the intended teachers/students. A student's My Courses contains only their assignments; teacher materials and the school catalog remain staff-only.
- Check one school, teacher and student before a broad content release. Unpublish by setting the source chapter inactive and syncing again.

Content, payment providers and deployment remain separate. This handover does not require live charges or publishing to production.

`course-intake.csv` lists the 42 API courses captured during local setup. Fill in each content folder and review status. Its two prefilled local mappings reflect this local database only; confirm all IDs again with `roboquest:content-status` before importing in another environment. A third locally mapped test TTT course is not automatically substituted for an API program. Empty local IDs deliberately require review; never copy an API ID into a local-ID field without checking the actual course.
