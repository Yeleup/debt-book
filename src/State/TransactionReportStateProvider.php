<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\ExpenseReport;
use App\ApiResource\TransactionReport;
use App\Entity\Market;
use App\Repository\MarketRepository;
use App\Repository\PaymentRepository;
use App\Repository\TransactionRepository;
use App\Repository\TypeRepository;
use App\Service\MoneyFormatter;
use Symfony\Bundle\SecurityBundle\Security;

class TransactionReportStateProvider implements ProviderInterface
{
    public function __construct(
        protected MarketRepository $marketRepository,
        protected TypeRepository $typeRepository,
        protected PaymentRepository $paymentRepository,
        protected MoneyFormatter $moneyFormatter,
        protected Security $security)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $markets = $this->marketRepository->findMarketsForUser($this->security->getUser());

        $data = [];
        /** @var Market $market */
        foreach ($markets as $market) {
            $startDate = $context['filter']['startDate'] ?? null;
            $endDate = $context['filter']['endDate'] ?? null;

            $typesWithTransactionsSum = $this->typeRepository->findTypesWithTransactionsSum($market, $startDate, $endDate);

            $statisticTypes = [];
            $amount = 0;
            foreach ($typesWithTransactionsSum as $typeWithTransactionsSum) {
                $type = $this->typeRepository->find($typeWithTransactionsSum['id']);
                $payments = $this->paymentRepository->findPaymentsWithTransactionsSum($type, $market, $startDate, $endDate);

                $statisticPayments = [];
                foreach ($payments as $payment) {
                    $statisticPayments[] = [
                        'id' => $payment['id'],
                        'title' => $payment['title'],
                        'amount' => $this->moneyFormatter->format($payment['amount']),
                    ];
                }

                $amount += $typeWithTransactionsSum['amount'];

                $statisticTypes[] = [
                    'id' => $typeWithTransactionsSum['id'],
                    'title' => $typeWithTransactionsSum['title'],
                    'amount' => $this->moneyFormatter->format($typeWithTransactionsSum['amount']),
                    'payments' => $statisticPayments,
                ];
            }

            if ($statisticTypes) {
                $data[] = [
                    'id' => $market->getId(),
                    'title' => $market->getTitle(),
                    'amount' => $this->moneyFormatter->format($amount),
                    'types' => $statisticTypes,
                ];
            }
        }

        return $data;
    }
}
