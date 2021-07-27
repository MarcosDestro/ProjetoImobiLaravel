<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        /** Verifica se existe uma sessão ativa, caso sim redirecione */
        if(Auth::check() === true){
            return redirect()->route('admin.home');
        }

        return view('admin.index');
    }

    public function home()
    {
        $lessors = User::lessors()->count();
        $lessees = User::lessees()->count();
        $team = User::where('admin', 1)->count();

        $propertiesAvailable = Property::available()->count();
        $propertiesUnavailable = Property::unavailable()->count();
        $propertiesTotal = Property::all()->count();

        $properties = Property::orderBy('id', 'DESC')->limit(3)->get();

        $contractsPending = Contract::pending()->count();
        $contractsActive = Contract::active()->count();
        $contractsCanceled = Contract::canceled()->count();
        $contractsTotal = Contract::all()->count();

        $contracts = Contract::orderBy('id', 'DESC')->limit(10)->get();

        return view('admin.dashboard',[
            'lessors' => $lessors,
            'lessees' => $lessees,
            'team' => $team,
            'propertiesAvailable' => $propertiesAvailable,
            'propertiesUnavailable' => $propertiesUnavailable,
            'propertiesTotal' => $propertiesTotal,
            'properties' => $properties,
            'contractsPending' => $contractsPending,
            'contractsActive' => $contractsActive,
            'contractsCanceled' => $contractsCanceled,
            'contractsTotal' => $contractsTotal,
            'contracts' => $contracts
        ]);
    }

    public function login(Request $request)
    {
        // Se no array possuir qualquer dado em branco, dentre as posições...
        if(in_array('', $request->only('email', 'password'))){
            $json['message'] = $this->message->error("Ooops, informe todos os dados para efetuar o login")->render();
            return response()->json($json);
        }

        // Se o email for inválido...
        if(!filter_var($request->email, FILTER_VALIDATE_EMAIL)){
            $json['message'] = $this->message->error("Ooops, informe um e-mail válido")->render();
            return response()->json($json);
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        // Se as credenciais para login não forem válidas...
        if(!Auth::attempt($credentials)){
            $json['message'] = $this->message->error("Ooops, usuário e/ou senha não conferem")->render();
            return response()->json($json);
        }

        if(!$this->isAdmin()){
            Auth::logout();
            $json['message'] = $this->message->error("Ooops, usuário não tem permissão para acessar o painel de controle")->render();
            return response()->json($json);
        }

        // Salva o último login passando o ip do $request
        $this->authenticated($request->getClientIp());

        // Uma vez logado, podemos retornar a url de acesso via json
        $json['redirect'] = route('admin.home');
        return response()->json($json);
    }

    public function logout(Request $request)
    {   
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    private function authenticated(string $ip)
    {
        $user = User::where('id', Auth::user()->id);
        $user->update([
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' =>$ip
        ]);
    }

    private function isAdmin()
    {
        $user = User::where('id', Auth::user()->id)->first();
        if($user->admin === 1){
            return true;
        } else {
            return false;
        }
    }
}
