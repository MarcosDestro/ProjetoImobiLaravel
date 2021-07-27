<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContractRequest;
use App\Models\Contract;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contracts = Contract::orderBy('id', 'DESC')->get();
        
        //$contracts = Contract::with(['acquirerRelation', 'ownerRelation'])->orderBy('id', 'DESC')->get() ;

        return view('admin.contracts.index', [
            'contracts' => $contracts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lessors = User::lessors()->get();
        $lessees = User::lessees()->get();

        return view('admin.contracts.create',[
            'lessors' => $lessors,
            'lessees' => $lessees
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContractRequest $request)
    {
        $contractCreate = Contract::create($request->all());

        /** Modo de teste */
        // $contract = new Contract();
        // $contract->fill($request->all());
        // var_dump($contract->getAttributes());

        return redirect()->route('admin.contracts.edit', ['contract' => $contractCreate->id])->with([
            'color' => 'green',
            'message' => 'Contrato cadastrado com sucesso!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $contract = Contract::find($id);
        $lessors = User::lessors()->get();
        $lessees = User::lessees()->get();

        return view('admin.contracts.edit', [
            'contract' => $contract,
            'lessors' => $lessors,
            'lessees' => $lessees
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ContractRequest $request, $id)
    {
        $contract = Contract::where('id', $id)->first();
        $contract->fill($request->all());
        $contract->save();

        if($request->property){
            $property = Property::find($request->property);

            if($request->status == 'active'){
                $property->status = 0;
                $property->save();
            } else {
                $property->status = 1;
                $property->save();
            }
        }

        return redirect()->route('admin.contracts.edit', ['contract' => $contract->id])->with([
            'color' => 'green',
            'message' => 'Contrato alterado com sucesso!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getDataOwner(Request $request)
    {
        /** Traz o usuários com somente estes campos */
        $lessor = User::where('id', $request->id)->first([
            'id',
            'civil_status',
            'spouse_name',
            'spouse_document'
        ]);

        /** Objeto Spouse */
        if(!empty($lessor)){
            // Array de validação para conjuge
            $civilStatusSpouseRequired = [
                'married',
                'separated'
            ];

            /** Se na posição civil status possuir algum item do array... */
            if(in_array($lessor->civil_status, $civilStatusSpouseRequired)){
                /** Monta o objeto spouse */
                $spouse = [
                    'spouse_name' => $lessor->spouse_name,
                    'spouse_document' => $lessor->spouse_document,
                ];
            } else {
                /** Seta null */
                $spouse = null;
            }
            /** Objeto Companies */
            $companies = $lessor->companies()->get([
                'id',
                'alias_name',
                'document_company'
            ]);

            $getProperties = $lessor->properties()->get();

            $properties = [];
            foreach($getProperties as $property){
                $properties[] = [
                    'id' => $property->id,
                    'description' => '#'. $property->id . " " . $property->street . ", " .
                    $property->number . " " . $property->complement . " " . $property->neighborhood . " " . 
                    $property->city . "/" . $property->state . " (" . $property->zipcode . ")"
                ];
            }


        } else {
            /** Seta null */
            $spouse = null;
            $companies = null;
            $properties = null;
        }

        /** Monta as posições para envio */
        $json = [
            'spouse' => $spouse,
            'companies' => (!empty($companies) && $companies->count() ? $companies : null),
            'properties' => (!empty($properties) ? $properties : null)
        ];

        return response()->json($json);
    }

    public function getDataAcquirer(Request $request)
    {
        /** Traz o usuários com somente estes campos */
        $lessee = User::where('id', $request->id)->first([
            'id',
            'civil_status',
            'spouse_name',
            'spouse_document'
        ]);

        /** Objeto Spouse */
        if(!empty($lessee)){
            // Array de validação para conjuge
            $civilStatusSpouseRequired = [
                'married',
                'separated'
            ];

            /** Se na posição civil status possuir algum item do array... */
            if(in_array($lessee->civil_status, $civilStatusSpouseRequired)){
                /** Monta o objeto spouse */
                $spouse = [
                    'spouse_name' => $lessee->spouse_name,
                    'spouse_document' => $lessee->spouse_document,
                ];
            } else {
                /** Seta null */
                $spouse = null;
            }

            /** Objeto Companies */
            $companies = $lessee->companies()->get([
                'id',
                'alias_name',
                'document_company'
            ]);
        } else {
            /** Seta null */
            $spouse = null;
            $companies = null;
        }

        

        /** Monta as posições para envio */
        $json = [
            'spouse' => $spouse,
            'companies' => (!empty($companies) && $companies->count() ? $companies : null)
        ];

        return response()->json($json);
    }

    public function getDataProperty(Request $request)
    {
        $property = Property::where('id', $request->id)->first();

        if(empty($property)){
            $property = null;
        } else {
            $property = [
                'id' => $property->id,
                'sale_price' => $property->sale_price,
                'rent_price' => $property->rent_price,
                'tribute' => $property->tribute,
                'condominium' => $property->condominium
            ];
        }

        $json = [
            'property' => $property
        ];
        
        return response()->json($json);
    }
}
