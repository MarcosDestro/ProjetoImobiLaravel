<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\Web\Contact;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WebController extends Controller
{
    public function home()
    {   
        // Traga 3 imóveis que estão a venda e disponíveis
        $propertiesForSale = Property::sale()->available()->limit(3)->get();
        $propertiesForRent = Property::rent()->available()->limit(3)->get();

        $head =  $this->seo->render(
            env('APP_NAME'). ' - UpInside Treinamentos',
            'Encontre o imóvel dos seus sonhos junto com a gente',
            route('web.home'),
            asset('frontend/assets/images/share.jpg')
        );
    
        return view('web.home',[
            'head' => $head,
            'propertiesForSale' => $propertiesForSale,
            'propertiesForRent' => $propertiesForRent
        ]);
    }

    public function spotlight()
    {
        $head =  $this->seo->render(
            env('APP_NAME'). ' - UpInside Treinamentos',
            'Encontre o imóvel dos seus sonhos junto com a gente',
            route('web.spotlight'),
            asset('frontend/assets/images/share.jpg')
        );
        return view('web.spotlight', [
            'head' => $head,
        ]);
    }

    public function rent()
    {   
        // limpa os dados de pesquisa da sessão
        (new FilterController())->clearAllData();
        $properties = Property::rent()->available()->get();

        $head =  $this->seo->render(
            env('APP_NAME'). ' - UpInside Treinamentos',
            'Alugue o imóvel dos seus sonhos junto com a gente',
            route('web.rent'),
            asset('frontend/assets/images/share.jpg')
        );

        return view('web.filter', [
            'head' => $head,
            'properties' => $properties,
            'type' => 'rent'
        ]);
    }

    public function rentProperty(Request $request)
    {   
        $property = Property::where('slug', $request->slug)->first();

        /** Aumentando as visualizações */
        $property->increment('views');
        // $property->views = $property->views + 1;
        $property->save();

        $head =  $this->seo->render(
            env('APP_NAME'). ' - UpInside Treinamentos',
            $property->headline ?? $property->title,
            route('web.rentProperty', ['slug' => $property->slug]),
            $property->cover()
        );

        return view('web.property', [
            'head' => $head,
            'property' => $property,
            'type' => 'rent'
        ]);
    }

    public function buy()
    {   
        // limpa os dados de pesquisa da sessão
        (new FilterController())->clearAllData();
        $properties = Property::sale()->available()->get();

        $head =  $this->seo->render(
            env('APP_NAME'). ' - UpInside Treinamentos',
            'Compre o imóvel dos seus sonhos junto com a gente',
            route('web.buy'),
            asset('frontend/assets/images/share.jpg')
        );

        return view('web.filter', [
            'head' => $head,
            'properties' => $properties,
            'type' => 'sale'
        ]);
    }

    public function buyProperty(Request $request)
    {   
        $property = Property::where('slug', $request->slug)->first();

        /** Aumentando as visualizações */
        $property->increment('views');
        // $property->views = $property->views + 1;
        $property->save();

        $head =  $this->seo->render(
            env('APP_NAME'). ' - UpInside Treinamentos',
            $property->headline ?? $property->title,
            route('web.buyProperty', ['slug' => $property->slug]),
            $property->cover()
        );

        return view('web.property', [
            'head' => $head,
            'property' => $property,
            'type' => 'sale'
        ]);
    }

    public function filter()
    {   
        $filter = new FilterController();
        $itemProperties = $filter->createQuery('id');

        $head =  $this->seo->render(
            env('APP_NAME'). ' - UpInside Treinamentos',
            'Filtre o imóvel dos seus sonhos junto com a gente',
            route('web.filter'),
            asset('frontend/assets/images/share.jpg')
        );
  
        // Alimente um array com todos os ids resgatados
        foreach ($itemProperties as $property){
            $properties[] = $property->id;
        }

        // Faça uma pesquisa utilizando um array
        if(!empty($properties)){
            $properties = Property::whereIn('id', $properties)->get();
        } else {
            $properties = Property::all();

        }

        return view('web.filter',[
            'head' => $head,
            'properties' => $properties
        ]);
    }

    public function experience()
    {
        // limpa os dados de pesquisa da sessão
        (new FilterController())->clearAllData();

        $properties = Property::whereNotNull('experience')->available()->get();

        $head =  $this->seo->render(
            env('APP_NAME'). ' - UpInside Treinamentos',
            'Viva a experiência com o imóvel dos seus sonhos junto com a gente',
            route('web.experience'),
            asset('frontend/assets/images/share.jpg')
        );

        return view('web.filter',[
            'head' => $head,
            'properties' => $properties
        ]);
    }

    public function experienceCategory(Request $request)
    {
        // limpa os dados de pesquisa da sessão
        (new FilterController())->clearAllData();

        if($request->slug == 'cobertura'){
            $properties = Property::where('experience', 'Cobertura')->get();
            $head =  $this->seo->render(
                env('APP_NAME'). ' - UpInside Treinamentos',
                'Alugue o imóvel dos seus sonhos junto com a gente',
                route('web.experienceCategory', ['slug' => 'cobertura']),
                asset('frontend/assets/images/share.jpg')
            );

        } elseif($request->slug == 'alto-padrao') {
            $properties = Property::where('experience', 'Alto Padrão')->get();
            $head =  $this->seo->render(
                env('APP_NAME'). ' - UpInside Treinamentos',
                'Alugue o imóvel dos seus sonhos junto com a gente',
                route('web.experienceCategory', ['slug' => 'alto-padrao']),
                asset('frontend/assets/images/share.jpg')
            );

        } elseif($request->slug == 'de-frente-para-o-mar') {
            $properties = Property::where('experience', 'De Frente Para o Mar')->get();
        } elseif($request->slug == 'condominio-fechado') {
            $properties = Property::where('experience', 'Condomínio Fechado')->get();
        } elseif($request->slug == 'compacto') {
            $properties = Property::where('experience', 'Compacto')->get();
        } elseif($request->slug == 'lojas-e-salas') {
            $properties = Property::where('experience', 'Lojas e Salas')->get();
        } else {
            $properties = Property::whereNotNull('experience')->available()->get();
        }

        if(empty($head)){
            $head =  $this->seo->render(
                env('APP_NAME'). ' - UpInside Treinamentos',
                'Título genérico e padronizado',
                route('web.experience'),
                asset('frontend/assets/images/share.jpg')
            );
        }

        return view('web.filter',[
            'head' => $head,
            'properties' => $properties
        ]);
    }

    public function contact()
    {   
        $head =  $this->seo->render(
            env('APP_NAME'). ' - UpInside Treinamentos',
            'Entre em contato conosco',
            route('web.contact'),
            asset('frontend/assets/images/share.jpg')
        );
        return view('web.contact', [
            'head' => $head
        ]);
    }

    public function sendEmail(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required',
            'cell' => 'int',
            'message' => 'required'
        ];

        $request->validate($rules);

        // Monta os dados do email
        $data = [
            'reply_name' => $request->name,
            'reply_email' => $request->email,
            'cell' => $request->cell ?? 'Não informado',
            'message' => $request->message
        ];

        // Envia o email
        Mail::send( new Contact($data) );

        // Envia para a rota de sucesso de envio
        return redirect()->route('web.sendEmailSuccess');
    }

    public function sendEmailSuccess()
    {
        return view('web.contact_success');
    }

}
