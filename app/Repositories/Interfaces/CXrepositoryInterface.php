<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\JsonResponse;

interface CXrepositoryInterface
{
    public function CX(array $inputs): JsonResponse;
     public function getMedic(array $inputs): JsonResponse;
    public function medicAffiliation(array $inputs): JsonResponse;
    public function verifyAffiliation(string $fesa): bool;
    public function registerFesaCode(array $inputs): JsonResponse;
    public function getMedicaments(array $inputs): JsonResponse;
    public function verifyFESA(string $fesa): bool;
    public function magentoStore(array $inputs): JsonResponse;
    public function magentoUpdate(array $inputs): JsonResponse;
    public function getToken(array $inputs): JsonResponse;
    public function getUserByToken(string $token): JsonResponse;
}

