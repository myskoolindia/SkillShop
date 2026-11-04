<?php
namespace App\Http\Controllers\Frontend\Concerns;

use App\Models\SchoolMember;
use App\Services\SchoolMemberProvisioner;
use Illuminate\Http\{RedirectResponse, Request};
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Shared add / import / status logic for school teachers and students.
 *
 * Login details for new accounts are shown once: in the confirmation message for a single add,
 * or as a one-time CSV download after an import. They are kept only in the school user's session.
 */
trait ManagesSchoolMembers
{
    private static int $maxImportRows = 2000;

    abstract protected function memberRole(): string;

    /** Session key for the one-time login details; separate per role so each list downloads from its own page. */
    public static function credentialsKey(string $role): string
    {
        return 'school_member_credentials.' . $role;
    }

    protected function addMember(Request $request, array $fields = []): RedirectResponse
    {
        $role = $this->memberRole();
        $result = app(SchoolMemberProvisioner::class)->add(userAuth()->id, $role, (string) $request->name, (string) $request->email, $request->password, $fields);
        if (!$result['added']) return redirect()->back()->withInput($request->except('password'))->with(['messege' => $result['error'], 'alert-type' => 'error']);
        $label = $role === 'teacher' ? __('Teacher') : __('Student');
        $message = !$result['created']
            ? __(':label added. They already had an account, so their current password is unchanged.', ['label' => $label])
            : __(':label added.', ['label' => $label]);
        // Shown once on the next page in a panel that stays until dismissed, not in a disappearing toast.
        $new = $result['password'] === null ? null : ['name' => $result['user']->name, 'email' => $result['user']->email, 'password' => $result['password']];
        return redirect()->route("school.{$role}s.index")->with(['messege' => $message, 'alert-type' => 'success', 'new_member_login' => $new]);
    }

    /**
     * @param callable $parse fn(array $row): array{name, email, password, fields}
     */
    protected function importMembers(Request $request, callable $parse): RedirectResponse
    {
        $request->validate(['csv_file' => 'required|file|max:5120'], ['csv_file.required' => __('Please select a CSV file to upload.')]);
        $role = $this->memberRole();
        $file = $request->file('csv_file');
        $back = fn(string $message) => redirect()->back()->with(['messege' => $message, 'alert-type' => 'error']);
        if (!in_array(strtolower($file->getClientOriginalExtension()), ['csv', 'txt'], true)) return $back(__('Please upload a valid .csv file.'));
        $content = (string) file_get_contents($file->getPathname());
        $firstLine = (string) strtok($content, "\r\n");
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) return $back(__('Unable to read the uploaded CSV file.'));
        fgetcsv($handle, 0, $delimiter); // header row

        $provisioner = app(SchoolMemberProvisioner::class);
        $added = 0; $problems = []; $credentials = []; $line = 1;
        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $line++;
                if (empty(array_filter($row, fn($cell) => trim((string) $cell) !== ''))) continue;
                if ($line - 1 > self::$maxImportRows) { $problems[] = ['line' => $line, 'email' => '', 'reason' => __('Only the first :max rows are imported. Upload the rest in another file.', ['max' => self::$maxImportRows])]; break; }
                $values = $parse(array_map(fn($cell) => trim((string) $cell), $row));
                $values['name'] = preg_replace('/^\xEF\xBB\xBF/', '', $values['name']);
                $result = $provisioner->add(userAuth()->id, $role, $values['name'], $values['email'], $values['password'], $values['fields']);
                if (!$result['added']) { $problems[] = ['line' => $line, 'email' => $values['email'], 'reason' => $result['error']]; continue; }
                $added++;
                if ($result['password'] !== null) $credentials[] = ['name' => $result['user']->name, 'email' => $result['user']->email, 'password' => $result['password']];
            }
        } finally {
            fclose($handle);
        }

        // Replace any undownloaded details from an earlier import: the newest list is the one the school expects.
        if ($credentials) session()->put(self::credentialsKey($role), ['role' => $role, 'rows' => $credentials, 'createdAt' => now()->toIso8601String()]);
        $message = __('Import complete: :added added, :skipped skipped.', ['added' => $added, 'skipped' => count($problems)]);
        return redirect()->route("school.{$role}s.index")->with(['messege' => $message, 'alert-type' => $problems ? 'warning' : 'success', 'import_problems' => array_slice($problems, 0, 200)]);
    }

    /** One-time download of passwords for accounts created by the last import. */
    public function downloadCredentials(): StreamedResponse|RedirectResponse
    {
        $data = session()->pull(self::credentialsKey($this->memberRole()));
        if (!$data) return redirect()->route('school.' . $this->memberRole() . 's.index')->with(['messege' => __('These login details were already downloaded.'), 'alert-type' => 'error']);
        $filename = $data['role'] . 's-login-details-' . now()->format('Y-m-d-His') . '.csv';
        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Name', 'Email', 'Password']);
            // Prevent spreadsheet formula execution from user-supplied values.
            foreach ($data['rows'] as $row) fputcsv($out, array_map(fn($value) => preg_match('/^[=+\-@\t\r]/', (string) $value) ? "'" . $value : $value, [$row['name'], $row['email'], $row['password']]));
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv', 'Cache-Control' => 'no-store, private']);
    }

    protected function toggleMemberStatus(SchoolMember $member): RedirectResponse
    {
        $role = $this->memberRole();
        abort_unless((int) $member->school_id === (int) userAuth()->id && $member->role_in_school === $role, 403);
        if ($member->status !== 'active' && !app(SchoolMemberProvisioner::class)->canActivate($member)) {
            return redirect()->back()->with(['messege' => __('This :role is already active in another school.', ['role' => $role]), 'alert-type' => 'error']);
        }
        $member->update(['status' => $member->status === 'active' ? 'inactive' : 'active']);
        return redirect()->back()->with(['messege' => $role === 'teacher' ? __('Teacher status updated.') : __('Student status updated.'), 'alert-type' => 'success']);
    }

    protected function csvTemplate(array $columns, array $sample, string $filename)
    {
        return response()->streamDownload(function () use ($columns, $sample) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, $sample);
            fclose($file);
        }, $filename, ['Content-Type' => 'text/csv', 'Cache-Control' => 'no-store']);
    }
}
