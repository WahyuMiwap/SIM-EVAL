<?php

namespace App\Services;

use App\Models\QuestionPackage;

class BankSoalService
{
    public function allPackages()
    {
        return QuestionPackage::withCount('questions')->with('creator')->orderByDesc('id')->get();
    }

    public function findPackage(int $id): ?QuestionPackage
    {
        return QuestionPackage::with(['questions', 'eventsPre', 'eventsPost'])->find($id);
    }
}
