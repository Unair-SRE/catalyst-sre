# Team documents in Google Drive

## Participant workflow
1. Create an account and verify email.
2. Confirm the captain profile, team name/institution, and up to two additional members.
3. Create ONE Drive folder for the team. Add every participant KTM as a readable PDF/JPG/PNG.
4. Share Viewer access with `unair@sre.co.id` (override with `CATALYST_DOCUMENTS_REVIEWER_EMAIL`).
5. Paste a folder link such as `https://drive.google.com/drive/folders/ID` in Team Management.
6. Register for MCC and/or one main competition (BCC or BPC).
7. Add `Bukti_Bayar_MCC`, `Bukti_Bayar_BCC`, or `Bukti_Bayar_BPC` to the SAME folder.
8. On that competition's payment page, enter the sender name, acknowledge document completeness/sharing, and submit for review.

The application does not upload files, call Google APIs, or inspect Drive permissions. A saved link is not verification. Each competition requires its own payment confirmation. Team roster and folder link are locked after the first confirmation. Files and sharing permissions can still be corrected inside the same folder.

## Committee workflow
Use the Google account above to open the folder from Teams, Team Members, or Competition Payments in Filament. Check the captain/member names against every KTM and the competition-specific payment proof against the fee. Approve documents and payment together, or request correction with a required explanation. Participants can see that note, correct the folder contents, and resubmit. Approvals record reviewer and time; confirmation and folder changes are logged.

Changing a locked folder link requires admin assistance. Changing the link requeues ALL previously submitted Drive payments for that team and clears prior reviewers/timestamps. A participant can add a first folder to an existing locked legacy team. Folder access within the application is owner/admin only; Drive sharing determines access after redirect. Documents in Drive remain editable by their owner even after approval.

## Data and compatibility
- `teams.documents_drive_url`: single source of the team folder.
- `payments.documents_submitted_at`: participant confirmation, not proof that bytes were uploaded.
- `payments.review_note`: latest correction/review note.
- Legacy KTM and payment URL/file-ID columns are retained; member KTM columns become nullable.
- Historical ImageKit storage services/tests and private-file routes remain for compatibility. Active competition forms and admin review no longer call ImageKit.
- Summit Pass and Submission prototypes are outside this team-registration change. Summit backend storage remains separate.

## Deployment
Run `php artisan migrate` against the intended database after backup. Do NOT use migrate:fresh or seed to deploy this change. Build assets with `npm run build`. If configuration is cached, rebuild it after changing the reviewer email.

The migration is additive and preserves existing rows. Rollback removes the new Drive metadata and deliberately leaves legacy member KTM columns nullable to avoid invalidating Drive-only members.

## Validation
Run tests with an isolated database. For PowerShell/SQLite:
```powershell
$env:DB_CONNECTION = 'sqlite'
$env:DB_DATABASE = ':memory:'
$env:DB_URL = ''
$env:APP_CONFIG_CACHE = 'bootstrap/cache/drive-test-config.php'
php vendor/bin/pest
```
