<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\ProductCategory;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::get();
        return view('product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('product.create');
    }
    
    /**
     * use to format csv data
     *
     *  @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function dataMapping(Request $request)
    {
        $request->validate([
            'csvFile'    => 'required|mimes:csv',
        ]);
        
        $filename = $_FILES["csvFile"]["tmp_name"];
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $headerRow = null;
        $combineData = [];
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($rows = fgetcsv($handle, 1000, $delimiter = ',')) !== false) {
                if (!$headerRow) {
                    $headerRow = $rows;
                } else {
                    $combineData[] = array_combine($headerRow, $rows);
                }
            }
            fclose($handle);
        }

        $csvData = $combineData;
        $tablesArray = new Product();
        $tableData = $tablesArray->fieldsArray;
       
        return view('product.mapping', compact('tableData', 'headerRow', 'combineData'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $mappingData = $request['mapping'];
        $csvData = unserialize($request['csvData']);

        if (!empty($csvData)) {
            $mainDataArray = [];
            foreach ($csvData as $csvValue) {
                $mappingDataArray = [];
                foreach ($csvValue as $key => $csv) {
                    if (in_array($key, $mappingData)) {
                        $mappingDataArray[array_search($key, $mappingData)] = $csv;
                    } else {
                        return redirect()->route('product.create')->withErrors(['msg'=>"Invalid field mapping"]);
                    }
                }
                $mainDataArray[] = $mappingDataArray;
            }
        }
        if (!empty($mainDataArray)) {
            $productIds = "";
            foreach ($mainDataArray as $arrayData) {
                $skuExist = Product::where('sku', $arrayData['sku'])->first();
                $productAction = !empty($skuExist) ? $skuExist : new Product();
                if ($productAction) {
                    $productAction->sku = $arrayData['sku'];
                    $productAction->title = $arrayData['title'];
                    $productAction->description = $arrayData['description'];
                    $productAction->price = $arrayData['price'];
                    $productAction->quantity = $arrayData['quantity'];
                    $productAction->save();

                    $productIds = $productAction->id;
                }
                if (isset($arrayData['category']) && !empty($arrayData['category']) && !empty($productIds)) {
                    $this->categorySave($arrayData['category'], $productIds);
                }
            }
        }
        return redirect()->route('product.index');
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
        //
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
        //
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
    /**
     * categorySave
     *
     * @param  mixed $category
     * @param  mixed $product
     * @return void
     */
    public function categorySave($category, $product)
    {
        $categories = array_unique(explode('|', $category));
        $categoryData = [];
        foreach ($categories as $category) {
            $Exist = Category::where('name', $category)->first();
            if (empty($Exist)) {
                $categoryNew = new Category();
                $categoryNew->name = $category;
                $categoryNew->save();
            }
            $categoryData = !empty($Exist) ? $Exist : $categoryNew;

            $checkPivot = ProductCategory::where(['product_id'=>$product, 'category_id'=>$categoryData->id])->first();
            if (empty($checkPivot)) {
                $savePivot = new ProductCategory();
                $savePivot->product_id = $product;
                $savePivot->category_id = $categoryData->id;
                $savePivot->save();
            }
        }
    }
}
