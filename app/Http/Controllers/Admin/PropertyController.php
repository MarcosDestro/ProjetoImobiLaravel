<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PropertyRequest;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use App\Support\Cropper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $properties = Property::orderBy('id', 'DESC')->get();
        return view('admin.properties.index',[
            'properties' => $properties
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $users = User::orderBy('name')->get();
        return view('admin.properties.create',[
            'users' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PropertyRequest $request)
    {
        $createProperty = Property::create($request->all());
        $createProperty->setSlug();

        /** Validação das imagens */
        $validateImages = Validator::make($request->only('files'), ['files.*' => 'image'] );
        
        if($validateImages->fails() == true){
            return redirect()->back()->withInput()->with([
                'color' => 'orange',
                'message' => 'Todas as imagens devem ser do tipo jpg, jpeg ou png'
            ]);
        }

        // Pegas todas as imagens enviadas
        if (!empty($request->allFiles())){
            // Abre o array no indice files
            foreach($request->allFiles()['files'] as $image){
                // Instancia um objeto e preenche
                $propertyImage = new PropertyImage();
                $propertyImage->property = $createProperty->id; // id do imóvel que está sendo salvo
                $propertyImage->path = $image->storeAs('properties/' . $createProperty->id, Str::slug($request->title) . '-' . str_replace('.', '', microtime(true)) . '.' . $image->extension());
                $propertyImage->save();
                unset($propertyImage);
            }
        }

        /** Modo de teste */
        // $property = new Property();
        // $property->fill($request->all());
        // var_dump($property->getAttributes());

        return redirect()->route('admin.properties.edit', ['property' => $createProperty->id])->with([
            'color' => 'green',
            'message' => 'Imóvel criado com sucesso!'
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
        $property = Property::find($id);
        $users = User::orderBy('name')->get();

        /** Modo de teste */
        // var_dump(
        //     $property->sale_price,
        //     $property->rent_price,
        //     $property->tribute,
        //     $property->condominium,
        // );

        return view('admin.properties.edit', [
            'property' => $property,
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
    public function update(Request $request, $id)
    {
        $property = Property::find($id);
        $property->setSaleAttribute($request->sale);
        $property->setRentAttribute($request->rent);
        $property->setAirConditioningAttribute($request->air_conditioning);
        $property->setBarAttribute($request->bar);
        $property->setLibraryAttribute($request->library);
        $property->setBarbecueGrillAttribute($request->barbecue_grill);
        $property->setAmericanKitchenAttribute($request->american_kitchen);
        $property->setFittedKitchenAttribute($request->fitted_kitchen);
        $property->setPantryAttribute($request->pantry);
        $property->setEdiculeAttribute($request->edicule);
        $property->setOfficeAttribute($request->office);
        $property->setBathtubAttribute($request->bathtub);
        $property->setFireplaceAttribute($request->fireplace);
        $property->setLavatoryAttribute($request->lavatory);
        $property->setFurnishedAttribute($request->furnished);
        $property->setPoolAttribute($request->pool);
        $property->setSteamRoomAttribute($request->steam_room);
        $property->setViewOfTheSeaAttribute($request->view_of_the_sea);

        $property->fill($request->all());
        $property->save();
        $property->setSlug();

        /** Validação das imagens */
        $validateImages = Validator::make($request->only('files'), ['files.*' => 'image'] );

        if($validateImages->fails() == true){
            return redirect()->back()->withInput()->with([
                'color' => 'orange',
                'message' => 'Todas as imagens devem ser do tipo jpg, jpeg ou png'
            ]);
        }

        // Pegas todas as imagens enviadas
        if (!empty($request->allFiles())){

            /** Insira uma validação de jpeg ou jpg */

            // Abre o array no indice files
            foreach($request->allFiles()['files'] as $image){
                // Instancia um objeto e preenche
                $propertyImage = new PropertyImage();
                $propertyImage->property = $property->id; // id do imóvel que está sendo salvo
                $propertyImage->path = $image->storeAs('properties/' . $property->id, Str::slug($request->title) . '-' . str_replace('.', '', microtime(true)) . '.' . $image->extension());
                //$propertyImage->path = $image->store('properties/' . $property->id);
                $propertyImage->save();
                unset($propertyImage);
            }
        }

        // /** Modo de teste */
        // var_dump($property);
        // die;

        return redirect()->route('admin.properties.edit', ['property' => $property->id])->with([
            'color' => 'green',
            'message' => 'Imóvel atualizado com sucesso!'
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

    public function imageSetCover(Request $request)
    {
        /** Trazer a imagem cujo o id foi informado pelo request */
        $imageSetCover = PropertyImage::find($request->image);

        /** Trazer todas as imagens que tem relação ao mesmo imóvel */        
        $allImagens = PropertyImage::where('property', $imageSetCover->property)->get();

        /** Tira a marcação de cover de todas as imagens */
        foreach($allImagens as $image){
            $image->cover = null; // Seta a posição
            $image->save(); // Salva a posição
        }

        /** Seta como cover a imagem recebida pelo request */
        $imageSetCover->cover = true;
        $imageSetCover->save();

        $json = [
            'success' => true
        ];

        return response()->json($json);
    }

    public function imageRemove(Request $request)
    {
        /** Inserir uma verificação mais segura */

        /** Trazer a imagem cujo o id foi informado pelo request */
        $imageDelete = PropertyImage::find($request->image);

        /** Limpeza da imagem */
        Storage::delete($imageDelete->path); // Deletar a imagem física
        Cropper::flush($imageDelete->path); // Limpar o cache da imagem
        $imageDelete->delete(); // Deletar o registro do banco de dados

        $json = [
            'success' => true
        ];

        return response()->json($json);
    }
}
