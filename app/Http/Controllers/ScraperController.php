<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DiDom\Document;

class ScraperController extends Controller
{
  /**
   * Simple page parser.
   *
   * @return void
   */
    public function parse(Request $request)
    {
      // validate request data
      $request->validate([
          'url' => ['required', 'regex:/(^(https\:)?\/\/www\.amazon\.co\.uk\/.+)/i'],
      ]);

      // initialise html parser
      $document = new Document($request->url, true);

      // initialize response data
      $response = [];

      // parse the property title
      $title = $document->first('#productTitle');

      if ($title != null)
      {
        $response['title'] = trim($title->text());
      }

      // parse the property asin value
      $asin = $document->first('.col2 .attrG tr td.value');

      if ($asin != null)
      {
        $response['asin'] = trim($asin->text());
      }

      // parse the property price value
      $price = $document->first('.offer-price');

      if ($price != null)
      {
        $response['price'] = trim($price->text());
      }

      // parse the property description value
      $description = $document->first('#productDescription p');

      if ($description != null)
      {
        $response['description'] = trim($description->text());
      }

      // parse the property specifications
      $specifications = $document->find('#feature-bullets .a-list-item');

      if ($specifications != null)
      {
        foreach ($specifications as $specification)
        {
          $response['specification'][] = trim($specification->text());
        }
      }

      // parse the property images
      $images = $document->find('#altImages .item img');

      if ($images != null)
      {
        foreach ($images as $image)
        {
          $response['image'][] = str_replace('_SS40_', '_SX522_', $image->getAttribute('src'));
        }
      }

      return response()->json($response);
    }
}
