<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FilterController extends Controller
{
    public function search(Request $request)
    {
        session()->remove('category');
        session()->remove('type');
        session()->remove('neighborhood');
        session()->remove('bedrooms');
        session()->remove('suites');
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('price_base');
        session()->remove('price_limit');

        /** Só temos uma posição na sessão */
        if($request->search === 'buy'){
            session()->put('sale', true);
            session()->remove('rent');
            $properties = $this->createQuery('category');
        }

        if($request->search === 'rent'){
            session()->put('rent', true);
            session()->remove('sale');
            $properties = $this->createQuery('category');
        }

        /** Caso tenha algum imóvel... */
        if($properties->count()){
            /** Alimenta um array com todas as categorias */
            foreach($properties as $categoryProperty){
                $category[] = $categoryProperty->category;
            }

            /** Joga em um coletivo */
            $collect = collect($category);

            /** Devolve a resposta */
            return response()->json($this->setResponse(
                'success',
                $collect->unique()->toArray() // Pega os valores unicos e devolve em array
            ));
        }

        /** Caso não atenda nenhuma pesquisa */
        return response()->json($this->setResponse(
            'fail',
            [],
            'Ooops, não foi retornado nenhum dado para a pesquisa'
        ));
    }

    public function category(Request $request)
    {
        session()->remove('type');
        session()->remove('neighborhood');
        session()->remove('bedrooms');
        session()->remove('suites');
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('price_base');
        session()->remove('price_limit');

        session()->put('category', $request->search);
        $typeProperties = $this->createQuery('type');

        /** Caso tenha alguma categoria... */
        if($typeProperties->count()){
            /** Alimenta um array com todas as categorias */
            foreach($typeProperties as $property){
                $type[] = $property->type;
            }

            /** Joga em um coletivo */
            $collect = collect($type)->unique()->toArray();// Pega os valores unicos e devolve em array

            /** Devolve a resposta */
            return response()->json($this->setResponse(
                'success', $collect 
            ));
        }
    }

    public function type(Request $request)
    {
        session()->remove('neighborhood');
        session()->remove('bedrooms');
        session()->remove('suites');
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('price_base');
        session()->remove('price_limit');

        session()->put('type', $request->search);
        $neighborhoodProperties = $this->createQuery('neighborhood');

        /** Caso tenha alguma categoria... */
        if($neighborhoodProperties->count()){
            /** Alimenta um array com todas as categorias */
            foreach($neighborhoodProperties as $property){
                $neighborhood[] = $property->neighborhood;
            }

            /** Joga em um coletivo */
            $collect = collect($neighborhood)->unique()->toArray();// Pega os valores unicos e devolve em array

            /** Devolve a resposta */
            return response()->json($this->setResponse(
                'success', $collect 
            ));
        }
    }

    public function neighborhood(Request $request)
    {
        session()->remove('bedrooms');
        session()->remove('suites');
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('price_base');
        session()->remove('price_limit');

        session()->put('neighborhood', $request->search);
        $bedroomsProperties = $this->createQuery('bedrooms');

        /** Caso tenha alguma categoria... */
        if($bedroomsProperties->count()){

            /** Alimenta um array com todas as categorias */
            foreach($bedroomsProperties as $property){
                if($property->bedrooms == 0 || $property->bedrooms == 1){
                    $bedrooms[] = $property->bedrooms . " quarto";
                } else {
                    $bedrooms[] = $property->bedrooms . " quartos";
                } 
            }
            $bedrooms[] = "Indiferente";

            /** Joga em um coletivo */
            $collect = collect($bedrooms)->unique()->toArray();// Pega os valores unicos e devolve em array
            sort($collect);

            /** Devolve a resposta */
            return response()->json($this->setResponse(
                'success', $collect 
            ));
        }
    }

    public function bedrooms(Request $request)
    {
        session()->remove('suites');
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('price_base');
        session()->remove('price_limit');

        session()->put('bedrooms', $request->search);
        $suitesProperties = $this->createQuery('suites');

        /** Caso tenha alguma categoria... */
        if($suitesProperties->count()){
            
            /** Alimenta um array com todas as categorias */
            foreach($suitesProperties as $property){
                if($property->suites == 1 || $property->suites == 0){
                    $suites[] = $property->suites . " suíte";
                } else {
                    $suites[] = $property->suites . " suítes";
                }
            }
            $suites[] = "Indiferente";

            /** Joga em um coletivo */
            $collect = collect($suites)->unique()->toArray();// Pega os valores unicos e devolve em array
            sort($collect);

            /** Devolve a resposta */
            return response()->json($this->setResponse(
                'success', $collect 
            ));
        }
    }

    public function suites(Request $request)
    {
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('price_base');
        session()->remove('price_limit');

        session()->put('suites', $request->search);
        $suitesProperties = $this->createQuery('bathrooms');

        /** Caso tenha alguma categoria... */
        if($suitesProperties->count()){
            
            /** Alimenta um array com todas as categorias */
            foreach($suitesProperties as $property){
                if($property->bathrooms == 1 || $property->bathrooms == 0){
                    $bathrooms[] = $property->bathrooms . " banheiro";
                } else {
                    $bathrooms[] = $property->bathrooms . " banheiros";
                }
            }

            $bathrooms[] = "Indiferente";

            /** Joga em um coletivo */
            $collect = collect($bathrooms)->unique()->toArray();// Pega os valores unicos e devolve em array
            sort($collect);

            /** Devolve a resposta */
            return response()->json($this->setResponse(
                'success', $collect 
            ));
        }
    }

    public function bathrooms(Request $request)
    {
        session()->remove('garage');
        session()->remove('price_base');
        session()->remove('price_limit');

        session()->put('bathrooms', $request->search);
        $garageProperties = $this->createQuery('garage,garage_covered');

        /** Caso tenha alguma categoria... */
        if($garageProperties->count()){
            
            /** Alimenta um array com todas as categorias */
            foreach($garageProperties as $property){
                $property->garage = $property->garage + $property->garage_covered;

                if($property->garage == 1 || $property->garage == 0){
                    $garage[] = $property->garage . " garagem";
                } else {
                    $garage[] = $property->garage . " garagens";
                }
            }

            $garage[] = "Indiferente";

            /** Joga em um coletivo */
            $collect = collect($garage)->unique()->toArray();// Pega os valores unicos e devolve em array
            sort($collect);

            /** Devolve a resposta */
            return response()->json($this->setResponse(
                'success', $collect 
            ));
        }
    }

    public function garage(Request $request)
    {
        session()->remove('price_base');
        session()->remove('price_limit');

        session()->put('garage', $request->search);

        // Se é compra, pesquise por valores de compra
        if(session('sale') == true){
            $priceBaseProperties = $this->createQuery('sale_price as price');
        } else {
            $priceBaseProperties = $this->createQuery('rent_price as price');
        }

        if($priceBaseProperties->count()){

            foreach($priceBaseProperties as $property){
                $price[] = /*"De R$ " . */number_format($property->price, 2, ',', '.');
            }

            /** Joga em um coletivo */
            $collect = collect($price)->unique()->toArray();// Pega os valores unicos e devolve em array
            sort($collect);

            /** Devolve a resposta */
            return response()->json($this->setResponse(
                'success', $collect 
            ));
        }
    }

    public function priceBase(Request $request)
    {
        session()->remove('price_limit');

        session()->put('price_base', $request->search);

        // Se é compra, pesquise por valores de compra
        if(session('sale') == true){
            $priceLimitProperties = $this->createQuery('sale_price as price');
        } else {
            $priceLimitProperties = $this->createQuery('rent_price as price');
        }

        if($priceLimitProperties->count()){

            foreach($priceLimitProperties as $property){
                $price[] = /*"Até R$ " . */number_format($property->price, 2, ',', '.');
            }

            /** Joga em um coletivo */
            $collect = collect($price)->unique()->toArray();// Pega os valores unicos e devolve em array
            sort($collect);

            /** Devolve a resposta */
            return response()->json($this->setResponse(
                'success', $collect 
            ));
        }
    }

    public function priceLimit(Request $request)
    {
        session()->put('price_limit', $request->search);
        return response()->json($this->setResponse('success', [] ));
    }
    
    /**
     * Padronização de respostas
     */
    private function setResponse(string $status, array $data = null, string $message = null)
    {
        return [
            'status' => $status,
            'data' => $data,
            'message' => $message
        ];
    }

    public function clearAllData()
    {
        session()->remove('category');
        session()->remove('sale');
        session()->remove('rent');
        session()->remove('type');
        session()->remove('neighborhood');
        session()->remove('bedrooms');
        session()->remove('suites');
        session()->remove('bathrooms');
        session()->remove('garage');
        session()->remove('price_base');
        session()->remove('price_limit');
    }

    public function createQuery($field)
    {
        $sale = session('sale');
        $rent = session('rent');
        $category = session('category');
        $type = session('type');
        $neighborhood = session('neighborhood');
        $bedrooms = session('bedrooms');
        $suites = session('suites');
        $bathrooms = session('bathrooms');
        $garage = session('garage');
        $priceBase = session('price_base');
        $priceLimit = session('price_limit');

        return DB::table('properties')
            ->where('status', '=', '1')
            ->when($sale, function($query, $sale){ // Retorna bool, caso a coluna seja igual a sale.
                return $query->where('sale', $sale);
            })
            ->when($rent, function($query, $rent){
                return $query->where('rent', $rent);
            })
            ->when($category, function($query, $category){ 
                return $query->where('category', $category);
            })
            // A partir daqui, retorna vários tipos
            ->when($type, function($query, $type){ 
                return $query->whereIn('type', $type);
            })
            ->when($neighborhood, function($query, $neighborhood){ 
                return $query->whereIn('neighborhood', $neighborhood);
            })
            //Voltamos a retornar 1 tipo só
            ->when($bedrooms, function($query, $bedrooms){ 
                // Se for indiferente, faça a consulta sem filtro
                if($bedrooms == "Indiferente"){
                    return $query;
                }
                // Pegue somente a parte inteira da variavel
                $bedrooms = (int) $bedrooms;
                return $query->where('bedrooms', $bedrooms);
            })
            ->when($suites, function($query, $suites){ 
                if($suites == "Indiferente"){
                    return $query;
                }
                $suites = (int) $suites;
                return $query->where('suites', $suites);
            })
            ->when($bathrooms, function($query, $bathrooms){ 
                if($bathrooms == "Indiferente"){
                    return $query;
                }
                $bathrooms = (int) $bathrooms;
                return $query->where('bathrooms', $bathrooms);
            })
            ->when($garage, function($query, $garage){ 
                if($garage == "Indiferente"){
                    return $query;
                }
                $garage = (int) $garage;
                // Devolve 3 possibilidades
                return $query->whereRaw('(garage + garage_covered = ? OR garage = ? OR garage_covered = ?)', [$garage, $garage, $garage]);
            })
            ->when($priceBase, function($query, $priceBase){ 
                if($priceBase == "Indiferente"){
                    return $query;
                }

                //$explode = (float) explode('R$ ', $priceBase, 2)[1];
                $replace = (float) str_replace('.', '', $priceBase);
                $priceBase = (float) str_replace(',', '', $replace);

                // Caso venda, pesquisar valores de compra
                if(session('sale') == true){
                    return $query->where('sale_price', '>=', $priceBase);
                } else {
                    return $query->where('rent_price', '>=', $priceBase);
                }                
            })
            ->when($priceLimit, function($query, $priceLimit){ 
                if($priceLimit == "Indiferente"){
                    return $query;
                }

                //$explode = (float) explode('R$ ', $priceLimit, 2)[1];
                $replace = (float) str_replace('.', '', $priceLimit);
                $priceLimit = (float) str_replace(',', '', $replace);

                // Caso venda, pesquisar valores de compra
                if(session('sale') == true){
                    return $query->where('sale_price', '<=', $priceLimit);
                } else {
                    return $query->where('rent_price', '<=', $priceLimit);
                }                
            })
            ->get(explode(',', $field));

    }
}
