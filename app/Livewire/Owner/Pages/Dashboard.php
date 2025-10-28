<?php

namespace App\Livewire\Owner\Pages;

use App\Models\Payment;
use App\Models\Request;
use App\Models\Property;
use App\Models\Expense;
use App\Livewire\Concerns\HasToast;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.owner', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    use HasToast;

    public string $filterPeriodIncome = 'monthly';
    public string $filterPeriodVacancy = 'monthly';

    /**
     * === Handle Income Chart Filter (monthly/yearly) ===
     */
    public function updatedFilterPeriodIncome(string $period)
    {
        $owner = auth()->user()->owner;
        $property = Property::find($owner->active_property);

        $chartData = $this->getIncomeExpenseData($property, $period);
        $this->dispatch('update-income-chart', data: $chartData);
    }

    /**
     * === Handle Vacancy Chart Filter (monthly/yearly) ===
     */
    public function updatedFilterPeriodVacancy(string $period)
    {
        $owner = auth()->user()->owner;
        $property = Property::find($owner->active_property);

        $chartData = $this->getVacancyData($property, $period);
        $this->dispatch('update-vacancy-chart', data: $chartData);
    }

    public function render()
    {
        $owner = auth()->user()->owner;
        $property = Property::find($owner->active_property);

        // === Recent Activity ===
        $recentPayments = Payment::with('tenant')
            ->where('status', 'paid')
            ->whereHas('lease.unit', fn($q) => $q->where('property_id', $property->id))
            ->latest()
            ->take(5)
            ->get();

        $recentRequests = Request::whereHas('unit', fn($q) => $q->where('property_id', $property->id))
            ->latest()
            ->take(5)
            ->get();

        // === Statistics ===
        $totalIncome = $property->units
            ->flatMap->leases
            ->flatMap->payments
            ->where('status', 'paid')
            ->sum('amount');
        $totalExpenses = $property->expenses
            ->sum('amount');

        $totalRevenue = $totalIncome - $totalExpenses;
        $isRevenueHigher = $totalRevenue > $totalExpenses;
        $totalUnits = $property->units()->count();
        $occupiedUnits = $property->units()->where('status', 'occupied')->count();
        $maintenanceUnits = $property->units()->where('status', 'maintenance')->count();
        $vacantUnits = $property->units()->where('status', 'vacant')->count();

        $vacancyChart = [
            'labels' => ['Occupied', 'Maintenance', 'Vacant'],
            'series' => [$occupiedUnits, $maintenanceUnits, $vacantUnits],
        ];

        // === Payment-based Occupancy Rate ===
        $unitsWithPayments = $property->units()
            ->whereHas('leases.payments', fn($q) => $q->where('status', 'paid'))
            ->count();

        $paymentOccupancyRate = $totalUnits > 0
            ? round(($unitsWithPayments / $totalUnits) * 100, 2)
            : 0;

        // === Monthly Income Data ===
        $monthlyIncome = Payment::selectRaw('MONTH(payment_date) as month, SUM(amount) as total')
            ->where('status', 'paid')
            ->whereHas('lease.unit', fn($q) => $q->where('property_id', $property->id))
            ->whereYear('payment_date', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        //  === Monthly Expenses Data ===
        $monthlyExpenses = Expense::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->whereHas('property', fn($q) => $q->where('id', $property->id))
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Prepare data aligned with months (Jan–Dec)
        $incomeData = [];
        for ($m = 1; $m <= 12; $m++) {
            $incomeData[] = $monthlyIncome[$m] ?? 0;
        }

        $expenseData = [];
        for ($m = 1; $m <= 12; $m++) {
            $expenseData[] = $monthlyExpenses[$m] ?? 0;
        }

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $expenseChartData = [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => $incomeData,
                ],
                [
                    'label' => 'Expenses',
                    'data' => $expenseData,
                ],
            ],
        ];


        return view('livewire.owner.pages.dashboard', compact(
            'recentPayments',
            'recentRequests',
            'totalIncome',
            'totalExpenses',
            'totalUnits',
            'totalRevenue',
            'isRevenueHigher',
            'vacancyChart',
            'paymentOccupancyRate',
            'maintenanceUnits',
            'expenseChartData'
        ));
    }


    /**
     * === Helper: Monthly or Yearly Data Grouping ===
     */
    protected function getIncomeExpenseData(Property $property, string $period): array
    {
        $now = now();

        // INCOME
        $incomeQuery = Payment::where('status', 'paid')
            ->whereHas('lease.unit', fn($q) => $q->where('property_id', $property->id));

        // EXPENSE
        $expenseQuery = Expense::whereHas('property', fn($q) => $q->where('id', $property->id));

        if ($period === 'yearly') {
            // === Group by year ===
            $income = $incomeQuery
                ->selectRaw('YEAR(payment_date) as year, SUM(amount) as total')
                ->groupBy('year')
                ->pluck('total', 'year')
                ->toArray();

            $expenses = $expenseQuery
                ->selectRaw('YEAR(created_at) as year, SUM(amount) as total')
                ->groupBy('year')
                ->pluck('total', 'year')
                ->toArray();

            $labels = array_keys($income + $expenses);
        } else {
            // === Group by month (current year) ===
            $income = $incomeQuery
                ->whereYear('payment_date', $now->year)
                ->selectRaw('MONTH(payment_date) as month, SUM(amount) as total')
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $expenses = $expenseQuery
                ->whereYear('created_at', $now->year)
                ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        }

        // Align data by label order
        $incomeData = [];
        $expenseData = [];

        if ($period === 'yearly') {
            foreach ($labels as $year) {
                $incomeData[] = $income[$year] ?? 0;
                $expenseData[] = $expenses[$year] ?? 0;
            }
        } else {
            for ($i = 1; $i <= 12; $i++) {
                $incomeData[] = $income[$i] ?? 0;
                $expenseData[] = $expenses[$i] ?? 0;
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                ['label' => 'Income', 'data' => $incomeData],
                ['label' => 'Expenses', 'data' => $expenseData],
            ],
        ];
    }

    /**
     * === Helper: Vacancy chart data ===
     */
    protected function getVacancyData(Property $property, string $period): array
    {
        $now = now();

        if ($period === 'yearly') {
            $labels = range($now->year - 4, $now->year); // Last 5 years (for trend)
            $series = [];

            foreach ($labels as $year) {
                $occupied = $property->units()->where('status', 'occupied')->count();
                $maintenance = $property->units()->where('status', 'maintenance')->count();
                $vacant = $property->units()->where('status', 'vacant')->count();

                $series[$year] = [$occupied, $maintenance, $vacant];
            }

            return [
                'labels' => ['Occupied', 'Maintenance', 'Vacant'],
                'datasets' => $series,
            ];
        }

        // Monthly (current snapshot only)
        $occupied = $property->units()->where('status', 'occupied')->count();
        $maintenance = $property->units()->where('status', 'maintenance')->count();
        $vacant = $property->units()->where('status', 'vacant')->count();

        return [
            'labels' => ['Occupied', 'Maintenance', 'Vacant'],
            'series' => [$occupied, $maintenance, $vacant],
        ];
    }


}
