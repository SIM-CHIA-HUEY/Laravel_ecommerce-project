<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Ad;
use Illuminate\Support\Facades\DB;
use App\Models\Picture;

class welcomeController extends Controller
{
    private $categories;
    private $adPerPage;

    public function __construct()
    {
        $this->categories = Category::all();
        $this->adPerPage = 8;
    }
    // Build data for the view.
    private function getViewData($ads, $pictures, $categoryList=Null, $page=1) {
        // Reset category list.
        session()->forget('categoryList');
        // Update session data.
        session(['ads' => $ads]);
        session(['pictures' => $pictures]);
        $data = [
                'categories' => $this->categories,
                'ads' => $ads,
                'page' => $page,
                'pictures' => $pictures,
                'number_of_page' => $this->getNumberOfPage($ads)
                ];
        if(!is_null($categoryList)) {
            session(['categoryList' => $categoryList]);
            $data['categoryList'] = $categoryList;
        }
        // Return display data.
        return $data;
    }
    // Main display for ads.
    public function index() {
        // Get ads
        $ads = DB::table('ads')
                ->where('active', '=', '1')
                ->join('pictures', 'pictures.ads_id', '=', 'ads.id')
                ->where('main_picture', '=', '1')
                ->join('locations', 'locations.id', '=', 'ads.location_id')
                ->select('ads.id AS main_id', 'ads.*', 'pictures.url', 'locations.*')
                ->get();

        // Retrieve display data.
        $pictures = "";
        $viewData = $this->getViewData($ads, $pictures);

        // Disable display by line instead of box.
        session()->forget('viewinline');

        // Display the view.
        return view('welcome', $viewData);
    }
    // User input search.
    public function search(Request $request) {
        $search = '%'.$request->search.'%';
        $location = $request->location;

        // Search in the DB for the location & search data.
        if(is_null($location)) {
            $ads = DB::table('ads')
                    ->where('ads.title', 'like', $search)
                    ->where('ads.active', '=', '1')
                    ->join('pictures', 'pictures.ads_id', '=', 'ads.id')
                    ->where('pictures.main_picture', '=', '1')
                    ->join('locations', 'locations.id', '=', 'ads.location_id')
                    ->select('ads.id AS main_id', 'ads.*', 'pictures.url', 'locations.*')
                    ->get();
        } else {
            $location = '%'.$location.'%';
            $ads = DB::table('ads')
                    ->where('ads.title', 'like', $search)
                    ->where('ads.active', '=', '1')
                    ->join('pictures', 'pictures.ads_id', '=', 'ads.id')
                    ->where('pictures.main_picture', '=', '1')
                    ->join('locations', 'locations.id', '=', 'ads.location_id')
                    ->where('locations.city', 'like', $location)
                    ->select('ads.id AS main_id', 'ads.*', 'pictures.url', 'locations.*')
                    ->get();
        }
        // Get pictures data
        $pictures = DB::table('ads')
                ->where('active', '=', '1')
                ->join('pictures', 'pictures.ads_id', '=', 'ads.id')
                ->select('ads.*', 'pictures.*')
                ->get();
        // Retrieve display data.
        $viewData = $this->getViewData($ads, $pictures);

        // Active display by line instead of box.
        session(['viewinline'=>true]);

        // Display the view.
        return view('welcome', $viewData);
    }
    // Get ads for a given category.
    public function displayCategory(int $categoryID) {

        // Get ads with given IDs
        $ids = $this->buildIDArray($categoryID);
        $ads = DB::table('ads')
                ->where('active', '=', '1')
                ->whereIn('ads.category_id', $ids)
                ->join('pictures', 'pictures.ads_id', '=', 'ads.id')
                ->where('main_picture', '=', '1')
                ->join('locations', 'locations.id', '=', 'ads.location_id')
                ->select('ads.id AS main_id', 'ads.*', 'pictures.url', 'locations.*')
                ->get();
        session(['ads' => $ads]);
        $pictures = DB::table('ads')
                    ->where('active', '=', '1')
                    ->whereIn('ads.category_id', $ids)
                    ->join('pictures', 'pictures.ads_id', '=', 'ads.id')
                    ->select('ads.*', 'pictures.*')
                    ->get();

        // Get category list for breadcrumb
        $categoryList = DB::select('with recursive tree AS (
            select id, name, parent_id from categories where id=?
            union all
            select parent.id, parent.name, parent.parent_id from categories as parent
            join tree on tree.parent_id = parent.id
            )
            select id, name from tree', [$categoryID]);
        $categoryList = array_reverse($categoryList);
        
        // Active display by line instead of box.
        session(['viewinline'=>true]);

        // Retrive display data.
        $viewData = $this->getViewData($ads,$pictures, $categoryList);

        // Display the view.
        return view('welcome', $viewData);
    }
    private function buildIDArray($categoryID) {
        // Initiate array
        $ids = array();
        // Get root category ID
        $selectedCat = Category::where('id',$categoryID)->first();
        // Store it's ID in the array.
        array_push($ids, $selectedCat->id);
        // Get sub category's ID.
        $subIDs = $this->getSubID($selectedCat->id);
        // Merge IDs in the same array.
        $ids = array_merge($ids, $subIDs);
        // Return completed array.
        return $ids;
    }
    // Works with buildIDArray();
    private function getSubID($categoryID) {
        // Initiate array
        $ids = array();
        // Get sub categories.
        $subCategories = Category::where('parent_id', $categoryID)->get();
        // For each : add ID to the array and check for sub categories.
        foreach($subCategories as $category) {
            array_push($ids, $category->id);
            $subIDs = $this->getSubID($category->id);
            $ids = array_merge($subIDs, $ids);
        }
        // Return completed array.
        return $ids;
    }
    public function displayPage($page) {
        $ads = session('ads');
        $pictures = session('pictures');
        $categoryList = session('categoryList');
        if($page > $this->getNumberOfPage($ads)) {
            $page = $this->getNumberOfPage($ads);
        }
        if($page < 1) {
            $page = 1;
        }
        $viewData = $this->getViewData($ads, $pictures, $categoryList, $page);
        return view('welcome', $viewData);
    }
    private function getNumberOfPage($ads) {
        $numberOfPage = CEIL(count($ads) / $this->adPerPage);
        return $numberOfPage;
    }
    public function filters(Request $request) {
        // Retrieve data
        $ads = session('ads');
        $newAds = array();
        // Validate the form
        $validated = $request->validate([
            'min_price' => 'numeric|min:0',
            'max_price' => 'nullable|numeric|min:0'
        ]);
        $min_price = $request->min_price;
        $max_price = $request->max_price;
        if(!is_null($max_price) && $max_price < $min_price) {
            $max_price = $min_price;
        }
        foreach($ads as $ad) {
            if(!is_null($max_price)) {
                if($ad->price < $max_price && $ad->price > $min_price) {
                    array_push($newAds, $ad);
                }
            } else {
                if($ad->price > $min_price) {
                    array_push($newAds, $ad);
                }
            }
        }
        // Get pictures data
        $pictures = DB::table('ads')
                ->where('active', '=', '1')
                ->join('pictures', 'pictures.ads_id', '=', 'ads.id')
                ->select('ads.*', 'pictures.*')
                ->get();
                
        // Retrieve display data.
        $viewData = $this->getViewData($newAds, $pictures);

        // Active display by line instead of box.
        session(['viewinline'=>true]);
        return view('welcome', $viewData);
    }
}