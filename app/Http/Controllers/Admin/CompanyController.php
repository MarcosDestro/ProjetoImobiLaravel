<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompanyRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::all();
        
        return view('admin.companies.index',[
            'companies' => $companies
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $users = User::orderBy('name')->get();

        // Se foi informado um parâmetro pela url...
        if(!empty($request->user)){
            // Procura no banco
            $selected = User::where('id', $request->user)->first();
        } else {
            // Seta como null
            $selected = null;
        }

        return view('admin.companies.create', [
            'users' => $users,
            'selected' => $selected
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyRequest $request)
    {
        $createCompany = Company::create($request->all());

        /** Modo de teste */
        // $createCompany = new Company();
        // $createCompany->fill($request->all());

        // Redireciona com mensagem
        return redirect()->route('admin.companies.edit', ['company' => $createCompany->id])->with([
            'color' => 'green',
            'message' => 'Empresa cadastrada com sucesso!'
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
        $company = Company::find($id);
        $users = User::orderBy('name')->get();
        return view('admin.companies.edit', [
            'company' => $company,
            'users' => $users
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyRequest $request, $id)
    {
        // Encontra o registro pelo id
        $company = Company::find($id);
        // Preenche os dados com os valores do request
        $company->fill($request->all());
        $company->save();

        // Redireciona com mensagem
        return redirect()->route('admin.companies.edit', ['company' => $company->id])->with([
            'color' => 'green',
            'message' => 'Empresa atualizada com sucesso!'
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
}
