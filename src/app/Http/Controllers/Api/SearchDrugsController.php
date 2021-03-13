<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Drug;
use App\Models\Substance;
use Illuminate\Http\Request;
use App\Http\Resources\Api\SearchDrugs\Absent\DrugCollection as AbsentCollection;
use App\Http\Resources\Api\SearchDrugs\Exact\DrugCollection as ExactCollection;
use App\Http\Resources\Api\SearchDrugs\Partial\DrugCollection as PartialCollection;
use Symfony\Component\CssSelector\Node\FunctionNode;

class SearchDrugsController extends Controller
{
    public function search(Request $request)
    {
        $per_page = 5;

        $substancesInput = $request->substances;

        if (count($substancesInput) < 2) {
            return response()->json([
                    'errors'         => [
                        'substances' => [
                            'не ленись, добавь веществ'
                        ]
                    ]
            ]);
        }

        $substancesArray = [];
        foreach($substancesInput as $substance) if (Substance::find($substance)->visible) array_push($substancesArray, (integer) $substance);
        if (count($substancesArray) < 2) return new AbsentCollection(Drug::where('id', 0)->paginate($per_page));

        $substancesCount = count($substancesArray);
        $substancesCollection = collect($substancesArray);
        $drugsID_bySubstance = $substancesCollection->map(function($substance) {
            return Substance::find($substance)->drugs->pluck('id')->toArray();
        });
        $drugsID_withSubstancesCount = array_count_values(array_merge(... $drugsID_bySubstance));

        $drugsExact_ID = array_keys(array_filter(
            $drugsID_withSubstancesCount, 
            function($drugSubsCount) use ($substancesCount) {return $drugSubsCount == $substancesCount;}
        ));
        $drugsExact = Drug::visible()->only($substancesCollection)->whereIn('id', $drugsExact_ID)->with('substances:id')->paginate($per_page);
        if ($drugsExact->count()) return new ExactCollection($drugsExact);


        $drugsPartial_ID = array_keys($drugsID_withSubstancesCount);
        $drugs = Drug::visible()->whereIn('id', $drugsPartial_ID)
            /**
             * упорядочить по числу нужных субстанций + приделать крад
             */
            ->with('substances:id')
            ->paginate($per_page);
        foreach ($drugs as $drug) {
            $drug->isset_substances = count(array_intersect($substancesArray, $drug->substances->pluck('id')->toArray()));
        }
        return new PartialCollection($drugs);

        return response()->json([]);
    }
}
