<?php
namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\TransactionReportStateProvider;

#[ApiResource(
    uriTemplate: '/transaction-reports',
    shortName: 'TransactionReport',
    operations: [
        new GetCollection()
    ],
    provider: TransactionReportStateProvider::class,
)]
class TransactionReport
{
}
