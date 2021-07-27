<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Support\Cropper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index',[
            'users' => $users
        ]);
    }

    public function team()
    {
        $users = User::where('admin', 1)->get();
        return view('admin.users.team',[
            'users' => $users
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $userCreate = User::create($request->all());

        /** Analisa novamente se foi enviado um arquivo de foto */
        if(!empty($request->file('cover'))){
            /** Caso sim, salva a nova imagem na pasta e no banco*/
            $userCreate->cover = $request->file('cover')->storeAs('user', Str::slug($request->name) . '-' . str_replace('.', '', microtime(true)) . '.' . $request->file('cover')->extension());
            $userCreate->save();
        }

        /** Modo de teste e visualiação */
        // $userCreate->fill($request->all());
        // var_dump($userCreate->getAttributes())
        // $userCreate->save();

        if(!$userCreate->save()){
            return redirect()->back()->withInput()->withError();
        }

        return redirect()->route('admin.users.edit', ['user' => $userCreate->id])->with([
            'color' => 'green',
            'message' => 'Cliente atualizado com sucesso!'
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
        $user = User::find($id);

        // var_dump(
        //     $user->document,
        //     $user->spouse_document,
        //     $user->date_of_birth,
        //     $user->spouse_date_of_birth,
        //     $user->income,
        //     $user->spouse_income,
        //     $user->getAttributes()
        // );

        /** Pode ser assim: */
        // $user = User::where('id', $id)->first();

        return view('admin.users.edit',[
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        /** Implementar validações do id recebido */

        /** Pesquisa o usuário */
        $user = User::find($id);

        /** Métodos para campos checkbox */
        $user->setLessorAttribute($request->lessor);
        $user->setLesseeAttribute($request->lessee);
        $user->setAdminAttribute($request->admin);
        $user->setClientAttribute($request->client);

        /** Analisa caso tenha informado uma imagem */
        if(!empty($request->file('cover'))){
            /** Caso sim, apaga a antiga e seta como vazio*/
            Storage::delete($user->cover);
            /** Limpa o cache da imagem */
            Cropper::flush($user->cover); 
            $user->cover = '';
        }

        /** Passar os dados da requisição para o modelo */
        $user->fill($request->all());

        /** Analisa novamente se foi enviado um arquivo de foto */
        if(!empty($request->file('cover'))){
            /** Caso sim, salva a nova imagem na pasta e no banco*/
            // $user->cover = $request->file('cover')->store('user'); // Dessa forma o cropper se perde (nome aleatório)
            $user->cover = $request->file('cover')->storeAs('user', Str::slug($request->name) . '-' . str_replace('.', '', microtime(true)) . '.' . $request->file('cover')->extension());
        }

        /** Atualiza o registro */
        if(!$user->save()){
            return redirect()->back()->withInput()->withError();
        }

        return redirect()->route('admin.users.edit', ['user' => $user->id])->with([
            'color' => 'green',
            'message' => 'Cliente atualizado com sucesso!'
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
