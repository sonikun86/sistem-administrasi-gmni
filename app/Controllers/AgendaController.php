<?php
// Controllers/AgendaController.php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Letter;
use App\Models\Komisariat;
use App\Models\LetterCategory;
use App\Helpers\Auth;
use App\Helpers\View;

class AgendaController
{
    public function index(): void
    {
        Auth::requireAuth();

        $letterModel = new Letter();
        $komisariatModel = new Komisariat();
        $catModel = new LetterCategory();

        $direction = $_GET['direction'] ?? 'masuk';
        $year = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? '';
        $komisariatId = $_GET['komisariat_id'] ?? '';

        $filters = [
            'direction' => $direction,
        ];

        if (Auth::isDpk()) {
            $filters['komisariat_id'] = (string)Auth::dpkId();
        } elseif (!empty($komisariatId)) {
            $filters['komisariat_id'] = $komisariatId;
        }

        $letters = $letterModel->getFiltered($filters, 200);

        View::render('agenda.index', [
            'title' => 'Buku Agenda Surat - SIMAS GMNI UNISDA',
            'letters' => $letters,
            'direction' => $direction,
            'year' => $year,
            'month' => $month,
            'komisariats' => $komisariatModel->getActive(),
            'categories' => $catModel->getActive(),
            'user' => Auth::user()
        ]);
    }

    public function print(): void
    {
        Auth::requireAuth();

        $letterModel = new Letter();
        $direction = $_GET['direction'] ?? 'masuk';
        $komisariatId = $_GET['komisariat_id'] ?? '';

        $filters = [
            'direction' => $direction,
        ];

        if (Auth::isDpk()) {
            $filters['komisariat_id'] = (string)Auth::dpkId();
        } elseif (!empty($komisariatId)) {
            $filters['komisariat_id'] = $komisariatId;
        }

        $letters = $letterModel->getFiltered($filters, 500);

        // Render printable view without standard web layout
        View::render('agenda.print', [
            'title' => 'Cetak Buku Agenda Surat ' . ucfirst($direction),
            'letters' => $letters,
            'direction' => $direction,
            'user' => Auth::user(),
            'datePrinted' => date('d F Y')
        ], null);
    }
}

