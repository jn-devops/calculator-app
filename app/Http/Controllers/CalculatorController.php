<?php

namespace App\Http\Controllers;

use Homeful\Mortgage\Data\MortgageData;
use Homeful\Common\Classes\Input;
use Homeful\Borrower\Borrower;
use Homeful\Mortgage\Mortgage;
use Homeful\Property\Property;
use Illuminate\Http\Request;
use Whitecube\Price\Price;
use Brick\Money\Money;

class CalculatorController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $property = (new Property)
            ->setTotalContractPrice(new Price(Money::of($tcp = 4500000, 'PHP')))
            ->setAppraisedValue(new Price(Money::of($tcp, 'PHP')));
        $borrower = (new Borrower)
            ->setRegional(false)
            ->setGrossMonthlyIncome(50000);
        $inputs = $request->all();
        $params = [
            Input::WAGES => $inputs['wages'],
            Input::TCP => $inputs['tcp'],
            Input::PERCENT_DP => $inputs['percent_dp'],
            Input::DP_TERM => $inputs['dp_term'],
            Input::BP_INTEREST_RATE => $inputs['bp_interest_rate'],
            Input::PERCENT_MF => $inputs['percent_mf'],
            Input::LOW_CASH_OUT => $inputs['low_cash_out'],
            Input::BP_TERM => $inputs['bp_term'],
            ];

        $mortgage = Mortgage::createWithTypicalBorrower($property, $params);
        $data = MortgageData::fromObject($mortgage);

        return back()->with('event', [
            'name' => 'loan.calculated',
            'data' => $data,
        ]);
    }
}
