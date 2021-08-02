<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Entities\Plan;

/**
 * @group Plan
 */
class PlanController extends Controller
{
    /**
     * Show Plan list
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plans = Plan::all();

        return response()->json($plans, 200);
    }

    /**
     * Srote Plan Item
     *
     * @bodyParam name int required The name is plan name.Example: Plan_A
     * @bodyParam amount int required The amount is plan amount.Example: 300
     * @bodyParam valid_date required int The valid_date is plan valid date.Example: 30
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:45',
            'amount' => 'required|integer',
            'valid_date' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->only([
            'name',
            'amount',
            'valid_date'
        ]);

        $plan = Plan::create($data);

        return response()->json($plan, 200);
    }

    /**
     * Show Plan Item
     *
     * @urlParam plan int The plan is plan id.Example: 1
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Plan $plan)
    {
        if (!$plan) {
            return response()->json(["message" => "No query results for this plas"], 404);
        }

        return response()->json($plan, 200);
    }

    /**
     * Update Plan Item
     * 
     * @urlParam plan int required The plan is plan id.Example: 1
     * @bodyParam name int The name is plan name.Example: Plan_A
     * @bodyParam amount int The amount is plan amount.Example: 300
     * @bodyParam valid_date int The valid_date is plan valid date.Example: 30
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Plan $plan)
    {
        if (!$plan) {
            return response()->json(["message" => "No query results for this plas"], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:45',
            'amount' => 'sometimes|required|integer',
            'valid_date' => 'sometimes|required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->only([
            'name',
            'amount',
            'valid_date'
        ]);

        $plan->update($data);
        $plan->refresh();

        return response()->json($plan, 200);
    }

    /**
     * Delete Plan Item
     * 
     * @urlParam plan int required The plan is plan id.Example: 1
     *
     * @param  int  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plan $plan)
    {
        if (!$plan) {
            return response()->json(["message" => "No query results for this plas"], 404);
        }

        $plan->delete();

        return response()->json(["message" => "Success"], 200);
    }
}
