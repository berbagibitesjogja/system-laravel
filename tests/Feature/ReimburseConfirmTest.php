<?php

use App\Http\Controllers\ReimburseController;
use App\Models\Volunteer\Division;
use App\Models\Volunteer\Reimburse;
use App\Models\Volunteer\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Vite;
use Illuminate\Validation\ValidationException;

function makeManagerUser(string $role = 'core', string $divisionName = 'Bendahara'): User
{
    $user = (new User())->forceFill([
        'id' => 1,
        'role' => $role,
        'name' => 'Bendahara BBJ',
        'email' => 'bendahara@bbj.org',
        'phone' => '081234567890',
    ]);
    $user->setRelation('division', (new Division())->forceFill(['name' => $divisionName]));

    return $user;
}

function makeApplicantUser(): User
{
    $user = (new User())->forceFill([
        'id' => 2,
        'role' => 'member',
        'name' => 'Relawan Uji',
        'email' => 'relawan@bbj.org',
        'phone' => '089876543210',
    ]);
    $user->setRelation('division', (new Division())->forceFill(['name' => 'Food']));

    return $user;
}

test('volunteer migration defines payment and notes columns in expected order on reimburses table', function () {
    $migrationSource = file_get_contents(database_path('migrations/2025_09_02_212250_volunteer.php'));
    $reimburseStart = strpos($migrationSource, "Schema::create('reimburses'");
    expect($reimburseStart)->not->toBeFalse();

    $reimburseBlock = substr($migrationSource, $reimburseStart);

    $filePos = strpos($reimburseBlock, "\$table->string('file');");
    $paymentPos = strpos($reimburseBlock, "\$table->string('payment')->nullable();");
    $userPos = strpos($reimburseBlock, "\$table->foreignIdFor(User::class)->constrained()->onDelete('cascade');");
    $notesPos = strpos($reimburseBlock, "\$table->string('notes')->nullable();");

    expect($filePos)->not->toBeFalse()
        ->and($paymentPos)->not->toBeFalse()
        ->and($userPos)->not->toBeFalse()
        ->and($notesPos)->not->toBeFalse()
        ->and($paymentPos)->toBeGreaterThan($filePos)
        ->and($userPos)->toBeGreaterThan($paymentPos)
        ->and($notesPos)->toBeGreaterThan($userPos);
});

test('update requires transfer proof file and rejects empty submission', function () {
    Storage::fake('public');
    Auth::shouldReceive('user')->andReturn(makeManagerUser());

    $reimburse = Mockery::mock(Reimburse::class)->makePartial();
    $reimburse->done = false;
    $reimburse->shouldNotReceive('update');

    $request = Request::create('/reimburse/10', 'PATCH');
    $controller = new ReimburseController();

    expect(fn () => $controller->update($request, $reimburse))
        ->toThrow(ValidationException::class);
});

test('update rejects invalid file mime type for payment proof', function () {
    Storage::fake('public');
    Auth::shouldReceive('user')->andReturn(makeManagerUser());

    $reimburse = Mockery::mock(Reimburse::class)->makePartial();
    $reimburse->done = false;
    $reimburse->shouldNotReceive('update');

    $invalidFile = UploadedFile::fake()->create('catatan.txt', 20, 'text/plain');
    $request = Request::create('/reimburse/10', 'PATCH', [], [], ['payment' => $invalidFile]);
    $controller = new ReimburseController();

    expect(fn () => $controller->update($request, $reimburse))
        ->toThrow(ValidationException::class);

    expect(Storage::disk('public')->allFiles('payment'))->toBeEmpty();
});

test('update stores image transfer proof and marks reimburse done', function () {
    Storage::fake('public');
    Auth::shouldReceive('user')->andReturn(makeManagerUser());

    $updatedPayload = null;
    $reimburse = Mockery::mock(Reimburse::class)->makePartial();
    $reimburse->id = 10;
    $reimburse->amount = 75000;
    $reimburse->done = false;
    $reimburse->payment = null;
    $reimburse->setRelation('user', makeApplicantUser());
    $reimburse->shouldReceive('update')
        ->once()
        ->andReturnUsing(function (array $attributes) use (&$updatedPayload) {
            $updatedPayload = $attributes;
            return true;
        });

    $proofImage = UploadedFile::fake()->create('bukti-transfer.jpg', 150, 'image/jpeg');
    $request = Request::create('/reimburse/10', 'PATCH', [], [], ['payment' => $proofImage]);

    $controller = new class extends ReimburseController {
        public static function send($target, $message, $media = null)
        {
            // No-op in unit/feature test
        }
    };

    $response = $controller->update($request, $reimburse);

    expect($updatedPayload)->not->toBeNull()
        ->and($updatedPayload['done'])->toBeTrue()
        ->and($updatedPayload['payment'])->toStartWith('payment/')
        ->and($response->getSession()->get('success'))->toBe('Reimbursement berhasil dikonfirmasi!');

    Storage::disk('public')->assertExists($updatedPayload['payment']);
});

test('update also accepts pdf transfer proof', function () {
    Storage::fake('public');
    Auth::shouldReceive('user')->andReturn(makeManagerUser('super', 'IT'));

    $updatedPayload = null;
    $reimburse = Mockery::mock(Reimburse::class)->makePartial();
    $reimburse->id = 11;
    $reimburse->amount = 120000;
    $reimburse->done = false;
    $reimburse->payment = null;
    $reimburse->setRelation('user', makeApplicantUser());
    $reimburse->shouldReceive('update')
        ->once()
        ->andReturnUsing(function (array $attributes) use (&$updatedPayload) {
            $updatedPayload = $attributes;
            return true;
        });

    $proofPdf = UploadedFile::fake()->create('struk-bank.pdf', 200, 'application/pdf');
    $request = Request::create('/reimburse/11', 'PATCH', [], [], ['payment' => $proofPdf]);

    $controller = new class extends ReimburseController {
        public static function send($target, $message, $media = null)
        {
        }
    };

    $controller->update($request, $reimburse);

    expect($updatedPayload['done'])->toBeTrue()
        ->and($updatedPayload['payment'])->toEndWith('.pdf');
    Storage::disk('public')->assertExists($updatedPayload['payment']);
});

test('unauthorized regular member cannot confirm reimburse', function () {
    Storage::fake('public');
    Auth::shouldReceive('user')->andReturn(makeApplicantUser());

    $reimburse = Mockery::mock(Reimburse::class)->makePartial();
    $reimburse->done = false;
    $reimburse->shouldNotReceive('update');

    $proofImage = UploadedFile::fake()->create('bukti.jpg', 100, 'image/jpeg');
    $request = Request::create('/reimburse/10', 'PATCH', [], [], ['payment' => $proofImage]);

    $controller = new ReimburseController();
    $response = $controller->update($request, $reimburse);

    expect($response->getSession()->get('error'))->toBe('Anda tidak memiliki akses untuk tindakan ini.')
        ->and(Storage::disk('public')->allFiles('payment'))->toBeEmpty();
});

test('reimburse index view renders confirmation modal with mandatory payment file input', function () {
    $manager = makeManagerUser();
    $this->actingAs($manager);
    request()->setRouteResolver(fn () => app('router')->getRoutes()->getByName('reimburse.index'));

    $hotFile = storage_path('framework/testing/vite.hot');
    @mkdir(dirname($hotFile), 0777, true);
    file_put_contents($hotFile, 'http://localhost:5173');
    Vite::useHotFile($hotFile);

    $item = new Reimburse([
        'method' => 'BCA',
        'target' => '1234567890',
        'amount' => 50000,
        'done' => false,
        'file' => 'reimburse/invoice.jpg',
        'payment' => null,
        'notes' => 'Beli konsumsi aksi',
    ]);
    $item->id = 99;
    $item->setRelation('user', makeApplicantUser());

    $html = view('pages.reimburse.index', [
        'reimburse' => collect([$item]),
    ])->render();

    $completedItem = (new Reimburse())->forceFill([
        'id' => 100,
        'method' => 'GoPay',
        'target' => '08123456789',
        'amount' => 45000,
        'done' => true,
        'file' => 'reimburse/invoice2.jpg',
        'payment' => 'payment/bukti-tf.jpg',
        'notes' => 'Parkir aksi',
    ]);

    $createHtml = view('pages.reimburse.create', [
        'reimburse' => collect([$completedItem]),
    ])->render();

    @unlink($hotFile);

    expect($html)
        ->toContain('Konfirmasi Penyelesaian Reimburse')
        ->toContain('name="payment"')
        ->toContain('enctype="multipart/form-data"')
        ->toContain('required')
        ->toContain('openConfirm($el.dataset)');

    expect($createHtml)
        ->toContain('Bukti TF')
        ->toContain('storage/payment/bukti-tf.jpg');
});
