<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * cheapAndExpensive
     * 
     * Route::get('/cheapAndExpensive', [ProductController::class, 'cheapAndExpensive']);
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function cheapAndExpensive(Request $request)
    {
        return response()->json([
            'data' => $this->getCheapAndExpensive($request),
        ], 200);
    }

    /**
     * byPrice
     * 
     * Route::get('/byPrice', [ProductController::class, 'byPrice']);
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function byPrice(Request $request)
    {
        // Check if the request has a price parameter and it's numeric
        $validator = Validator::make($request->all(), [
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'price is missing or not valid',
            ], 400);
        }

        return response()->json([
            'data' => $this->getByPrice($request, $request->input('price')),
        ], 200);
    }

    /**
     * mostBottles
     * 
     * Route::get('/mostBottles', [ProductController::class, 'mostBottles']);
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function mostBottles(Request $request)
    {
        return response()->json([
            'data' => $this->getMostBottles($request),
        ], 200);
    }

    /**
     * all
     * 
     * Route::get('/all', [ProductController::class, 'all']);
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        return response()->json([
            'data' => $this->getAll($request),
        ], 200);
    }

    /**
     * getCheapAndExpensive
     * 
     * Combines the cheapest and most expensive products by beer per liter
     *
     * @param  \Illuminate\Http\Request $request
     * @return Illuminate\Support\Collection
     */
    private function getCheapAndExpensive(Request $request)
    {
        // Firstly grouping the products by pricePerUnitText as a key and then sorting them by key
        $results = $this->getProductsFromJson($request)->mapToGroups(function ($item, $key) {
            // parsing the actual price from the pricePerUnitText
            return [str_replace(['(',' €/Liter)'], '', $item['articles'][0]['pricePerUnitText']) => $item];
        })->sortKeys();

        return collect([
            'cheapest' => $results->first(), 
            'mostExpensive' => $results->last()]
        );
    }

    /**
     * getByPrice
     * 
     * Gets all products with a price equal to the given price
     *
     * @param  \Illuminate\Http\Request $request
     * @param  mixed $price
     * @return Illuminate\Support\Collection
     */
    private function getByPrice(Request $request, $price)
    {
        return $this->getProductsFromJson($request)
                    ->where('articles.0.price', $price) // condition works here
                    ->sortBy('articles.0.pricePerUnitText', SORT_NATURAL);
    }

    /**
     * getMostBottles
     * 
     * Finds the only one product which has most bottles
     * 
     * In case of multiple products with the same amount of bottles,
     * the last product by descending order of 'shortDescription' returned
     * 
     * SORT_NATURAL handles ordering numbers in texts like '1,2,11,12'
     *
     * @param  \Illuminate\Http\Request $request
     * @return Illuminate\Support\Collection
     */
    private function getMostBottles(Request $request)
    {
        return $this->getProductsFromJson($request)
                    ->sortBy('articles.0.shortDescription', SORT_NATURAL)
                    ->last(); // because the task asks for 'one' product
    }

    /**
     * getAll
     * 
     * Combines all the methods above
     *
     * @param  \Illuminate\Http\Request $request
     * @return Illuminate\Support\Collection
     */
    private function getAll(Request $request)
    {
        return collect([
            'cheapestAndMostExpensiveBeers' => $this->getCheapAndExpensive($request),
            'beersCost_17_99' => $this->getByPrice($request, '17.99'),
            'mostBottles' => $this->getMostBottles($request),
        ]);
    }

    /**
     * getProductsFromJson
     * 
     * Fetchs json from the API and returns a collection of products
     *
     * @param  \Illuminate\Http\Request $request
     * @return App\Http\Resources\ProductResource
     */
    private function getProductsFromJson(Request $request)
    {
        $response = Http::get($request->input('url'));

        if($response->failed()) {
            // This is not a great way to handle errors, but it's just a demo
            // For example when Guzzle can't resolve address, it throws an exception
            // I just didn't want to check if the URL is exactly right
            // Therefore this part is open to improvement
            abort( response()->json([
                'message' => 'Couldn\'t connect to given URL'
            ], 400) );
        }

        $products = $response->json();

        if(empty($products)) {
            abort( response()->json([
                'message' => 'No products found'
            ], 204) );
        }

        // I'm trying to catch multiple sub items like the different bottle sizes of the 
        // same brand and prepend them to the master collection as new items with same 
        // main id to get a proper collection of products.
        foreach($products as $key => $product) {
            if(count($product['articles']) > 1) {
                foreach($product['articles'] as $subProduct) {
                    $products[] = array(
                        'id' => $product['id'],
                        'brandName' => $product['brandName'],
                        'name' => $product['name'],
                        'descriptionText' => $product['descriptionText'] ?? '',
                        'articles' => array($subProduct),
                    );
                }

                unset($products[$key]);
            }
        }

        return new ProductResource(collect($products)->sortBy('id', SORT_NATURAL));
    }
}